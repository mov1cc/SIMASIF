<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Request;
use App\Services\DashboardOwnerService;

class DashboardController extends Controller
{
    private DashboardOwnerService $service;

    public function __construct()
    {
        $this->service = new DashboardOwnerService();
    }

    /**
     * GET /owner/dashboard?periode=30
     */
    public function index(): void
    {
        $periode = DashboardOwnerService::normalisasiPeriode(Request::get('periode'));
        ['dari' => $dari, 'sampai' => $sampai] = DashboardOwnerService::rentang($periode);

        $this->render('owner/dashboard', [
            'title'      => 'Dashboard Owner',
            'periode'    => $periode,
            'daftarPeriode' => DashboardOwnerService::PERIODE,
            'dari'       => $dari,
            'sampai'     => $sampai,
            'stat'       => $this->service->ringkasan($dari, $sampai),
            'populer'    => $this->service->layananTerpopuler($dari, $sampai),
            'aktivitas'  => $this->service->aktivitasTerbaru(),
        ]);
    }

    /**
     * GET /owner/dashboard/data?periode=30   (JSON untuk Chart.js)
     */
    public function data(): void
    {
        $periode = DashboardOwnerService::normalisasiPeriode(Request::get('periode'));
        ['dari' => $dari, 'sampai' => $sampai] = DashboardOwnerService::rentang($periode);

        $harian  = $this->service->pendapatanHarian($dari, $sampai);
        $populer = $this->service->layananTerpopuler($dari, $sampai);

        $this->json([
            'pendapatan' => [
                'labels' => array_column($harian, 'tanggal'),
                'values' => array_column($harian, 'total'),
            ],
            'populer' => [
                'labels' => array_column($populer, 'nama'),
                'values' => array_column($populer, 'sesi'),
            ],
        ]);
    }
}