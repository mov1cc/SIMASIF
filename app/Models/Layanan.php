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
     * @param array{nama:string
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