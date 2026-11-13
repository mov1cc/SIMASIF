<?php

namespace App\Controllers\Guest;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Pemesanan;
use App\Models\WorkflowLayanan;
use App\Services\KodeUnikService;
use App\Services\WorkflowService;
use App\Models\FileFotografi;

class TrackingController extends Controller
{
    private Pemesanan $pemesanan;
    private WorkflowLayanan $workflow;
    private FileFotografi $files;

    public function __construct()
    {
        $this->pemesanan = new Pemesanan();
        $this->workflow  = new WorkflowLayanan();
        $this->files     = new FileFotografi();
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

        $semuaFile = $this->files->findByPemesanan((int) $row['id']);
        $bolehUnduh = in_array($row['status'], FileFotografi::STATUS_BOLEH_UNDUH, true);

        $this->render('guest/tracking/status', [
            'title'       => 'Status Pesanan',
            'pemesanan'   => $row,
            'tahapan'     => WorkflowService::tahapan($row['status']),
            'persen'      => WorkflowService::persen($row['status']),
            'riwayat'     => $this->workflow->findByPemesanan((int) $row['id']),
            'referensi'   => array_values(array_filter(
                $semuaFile,
                fn ($f) => $f['jenis_file'] === FileFotografi::JENIS_REFERENSI
            )),
            'hasil'       => $bolehUnduh
                ? array_values(array_filter(
                    $semuaFile,
                    fn ($f) => $f['jenis_file'] === FileFotografi::JENIS_HASIL
                        && $f['status_file'] === FileFotografi::TERSEDIA
                ))
                : [],
            'bolehUpload' => !$bolehUnduh,
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