<?php

namespace App\Services;

class UploadService
{
    /** MIME gambar yang diizinkan => ekstensi yang dipakai di server (SVG sengaja tidak diizinkan) */
    public const MIME_GAMBAR = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

        /** MIME bukti pembayaran: gambar atau PDF */
    public const MIME_BUKTI = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/webp'      => 'webp',
        'application/pdf' => 'pdf',
    ];

        /** MIME file fotografi: gambar atau ZIP */
    public const MIME_FOTO = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/webp'      => 'webp',
        'application/zip' => 'zip',
    ];

    /**
     * Validasi dan simpan satu file upload.
     *
     * Ekstensi ditentukan dari MIME hasil pembacaan isi file, bukan dari nama
     * asli, dan nama di server diacak (anti tabrakan dan path traversal).
     *
     * @param array|null           $file        entri $_FILES['field']
     * @param array<string,string> $mimeExt     mime => ekstensi yang diizinkan
     * @param bool                 $wajibGambar true = isi file harus lolos getimagesize()
     *
     * @return array{ok:bool, error?:string, nama_file?:string, nama_asli?:string, ukuran?:int, mime?:string}
     */
    public static function simpan(
        ?array $file,
        string $dirAbs,
        array $mimeExt,
        int $maksByte,
        bool $wajibGambar = false
    ): array {
        if ($file === null || !isset($file['error']) || is_array($file['error'])) {
            return self::gagal('Berkas tidak valid.');
        }

        $pesan = match ($file['error']) {
            UPLOAD_ERR_OK                           => null,
            UPLOAD_ERR_NO_FILE                      => 'Pilih berkas terlebih dahulu.',
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran berkas melebihi batas yang diizinkan.',
            default                                 => 'Berkas gagal diunggah. Silakan coba lagi.',
        };

        if ($pesan !== null) {
            return self::gagal($pesan);
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return self::gagal('Berkas tidak valid.');
        }

        $ukuran = (int) $file['size'];

        if ($ukuran <= 0) {
            return self::gagal('Berkas kosong.');
        }

        if ($ukuran > $maksByte) {
            return self::gagal(
                'Ukuran berkas maksimal ' . self::formatUkuran($maksByte) . '.'
            );
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = (string) $finfo->file($file['tmp_name']);

        if (!isset($mimeExt[$mime])) {
            return self::gagal('Tipe berkas tidak diizinkan.');
        }

        if ($wajibGambar && !self::isGambarValid($file['tmp_name'])) {
            return self::gagal('Berkas bukan gambar yang valid.');
        }

        if (!is_dir($dirAbs) && !mkdir($dirAbs, 0755, true) && !is_dir($dirAbs)) {
            throw new \RuntimeException('Folder upload tidak bisa dibuat.');
        }

        $namaFile = bin2hex(random_bytes(16)) . '.' . $mimeExt[$mime];
        $tujuan   = rtrim($dirAbs, '/\\') . DIRECTORY_SEPARATOR . $namaFile;

        if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
            return self::gagal('Berkas gagal disimpan di server.');
        }

        try {
            chmod($tujuan, 0644);
        } catch (\Throwable) {
            // Tidak fatal (mis. di Windows)
        }

        return [
            'ok'        => true,
            'nama_file' => $namaFile,
            'nama_asli' => self::bersihkanNama((string) $file['name']),
            'ukuran'    => $ukuran,
            'mime'      => $mime,
        ];
    }

        /**
     * Kirim file dari storage/ ke browser, lalu berhenti.
     * Tipe dicek ulang dari isi file; nama unduhan dibersihkan.
     *
     * @param array<string,string> $mimeDiizinkan mime => ekstensi
     */
    public static function kirim(
        string $path,
        string $namaUnduh,
        array $mimeDiizinkan,
        bool $inline = false
    ): never {
        if (!is_file($path)) {
            throw new \Exception('File tidak ditemukan di server.', 404);
        }

        $mime = (string) (new \finfo(FILEINFO_MIME_TYPE))->file($path);

        if (!isset($mimeDiizinkan[$mime])) {
            throw new \Exception('Tipe file tidak diizinkan.', 403);
        }

        // inline hanya untuk gambar
        $inline = $inline && str_starts_with($mime, 'image/');

        $namaUnduh = $namaUnduh !== '' ? $namaUnduh : 'file';
        $fallback  = preg_replace('/[^A-Za-z0-9._-]/', '_', $namaUnduh) ?: 'file';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header(
            'Content-Disposition: ' . ($inline ? 'inline' : 'attachment')
            . '; filename="' . $fallback . '"'
            . "; filename*=UTF-8''" . rawurlencode($namaUnduh)
        );
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');

        readfile($path);
        exit;
    }

    /**
     * Hapus file fisik. Hanya nama file yang dipakai (basename), jadi
     * path dari database tidak bisa dipakai untuk keluar dari folder tujuan.
     */
    public static function hapus(string $dirAbs, string $namaFile): void
    {
        $namaFile = basename($namaFile);

        if ($namaFile === '' || $namaFile === '.' || $namaFile === '..') {
            return;
        }

        $path = rtrim($dirAbs, '/\\') . DIRECTORY_SEPARATOR . $namaFile;

        if (is_file($path)) {
            try {
                unlink($path);
            } catch (\Throwable) {
                // File yatim lebih baik daripada error di depan pengguna
            }
        }
    }

    // ------------------------------------------------------------------

    private static function isGambarValid(string $tmp): bool
    {
        // Handler error proyek mengubah warning menjadi exception,
        // jadi file rusak harus ditangkap di sini.
        try {
            return getimagesize($tmp) !== false;
        } catch (\Throwable) {
            return false;
        }
    }

    private static function bersihkanNama(string $nama): string
    {
        $nama = basename(str_replace('\\', '/', $nama));
        $nama = preg_replace('/[\x00-\x1F\x7F]/u', '', $nama) ?? '';
        $nama = mb_substr($nama, 0, 255);

        return $nama !== '' ? $nama : 'gambar';
    }

    private static function formatUkuran(int $byte): string
    {
        return rtrim(rtrim(number_format($byte / 1048576, 1, ',', '.'), '0'), ',') . ' MB';
    }

    private static function gagal(string $pesan): array
    {
        return ['ok' => false, 'error' => $pesan];
    }
}