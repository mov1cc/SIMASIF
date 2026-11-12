<?php

namespace App\Controllers\Pegawai;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper;
use App\Models\Pemesanan;
use App\Services\WorkflowService;

class WorkflowController extends Controller
{
    private Pemesanan $pemesanan;
    private WorkflowService $service;

    public function __construct()
    {
        $this->pemesanan = new Pemesanan();
        $this->service   = new WorkflowService();
    }

    /**
     * GET /pegawai/workflow
     * Papan per status (satu kolom = satu tahap).
     */
    public function index(): void
    {
        $kolom = [];

        foreach (array_keys(FormatHelper::daftarStatusPemesanan()) as $kode) {
            $kolom[$kode] = [];
        }

        foreach ($this->pemesanan->all() as $row) {
            if (isset($kolom[$row['status']])) {
                $kolom[$row['status']][] = $row;
            }
        }

        $this->render('pegawai/workflow/index', [
            'title' => 'Workflow Layanan',
            'kolom' => $kolom,
        ]);
    }

    /**
     * POST /pegawai/workflow/update
     * Field: id, status (tahap tujuan), catatan (opsional), kembali (board|detail)
     */
    public function updateStatus(): void
    {
        $this->verifyCsrf();

        $id         = (int) $this->postString('id');
        $statusBaru = $this->postString('status');
        $catatan    = $this->postString('catatan');

        if ($id <= 0) {
            throw new \Exception('Pemesanan tidak ditemukan.', 404);
        }

        $balik = $this->postString('kembali') === 'detail'
            ? '/pegawai/pemesanan/detail?id=' . $id
            : '/pegawai/workflow';

        if (mb_strlen($catatan) > 500) {
            Session::flash('error', 'Catatan maksimal 500 karakter.');
            $this->redirect($balik);
        }

        $hasil = $this->service->ubahStatus(
            $id,
            $statusBaru,
            (int) Session::get('user_id'),
            $catatan !== '' ? $catatan : null
        );

        if ($hasil['ok']) {
            Session::flash(
                'success',
                'Status berubah menjadi "' . FormatHelper::statusPemesanan($hasil['ke']) . '".'
            );
        } else {
            Session::flash('error', $hasil['error']);
        }

        $this->redirect($balik);
    }

    // ------------------------------------------------------------------
    // Helper privat
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
}