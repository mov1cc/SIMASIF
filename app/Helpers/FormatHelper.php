<?php

namespace App\Helpers;

class FormatHelper
{
    public static function rupiah(mixed $nilai): string
    {
        $angka    = (float) $nilai;
        $desimal  = floor($angka) == $angka ? 0 : 2;

        return 'Rp ' . number_format($angka, $desimal, ',', '.');
    }

    public static function durasi(mixed $menit): string
    {
        if ($menit === null || $menit === '') {
            return '-';
        }

        $menit = (int) $menit;

        if ($menit < 60) {
            return "{$menit} menit";
        }

        $jam  = intdiv($menit, 60);
        $sisa = $menit % 60;

        return $sisa === 0 ? "{$jam} jam" : "{$jam} jam {$sisa} menit";
    }

    public static function e(mixed $nilai): string
    {
        return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
    }

    public static function bool(mixed $nilai): bool
    {
        if (is_bool($nilai)) {
            return $nilai;
        }

        return in_array(
            strtolower(trim((string) $nilai)),
            ['1', 't', 'true', 'y', 'yes', 'on'],
            true
        );
    }
}