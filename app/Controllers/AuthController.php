<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper;
use App\Helpers\ValidationHelper;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\AuthService;
use App\Services\MailService;

class AuthController extends Controller
{
    /** Masa berlaku tautan reset password */
    private const RESET_MENIT = 60;

    private AuthService $auth;
    private User $users;

    public function __construct()
    {
        $this->auth  = new AuthService();
        $this->users = new User();
    }

    // ------------------------------------------------------------------
    // Login / logout
    // ------------------------------------------------------------------

    /**
     * GET /login
     */
    public function showLoginForm(): void
    {
        if ($this->auth->check()) {
            $this->redirect(
                AuthService::dashboardPath($this->auth->role())
            );
        }

        $this->render('auth/login', [
            'title' => 'Login',
        ]);
    }

    /**
     * POST /login
     */
    public function login(): void
    {
        $this->verifyCsrf();

        $email    = trim((string) Request::post('email', ''));
        $password = (string) Request::post('password', '');

        $errors = ValidationHelper::validate(
            ['email' => $email, 'password' => $password],
            [
                'email'    => ['required', 'email', 'max:100'],
                'password' => ['required'],
            ],
            ['email' => 'Email', 'password' => 'Password']
        );

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', ['email' => $email]);
            $this->redirect('/login');
        }

        $result = $this->auth->attemptLogin($email, $password);

        if (!$result['success']) {
            AuditLogService::log(AuditLogService::MODUL_AUTH, 'Login gagal', [
                'email'  => strtolower($email),
                'alasan' => $result['message'],
            ]);

            Session::flash('error', $result['message']);
            Session::flash('old', ['email' => $email]);
            $this->redirect('/login');
        }

        // Session sudah terisi oleh AuthService, jadi user_id terbaca otomatis
        AuditLogService::log(AuditLogService::MODUL_AUTH, 'Login berhasil', [
            'email' => strtolower($email),
            'role'  => $result['role'],
        ]);

        $this->redirect(
            AuthService::dashboardPath($result['role'])
        );
    }

    /**
     * POST /logout
     */
    public function logout(): void
    {
        $this->verifyCsrf();

        // Dicatat SEBELUM session dihancurkan
        AuditLogService::log(AuditLogService::MODUL_AUTH, 'Logout');

        $this->auth->logout();

        Session::flash('success', 'Anda berhasil logout.');

        $this->redirect('/login');
    }

    // ------------------------------------------------------------------
    // Lupa password
    // ------------------------------------------------------------------

    /**
     * GET /forgot-password
     */
    public function showForgotPasswordForm(): void
    {
        $this->render('auth/forgot-password', [
            'title' => 'Lupa Password',
        ]);
    }

    /**
     * POST /forgot-password
     * Pesan balasan SELALU sama, supaya orang tidak bisa menebak email terdaftar.
     */
    public function forgotPassword(): void
    {
        $this->verifyCsrf();

        $email = strtolower(trim((string) Request::post('email', '')));

        $errors = ValidationHelper::validate(
            ['email' => $email],
            ['email' => ['required', 'email', 'max:100']],
            ['email' => 'Email']
        );

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', ['email' => $email]);
            $this->redirect('/forgot-password');
        }

        $user = $this->users->findByEmail($email);

        if ($user !== null && FormatHelper::bool($user['is_active'])) {
            // Token asli hanya dikirim lewat email; yang disimpan di DB hanya hash-nya
            $token = bin2hex(random_bytes(32));

            $this->users->setResetToken(
                (int) $user['id'],
                hash('sha256', $token),
                self::RESET_MENIT
            );

            $appUrl = (require BASE_PATH . '/app/Config/app.php')['url'];
            $link   = rtrim((string) $appUrl, '/') . '/reset-password?token=' . $token;

            $terkirim = (new MailService())->kirimResetPassword(
                $user['email'],
                $user['nama'],
                $link,
                self::RESET_MENIT
            );

            AuditLogService::log(
                AuditLogService::MODUL_AUTH,
                'Permintaan reset password',
                ['email' => $email, 'email_terkirim' => $terkirim],
                (int) $user['id']
            );
        }

        Session::flash(
            'success',
            'Jika email terdaftar, tautan reset password sudah dikirim. Cek kotak masuk atau folder spam.'
        );

        $this->redirect('/forgot-password');
    }

    /**
     * GET /reset-password?token=...
     */
    public function showResetPasswordForm(): void
    {
        $token = $this->tokenDari(Request::get('token'));

        if ($this->userDariToken($token) === null) {
            Session::flash('error', 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.');
            $this->redirect('/forgot-password');
        }

        $this->render('auth/reset-password', [
            'title' => 'Reset Password',
            'token' => $token,
        ]);
    }

    /**
     * POST /reset-password
     */
    public function resetPassword(): void
    {
        $this->verifyCsrf();

        $token = $this->tokenDari(Request::post('token'));
        $user  = $this->userDariToken($token);

        if ($user === null) {
            Session::flash('error', 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.');
            $this->redirect('/forgot-password');
        }

        $baru   = $this->raw('password');
        $konf   = $this->raw('password_confirm');
        $errors = $this->validasiPasswordBaru($baru, $konf);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->redirect('/reset-password?token=' . urlencode($token));
        }

        $this->users->updatePassword(
            (int) $user['id'],
            password_hash($baru, PASSWORD_BCRYPT)
        );

        AuditLogService::log(
            AuditLogService::MODUL_AUTH,
            'Reset password berhasil',
            ['email' => $user['email']],
            (int) $user['id']
        );

        Session::flash('success', 'Password berhasil diubah. Silakan login dengan password baru.');
        $this->redirect('/login');
    }

    // ------------------------------------------------------------------
    // Ganti password (sudah login)
    // ------------------------------------------------------------------

    /**
     * POST /password/change   (AuthMiddleware)
     */
    public function changePassword(): void
    {
        $this->verifyCsrf();

        $user = $this->users->findById((int) Session::get('user_id'));

        if ($user === null) {
            throw new \Exception('Akun tidak ditemukan.', 404);
        }

        $balik = Session::get('role') === ROLE_OWNER
            ? '/owner/profil'
            : '/pegawai/dashboard';

        $lama   = $this->raw('password_lama');
        $baru   = $this->raw('password');
        $konf   = $this->raw('password_confirm');
        $errors = $this->validasiPasswordBaru($baru, $konf);

        if ($lama === '') {
            $errors['password_lama'] = 'Password saat ini wajib diisi.';
        } elseif (!password_verify($lama, $user['password'])) {
            $errors['password_lama'] = 'Password saat ini salah.';
        } elseif (!isset($errors['password']) && hash_equals($lama, $baru)) {
            $errors['password'] = 'Password baru tidak boleh sama dengan password saat ini.';
        }

        if (!empty($errors)) {
            Session::flash('errors_password', $errors);
            $this->redirect($balik);
        }

        $this->users->updatePassword(
            (int) $user['id'],
            password_hash($baru, PASSWORD_BCRYPT)
        );

        AuditLogService::log(AuditLogService::MODUL_PROFIL, 'Ganti password sendiri');

        Session::regenerate();

        Session::flash('success', 'Password berhasil diubah.');
        $this->redirect($balik);
    }

    // ------------------------------------------------------------------
    // Helper privat
    // ------------------------------------------------------------------

    private function verifyCsrf(): void
    {
        if (!CsrfHelper::verify(Request::post('_csrf'))) {
            throw new \Exception('Token keamanan tidak valid.', 403);
        }
    }

    /** Nilai POST apa adanya (password tidak boleh di-trim) */
    private function raw(string $key): string
    {
        $v = Request::post($key, '');

        return is_scalar($v) ? (string) $v : '';
    }

    /** Token valid = 64 karakter hex, selain itu string kosong */
    private function tokenDari(mixed $raw): string
    {
        $t = is_scalar($raw) ? trim((string) $raw) : '';

        return preg_match('/^[a-f0-9]{64}$/', $t) === 1 ? $t : '';
    }

    private function userDariToken(string $token): ?array
    {
        return $token === ''
            ? null
            : $this->users->findByResetToken(hash('sha256', $token));
    }

    /** @return array<string,string> */
    private function validasiPasswordBaru(string $baru, string $konfirmasi): array
    {
        $errors = ValidationHelper::validate(
            ['password' => $baru],
            ['password' => ['required', 'min:8', 'max:72']],
            ['password' => 'Password baru']
        );

        if (!isset($errors['password']) && $baru !== $konfirmasi) {
            $errors['password_confirm'] = 'Konfirmasi password tidak cocok.';
        }

        return $errors;
    }
}