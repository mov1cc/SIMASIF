<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper;
use App\Helpers\ValidationHelper;
use App\Models\User;
use App\Services\AuditLogService;
use PDOException;

class UserController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    /**
     * GET /owner/users
     */
    public function index(): void
    {
        $this->render('owner/users/index', [
            'title' => 'Kelola Pegawai',
            'items' => $this->users->all(ROLE_PEGAWAI),
        ]);
    }

    /**
     * GET /owner/users/create
     */
    public function create(): void
    {
        $this->render('owner/users/form', [
            'title'  => 'Tambah Pegawai',
            'user'   => null,
            'action' => '/owner/users/store',
        ]);
    }

    /**
     * POST /owner/users/store
     */
    public function store(): void
    {
        $this->verifyCsrf();

        $input  = $this->collectInput();
        $errors = $this->validateInput($input, true);

        if (!isset($errors['email']) && $this->users->emailDipakai($input['email'])) {
            $errors['email'] = 'Email sudah dipakai akun lain.';
        }

        if (!empty($errors)) {
            $this->gagal($errors, $input, '/owner/users/create');
        }

        try {
            $id = $this->users->createPegawai([
                'nama'          => $input['nama'],
                'email'         => $input['email'],
                'no_telepon'    => $input['no_telepon'] !== '' ? $input['no_telepon'] : null,
                'password_hash' => password_hash($input['password'], PASSWORD_BCRYPT),
                'is_active'     => $input['is_active'],
            ]);
        } catch (PDOException $e) {
            // 23505 = unique_violation (dua request bersamaan)
            if ((string) $e->getCode() === '23505') {
                $this->gagal(['email' => 'Email sudah dipakai akun lain.'], $input, '/owner/users/create');
            }
            throw $e;
        }

        AuditLogService::log(AuditLogService::MODUL_USER, 'Tambah akun pegawai', [
            'id'        => $id,
            'nama'      => $input['nama'],
            'email'     => $input['email'],
            'is_active' => $input['is_active'],
        ]);

        Session::flash('success', 'Akun pegawai berhasil ditambahkan.');
        $this->redirect('/owner/users');
    }

    /**
     * GET /owner/users/edit?id=1
     */
    public function edit(): void
    {
        $row = $this->pegawaiOrFail($this->queryInt('id'));

        $this->render('owner/users/form', [
            'title'  => 'Edit Pegawai',
            'user'   => $row,
            'action' => '/owner/users/update',
        ]);
    }

    /**
     * POST /owner/users/update
     * Password kosong = password tidak diubah.
     */
    public function update(): void
    {
        $this->verifyCsrf();

        $id   = (int) $this->postString('id');
        $lama = $this->pegawaiOrFail($id);
        $edit = '/owner/users/edit?id=' . $id;

        $input  = $this->collectInput();
        $errors = $this->validateInput($input, false);

        if (!isset($errors['email']) && $this->users->emailDipakai($input['email'], $id)) {
            $errors['email'] = 'Email sudah dipakai akun lain.';
        }

        if (!empty($errors)) {
            $this->gagal($errors, $input, $edit);
        }

        try {
            $this->users->updateAkun($id, [
                'nama'       => $input['nama'],
                'email'      => $input['email'],
                'no_telepon' => $input['no_telepon'] !== '' ? $input['no_telepon'] : null,
                'is_active'  => $input['is_active'],
            ]);
        } catch (PDOException $e) {
            if ((string) $e->getCode() === '23505') {
                $this->gagal(['email' => 'Email sudah dipakai akun lain.'], $input, $edit);
            }
            throw $e;
        }

        $passwordDiubah = $input['password'] !== '';

        if ($passwordDiubah) {
            $this->users->updatePassword($id, password_hash($input['password'], PASSWORD_BCRYPT));
        }

        // Catat apa saja yang berubah (password TIDAK PERNAH dicatat nilainya)
        $perubahan = [];

        foreach (['nama', 'email', 'no_telepon'] as $k) {
            $a = (string) ($lama[$k] ?? '');

            if ($a !== $input[$k]) {
                $perubahan[$k] = ['dari' => $a, 'ke' => $input[$k]];
            }
        }

        $aktifLama = FormatHelper::bool($lama['is_active']);

        if ($aktifLama !== $input['is_active']) {
            $perubahan['is_active'] = ['dari' => $aktifLama, 'ke' => $input['is_active']];
        }

        if ($passwordDiubah) {
            $perubahan['password'] = 'diganti oleh owner';
        }

        AuditLogService::log(AuditLogService::MODUL_USER, 'Ubah akun pegawai', [
            'id'        => $id,
            'perubahan' => $perubahan,
        ]);

        Session::flash('success', 'Akun pegawai berhasil diperbarui.');
        $this->redirect('/owner/users');
    }

    /**
     * POST /owner/users/toggle   (field: id, aktif = 1|0)
     */
    public function toggle(): void
    {
        $this->verifyCsrf();

        $row   = $this->pegawaiOrFail((int) $this->postString('id'));
        $aktif = $this->postString('aktif') === '1';

        $this->users->setActive((int) $row['id'], $aktif);

        AuditLogService::log(
            AuditLogService::MODUL_USER,
            $aktif ? 'Aktifkan akun pegawai' : 'Nonaktifkan akun pegawai',
            ['id' => (int) $row['id'], 'nama' => $row['nama'], 'email' => $row['email']]
        );

        Session::flash(
            'success',
            'Akun "' . $row['nama'] . '" ' . ($aktif ? 'diaktifkan.' : 'dinonaktifkan. Pegawai ini langsung tidak bisa mengakses sistem.')
        );

        $this->redirect('/owner/users');
    }

    /**
     * POST /owner/users/delete
     * Riwayat (workflow, verifikasi bayar, audit log) tetap ada, kolom pelakunya menjadi NULL.
     * Untuk pegawai yang sudah pernah bekerja, nonaktifkan lebih disarankan.
     */
    public function destroy(): void
    {
        $this->verifyCsrf();

        $row = $this->pegawaiOrFail((int) $this->postString('id'));

        $this->users->delete((int) $row['id']);

        AuditLogService::log(AuditLogService::MODUL_USER, 'Hapus akun pegawai', [
            'id'    => (int) $row['id'],
            'nama'  => $row['nama'],
            'email' => $row['email'],
        ]);

        Session::flash('success', 'Akun pegawai berhasil dihapus.');
        $this->redirect('/owner/users');
    }

    // ------------------------------------------------------------------
    // Helper privat
    // ------------------------------------------------------------------

    private function gagal(array $errors, array $input, string $url): never
    {
        unset($input['password']); // jangan kirim balik password ke sesi/form

        Session::flash('errors', $errors);
        Session::flash('old', $input);

        $this->redirect($url);
    }

    private function verifyCsrf(): void
    {
        if (!CsrfHelper::verify(Request::post('_csrf'))) {
            throw new \Exception('Token keamanan tidak valid.', 403);
        }
    }

    /** Hanya akun ber-role pegawai yang boleh dikelola dari sini */
    private function pegawaiOrFail(int $id): array
    {
        $row = $id > 0 ? $this->users->findById($id) : null;

        if ($row === null || $row['role'] !== ROLE_PEGAWAI) {
            throw new \Exception('Akun pegawai tidak ditemukan.', 404);
        }

        return $row;
    }

    private function queryInt(string $key): int
    {
        $v = Request::get($key, 0);

        return is_scalar($v) ? (int) $v : 0;
    }

    private function postString(string $key): string
    {
        $value = Request::post($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function collectInput(): array
    {
        $pw = Request::post('password', '');

        return [
            'nama'       => $this->postString('nama'),
            'email'      => strtolower($this->postString('email')),
            'no_telepon' => $this->postString('no_telepon'),
            'password'   => is_scalar($pw) ? (string) $pw : '',
            'is_active'  => Request::post('is_active') !== null,
        ];
    }

    private function validateInput(array $input, bool $passwordWajib): array
    {
        $rules = [
            'nama'       => ['required', 'max:100'],
            'email'      => ['required', 'email', 'max:100'],
            'no_telepon' => ['max:20'],
        ];

        if ($passwordWajib) {
            $rules['password'] = ['required', 'min:8', 'max:72'];
        } elseif ($input['password'] !== '') {
            $rules['password'] = ['min:8', 'max:72'];
        }

        $errors = ValidationHelper::validate(
            $input,
            $rules,
            [
                'nama'       => 'Nama',
                'email'      => 'Email',
                'no_telepon' => 'No telepon',
                'password'   => 'Password',
            ]
        );

        if (!isset($errors['no_telepon']) && !ValidationHelper::noTelepon($input['no_telepon'])) {
            $errors['no_telepon'] = 'Format no telepon tidak valid (angka, +, -, spasi; 8-20 karakter).';
        }

        return $errors;
    }
}