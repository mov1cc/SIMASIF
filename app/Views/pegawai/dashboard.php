<?php
use App\Core\Session;
use App\Helpers\FormatHelper as F;

/** @var array $stat */
/** @var array $terbaru */
/** @var array $jadwal */
/** @var array $menunggu */
/** @var array $aktivitas */

$title = 'Dashboard Pegawai';

$kartu = [
    [
        'label' => 'Sesi Hari Ini',
        'value' => $stat['sesi_hari_ini'],
        'icon'  => 'bi-camera',
        'sub'   => F::tanggalIndo(date('Y-m-d')),
        'href'  => '/pegawai/jadwal',
        'tone'  => 'dark',
    ],
    [
        'label' => 'Pemesanan Baru Hari Ini',
        'value' => $stat['pemesanan_baru'],
        'icon'  => 'bi-inbox',
        'href'  => '/pegawai/pemesanan',
        'tone'  => 'primary',
    ],
    [
        'label' => 'Menunggu Verifikasi',
        'value' => $stat['menunggu_verifikasi'],
        'icon'  => 'bi-hourglass-split',
        'sub'   => 'Transaksi pembayaran',
        'href'  => '/pegawai/transaksi?status=menunggu_verifikasi',
        'tone'  => 'warning',
    ],
    [
        'label' => 'Layanan Berjalan',
        'value' => $stat['berjalan'],
        'icon'  => 'bi-arrow-repeat',
        'sub'   => 'Terjadwal sampai proses editing',
        'href'  => '/pegawai/workflow',
        'tone'  => 'success',
    ],
];

ob_start();
?>

<div class="mb-4">
    <h3 class="mb-0">Dashboard Pegawai</h3>
    <p class="text-muted small mb-0">
        Halo, <?= F::e(Session::get('nama', '')) ?>. Ringkasan operasional studio hari ini.
    </p>
</div>

<!-- Kartu ringkasan -->
<div class="row g-3 mb-4">
    <?php foreach ($kartu as $card): ?>
        <div class="col-sm-6 col-xl-3">
            <?php require dirname(__DIR__) . '/components/card.php'; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">

    <!-- Jadwal hari ini -->
    <div class="col-lg-7">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Jadwal Hari Ini (<?= count($jadwal) ?>)</span>
                <a href="/pegawai/jadwal" class="small">Lihat jadwal</a>
            </div>

            <?php if (empty($jadwal)): ?>
                <div class="card-body text-center text-muted py-4">Tidak ada sesi hari ini.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwal as $s): ?>
                                <tr>
                                    <td class="text-nowrap fw-semibold">
                                        <?= F::e(F::jam($s['jam_jadwal'])) ?> – <?= F::e(F::jam($s['jam_selesai'])) ?>
                                    </td>
                                    <td><?= F::e($s['pelanggan_nama']) ?></td>
                                    <td><?= F::e($s['layanan_nama']) ?></td>
                                    <td>
                                        <span class="badge text-bg-<?= F::e(F::statusPemesananBadge($s['status'])) ?>">
                                            <?= F::e(F::statusPemesanan($s['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/pegawai/pemesanan/detail?id=<?= (int) $s['id'] ?>"
                                           class="btn btn-sm btn-outline-dark">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pemesanan terbaru -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Pemesanan Terbaru</span>
                <a href="/pegawai/pemesanan" class="small">Semua pemesanan</a>
            </div>

            <?php if (empty($terbaru)): ?>
                <div class="card-body text-center text-muted py-4">Belum ada pemesanan masuk.</div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($terbaru as $r): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <div style="min-width:0">
                                <a href="/pegawai/pemesanan/detail?id=<?= (int) $r['id'] ?>"
                                   class="font-monospace fw-semibold text-decoration-none">
                                    <?= F::e($r['kode_unik']) ?>
                                </a>
                                <div class="small text-muted text-truncate">
                                    <?= F::e($r['pelanggan_nama']) ?> · <?= F::e($r['layanan_nama']) ?>
                                    · <?= F::e(date('d/m/Y', strtotime($r['tanggal_jadwal']))) ?>
                                </div>
                            </div>
                            <span class="badge text-bg-<?= F::e(F::statusPemesananBadge($r['status'])) ?>">
                                <?= F::e(F::statusPemesanan($r['status'])) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-5">

        <!-- Transaksi menunggu verifikasi -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Menunggu Verifikasi</span>
                <a href="/pegawai/transaksi?status=menunggu_verifikasi" class="small">Buka transaksi</a>
            </div>

            <?php if (empty($menunggu)): ?>
                <div class="card-body text-center text-muted py-4">
                    Tidak ada pembayaran yang perlu diverifikasi.
                </div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($menunggu as $t): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <a href="/pegawai/pemesanan/detail?id=<?= (int) $t['pemesanan_id'] ?>"
                                   class="font-monospace fw-semibold text-decoration-none">
                                    <?= F::e($t['kode_unik']) ?>
                                </a>
                                <div class="small text-muted">
                                    <?= F::e($t['pelanggan_nama']) ?> ·
                                    <?= $t['metode_pembayaran'] === 'transfer' ? 'Transfer' : 'Langsung' ?>
                                </div>
                            </div>
                            <span class="text-nowrap fw-semibold"><?= F::e(F::rupiah($t['jumlah_bayar'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Aktivitas -->
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Aktivitas Terbaru</div>

            <?php if (empty($aktivitas)): ?>
                <div class="card-body text-center text-muted py-4">Belum ada aktivitas.</div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($aktivitas as $a): ?>
                        <li class="list-group-item small">
                            <a href="/pegawai/pemesanan/detail?id=<?= (int) $a['pemesanan_id'] ?>"
                               class="font-monospace text-decoration-none"><?= F::e($a['kode_unik']) ?></a>
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

require dirname(__DIR__) . '/layouts/pegawai.php';