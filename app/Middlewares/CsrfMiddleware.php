<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Csrf;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\HttpException;

final class CsrfMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');

        if (!Csrf::verify(is_string($token) ? $token : null)) {
            throw new HttpException(419);
        }

        return $next($request);
    }
}