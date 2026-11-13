<?php

namespace App\Controllers\Guest;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Models\FileFotografi;
use App\Models\Pemesanan;
use App\Services\KodeUnikService;
use App\Services\UploadService;

class FileFotografiController extends Controller
{
    /** Batas file referensi per pemesanan (sistem bukan penyimpanan skala besar) */
    private const MAKS_JUMLAH = 10;

    /** Batas ukuran per file referensi: 10 MB */
    private const MAKS_BYTE = 10 * 1024 * 1024;

    private Pemesanan $pemesanan;
    private FileFotografi $files;

    public function __construct()
    {
        $this->pemesanan = new Pemesanan();
        $this->files     = new FileFotografi();
    }

    /**
     * POST /file/upload?kode=SF-XXXXXX   (multipart/form-data)
     * Kode dibaca dari query string supaya KodeUnikMiddleware ikut berlaku.
     */
    public function upload(): void
    {
        $row   = $this->pemesananDariKode();
        $id    = (int) $row['id'];
        $balik = '/tracking?kode=' . urlencode($row['kode_unik']);

        // post_max_size terlewati -> PHP mengosongkan $_POST dan $_FILES
        if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            Session::flash('error', 'Ukuran berkas melebihi batas server.');
            $this->redirect($balik);
        }

        if (!CsrfHelper::verify(Request::post('_csrf'))) {
            throw new \Exception('Token keamanan tidak valid.', 403);
        }

        if (in_array($row['status'], FileFotografi::STATUS_BOLEH_UNDUH, true)) {
            Session::flash('error', 'Hasil sudah siap, upload file baru tidak dibuka lagi. Hubungi studio via WhatsApp.');
            $this->redirect($balik);
        }

        if ($this->files->countByJenis($id, FileFotografi::JENIS_REFERENSI) >= self::MAKS_JUMLAH) {
            Session::flash('error', 'Maksimal ' . self::MAKS_JUMLAH . ' file per pemesanan.');
            $this->redirect($balik);
        }

        $file  = Request::file('berkas');
        $hasil = UploadService::simpan(
            is_array($file) ? $file : null,
            FileFotografi::dirAbs(FileFotografi::JENIS_REFERENSI),
            UploadService::MIME_FOTO,
            self::MAKS_BYTE
        );

        if (!$hasil['ok']) {
            Session::flash('error', $hasil['error']);
            $this->redirect($balik);
        }

        try {
            $this->files->create([
                'pemesanan_id'  => $id,
                'jenis'         => FileFotografi::JENIS_REFERENSI,
                'nama_asli'     => $hasil['nama_asli'],
                'nama_file'     => $hasil['nama_file'],
                'ukuran'        => $hasil['ukuran'],
                'mime'          => $hasil['mime'],
                'status'        => FileFotografi::BELUM_TERSEDIA,
                'diunggah_oleh' => null, // pelanggan -> NULL (chk_pengunggah_file)
            ]);
        } catch (\Throwable $e) {
            // Jangan tinggalkan file yatim kalau insert gagal
            UploadService::hapus(
                FileFotografi::dirAbs(FileFotografi::JENIS_REFERENSI),
                $hasil['nama_file']
            );
            throw $e;
        }

        Session::flash('success', 'File berhasil diunggah.');
        $this->redirect($balik);
    }

    /**
     * GET /file/download?kode=SF-XXXXXX&id=5
     *
     * Urutan pengecekan (inti keamanan):
     *  1. kode unik valid          (KodeUnikMiddleware, 404)
     *  2. file memang ada          (404)
     *  3. file milik pemesanan ini (403)
     *  4. jenis hasil + tersedia   (403)
     *  5. pemesanan sudah hasil_siap/selesai (403)
     *  6. baru stream dari storage/hasil-foto/
     */
    public function download(): void
    {
        $row = $this->pemesananDariKode();

        $raw  = Request::get('id', 0);
        $id   = is_scalar($raw) ? (int) $raw : 0;
        $file = $id > 0 ? $this->files->find($id) : null;

        if ($file === null) {
            throw new \Exception('File tidak ditemukan.', 404);
        }

        if ((int) $file['pemesanan_id'] !== (int) $row['id']) {
            throw new \Exception('Akses ditolak.', 403);
        }

        if ($file['jenis_file'] !== FileFotografi::JENIS_HASIL
            || $file['status_file'] !== FileFotografi::TERSEDIA
        ) {
            throw new \Exception('File belum tersedia.', 403);
        }

        if (!in_array($row['status'], FileFotografi::STATUS_BOLEH_UNDUH, true)) {
            throw new \Exception('Hasil belum dapat diunduh.', 403);
        }

        UploadService::kirim(
            FileFotografi::pathFisik($file),
            $file['nama_file_asli'],
            UploadService::MIME_FOTO
        );
    }

    // ------------------------------------------------------------------

    private function pemesananDariKode(): array
    {
        $raw  = Request::get('kode', '');
        $kode = is_scalar($raw) ? KodeUnikService::normalize((string) $raw) : '';

        $row = $this->pemesanan->findByKodeUnik($kode);

        if ($row === null) {
            throw new \Exception('Pemesanan tidak ditemukan.', 404);
        }

        return $row;
    }
}