<?php

namespace App\Controllers\Guest;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Models\Pemesanan;
use App\Models\TransaksiPembayaran;
use App\Services\KodeUnikService;
use App\Services\PembayaranService;

class PembayaranController extends Controller
{
    private Pemesanan $pemesanan;
    private TransaksiPembayaran $transaksi;
    private PembayaranService $service;

    public function __construct()
    {
        $this->pemesanan = new Pemesanan();
        $this->transaksi = new TransaksiPembayaran();
        $this->service   = new PembayaranService();
    }

    /**
     * GET /pembayaran?kode=SF-XXXXXX
     * (kode sudah divalidasi KodeUnikMiddleware)
     */
    public function showForm(): void
    {
        $row = $this->pemesananDariKode();

        $this->render('guest/pembayaran/upload-bukti', [
            'title'     => 'Pembayaran ' . $row['kode_unik'],
            'pemesanan' => $row,
            'transaksi' => $this->transaksi->findByPemesanan((int) $row['id']),
            'rekening'  => PembayaranService::rekening(),
        ]);
    }

    /**
     * POST /pembayaran/upload?kode=SF-XXXXXX   (multipart/form-data)
     * Kode dibaca dari query string supaya KodeUnikMiddleware ikut berlaku.
     */
    public function uploadBukti(): void
    {
        $row   = $this->pemesananDariKode();
        $balik = '/pembayaran?kode=' . urlencode($row['kode_unik']);

        // post_max_size terlewati -> PHP mengosongkan $_POST dan $_FILES
        if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            Session::flash('error', 'Ukuran berkas melebihi batas server.');
            $this->redirect($balik);
        }

        if (!CsrfHelper::verify(Request::post('_csrf'))) {
            throw new \Exception('Token keamanan tidak valid.', 403);
        }

        $metode = $this->postString('metode');
        $noRef  = $this->postString('nomor_referensi');

        if (mb_strlen($noRef) > 100) {
            Session::flash('error', 'Nomor referensi maksimal 100 karakter.');
            $this->redirect($balik);
        }

        $file = Request::file('bukti');

        $hasil = $this->service->kirim(
            $row,
            $metode,
            $noRef,
            is_array($file) ? $file : null
        );

        if (!$hasil['ok']) {
            Session::flash('error', $hasil['error']);
            $this->redirect($balik);
        }

        Session::flash(
            'success',
            $metode === 'transfer'
                ? 'Bukti pembayaran terkirim. Mohon tunggu verifikasi dari studio.'
                : 'Pilihan bayar langsung tercatat. Silakan bayar di studio, pegawai akan memverifikasi.'
        );

        $this->redirect('/tracking?kode=' . urlencode($row['kode_unik']));
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

    private function postString(string $key): string
    {
        $value = Request::post($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }
}