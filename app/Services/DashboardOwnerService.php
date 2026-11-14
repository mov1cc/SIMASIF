<?php

namespace App\Services;

use App\Core\Database;
use DateTimeImmutable;
use PDO;

class DashboardOwnerService
{
    /** kode periode => label */
    public const PERIODE = [
        '7'     => '7 Hari',
        '30'    => '30 Hari',
        '90'    => '90 Hari',
        'bulan' => 'Bulan Ini',
    ];

    public const PERIODE_DEFAULT = '30';

    /** Fragmen WHERE rentang tanggal (parameter: dari, sampai) */
    private const RENTANG = "(%s >= CAST(? AS DATE)
                              AND %s < CAST(? AS DATE) + INTERVAL '1 day')";

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /** Whitelist kode periode dari query string */
    public static function normalisasiPeriode(mixed $raw): string
    {
        $p = is_scalar($raw) ? (string) $raw : '';

        return isset(self::PERIODE[$p]) ? $p : self::PERIODE_DEFAULT;
    }

    /**
     * Rentang tanggal Y-m-d (inklusif) untuk satu kode periode.
     *
     * @return array{dari:string, sampai:string}
     */
    public static function rentang(string $periode): array
    {
        $hariIni = new DateTimeImmutable('today');

        $dari = $periode === 'bulan'
            ? $hariIni->modify('first day of this month')
            : $hariIni->modify('-' . ((int) $periode - 1) . ' days');

        return [
            'dari'   => $dari->format('Y-m-d'),
            'sampai' => $hariIni->format('Y-m-d'),
        ];
    }

    /**
     * Angka untuk kartu statistik.
     *
     * @return array{pemesanan:int, pelanggan_total:int, pelanggan_baru:int,
     *               pendapatan:float, booking_aktif:int}
     */
    public function ringkasan(string $dari, string $sampai): array
    {
        $pemesanan = $this->satu(
            "SELECT COUNT(*) AS n FROM pemesanan
              WHERE " . sprintf(self::RENTANG, 'created_at', 'created_at'),
            [$dari, $sampai]
        );

        $pelanggan = $this->satu(
            "SELECT COUNT(*) AS total,
                    COUNT(*) FILTER (WHERE "
                    . sprintf(self::RENTANG, 'created_at', 'created_at')
                    . ") AS baru
               FROM pelanggan",
            [$dari, $sampai]
        );

        $pendapatan = $this->satu(
            "SELECT COALESCE(SUM(jumlah_bayar), 0) AS total
               FROM transaksi_pembayaran
              WHERE status_pembayaran = 'lunas'
                AND " . sprintf(self::RENTANG, 'diverifikasi_at', 'diverifikasi_at'),
            [$dari, $sampai]
        );

        $aktif = $this->satu(
            "SELECT COUNT(*) AS n FROM pemesanan WHERE status <> 'selesai'"
        );

        return [
            'pemesanan'       => (int) $pemesanan['n'],
            'pelanggan_total' => (int) $pelanggan['total'],
            'pelanggan_baru'  => (int) $pelanggan['baru'],
            'pendapatan'      => (float) $pendapatan['total'],
            'booking_aktif'   => (int) $aktif['n'],
        ];
    }

    /**
     * Pendapatan per hari (hari tanpa transaksi tetap muncul dengan nilai 0).
     *
     * @return array<int, array{tanggal:string, total:float}>
     */
    public function pendapatanHarian(string $dari, string $sampai): array
    {
        $rows = $this->semua(
            "SELECT to_char(d, 'YYYY-MM-DD') AS tanggal,
                    COALESCE(SUM(t.jumlah_bayar), 0) AS total
               FROM generate_series(CAST(? AS TIMESTAMP),
                                    CAST(? AS TIMESTAMP),
                                    INTERVAL '1 day') AS d
               LEFT JOIN transaksi_pembayaran t
                      ON t.status_pembayaran = 'lunas'
                     AND t.diverifikasi_at >= d
                     AND t.diverifikasi_at <  d + INTERVAL '1 day'
              GROUP BY d
              ORDER BY d",
            [$dari, $sampai]
        );

        return array_map(fn (array $r) => [
            'tanggal' => $r['tanggal'],
            'total'   => (float) $r['total'],
        ], $rows);
    }

    /**
     * Layanan terpopuler pada periode (berdasarkan jumlah pemesanan).
     *
     * @return array<int, array{nama:string, sesi:int, omzet:float}>
     */
    public function layananTerpopuler(string $dari, string $sampai, int $limit = 5): array
    {
        $rows = $this->semua(
            "SELECT l.nama,
                    COUNT(p.id) AS sesi,
                    COALESCE(SUM(CASE WHEN t.status_pembayaran = 'lunas'
                                      THEN t.jumlah_bayar ELSE 0 END), 0) AS omzet
               FROM layanan l
               JOIN pemesanan p
                 ON p.layanan_id = l.id
                AND " . sprintf(self::RENTANG, 'p.created_at', 'p.created_at') . "
               LEFT JOIN transaksi_pembayaran t ON t.pemesanan_id = p.id
              GROUP BY l.id, l.nama
              ORDER BY sesi DESC, omzet DESC, l.nama ASC
              LIMIT " . max(1, $limit),
            [$dari, $sampai]
        );

        return array_map(fn (array $r) => [
            'nama'  => $r['nama'],
            'sesi'  => (int) $r['sesi'],
            'omzet' => (float) $r['omzet'],
        ], $rows);
    }

    /**
     * Aktivitas operasional terbaru (sementara dari riwayat workflow,
     * diganti audit_log di Tahap 13).
     */
    public function aktivitasTerbaru(int $limit = 8): array
    {
        return $this->semua(
            "SELECT pemesanan_id, kode_unik, pelanggan_nama, status,
                    diubah_oleh_nama, created_at
               FROM v_riwayat_workflow
              ORDER BY created_at DESC, workflow_id DESC
              LIMIT " . max(1, $limit)
        );
    }

    // ------------------------------------------------------------------

    private function semua(string $sql, array $params = []): array
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    private function satu(string $sql, array $params = []): array
    {
        return $this->semua($sql, $params)[0] ?? [];
    }
}