<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;
use LogicException;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';

  
    protected array $fillable = [];

    public function __construct()
    {
        if ($this->table === '') {
            throw new LogicException(static::class . ' belum mendefinisikan $table.');
        }
    }

    public function find(int|string $id): ?array
    {
        return Database::fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function findBy(string $column, mixed $value): ?array
    {
        return $this->first([$column => $value]);
    }

    public function first(array $where = []): ?array
    {
        [$sql, $params] = $this->buildWhere($where);

        return Database::fetch("SELECT * FROM {$this->table}{$sql} LIMIT 1", $params);
    }

    public function all(?string $orderBy = null, string $direction = 'ASC'): array
    {
        return $this->where([], $orderBy, $direction);
    }

    public function where(array $where, ?string $orderBy = null, string $direction = 'ASC', ?int $limit = null): array
    {
        [$sql, $params] = $this->buildWhere($where);

        $order = $this->column($orderBy ?? $this->primaryKey) . ' ' . $this->direction($direction);
        $query = "SELECT * FROM {$this->table}{$sql} ORDER BY {$order}";

        if ($limit !== null) {
            $query .= ' LIMIT :__limit';
            $params['__limit'] = $limit;
        }

        return Database::fetchAll($query, $params);
    }

    public function count(array $where = []): int
    {
        [$sql, $params] = $this->buildWhere($where);

        return (int) Database::scalar("SELECT COUNT(*) FROM {$this->table}{$sql}", $params);
    }

    
    public function create(array $data): int|string
    {
        $data = $this->onlyFillable($data);

        if ($data === []) {
            throw new InvalidArgumentException('Tidak ada kolom valid untuk disimpan.');
        }

        $columns      = array_map(fn (string $c): string => $this->column($c), array_keys($data));
        $placeholders = array_map(static fn (string $c): string => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s) RETURNING %s',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders),
            $this->primaryKey
        );

        return Database::query($sql, $data)->fetchColumn();
    }

    public function update(int|string $id, array $data): bool
    {
        $data = $this->onlyFillable($data);

        if ($data === []) {
            return false;
        }

        $sets   = [];
        $params = ['__id' => $id];

        foreach ($data as $column => $value) {
            $column = $this->column($column);
            $sets[] = "{$column} = :s_{$column}";
            $params['s_' . $column] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :__id',
            $this->table,
            implode(', ', $sets),
            $this->primaryKey
        );

        return Database::execute($sql, $params) > 0;
    }

    public function delete(int|string $id): bool
    {
        return Database::execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        ) > 0;
    }

    /** @return array{data: array, total: int, page: int, per_page: int, last_page: int} */
    public function paginate(
        int $page = 1,
        int $perPage = 10,
        array $where = [],
        ?string $orderBy = null,
        string $direction = 'DESC'
    ): array {
        [$sql, $params] = $this->buildWhere($where);

        $total    = $this->count($where);
        $perPage  = max(1, $perPage);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min(max(1, $page), $lastPage);
        $order    = $this->column($orderBy ?? $this->primaryKey) . ' ' . $this->direction($direction);

        $rows = Database::fetchAll(
            "SELECT * FROM {$this->table}{$sql} ORDER BY {$order} LIMIT :__limit OFFSET :__offset",
            $params + ['__limit' => $perPage, '__offset' => ($page - 1) * $perPage]
        );

        return [
            'data'      => $rows,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => $lastPage,
        ];
    }

    
    protected function buildWhere(array $where): array
    {
        $parts  = [];
        $params = [];
        $i      = 0;

        foreach ($where as $column => $value) {
            $column = $this->column((string) $column);

            if ($value === null) {
                $parts[] = "{$column} IS NULL";
                continue;
            }

            $key          = 'w' . $i++;
            $parts[]      = "{$column} = :{$key}";
            $params[$key] = $value;
        }

        return [$parts === [] ? '' : ' WHERE ' . implode(' AND ', $parts), $params];
    }

    protected function onlyFillable(array $data): array
    {
        return $this->fillable === [] ? [] : array_intersect_key($data, array_flip($this->fillable));
    }

    protected function column(string $name): string
    {
        if (!preg_match('/^[a-z_][a-z0-9_]*$/i', $name)) {
            throw new InvalidArgumentException("Nama kolom tidak valid: {$name}");
        }

        return $name;
    }

    protected function direction(string $direction): string
    {
        return strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
    }
}