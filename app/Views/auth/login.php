<?php

namespace App\Services;

use App\Core\Session;
use App\Models\User;

class AuthService
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    /**
     * @return array{success
     */
    public function attemptLogin(string $email, string $password): array
    {
        $email = strtolower(trim($email));

        $user = $this->users->findByEmail($email);

        $hash = $user['password'] ?? password_hash('dummy-password', PASSWORD_BCRYPT);
        $passwordOk = password_verify($password, $hash);

        if ($user === null || !$passwordOk) {
            return [
                'success' => false,
                'message' => 'Email atau password salah.',
            ];
        }

        if (!$user['is_active']) {
            return [
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Hubungi owner.',
            ];
        }

        $this->startSession($user);

        $this->users->updateLastLogin((int) $user['id']);

        return [
            'success' => true,
            'message' => 'Login berhasil.',
            'role'    => $user['role'],
        ];
    }

    
    private function startSession(array $user): void
    {
        
        Session::regenerate();

        Session::set('user_id', (int) $user['id']);
        Session::set('nama', $user['nama']);
        Session::set('email', $user['email']);
        Session::set('role', $user['role']);
    }

    
    public function logout(): void
    {
        Session::destroy();

        session_start();
    }

    public function check(): bool
    {
        return Session::has('user_id');
    }

    public function role(): ?string
    {
        return Session::get('role');
    }

    public static function dashboardPath(?string $role): string
    {
        return match ($role) {
            ROLE_OWNER   => '/owner/dashboard',
            ROLE_PEGAWAI => '/pegawai/dashboard',
            default      => '/login',
        };
    }
}