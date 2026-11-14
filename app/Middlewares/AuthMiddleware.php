<?php

namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Response;
use App\Core\Session;
use App\Helpers\FormatHelper;
use App\Models\User;

class AuthMiddleware implements Middleware
{
    public function handle(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            Response::redirect('/login');
        }

        $user = (new User())->findById((int) Session::get('user_id'));

        if ($user === null || !FormatHelper::bool($user['is_active'])) {
            // Sama seperti AuthService::logout(): session baru supaya flash bisa tampil
            Session::destroy();
            session_start();

            Session::flash('error', 'Akun Anda tidak aktif atau sudah dihapus. Hubungi owner.');
            Response::redirect('/login');
        }

        // Role selalu mengikuti database
        Session::set('role', $user['role']);
    }
}