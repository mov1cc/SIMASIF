<?php

namespace App\Controllers\Pegawai;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Models\TransaksiPembayaran;
use App\Services\PembayaranService;
use App\Services\UploadService;

class TransaksiController extends Controller
{
    private TransaksiPembayaran $transaksi;
    private PembayaranService $service;

    public function __construct()
    {
        $this->transaksi = new TransaksiPembayaran();
        $this->service   = new PembayaranService();
    }

    /**
     * GET /pegawai/transaksi?q=&status=
     */
    public function index(): void
    {
        $status = $this->queryString('status');

        // Whitelist nilai enum
        if (!in_array($status, [STATUS_PENDING, STATUS_LUNAS, STATUS_DITOLAK], true)) {
            $status = '';
        }

        $filter = [
            'q'      => $this->queryString('q'),
            'status' => $status,
        ];

        $this->render('pegawai/transaksi/index', [
            'title'  => 'Transaksi Pembayaran',
            'items'  => $this->transaksi->all($filter),
            'filter' => $filter,
        ]);
    }

    /**
     * POST /pegawai/transaksi/verify
     */
    public function verify(): void
    {
        $this->verifyCsrf();

        $hasil = $this->service->verifikasi(
            (int) $this->postString('id'),
            (int) Session::get('user_id')
        );

        if ($hasil['ok']) {
            Session::flash('success', 'Pembayaran diverifikasi (lunas). Status pemesanan diperbarui otomatis.');
        } else {
            Session::flash('error', $hasil['error']);
        }

        $this->redirect('/pegawai/transaksi');
    }

    /**
     * POST /pegawai/transaksi/reject
     */
    public function reject(): void
    {
        $this->verifyCsrf();

        $hasil = $this->service->tolak(
            (int) $this->postString('id'),
            (int) Session::get('user_id')
        );

        if ($hasil['ok']) {
            Session::flash('success', 'Pembayaran ditolak. Pelanggan dapat mengirim ulang bukti.');
        } else {
            Session::flash('error', $hasil['error']);
        }

        $this->redirect('/pegawai/transaksi');
    }

    /**
     * GET /pegawai/transaksi/bukti?id=1
     * Menyajikan file dari storage/ (tidak bisa diakses langsung lewat URL).
     */
    public function bukti(): void
    {
        $raw = Request::get('id', 0);
        $id  = is_scalar($raw) ? (int) $raw : 0;

        $row = $id > 0 ? $this->transaksi->findDetail($id) : null;

        if ($row === null || empty($row['bukti_transfer'])) {
            throw new \Exception('Bukti pembayaran tidak ditemukan.', 404);
        }

        // basename(): nama dari database tidak bisa dipakai keluar dari folder bukti
        $path = PembayaranService::dirAbs() . DIRECTORY_SEPARATOR . basename($row['bukti_transfer']);

        if (!is_file($path)) {
            throw new \Exception('File bukti tidak ditemukan di server.', 404);
        }

        $mime = (string) (new \finfo(FILEINFO_MIME_TYPE))->file($path);

        if (!isset(UploadService::MIME_BUKTI[$mime])) {
            throw new \Exception('Tipe file tidak diizinkan.', 403);
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: inline; filename="bukti-' . (int) $row['transaksi_id'] . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');

        readfile($path);
        exit;
    }

    // ------------------------------------------------------------------

    private function verifyCsrf(): void
    {
        if (!CsrfHelper::verify(Request::post('_csrf'))) {
            throw new \Exception('Token keamanan tidak valid.', 403);
        }
    }

    private function postString(string $key): string
    {
        $value = Request::post($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function queryString(string $key): string
    {
        $value = Request::get($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }
}