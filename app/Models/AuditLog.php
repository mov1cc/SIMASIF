<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected string $table = 'audit_log';

    public function create(
        ?int $userId,
        string $modul,
        string $aktivitas,
        array $detail,
        ?string $ip
    ): int {
        $json = $detail === []
            ? null
            : (json_encode(
                $detail,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
            ) ?: null);

        $row = $this->first(
            "INSERT INTO audit_log (user_id, modul, aktivitas, detail, ip_address)
             VALUES (?, ?, ?, CAST(? AS jsonb), ?)
             RETURNING id",
            [$userId, $modul, $aktivitas, $json, $ip]
        );

        return (int) $row['id'];
    }

    /**
     * @param array{user_id?:int, modul?:string, dari?:string, sampai?:string} $f
     * @return array{0:string, 1:array}
     */
    private function where(array $f): array
    {
        $sql    = " WHERE 1 = 1";
        $params = [];

        if (!empty($f['user_id'])) {
            $sql     .= " AND a.user_id = ?";
            $params[] = (int) $f['user_id'];
        }

        if (!empty($f['modul'])) {
            $sql     .= " AND a.modul = ?";
            $params[] = $f['modul'];
        }

        if (!empty($f['dari'])) {
            $sql     .= " AND a.created_at >= CAST(? AS DATE)";
            $params[] = $f['dari'];
        }

        if (!empty($f['sampai'])) {
            $sql     .= " AND a.created_at < CAST(? AS DATE) + INTERVAL '1 day'";
            $params[] = $f['sampai'];
        }

        return [$sql, $params];
    }

    public function count(array $f = []): int
    {
        [$where, $params] = $this->where($f);

        $row = $this->first(
            "SELECT COUNT(*) AS n FROM audit_log a" . $where,
            $params
        );

        return (int) ($row['n'] ?? 0);
    }

    public function paginate(array $f, int $limit, int $offset): array
    {
        [$where, $params] = $this->where($f);

        return $this->query(
            "SELECT a.id, a.user_id, u.nama AS user_nama, u.role AS user_role,
                    a.modul, a.aktivitas, a.detail, a.ip_address, a.created_at
               FROM audit_log a
               LEFT JOIN users u ON u.id = a.user_id"
            . $where
            . " ORDER BY a.created_at DESC, a.id DESC"
            . " LIMIT " . max(1, $limit) . " OFFSET " . max(0, $offset),
            $params
        );
    }

    /** @return string[] */
    public function daftarModul(): array
    {
        return array_column(
            $this->query("SELECT DISTINCT modul FROM audit_log ORDER BY modul ASC"),
            'modul'
        );
    }
}