<?php

use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;

$owner = [
    AuthMiddleware::class,
    RoleMiddleware::class . ':owner',
];

$router->get('/owner/dashboard',      'Owner\DashboardController@index', $owner);
$router->get('/owner/dashboard/data', 'Owner\DashboardController@data',  $owner);