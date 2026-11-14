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

    /**
     * Daftar akun. $role null = semua akun.
     */
    public function all(?string $role = null): array
    {
        if ($role === null) {
            return $this->query(
                "SELECT id, nama, email, no_telepon, role, is_active, last_login_at, created_at
                   FROM users
                  ORDER BY role ASC, nama ASC"
            );
        }

        return $this->query(
            "SELECT id, nama, email, no_telepon, role, is_active, last_login_at, created_at
               FROM users
              WHERE role = CAST(? AS user_role)
              ORDER BY is_active DESC, nama ASC",
            [$role]
        );
    }

    /**
     * Apakah email sudah dipakai akun lain?
     */
    public function emailDipakai(string $email, ?int $exceptId = null): bool
    {
        if ($exceptId === null) {
            return $this->first(
                "SELECT 1 AS ada FROM users WHERE email = ? LIMIT 1",
                [$email]
            ) !== null;
        }

        return $this->first(
            "SELECT 1 AS ada FROM users WHERE email = ? AND id <> ? LIMIT 1",
            [$email, $exceptId]
        ) !== null;
    }

    /**
     * Tambah akun pegawai (role selalu pegawai), mengembalikan ID baru.
     *
     * @param array{nama:string, email:string, no_telepon:?string,
     *              password_hash:string, is_active:bool} $d
     */
    public function createPegawai(array $d): int
    {
        $row = $this->first(
            "INSERT INTO users (nama, email, password, no_telepon, role, is_active)
             VALUES (?, ?, ?, ?, 'pegawai', ?)
             RETURNING id",
            [
                $d['nama'],
                $d['email'],
                $d['password_hash'],
                $d['no_telepon'],
                $d['is_active'] ? 'true' : 'false',
            ]
        );

        return (int) $row['id'];
    }

    /**
     * Ubah data akun oleh owner (tanpa password).
     */
    public function updateAkun(int $id, array $d): bool
    {
        return $this->execute(
            "UPDATE users
                SET nama = ?, email = ?, no_telepon = ?, is_active = ?
              WHERE id = ?",
            [
                $d['nama'],
                $d['email'],
                $d['no_telepon'],
                $d['is_active'] ? 'true' : 'false',
                $id,
            ]
        );
    }

    /**
     * Ubah profil sendiri (nama, email, no telepon).
     */
    public function updateProfil(int $id, array $d): bool
    {
        return $this->execute(
            "UPDATE users SET nama = ?, email = ?, no_telepon = ? WHERE id = ?",
            [$d['nama'], $d['email'], $d['no_telepon'], $id]
        );
    }

    public function setActive(int $id, bool $aktif): bool
    {
        return $this->execute(
            "UPDATE users SET is_active = ? WHERE id = ?",
            [$aktif ? 'true' : 'false', $id]
        );
    }

    /**
     * Ganti password sekaligus membatalkan token reset yang masih ada.
     */
    public function updatePassword(int $id, string $hash): bool
    {
        return $this->execute(
            "UPDATE users
                SET password = ?, reset_token = NULL, reset_token_expires_at = NULL
              WHERE id = ?",
            [$hash, $id]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute("DELETE FROM users WHERE id = ?", [$id]);
    }

    /**
     * Simpan HASH token reset (token asli hanya ada di email).
     */
    public function setResetToken(int $id, string $tokenHash, int $menit): bool
    {
        $menit = max(1, $menit);

        return $this->execute(
            "UPDATE users
                SET reset_token = ?,
                    reset_token_expires_at = NOW() + INTERVAL '{$menit} minutes'
              WHERE id = ?",
            [$tokenHash, $id]
        );
    }

    /**
     * Cari akun aktif dengan token reset yang cocok dan belum kedaluwarsa.
     */
    public function findByResetToken(string $tokenHash): ?array
    {
        return $this->first(
            "SELECT * FROM users
              WHERE reset_token = ?
                AND reset_token_expires_at > NOW()
                AND is_active = TRUE
              LIMIT 1",
            [$tokenHash]
        );
    }
}