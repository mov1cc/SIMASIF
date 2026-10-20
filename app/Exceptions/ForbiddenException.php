<?php

declare(strict_types=1);

namespace App\Exceptions;

class ForbiddenException extends HttpException
{
    public function __construct(string $message = 'Anda tidak memiliki akses ke halaman ini.')
    {
        parent::__construct(403, $message);
    }
}