<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class AuthMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return Response::json(['message' => 'Silakan login terlebih dahulu.'], 401);
        }

        Session::flash('error', 'Silakan login terlebih dahulu.');

        return Response::redirect(\url((string) Config::get('auth.login_path', '/login')));
    }
}