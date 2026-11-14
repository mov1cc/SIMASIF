<?php

namespace App\Controllers\Pegawai;

use App\Core\Controller;
use App\Services\DashboardPegawaiService;

class DashboardController extends Controller
{
    private DashboardPegawaiService $service;

    public function __construct()
    {
        $this->service = new DashboardPegawaiService();
    }

    /**
     * GET /pegawai/dashboard
     */
    public function index(): void
    {
        $this->render('pegawai/dashboard', [
            'title'     => 'Dashboard Pegawai',
            'stat'      => $this->service->ringkasan(),
            'terbaru'   => $this->service->pemesananTerbaru(),
            'jadwal'    => $this->service->jadwalHariIni(),
            'menunggu'  => $this->service->transaksiMenunggu(),
            'aktivitas' => $this->service->aktivitasTerbaru(),
        ]);
    }
}