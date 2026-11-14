<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\ValidationHelper;
use App\Models\User;
use App\Services\AuditLogService;
use PDOException;

class ProfilController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    /**
     * GET /owner/profil
     */
    public function index(): void
    {
        $this->render('owner/profil', [
            'title' => 'Profil Saya',
            'user'  => $this->saya(),
        ]);
    }

    /**
     * POST /owner/profil/update
     * Mengganti email wajib memasukkan password saat ini
     * (supaya sesi yang dibajak tidak bisa mengambil alih akun).
     */
    public function update(): void
    {
        $this->verifyCsrf();

        $user = $this->saya();
        $id   = (int) $user['id'];

        $input = [
            'nama'       => $this->postString('nama'),
            'email'      => strtolower($this->postString('email')),
            'no_telepon' => $this->postString('no_telepon'),
        ];

        $pwSaatIni = Request::post('password_saat_ini', '');
        $pwSaatIni = is_scalar($pwSaatIni) ? (string) $pwSaatIni : '';

        $errors = ValidationHelper::validate(
            $input,
            [
                'nama'       => ['required', 'max:100'],
                'email'      => ['required', 'email', 'max:100'],
                'no_telepon' => ['max:20'],
            ],
            ['nama' => 'Nama', 'email' => 'Email', 'no_telepon' => 'No telepon']
        );

        if (!isset($errors['no_telepon']) && !ValidationHelper::noTelepon($input['no_telepon'])) {
            $errors['no_telepon'] = 'Format no telepon tidak valid (angka, +, -, spasi; 8-20 karakter).';
        }

        $emailBerubah = $input['email'] !== strtolower((string) $user['email']);

        if (!isset($errors['email']) && $emailBerubah) {
            if ($this->users->emailDipakai($input['email'], $id)) {
                $errors['email'] = 'Email sudah dipakai akun lain.';
            } elseif (!password_verify($pwSaatIni, $user['password'])) {
                $errors['password_saat_ini'] = 'Password saat ini salah (wajib diisi untuk mengganti email).';
            }
        }

        if (!empty($errors)) {
            Session::flash('errors_profil', $errors);
            Session::flash('old_profil', $input);
            $this->redirect('/owner/profil');
        }

        try {
            $this->users->updateProfil($id, [
                'nama'       => $input['nama'],
                'email'      => $input['email'],
                'no_telepon' => $input['no_telepon'] !== '' ? $input['no_telepon'] : null,
            ]);
        } catch (PDOException $e) {
            if ((string) $e->getCode() === '23505') {
                Session::flash('errors_profil', ['email' => 'Email sudah dipakai akun lain.']);
                Session::flash('old_profil', $input);
                $this->redirect('/owner/profil');
            }
            throw $e;
        }

        // Nama/email di navbar ikut berubah
        Session::set('nama', $input['nama']);
        Session::set('email', $input['email']);

        $perubahan = [];

        foreach (['nama', 'email', 'no_telepon'] as $k) {
            $a = (string) ($user[$k] ?? '');

            if ($a !== $input[$k]) {
                $perubahan[$k] = ['dari' => $a, 'ke' => $input[$k]];
            }
        }

        AuditLogService::log(AuditLogService::MODUL_PROFIL, 'Ubah profil sendiri', [
            'perubahan' => $perubahan,
        ]);

        Session::flash('success', 'Profil berhasil diperbarui.');
        $this->redirect('/owner/profil');
    }

    // ------------------------------------------------------------------

    private function saya(): array
    {
        $row = $this->users->findById((int) Session::get('user_id'));

        if ($row === null) {
            throw new \Exception('Akun tidak ditemukan.', 404);
        }

        return $row;
    }

    private function verifyCsrf(): void
    {
        if (!CsrfHelper::verify(Request::post('_csrf'))) {
            throw new \Exception('Token keamanan tidak valid.', 403);
        }
    }

    private function postString(string $key): string
    {
        $value = Request::post($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }
}