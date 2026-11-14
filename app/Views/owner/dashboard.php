<?php
use App\Helpers\FormatHelper as F;

/** @var string $periode */
/** @var array<string,string> $daftarPeriode */
/** @var string $dari */
/** @var string $sampai */
/** @var array $stat */
/** @var array $populer */
/** @var array $aktivitas */

$title = 'Dashboard Owner';

// Chart.js dimuat dulu, baru dashboard.js
$scripts = [
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js',
    '/assets/js/modules/dashboard.js',
];

$kartu = [
    [
        'label' => 'Jumlah Pemesanan',
        'value' => $stat['pemesanan'],
        'icon'  => 'bi-calendar-check',
        'sub'   => 'Dibuat pada periode ini',
        'tone'  => 'dark',
    ],
    [
        'label' => 'Jumlah Pelanggan',
        'value' => $stat['pelanggan_total'],
        'icon'  => 'bi-people',
        'sub'   => '+' . $stat['pelanggan_baru'] . ' pelanggan baru pada periode ini',
        'tone'  => 'primary',
    ],
    [
        'label' => 'Pendapatan',
        'value' => F::rupiah($stat['pendapatan']),
        'icon'  => 'bi-cash-stack',
        'sub'   => 'Pembayaran lunas terverifikasi',
        'tone'  => 'success',
    ],
    [
        'label' => 'Booking Aktif',
        'value' => $stat['booking_aktif'],
        'icon'  => 'bi-hourglass-split',
        'sub'   => 'Belum berstatus selesai (saat ini)',
        'tone'  => 'warning',
    ],
];

ob_start();
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h3 class="mb-0">Dashboard Owner</h3>
        <p class="text-muted small mb-0">
            Periode: <?= F::e(F::tanggalIndo($dari)) ?> – <?= F::e(F::tanggalIndo($sampai)) ?>
        </p>
    </div>

    <div class="btn-group" role="group" aria-label="Pilih periode">
        <?php foreach ($daftarPeriode as $kode => $label): ?>
            <a href="/owner/dashboard?periode=<?= F::e((string) $kode) ?>"
               class="btn btn-sm <?= (string) $kode === $periode ? 'btn-dark' : 'btn-outline-dark' ?>">
                <?= F::e($label) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Kartu statistik -->
<div class="row g-3 mb-4">
    <?php foreach ($kartu as $card): ?>
        <div class="col-sm-6 col-xl-3">
            <?php require dirname(__DIR__) . '/components/card.php'; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Grafik -->
<div class="row g-3 mb-4" data-dashboard-owner data-periode="<?= F::e($periode) ?>">

    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Pendapatan per Hari</div>
            <div class="card-body">
                <div id="pendapatanWrap" style="position:relative;height:300px">
                    <canvas id="chartPendapatan"></canvas>
                </div>
                <div id="pendapatanKosong" class="text-center text-muted py-5 d-none">
                    Belum ada pembayaran lunas pada periode ini.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Layanan Terpopuler</div>
            <div class="card-body">
                <div id="populerWrap" style="position:relative;height:300px">
                    <canvas id="chartPopuler"></canvas>
                </div>
                <div id="populerKosong" class="text-center text-muted py-5 d-none">
                    Belum ada pemesanan pada periode ini.
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-3">

    <!-- Tabel layanan terpopuler -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Peringkat Layanan</div>

            <?php if (empty($populer)): ?>
                <div class="card-body text-center text-muted py-4">
                    Belum ada pemesanan pada periode ini.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Layanan</th>
                                <th class="text-center">Pemesanan</th>
                                <th class="text-end">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($populer as $i => $r): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="fw-semibold"><?= F::e($r['nama']) ?></td>
                                    <td class="text-center"><?= (int) $r['sesi'] ?></td>
                                    <td class="text-end text-nowrap"><?= F::e(F::rupiah($r['omzet'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Aktivitas -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Aktivitas Operasional Terbaru</div>

            <?php if (empty($aktivitas)): ?>
                <div class="card-body text-center text-muted py-4">Belum ada aktivitas.</div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($aktivitas as $a): ?>
                        <li class="list-group-item small">
                            <span class="font-monospace fw-semibold"><?= F::e($a['kode_unik']) ?></span>
                            (<?= F::e($a['pelanggan_nama']) ?>)
                            → <strong><?= F::e(F::statusPemesanan($a['status'])) ?></strong>
                            <div class="text-muted">
                                <?= F::e($a['diubah_oleh_nama'] ?? 'Pelanggan / sistem') ?> ·
                                <?= F::e(date('d/m/Y H:i', strtotime($a['created_at']))) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__) . '/layouts/owner.php';