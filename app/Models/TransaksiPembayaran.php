<?php

namespace App\Models;

use App\Core\Model;

class TransaksiPembayaran extends Model
{
    protected string $table = 'transaksi_pembayaran';

    /**
     * Transaksi milik satu pemesanan (maksimal satu).
     */
    public function findByPemesanan(int $pemesananId): ?array
    {
        return $this->first(
            "SELECT * FROM transaksi_pembayaran WHERE pemesanan_id = ? LIMIT 1",
            [$pemesananId]
        );
    }

    /**
     * Detail satu transaksi (lengkap dengan pelanggan/layanan) dari view.
     */
    public function findDetail(int $transaksiId): ?array
    {
        return $this->first(
            "SELECT * FROM v_detail_pembayaran WHERE transaksi_id = ? LIMIT 1",
            [$transaksiId]
        );
    }

    /**
     * Ambil transaksi sambil mengunci barisnya sampai COMMIT/ROLLBACK.
     * Harus dipanggil di dalam transaksi database.
     */
    public function kunci(int $transaksiId): ?array
    {
        return $this->first(
            "SELECT * FROM transaksi_pembayaran WHERE id = ? FOR UPDATE",
            [$transaksiId]
        );
    }

    /**
     * Daftar transaksi untuk pegawai. Yang menunggu verifikasi tampil paling atas.
     *
     * @param array{q?:string, status?:string} $f
     */
    public function all(array $f = []): array
    {
        $sql    = "SELECT * FROM v_detail_pembayaran WHERE 1 = 1";
        $params = [];

        if (!empty($f['q'])) {
            $like     = '%' . addcslashes($f['q'], '%_\\') . '%';
            $sql     .= " AND (kode_unik ILIKE ? OR pelanggan_nama ILIKE ? OR pelanggan_no_hp LIKE ?)";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if (!empty($f['status'])) {
            $sql     .= " AND status_pembayaran = CAST(? AS status_pembayaran)";
            $params[] = $f['status'];
        }

        $sql .= " ORDER BY CASE WHEN status_pembayaran = 'menunggu_verifikasi' THEN 0 ELSE 1 END,
                           created_at DESC";

        return $this->query($sql, $params);
    }

    /**
     * Simpan transaksi baru, mengembalikan ID.
     */
    public function create(array $d): int
    {
        $row = $this->first(
            "INSERT INTO transaksi_pembayaran
                (pemesanan_id, metode_pembayaran, jumlah_bayar, nomor_referensi, bukti_transfer)
             VALUES (?, CAST(? AS metode_pembayaran), ?, ?, ?)
             RETURNING id",
            [
                $d['pemesanan_id'],
                $d['metode'],
                $d['jumlah'],
                $d['nomor_referensi'],
                $d['bukti'],
            ]
        );

        return (int) $row['id'];
    }

    /**
     * Kirim ulang setelah ditolak: baris lama dipakai lagi dan status direset.
     */
    public function kirimUlang(int $id, array $d): bool
    {
        return $this->execute(
            "UPDATE transaksi_pembayaran
                SET metode_pembayaran = CAST(? AS metode_pembayaran),
                    jumlah_bayar      = ?,
                    nomor_referensi   = ?,
                    bukti_transfer    = ?,
                    status_pembayaran = 'menunggu_verifikasi',
                    diverifikasi_oleh = NULL,
                    diverifikasi_at   = NULL
              WHERE id = ?",
            [$d['metode'], $d['jumlah'], $d['nomor_referensi'], $d['bukti'], $id]
        );
    }

    /**
     * Ubah status (lunas / ditolak) beserta pegawai yang memverifikasi.
     * Constraint chk_verifikasi_pembayaran_transfer mewajibkan diverifikasi_oleh terisi.
     */
    public function updateStatus(int $id, string $status, int $pegawaiId): bool
    {
        return $this->execute(
            "UPDATE transaksi_pembayaran
                SET status_pembayaran = CAST(? AS status_pembayaran),
                    diverifikasi_oleh = ?,
                    diverifikasi_at   = NOW()
              WHERE id = ?",
            [$status, $pegawaiId, $id]
        );
    }
}