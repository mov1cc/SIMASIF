<?php

declare(strict_types=1);

return [
    'table'         => 'users',
    'id'            => 'id',
    'identifier'    => 'email',      // kolom untuk login: email atau username
    'password'      => 'password',   // kolom berisi hash password (password_hash)
    'role'          => 'role',       // nilai role: 'pegawai' atau 'owner'
    'active_column' => null,         // isi mis. 'is_active' jika ada kolom status aktif
    'login_path'    => '/login',
    'home'          => [
        'pegawai' => '/pegawai/dashboard',
        'owner'   => '/owner/dashboard',
    ],
];