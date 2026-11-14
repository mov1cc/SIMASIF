<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;

$owner = [
    AuthMiddleware::class,
    RoleMiddleware::class . ':owner',
];

// Dashboard (Tahap 12)
$router->get('/owner/dashboard',      'Owner\DashboardController@index', $owner);
$router->get('/owner/dashboard/data', 'Owner\DashboardController@data',  $owner);

// Laporan (Tahap 13)
$router->get('/owner/laporan/pemesanan',  'Owner\LaporanController@pemesanan',  $owner);
$router->get('/owner/laporan/pendapatan', 'Owner\LaporanController@pendapatan', $owner);

// Audit log
$router->get('/owner/audit-log', 'Owner\AuditLogController@index', $owner);

// Kelola pegawai
$router->get('/owner/users',          'Owner\UserController@index',   $owner);
$router->get('/owner/users/create',   'Owner\UserController@create',  $owner);
$router->post('/owner/users/store',   'Owner\UserController@store',   $owner);
$router->get('/owner/users/edit',     'Owner\UserController@edit',    $owner);
$router->post('/owner/users/update',  'Owner\UserController@update',  $owner);
$router->post('/owner/users/toggle',  'Owner\UserController@toggle',  $owner);
$router->post('/owner/users/delete',  'Owner\UserController@destroy', $owner);

// Profil
$router->get('/owner/profil',          'Owner\ProfilController@index',  $owner);
$router->post('/owner/profil/update',  'Owner\ProfilController@update', $owner);