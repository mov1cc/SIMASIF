<?php

namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Response;
use App\Core\Session;

class RoleMiddleware implements Middleware
{
    /** @var string[] */
    private array $roles;

    public function __construct(string ...$roles)
    {
        $this->roles = $roles;
    }

    public function handle(): void
    {

        if (!Session::has('user_id')) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            Response::redirect('/login');
        }

        if (!in_array(Session::get('role'), $this->roles, true)) {
            throw new \Exception('Akses ditolak.', 403);
        }
    }
}