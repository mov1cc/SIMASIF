<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Request;
use App\Helpers\FormatHelper;
use App\Helpers\ValidationHelper;
use App\Services\LaporanService;

class LaporanController extends Controller
{
    private LaporanService $service;

    public function __construct()
    {
        $this->service = new LaporanService();
    }

    /**
     * GET /owner/laporan/pemesanan?dari=&sampai=&status=
     */
    public function pemesanan(): void
    {
        [$dari, $sampai] = $this->rentang();

        $status = $this->queryString('status');

        // Whitelist: hanya nilai enum yang sah
        if (!array_key_exists($status, FormatHelper::daftarStatusPemesanan())) {
            $status = '';
        }

        $data = $this->service->pemesanan($dari, $sampai, $status);

        $this->render('owner/laporan/pemesanan', [
            'title'     => 'Laporan Pemesanan',
            'dari'      => $dari,
            'sampai'    => $sampai,
            'status'    => $status,
            'rows'      => $data['rows'],
            'ringkasan' => $data['ringkasan'],
        ]);
    }

    /**
     * GET /owner/laporan/pendapatan?dari=&sampai=
     */
    public function pendapatan(): void
    {
        [$dari, $sampai] = $this->rentang();

        $data = $this->service->pendapatan($dari, $sampai);

        $this->render('owner/laporan/pendapatan', [
            'title'     => 'Laporan Pendapatan',
            'dari'      => $dari,
            'sampai'    => $sampai,
            'rows'      => $data['rows'],
            'ringkasan' => $data['ringkasan'],
        ]);
    }

    // ------------------------------------------------------------------

    /**
     * Rentang tanggal dari query string. Default: awal bulan ini sampai hari ini.
     * Kalau terbalik, otomatis ditukar.
     *
     * @return array{0:string, 1:string}
     */
    private function rentang(): array
    {
        $dari   = ValidationHelper::tanggalValid(Request::get('dari'));
        $sampai = ValidationHelper::tanggalValid(Request::get('sampai'));

        if ($dari === '') {
            $dari = date('Y-m-01');
        }

        if ($sampai === '') {
            $sampai = date('Y-m-d');
        }

        if ($dari > $sampai) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        return [$dari, $sampai];
    }

    private function queryString(string $key): string
    {
        $value = Request::get($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }
}