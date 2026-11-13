<?php

namespace App\Models;

use App\Core\Model;

class FileFotografi extends Model
{
    protected string $table = 'file_fotografi';

    public const JENIS_REFERENSI = 'referensi';
    public const JENIS_HASIL     = 'hasil';

    public const TERSEDIA        = 'tersedia';
    public const BELUM_TERSEDIA  = 'belum_tersedia';

    /** Status pemesanan yang membolehkan pelanggan mengunduh hasil */
    public const STATUS_BOLEH_UNDUH = ['hasil_siap', 'selesai'];

    /** jenis_file => subfolder di storage/ */
    private const DIR = [
        self::JENIS_REFERENSI => 'upload-pelanggan',
        self::JENIS_HASIL     => 'hasil-foto',
    ];

    /** Folder fisik per jenis (di luar public/) */
    public static function dirAbs(string $jenis): string
    {
        $sub = self::DIR[$jenis]
            ?? throw new \InvalidArgumentException('Jenis file tidak dikenal.');

        return BASE_PATH . '/storage/' . $sub;
    }

    /** Path fisik file. basename(): nilai dari DB tidak bisa keluar dari folder. */
    public static function pathFisik(array $row): string
    {
        return self::dirAbs($row['jenis_file'])
            . DIRECTORY_SEPARATOR
            . basename($row['nama_file']);
    }

    public function find(int $id): ?array
    {
        return parent::find($id);
    }

    /**
     * File satu pemesanan, urut dari yang paling awal.
     */
    public function findByPemesanan(int $pemesananId, ?string $jenis = null): array
    {
        if ($jenis === null) {
            return $this->query(
                "SELECT * FROM file_fotografi
                  WHERE pemesanan_id = ?
                  ORDER BY created_at ASC, id ASC",
                [$pemesananId]
            );
        }

        return $this->query(
            "SELECT * FROM file_fotografi
              WHERE pemesanan_id = ?
                AND jenis_file = CAST(? AS jenis_file_fotografi)
              ORDER BY created_at ASC, id ASC",
            [$pemesananId, $jenis]
        );
    }

    public function countByJenis(int $pemesananId, string $jenis): int
    {
        $row = $this->first(
            "SELECT COUNT(*) AS n FROM file_fotografi
              WHERE pemesanan_id = ?
                AND jenis_file = CAST(? AS jenis_file_fotografi)",
            [$pemesananId, $jenis]
        );

        return (int) ($row['n'] ?? 0);
    }

    /**
     * Simpan metadata file.
     * diunggah_oleh WAJIB NULL untuk 'referensi' dan WAJIB terisi untuk 'hasil'
     * (constraint chk_pengunggah_file).
     *
     * @param array{pemesanan_id:int, jenis:string, nama_asli:string, nama_file:string,
     *              ukuran:int, mime:string, status:string, diunggah_oleh:?int} $d
     */
    public function create(array $d): int
    {
        $row = $this->first(
            "INSERT INTO file_fotografi
                (pemesanan_id, jenis_file, nama_file_asli, nama_file, path_file,
                 ukuran_file, mime_type, status_file, diunggah_oleh)
             VALUES (?, CAST(? AS jenis_file_fotografi), ?, ?, ?,
                     ?, ?, CAST(? AS status_file_fotografi), ?)
             RETURNING id",
            [
                $d['pemesanan_id'],
                $d['jenis'],
                $d['nama_asli'],
                $d['nama_file'],
                self::DIR[$d['jenis']] . '/' . $d['nama_file'],
                $d['ukuran'],
                $d['mime'],
                $d['status'],
                $d['diunggah_oleh'],
            ]
        );

        return (int) $row['id'];
    }

    public function setStatus(int $id, string $status): bool
    {
        return $this->execute(
            "UPDATE file_fotografi
                SET status_file = CAST(? AS status_file_fotografi)
              WHERE id = ?",
            [$status, $id]
        );
    }

    /**
     * Hapus baris, kembalikan baris yang dihapus (untuk menghapus file fisiknya).
     */
    public function hapus(int $id): ?array
    {
        $row = $this->find($id);

        if ($row === null) {
            return null;
        }

        $this->execute("DELETE FROM file_fotografi WHERE id = ?", [$id]);

        return $row;
    }
}