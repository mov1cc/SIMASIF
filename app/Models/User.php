<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    
    public function findByEmail(string $email): ?array
    {
        return $this->first(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$email]
        );
    }

    
    public function findById(int $id): ?array
    {
        return $this->find($id);
    }

    
    public function updateLastLogin(int $id): bool
    {
        return $this->execute(
            "UPDATE users SET last_login_at = NOW() WHERE id = ?",
            [$id]
        );
    }
}