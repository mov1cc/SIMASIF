<?php

namespace App\Models;

use App\Core\Model;

class WorkflowLayanan extends Model
{
    protected string $table = 'workflow_layanan';

    /**
     * Tambah satu baris riwayat perubahan status.
     * $sebelumnya NULL hanya untuk entri pertama.
     */
    public function create(
        int $pemesananId,
        ?string $sebelumnya,
        string $status,
        ?string $catatan,
        ?int $diubahOleh
    ): int {
        $row = $this->first(
            "INSERT INTO workflow_layanan
                (pemesanan_id, status_sebelumnya, status, catatan, diubah_oleh)
             VALUES (?, CAST(? AS status_pemesanan), CAST(? AS status_pemesanan), ?, ?)
             RETURNING id",
            [$pemesananId, $sebelumnya, $status, $catatan, $diubahOleh]
        );

        return (int) $row['id'];
    }

    /**
     * Riwayat satu pemesanan, urut dari yang paling awal.
     */
    public function findByPemesanan(int $pemesananId): array
    {
        return $this->query(
            "SELECT status_sebelumnya, status, catatan, created_at, diubah_oleh_nama
               FROM v_riwayat_workflow
              WHERE pemesanan_id = ?
              ORDER BY urutan_ke ASC",
            [$pemesananId]
        );
    }

    /**
     * Ambil status saat ini sambil mengunci barisnya sampai COMMIT/ROLLBACK.
     * Harus dipanggil di dalam transaksi.
     */
    public function kunciStatus(int $pemesananId): ?string
    {
        $row = $this->first(
            "SELECT status::text AS status
               FROM pemesanan
              WHERE id = ?
                FOR UPDATE",
            [$pemesananId]
        );

        return $row['status'] ?? null;
    }

    /**
     * Ubah status pemesanan. pegawai_id hanya diisi kalau masih kosong.
     */
    public function ubahStatusPemesanan(int $pemesananId, string $status, int $pegawaiId): bool
    {
        return $this->execute(
            "UPDATE pemesanan
                SET status     = CAST(? AS status_pemesanan),
                    pegawai_id = COALESCE(pegawai_id, ?)
              WHERE id = ?",
            [$status, $pegawaiId, $pemesananId]
        );
    }
}