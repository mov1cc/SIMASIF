<?php

namespace App\Controllers\Guest;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Pemesanan;
use App\Models\WorkflowLayanan;
use App\Services\KodeUnikService;
use App\Services\WorkflowService;

class TrackingController extends Controller
{
    private Pemesanan $pemesanan;
    private WorkflowLayanan $workflow;

    public function __construct()
    {
        $this->pemesanan = new Pemesanan();
        $this->workflow  = new WorkflowLayanan();
    }

    /**
     * GET /tracking            -> form input kode
     * GET /tracking?kode=SF-.. -> status layanan
     */
    public function index(): void
    {
        $kode = $this->kodeDariQuery();

        if ($kode === '') {
            $this->render('guest/tracking/form', [
                'title' => 'Lacak Pesanan',
            ]);

            return;
        }

        $row = KodeUnikService::isValidFormat($kode)
            ? $this->pemesanan->findByKodeUnik($kode)
            : null;

        if ($row === null) {
            Session::flash('error', 'Kode pemesanan tidak ditemukan. Periksa kembali kode Anda.');
            $this->redirect('/tracking');
        }

        $this->render('guest/tracking/status', [
            'title'     => 'Status Pesanan',
            'pemesanan' => $row,
            'tahapan'   => WorkflowService::tahapan($row['status']),
            'persen'    => WorkflowService::persen($row['status']),
            'riwayat'   => $this->workflow->findByPemesanan((int) $row['id']),
        ]);
    }

    /**
     * GET /tracking/data?kode=SF-..  (JSON untuk auto-refresh)
     * Kode sudah divalidasi KodeUnikMiddleware.
     */
    public function data(): void
    {
        $row = $this->pemesanan->findByKodeUnik($this->kodeDariQuery());

        if ($row === null) {
            $this->json(['error' => 'not_found'], 404);
        }

        $this->json([
            'status'            => $row['status'],
            'status_pembayaran' => $row['status_pembayaran'],
        ]);
    }

    private function kodeDariQuery(): string
    {
        $raw = Request::get('kode', '');

        return is_scalar($raw) ? KodeUnikService::normalize((string) $raw) : '';
    }
}