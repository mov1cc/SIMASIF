<?php

namespace App\Controllers\Pegawai;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Models\FileFotografi;
use App\Models\Pemesanan;
use App\Services\UploadService;

class FileFotografiController extends Controller
{
    /** Batas ukuran file hasil: 25 MB */
    private const MAKS_BYTE = 25 * 1024 * 1024;

    /** Batas jumlah file hasil per pemesanan */
    private const MAKS_JUMLAH = 50;

    private Pemesanan $pemesanan;
    private FileFotografi $files;

    public function __construct()
    {
        $this->pemesanan = new Pemesanan();
        $this->files     = new FileFotografi();
    }

    /**
     * GET /pegawai/file?pemesanan_id=1
     */
    public function index(): void
    {
        $p  = $this->pemesananOrFail($this->queryInt('pemesanan_id'));
        $id = (int) $p['id'];

        $this->render('pegawai/file-fotografi/index', [
            'title'     => 'File ' . $p['kode_unik'],
            'p'         => $p,
            'referensi' => $this->files->findByPemesanan($id, FileFotografi::JENIS_REFERENSI),
            'hasil'     => $this->files->findByPemesanan($id, FileFotografi::JENIS_HASIL),
            'maksMb'    => self::MAKS_BYTE / 1048576,
        ]);
    }

    /**
     * POST /pegawai/file/upload   (multipart/form-data)
     * Field: pemesanan_id, berkas, langsung_tersedia (checkbox)
     */
    public function uploadHasil(): void
    {
        if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            Session::flash('error', 'Ukuran berkas melebihi batas server (post_max_size di php.ini).');
            $this->redirect('/pegawai/pemesanan');
        }

        $this->verifyCsrf();

        $p     = $this->pemesananOrFail((int) $this->postString('pemesanan_id'));
        $id    = (int) $p['id'];
        $balik = '/pegawai/file?pemesanan_id=' . $id;

        if ($this->files->countByJenis($id, FileFotografi::JENIS_HASIL) >= self::MAKS_JUMLAH) {
            Session::flash('error', 'Maksimal ' . self::MAKS_JUMLAH . ' file hasil per pemesanan.');
            $this->redirect($balik);
        }

        $file  = Request::file('berkas');
        $hasil = UploadService::simpan(
            is_array($file) ? $file : null,
            FileFotografi::dirAbs(FileFotografi::JENIS_HASIL),
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
                'jenis'         => FileFotografi::JENIS_HASIL,
                'nama_asli'     => $hasil['nama_asli'],
                'nama_file'     => $hasil['nama_file'],
                'ukuran'        => $hasil['ukuran'],
                'mime'          => $hasil['mime'],
                'status'        => Request::post('langsung_tersedia') !== null
                    ? FileFotografi::TERSEDIA
                    : FileFotografi::BELUM_TERSEDIA,
                'diunggah_oleh' => (int) Session::get('user_id'), // wajib terisi untuk 'hasil'
            ]);
        } catch (\Throwable $e) {
            UploadService::hapus(
                FileFotografi::dirAbs(FileFotografi::JENIS_HASIL),
                $hasil['nama_file']
            );
            throw $e;
        }

        Session::flash('success', 'File hasil berhasil diunggah.');
        $this->redirect($balik);
    }

    /**
     * POST /pegawai/file/toggle
     * Field: id, status (tersedia | belum_tersedia). Hanya untuk file hasil.
     */
    public function toggle(): void
    {
        $this->verifyCsrf();

        $file   = $this->fileOrFail((int) $this->postString('id'));
        $status = $this->postString('status');
        $balik  = '/pegawai/file?pemesanan_id=' . (int) $file['pemesanan_id'];

        if ($file['jenis_file'] !== FileFotografi::JENIS_HASIL) {
            Session::flash('error', 'Hanya file hasil yang bisa diatur ketersediaannya.');
            $this->redirect($balik);
        }

        if (!in_array($status, [FileFotografi::TERSEDIA, FileFotografi::BELUM_TERSEDIA], true)) {
            Session::flash('error', 'Status tidak valid.');
            $this->redirect($balik);
        }

        $this->files->setStatus((int) $file['id'], $status);

        Session::flash(
            'success',
            $status === FileFotografi::TERSEDIA
                ? 'File dibuka untuk pelanggan.'
                : 'File disembunyikan dari pelanggan.'
        );

        $this->redirect($balik);
    }

    /**
     * POST /pegawai/file/delete
     */
    public function destroy(): void
    {
        $this->verifyCsrf();

        $file  = $this->fileOrFail((int) $this->postString('id'));
        $balik = '/pegawai/file?pemesanan_id=' . (int) $file['pemesanan_id'];

        $dihapus = $this->files->hapus((int) $file['id']);

        if ($dihapus !== null) {
            UploadService::hapus(
                FileFotografi::dirAbs($dihapus['jenis_file']),
                $dihapus['nama_file']
            );
        }

        Session::flash('success', 'File berhasil dihapus.');
        $this->redirect($balik);
    }

    /**
     * GET /pegawai/file/unduh?id=5[&tampil=1]
     * Pegawai bisa mengunduh/melihat file referensi maupun hasil.
     * tampil=1 -> tampil di browser (khusus gambar, untuk thumbnail).
     */
    public function unduh(): void
    {
        $file = $this->fileOrFail($this->queryInt('id'));

        UploadService::kirim(
            FileFotografi::pathFisik($file),
            $file['nama_file_asli'],
            UploadService::MIME_FOTO,
            Request::get('tampil') === '1'
        );
    }

    // ------------------------------------------------------------------

    private function verifyCsrf(): void
    {
        if (!CsrfHelper::verify(Request::post('_csrf'))) {
            throw new \Exception('Token keamanan tidak valid.', 403);
        }
    }

    private function pemesananOrFail(int $id): array
    {
        $row = $id > 0 ? $this->pemesanan->findDetail($id) : null;

        if ($row === null) {
            throw new \Exception('Pemesanan tidak ditemukan.', 404);
        }

        return $row;
    }

    private function fileOrFail(int $id): array
    {
        $row = $id > 0 ? $this->files->find($id) : null;

        if ($row === null) {
            throw new \Exception('File tidak ditemukan.', 404);
        }

        return $row;
    }

    private function queryInt(string $key): int
    {
        $v = Request::get($key, 0);

        return is_scalar($v) ? (int) $v : 0;
    }

    private function postString(string $key): string
    {
        $value = Request::post($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }
}