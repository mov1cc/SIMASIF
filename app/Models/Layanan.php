<?php

namespace App\Models;

use App\Core\Model;

class Layanan extends Model
{
    protected string $table = 'layanan';
    
    public function all(): array
    {
        return $this->query(
            "SELECT * FROM layanan ORDER BY is_active DESC, nama ASC"
        );
    }

    public function findActive(): array
    {
        return $this->query(
            "SELECT * FROM layanan WHERE is_active = TRUE ORDER BY nama ASC"
        );
    }

    public function find(int $id): ?array
    {
        return parent::find($id);
    }

    /**
     * @param array{nama:string, deskripsi:string, harga:string, estimasi_durasi_menit:string, is_active:bool} $data
     */
    public function create(array $data): int
    {
        $row = $this->first(
            "INSERT INTO layanan
                (nama, deskripsi, harga, estimasi_durasi_menit, is_active)
             VALUES (?, ?, ?, ?, ?)
             RETURNING id",
            $this->toParams($data)
        );

        return (int) $row['id'];
    }

    public function update(int $id, array $data): bool
    {
        return $this->execute(
            "UPDATE layanan
                SET nama = ?,
                    deskripsi = ?,
                    harga = ?,
                    estimasi_durasi_menit = ?,
                    is_active = ?
              WHERE id = ?",
            [...$this->toParams($data), $id]
        );
    }


    public function delete(int $id): bool
    {
        return $this->execute(
            "DELETE FROM layanan WHERE id = ?",
            [$id]
        );
    }

    public function setActive(int $id, bool $aktif): bool
    {
        return $this->execute(
            "UPDATE layanan SET is_active = ? WHERE id = ?",
            [$aktif ? 'true' : 'false', $id]
        );
    }

    /**
     * @return array{top:?array
     */
    public function statistikBulanIni(): array
    {
        $rows = $this->query(
            "SELECT l.id, l.nama,
                    COUNT(p.id) AS sesi,
                    COALESCE(SUM(
                        CASE WHEN t.status_pembayaran = 'lunas'
                             THEN p.total_tagihan ELSE 0 END
                    ), 0) AS omzet
               FROM layanan l
               LEFT JOIN pemesanan p
                      ON p.layanan_id = l.id
                     AND p.created_at >= date_trunc('month', NOW())
                     AND p.created_at <  date_trunc('month', NOW()) + INTERVAL '1 month'
               LEFT JOIN transaksi_pembayaran t ON t.pemesanan_id = p.id
              WHERE l.is_active = TRUE
              GROUP BY l.id, l.nama
              ORDER BY sesi DESC, omzet DESC, l.nama ASC"
        );

        $totalRow = $this->first(
            "SELECT COALESCE(SUM(p.total_tagihan), 0) AS total
               FROM pemesanan p
               JOIN transaksi_pembayaran t ON t.pemesanan_id = p.id
              WHERE t.status_pembayaran = 'lunas'
                AND p.created_at >= date_trunc('month', NOW())
                AND p.created_at <  date_trunc('month', NOW()) + INTERVAL '1 month'"
        );

        $rows = array_map(fn (array $r) => [
            'id'    => (int) $r['id'],
            'nama'  => $r['nama'],
            'sesi'  => (int) $r['sesi'],
            'omzet' => (float) $r['omzet'],
        ], $rows);

        $top      = null;
        $terendah = null;

        if (!empty($rows) && $rows[0]['sesi'] > 0) {
            $top = $rows[0];

            $terakhir = end($rows);
            if ($terakhir['id'] !== $top['id']) {
                $terendah = $terakhir;
            }
        }

        return [
            'top'         => $top,
            'terendah'    => $terendah,
            'total_omzet' => (float) ($totalRow['total'] ?? 0),
        ];
    }

    private function toParams(array $data): array
    {
        return [
            $data['nama'],
            $data['deskripsi'] !== '' ? $data['deskripsi'] : null,
            $data['harga'],
            $data['estimasi_durasi_menit'] !== ''
                ? $data['estimasi_durasi_menit']
                : null,
            $data['is_active'] ? 'true' : 'false',
        ];
    }
}