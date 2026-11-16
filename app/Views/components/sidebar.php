<?php
use App\Core\Request;
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

/**
 * @var string $sbBrand   teks brand
 * @var string $sbHome    link brand
 * @var string $sbTema    'pegawai' | 'owner'
 * @var array<int, array{label:string, href:string, icon:string, match:string, exact?:bool}> $sbMenu
 */

$uri = Request::uri();

$aktif = static fn (array $m): bool => !empty($m['exact'])
    ? $uri === $m['match']
    : str_starts_with($uri, $m['match']);

$peran = $sbTema === 'owner' ? 'Owner' : 'Pegawai';
?>
<aside class="sf-sidebar sf-sidebar-<?= F::e($sbTema) ?> d-print-none"
       id="sfSidebar" aria-label="Menu utama">

    <div class="sf-sidebar-head">
        <a href="<?= F::e($sbHome) ?>" class="sf-brand">
            <img src="/assets/images/logo.jpeg" alt="" class="sf-logo">
            <span><?= F::e($sbBrand) ?></span>
        </a>

        <button type="button" class="sf-toggle" data-sf-toggle
                aria-controls="sfSidebar" aria-expanded="true"
                aria-label="Tutup sidebar" title="Tutup sidebar">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>
    </div>

    <div class="sf-sidebar-body">

        <div class="sf-section">Menu</div>

        <ul class="sf-nav">
            <?php foreach ($sbMenu as $m): ?>
                <li>
                    <a href="<?= F::e($m['href']) ?>"
                       class="sf-link <?= $aktif($m) ? 'active' : '' ?>"
                       <?= $aktif($m) ? 'aria-current="page"' : '' ?>>
                        <i class="bi <?= F::e($m['icon']) ?>"></i>
                        <span><?= F::e($m['label']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="sf-user">
            <div class="sf-user-card">
                <i class="bi bi-person-circle"></i>
                <div class="sf-user-info">
                    <div class="sf-user-name text-truncate">
                        <?= F::e(Session::get('nama', '')) ?>
                    </div>
                    <div class="sf-user-role"><?= F::e($peran) ?></div>
                </div>
            </div>

            <form action="/logout" method="POST" class="m-0">
                <?= CsrfHelper::field() ?>
                <button type="submit" class="btn btn-sm btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>

    </div>
</aside>

<div class="sf-backdrop d-print-none" data-sf-backdrop></div>