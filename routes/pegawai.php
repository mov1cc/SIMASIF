<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;

$pegawai = [
    AuthMiddleware::class,
    RoleMiddleware::class . ':pegawai',
];

// Dashboard
$router->get('/pegawai/dashboard', 'Pegawai\DashboardController@index', $pegawai);

// Layanan (Tahap 4)
$router->get('/pegawai/layanan',          'Pegawai\LayananController@index',   $pegawai);
$router->get('/pegawai/layanan/create',   'Pegawai\LayananController@create',  $pegawai);
$router->post('/pegawai/layanan/store',   'Pegawai\LayananController@store',   $pegawai);
$router->get('/pegawai/layanan/edit',     'Pegawai\LayananController@edit',    $pegawai);
$router->post('/pegawai/layanan/update',  'Pegawai\LayananController@update',  $pegawai);
$router->post('/pegawai/layanan/delete',  'Pegawai\LayananController@destroy', $pegawai);
$router->post('/pegawai/layanan/toggle',  'Pegawai\LayananController@toggle',  $pegawai);

// Add-on layanan
$router->get('/pegawai/layanan/addon',         'Pegawai\AddonLayananController@index',   $pegawai);
$router->get('/pegawai/layanan/addon/edit',    'Pegawai\AddonLayananController@edit',    $pegawai);
$router->post('/pegawai/layanan/addon/store',  'Pegawai\AddonLayananController@store',   $pegawai);
$router->post('/pegawai/layanan/addon/update', 'Pegawai\AddonLayananController@update',  $pegawai);
$router->post('/pegawai/layanan/addon/delete', 'Pegawai\AddonLayananController@destroy', $pegawai);
$router->post('/pegawai/layanan/addon/toggle', 'Pegawai\AddonLayananController@toggle',  $pegawai);

// Galeri gambar layanan
$router->get('/pegawai/layanan/gambar',         'Pegawai\LayananGambarController@index',   $pegawai);
$router->post('/pegawai/layanan/gambar/upload', 'Pegawai\LayananGambarController@upload',  $pegawai);
$router->post('/pegawai/layanan/gambar/cover',  'Pegawai\LayananGambarController@cover',   $pegawai);
$router->post('/pegawai/layanan/gambar/update', 'Pegawai\LayananGambarController@update',  $pegawai);
$router->post('/pegawai/layanan/gambar/delete', 'Pegawai\LayananGambarController@destroy', $pegawai);

// Pelanggan (Tahap 5)
$router->get('/pegawai/pelanggan',          'Pegawai\PelangganController@index',   $pegawai);
$router->get('/pegawai/pelanggan/create',   'Pegawai\PelangganController@create',  $pegawai);
$router->post('/pegawai/pelanggan/store',   'Pegawai\PelangganController@store',   $pegawai);
$router->get('/pegawai/pelanggan/edit',     'Pegawai\PelangganController@edit',    $pegawai);
$router->post('/pegawai/pelanggan/update',  'Pegawai\PelangganController@update',  $pegawai);
$router->post('/pegawai/pelanggan/delete',  'Pegawai\PelangganController@destroy', $pegawai);

// Pemesanan & Jadwal (Tahap 8)
$router->get('/pegawai/pemesanan',         'Pegawai\PemesananController@index',  $pegawai);
$router->get('/pegawai/pemesanan/detail',  'Pegawai\PemesananController@detail', $pegawai);
$router->get('/pegawai/jadwal',            'Pegawai\JadwalController@index',     $pegawai);

// Workflow (Tahap 9)
$router->get('/pegawai/workflow',         'Pegawai\WorkflowController@index',        $pegawai);
$router->post('/pegawai/workflow/update', 'Pegawai\WorkflowController@updateStatus', $pegawai);

// Transaksi Pembayaran (Tahap 10)
$router->get('/pegawai/transaksi',          'Pegawai\TransaksiController@index',  $pegawai);
$router->get('/pegawai/transaksi/bukti',    'Pegawai\TransaksiController@bukti',  $pegawai);
$router->post('/pegawai/transaksi/verify',  'Pegawai\TransaksiController@verify', $pegawai);
$router->post('/pegawai/transaksi/reject',  'Pegawai\TransaksiController@reject', $pegawai);

// File Fotografi (Tahap 11)
$router->get('/pegawai/file',          'Pegawai\FileFotografiController@index',      $pegawai);
$router->get('/pegawai/file/unduh',    'Pegawai\FileFotografiController@unduh',      $pegawai);
$router->post('/pegawai/file/upload',  'Pegawai\FileFotografiController@uploadHasil', $pegawai);
$router->post('/pegawai/file/toggle',  'Pegawai\FileFotografiController@toggle',     $pegawai);
$router->post('/pegawai/file/delete',  'Pegawai\FileFotografiController@destroy',    $pegawai);