<?php

declare(strict_types=1);

use App\Controllers\Guest\HomeController;
use App\Core\Database;

/** @var \App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);

if (config('app.debug')) {
    $router->get('/ping', fn (): array => [
        'status' => 'ok',
        'app'    => config('app.name'),
        'time'   => date('c'),
    ]);

    $router->get('/db-check', function (): array {
        $row = Database::fetch('SELECT version() AS version');

        return ['database' => 'terhubung', 'version' => $row['version'] ?? null];
    });
}