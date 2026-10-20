<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\ForbiddenException;

final class RoleMiddleware implements Middleware
{
    /** @var string[] */
    private array $roles;

    public function __construct(string ...$roles)
    {
        $this->roles = $roles;
    }

    public function handle(Request $request, callable $next): Response
    {
        if (!Auth::hasRole(...$this->roles)) {
            throw new ForbiddenException();
        }

        return $next($request);
    }
}