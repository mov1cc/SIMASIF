<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class DashboardPegawaiService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Angka ringkas untuk kartu dashboard pegawai.
     *
     * @return array{sesi_hari_ini:int, pemesanan_baru:int, menunggu_verifikasi:int, berjalan:int}
     */
    public function ringkasan(): array
    {
        $hariIni = date('Y-m-d');

        $sesi = $this->satu(
            "SELECT COUNT(*) AS n FROM pemesanan
              WHERE tanggal_jadwal = CAST(? AS DATE)",
            [$hariIni]
        );

        $baru = $this->satu(
            "SELECT COUNT(*) AS n FROM pemesanan
              WHERE created_at >= CAST(? AS DATE)",
            [$hariIni]
        );

        $menunggu = $this->satu(
            "SELECT COUNT(*) AS n FROM transaksi_pembayaran
              WHERE status_pembayaran = 'menunggu_verifikasi'"
        );

        $berjalan = $this->satu(
            "SELECT COUNT(*) AS n FROM pemesanan
              WHERE status IN ('terjadwal', 'sesi_foto', 'proses_editing')"
        );

        return [
            'sesi_hari_ini'       => (int) $sesi['n'],
            'pemesanan_baru'      => (int) $baru['n'],
            'menunggu_verifikasi' => (int) $menunggu['n'],
            'berjalan'            => (int) $berjalan['n'],
        ];
    }

    /** Pemesanan terbaru (default 5) */
    public function pemesananTerbaru(int $limit = 5): array
    {
        return $this->semua(
            "SELECT pemesanan_id AS id, kode_unik, status, status_pembayaran,
                    pelanggan_nama, layanan_nama, tanggal_jadwal, jam_jadwal, created_at
               FROM v_detail_pemesanan
              ORDER BY created_at DESC, pemesanan_id DESC
              LIMIT " . max(1, $limit)
        );
    }

    /** Sesi foto yang dijadwalkan hari ini, urut jam */
    public function jadwalHariIni(): array
    {
        return $this->semua(
            "SELECT pemesanan_id AS id, kode_unik, status, jam_jadwal, jam_selesai,
                    pelanggan_nama, layanan_nama, pegawai_nama
               FROM v_detail_pemesanan
              WHERE tanggal_jadwal = CAST(? AS DATE)
              ORDER BY jam_jadwal ASC, pemesanan_id ASC",
            [date('Y-m-d')]
        );
    }

    /** Transaksi yang menunggu verifikasi, paling lama menunggu di atas */
    public function transaksiMenunggu(int $limit = 5): array
    {
        return $this->semua(
            "SELECT transaksi_id, pemesanan_id, kode_unik, pelanggan_nama,
                    metode_pembayaran, jumlah_bayar, created_at
               FROM v_detail_pembayaran
              WHERE status_pembayaran = 'menunggu_verifikasi'
              ORDER BY created_at ASC
              LIMIT " . max(1, $limit)
        );
    }

    /**
     * Aktivitas operasional terbaru.
     * Sumber sementara: riwayat workflow (audit_log baru terisi di Tahap 13).
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