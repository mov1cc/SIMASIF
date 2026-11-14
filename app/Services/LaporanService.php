<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class LaporanService
{
    /** Fragmen WHERE rentang tanggal inklusif (parameter: dari, sampai) */
    private const RENTANG = "(%s >= CAST(? AS DATE)
                              AND %s < CAST(? AS DATE) + INTERVAL '1 day')";

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Laporan pemesanan yang DIBUAT pada rentang tanggal.
     *
     * @return array{rows:array, ringkasan:array}
     */
    public function pemesanan(string $dari, string $sampai, string $status = ''): array
    {
        $sql = "SELECT pemesanan_id AS id, kode_unik, status, tanggal_jadwal, jam_jadwal,
                       pelanggan_nama, layanan_nama, total_tagihan,
                       status_pembayaran, created_at
                  FROM v_detail_pemesanan
                 WHERE " . sprintf(self::RENTANG, 'created_at', 'created_at');
        $params = [$dari, $sampai];

        if ($status !== '') {
            $sql     .= " AND status = CAST(? AS status_pemesanan)";
            $params[] = $status;
        }

        $sql .= " ORDER BY created_at ASC, pemesanan_id ASC";

        $rows = $this->semua($sql, $params);

        $perStatus  = [];
        $perLayanan = [];
        $nilai      = 0.0;

        foreach ($rows as $r) {
            $perStatus[$r['status']]         = ($perStatus[$r['status']] ?? 0) + 1;
            $perLayanan[$r['layanan_nama']]  = ($perLayanan[$r['layanan_nama']] ?? 0) + 1;
            $nilai                          += (float) $r['total_tagihan'];
        }

        arsort($perLayanan);

        return [
            'rows'      => $rows,
            'ringkasan' => [
                'total'       => count($rows),
                'nilai'       => $nilai,
                'per_status'  => $perStatus,
                'per_layanan' => $perLayanan,
            ],
        ];
    }

    /**
     * Laporan pendapatan: pembayaran LUNAS yang diverifikasi pada rentang tanggal.
     * (Dasar yang sama dengan dashboard owner supaya angkanya cocok.)
     *
     * @return array{rows:array, ringkasan:array}
     */
    public function pendapatan(string $dari, string $sampai): array
    {
        $rows = $this->semua(
            "SELECT transaksi_id, pemesanan_id, kode_unik, pelanggan_nama, layanan_nama,
                    metode_pembayaran, jumlah_bayar, diverifikator_nama, diverifikasi_at
               FROM v_detail_pembayaran
              WHERE status_pembayaran = 'lunas'
                AND " . sprintf(self::RENTANG, 'diverifikasi_at', 'diverifikasi_at') . "
              ORDER BY diverifikasi_at ASC, transaksi_id ASC",
            [$dari, $sampai]
        );

        $total      = 0.0;
        $perMetode  = [];
        $perLayanan = [];
        $perHari    = [];

        foreach ($rows as $r) {
            $jumlah = (float) $r['jumlah_bayar'];
            $total += $jumlah;

            $m = $r['metode_pembayaran'];
            $perMetode[$m]['n']     = ($perMetode[$m]['n'] ?? 0) + 1;
            $perMetode[$m]['total'] = ($perMetode[$m]['total'] ?? 0) + $jumlah;

            $l = $r['layanan_nama'];
            $perLayanan[$l]['n']     = ($perLayanan[$l]['n'] ?? 0) + 1;
            $perLayanan[$l]['total'] = ($perLayanan[$l]['total'] ?? 0) + $jumlah;

            $h = substr((string) $r['diverifikasi_at'], 0, 10);
            $perHari[$h] = ($perHari[$h] ?? 0) + $jumlah;
        }

        uasort($perLayanan, fn ($a, $b) => $b['total'] <=> $a['total']);

        $n = count($rows);

        return [
            'rows'      => $rows,
            'ringkasan' => [
                'total'       => $total,
                'jumlah'      => $n,
                'rata_rata'   => $n > 0 ? $total / $n : 0.0,
                'per_metode'  => $perMetode,
                'per_layanan' => $perLayanan,
                'per_hari'    => $perHari,
            ],
        ];
    }

    private function semua(string $sql, array $params = []): array
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}