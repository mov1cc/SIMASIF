<?php

declare(strict_types=1);

use App\Controllers\Guest\AuthController;

/** @var \App\Core\Router $router */

$router->group(['middleware' => ['guest']], function ($r): void {
    $r->get('/login', [AuthController::class, 'showLogin']);
    $r->post('/login', [AuthController::class, 'login']);
});

$router->post('/logout', [AuthController::class, 'logout'], ['auth']);