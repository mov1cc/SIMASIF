<?php

use App\Middlewares\AuthMiddleware;

$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');

$router->post('/logout', 'AuthController@logout', [
    AuthMiddleware::class,
]);

// Lupa / reset password (Tahap 13), publik tapi dijaga token
$router->get('/forgot-password',  'AuthController@showForgotPasswordForm');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password',   'AuthController@showResetPasswordForm');
$router->post('/reset-password',  'AuthController@resetPassword');

// Ganti password (sudah login, owner maupun pegawai)
$router->post('/password/change', 'AuthController@changePassword', [
    AuthMiddleware::class,
]);