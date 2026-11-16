<?php
use App\Helpers\FormatHelper as F;

$sbBrand = 'SIMASIF';
$sbHome  = '/pegawai/dashboard';
$sbTema  = 'pegawai';
$sbMenu  = [
    ['label' => 'Dashboard', 'href' => '/pegawai/dashboard', 'icon' => 'bi-speedometer2',   'match' => '/pegawai/dashboard', 'exact' => true],
    ['label' => 'Layanan',   'href' => '/pegawai/layanan',   'icon' => 'bi-camera',         'match' => '/pegawai/layanan'],
    ['label' => 'Pelanggan', 'href' => '/pegawai/pelanggan', 'icon' => 'bi-people',         'match' => '/pegawai/pelanggan'],
    ['label' => 'Pemesanan', 'href' => '/pegawai/pemesanan', 'icon' => 'bi-calendar-check', 'match' => '/pegawai/pemesanan'],
    ['label' => 'Jadwal',    'href' => '/pegawai/jadwal',    'icon' => 'bi-calendar3',      'match' => '/pegawai/jadwal'],
    ['label' => 'Workflow',  'href' => '/pegawai/workflow',  'icon' => 'bi-kanban',         'match' => '/pegawai/workflow'],
    ['label' => 'Transaksi', 'href' => '/pegawai/transaksi', 'icon' => 'bi-wallet2',        'match' => '/pegawai/transaksi'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= F::e($title ?? 'Pegawai') ?> - SIMASIF</title>
    <link rel="icon" href="/assets/images/logo.jpeg">

    <!-- Terapkan keadaan sidebar sebelum halaman digambar (anti kedip) -->
    <script>
        (function () {
            var tutup = true;
            try {
                if (window.innerWidth >= 992) {
                    tutup = localStorage.getItem('sf_sidebar_collapsed') === '1';
                }
            } catch (e) {}
            if (tutup) document.documentElement.classList.add('sf-collapsed');
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/sidebar.css" rel="stylesheet">
    <?php foreach (($styles ?? []) as $css): ?>
        <link href="<?= F::e($css) ?>" rel="stylesheet">
    <?php endforeach; ?>
</head>
<body class="bg-light">

<div class="sf-shell">

    <?php require dirname(__DIR__) . '/components/sidebar.php'; ?>

    <div class="sf-main">

        <?php require dirname(__DIR__) . '/components/sidebar-topbar.php'; ?>

        <main class="container-fluid py-4">
            <?php require dirname(__DIR__) . '/components/alert.php'; ?>
            <?= $content ?? '' ?>
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/modules/sidebar.js"></script>
<?php foreach (($scripts ?? []) as $js): ?>
    <script src="<?= F::e($js) ?>"></script>
<?php endforeach; ?>
</body>
</html>