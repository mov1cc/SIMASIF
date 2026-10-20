<?php

declare(strict_types=1);

namespace App\Core;

use DateTime;
use finfo;
use InvalidArgumentException;

final class Validator
{
    private const MESSAGES = [
        'required' => ':field wajib diisi.',
        'string'   => ':field harus berupa teks.',
        'email'    => ':field harus berupa alamat email yang valid.',
        'numeric'  => ':field harus berupa angka.',
        'integer'  => ':field harus berupa bilangan bulat.',
        'min'      => ':field minimal :arg.',
        'max'      => ':field maksimal :arg.',
        'in'       => ':field tidak valid.',
        'same'     => ':field tidak cocok dengan :arg.',
        'date'     => ':field harus berupa tanggal yang valid.',
        'regex'    => 'Format :field tidak valid.',
        'unique'   => ':field sudah digunakan.',
        'exists'   => ':field tidak ditemukan.',
        'file'     => ':field harus berupa file yang valid.',
        'mimes'    => ':field harus berformat :arg.',
        'max_kb'   => 'Ukuran :field maksimal :arg KB.',
    ];

    private const MIME_MAP = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'pdf'  => 'application/pdf',
    ];

    /** @var array<string, string[]> */
    private array $errors = [];

    private function __construct(
        private array $data,
        private array $rules,
        private array $messages,
        private array $labels,
    ) {
    }

    public static function make(array $data, array $rules, array $messages = [], array $labels = []): self
    {
        $validator = new self($data, $rules, $messages, $labels);
        $validator->run();

        return $validator;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string[]> */
    public function errors(): array
    {
        return $this->errors;
    }

  
    public function validated(): array
    {
        $result = [];

        foreach (array_keys($this->rules) as $field) {
            $value = $this->data[$field] ?? null;
            $result[$field] = is_string($value) ? trim($value) : $value;
        }

        return $result;
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleSet) {
            $list    = is_array($ruleSet) ? $ruleSet : explode('|', (string) $ruleSet);
            $value   = $this->data[$field] ?? null;
            $empty   = $this->isEmpty($value);
            $numeric = in_array('numeric', $list, true) || in_array('integer', $list, true);

            foreach ($list as $rule) {
                [$name, $raw] = array_pad(explode(':', (string) $rule, 2), 2, null);

                if ($name === 'nullable') {
                    continue;
                }

                if ($name === 'required') {
                    if ($empty) {
                        $this->fail($field, 'required', null);
                        break;
                    }
                    continue;
                }

                if ($empty) {
                    continue; 
                }

                $args = $raw === null ? [] : ($name === 'regex' ? [$raw] : explode(',', $raw));

                if (!$this->passes($name, $value, $args, $numeric)) {
                    $this->fail($field, $name, $raw);
                    break;
                }
            }
        }
    }

    private function passes(string $rule, mixed $value, array $args, bool $numeric): bool
    {
        return match ($rule) {
            'string'  => is_string($value),
            'email'   => is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'numeric' => is_numeric($value),
            'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false,
            'min'     => $this->size($value, $numeric) >= (float) ($args[0] ?? 0),
            'max'     => $this->size($value, $numeric) <= (float) ($args[0] ?? 0),
            'in'      => in_array((string) $value, $args, true),
            'same'    => ($this->data[$args[0] ?? ''] ?? null) === $value,
            'date'    => $this->isDate((string) $value, $args[0] ?? 'Y-m-d'),
            'regex'   => preg_match($args[0] ?? '', (string) $value) === 1,
            'unique'  => $this->isUnique($value, $args),
            'exists'  => $this->exists($value, $args),
            'file'    => $this->isUploadedFile($value),
            'mimes'   => $this->hasMime($value, $args),
            'max_kb'  => is_array($value) && (int) ($value['size'] ?? 0) <= ((int) ($args[0] ?? 0)) * 1024,
            default   => throw new InvalidArgumentException("Aturan validasi tidak dikenal: {$rule}"),
        };
    }

    private function size(mixed $value, bool $numeric): float
    {
        return $numeric ? (float) $value : (float) mb_strlen((string) $value);
    }

    private function isEmpty(mixed $value): bool
    {
        return $value === null
            || (is_string($value) && trim($value) === '')
            || $value === []
            || (is_array($value) && ($value['error'] ?? null) === UPLOAD_ERR_NO_FILE);
    }

    private function isDate(string $value, string $format): bool
    {
        $date = DateTime::createFromFormat($format, $value);

        return $date !== false && $date->format($format) === $value;
    }

    private function isUnique(mixed $value, array $args): bool
    {
        $table  = $this->identifier($args[0] ?? '');
        $column = $this->identifier($args[1] ?? '');
        $sql    = "SELECT 1 FROM {$table} WHERE {$column} = :v";
        $params = ['v' => $value];

        if (isset($args[2]) && $args[2] !== '') {
            $idColumn         = $this->identifier($args[3] ?? 'id');
            $sql             .= " AND {$idColumn} <> :except";
            $params['except'] = $args[2];
        }

        return Database::fetch($sql . ' LIMIT 1', $params) === null;
    }

    private function exists(mixed $value, array $args): bool
    {
        $table  = $this->identifier($args[0] ?? '');
        $column = $this->identifier($args[1] ?? '');

        return Database::fetch("SELECT 1 FROM {$table} WHERE {$column} = :v LIMIT 1", ['v' => $value]) !== null;
    }

    private function isUploadedFile(mixed $value): bool
    {
        return is_array($value)
            && ($value['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
            && isset($value['tmp_name'])
            && is_uploaded_file($value['tmp_name']);
    }

    
    private function hasMime(mixed $value, array $args): bool
    {
        if (!is_array($value) || !isset($value['tmp_name']) || !is_file($value['tmp_name'])) {
            return false;
        }

        $allowed = array_filter(array_map(
            static fn (string $ext): ?string => self::MIME_MAP[strtolower(trim($ext))] ?? null,
            $args
        ));

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($value['tmp_name']);

        return in_array($mime, $allowed, true);
    }

    private function identifier(string $name): string
    {
        if (!preg_match('/^[a-z_][a-z0-9_]*$/i', $name)) {
            throw new InvalidArgumentException("Identifier tidak valid: {$name}");
        }

        return $name;
    }

    private function fail(string $field, string $rule, ?string $arg): void
    {
        $template = $this->messages[$field . '.' . $rule]
            ?? self::MESSAGES[$rule]
            ?? ':field tidak valid.';

        $label = $this->labels[$field] ?? ucfirst(str_replace('_', ' ', $field));

        $this->errors[$field][] = strtr($template, [
            ':field' => $label,
            ':arg'   => $arg ?? '',
        ]);
    }
}