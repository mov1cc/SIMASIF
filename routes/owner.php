<?php

declare(strict_types=1);

/** @var \App\Core\Router $router */

$router->group(['prefix' => '/owner', 'middleware' => ['auth', 'role:owner']], function ($r): void {
    
    $r->get('/dashboard', fn () => view('dashboard', ['title' => 'Dashboard Owner', 'heading' => 'Dashboard Owner']));
});