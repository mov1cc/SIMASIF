<?php

declare(strict_types=1);

namespace App\Exceptions;

class ValidationException extends HttpException
{
    public function __construct(private array $errors, private array $old = [])
    {
        parent::__construct(422, 'Data yang dikirim tidak valid.');
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function old(): array
    {
        return $this->old;
    }
}