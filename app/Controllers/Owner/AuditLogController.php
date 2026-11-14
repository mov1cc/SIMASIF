<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use App\Core\Request;
use App\Helpers\ValidationHelper;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogController extends Controller
{
    private const PER_HALAMAN = 25;

    private AuditLog $log;

    public function __construct()
    {
        $this->log = new AuditLog();
    }

    /**
     * GET /owner/audit-log?user_id=&modul=&dari=&sampai=&page=
     */
    public function index(): void
    {
        $daftarModul = $this->log->daftarModul();

        $modul = $this->queryString('modul');
        if (!in_array($modul, $daftarModul, true)) {
            $modul = '';
        }

        $filter = [
            'user_id' => (int) $this->queryString('user_id'),
            'modul'   => $modul,
            'dari'    => ValidationHelper::tanggalValid(Request::get('dari')),
            'sampai'  => ValidationHelper::tanggalValid(Request::get('sampai')),
        ];

        $total        = $this->log->count($filter);
        $halamanTotal = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman      = min(max(1, (int) $this->queryString('page')), $halamanTotal);

        $this->render('owner/audit-log/index', [
            'title'        => 'Audit Log',
            'items'        => $this->log->paginate(
                $filter,
                self::PER_HALAMAN,
                ($halaman - 1) * self::PER_HALAMAN
            ),
            'filter'       => $filter,
            'daftarModul'  => $daftarModul,
            'daftarUser'   => (new User())->all(),
            'total'        => $total,
            'halaman'      => $halaman,
            'halamanTotal' => $halamanTotal,
        ]);
    }

    private function queryString(string $key): string
    {
        $value = Request::get($key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }
}