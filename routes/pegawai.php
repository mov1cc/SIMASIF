<?php

declare(strict_types=1);

/** @var \App\Core\Router $router */

$router->group(['prefix' => '/pegawai', 'middleware' => ['auth', 'role:pegawai']], function ($r): void {
    
    $r->get('/dashboard', fn () => view('dashboard', ['title' => 'Dashboard Pegawai', 'heading' => 'Dashboard Pegawai']));
});