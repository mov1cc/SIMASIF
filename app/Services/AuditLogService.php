<?php

namespace App\Services;

use App\Core\Session;
use App\Models\AuditLog;

class AuditLogService
{
    public const MODUL_AUTH       = 'Auth';
    public const MODUL_WORKFLOW   = 'Workflow';
    public const MODUL_PEMBAYARAN = 'Pembayaran';
    public const MODUL_USER       = 'User';
    public const MODUL_PROFIL     = 'Profil';

    /**
     * Catat satu aktivitas. TIDAK PERNAH melempar error: kegagalan log
     * tidak boleh menggagalkan aksi utama.
     *
     * Panggil SETELAH transaksi database selesai (commit), supaya error
     * log tidak ikut membatalkan transaksi PostgreSQL yang sedang berjalan.
     *
     * @param int|null $userId null = ambil dari session (kalau ada)
     */
    public static function log(
        string $modul,
        string $aktivitas,
        array $detail = [],
        ?int $userId = null
    ): void {
        try {
            $userId ??= Session::has('user_id') ? (int) Session::get('user_id') : null;

            $ip = $_SERVER['REMOTE_ADDR'] ?? null;

            (new AuditLog())->create(
                $userId,
                mb_substr($modul, 0, 50),
                mb_substr($aktivitas, 0, 150),
                $detail,
                $ip !== null ? substr((string) $ip, 0, 45) : null
            );
        } catch (\Throwable $e) {
            error_log('[AuditLog] gagal mencatat: ' . $e->getMessage());
        }
    }
}