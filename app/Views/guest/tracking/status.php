<?php
use App\Helpers\FormatHelper as F;

/** @var array $pemesanan */
/** @var array $tahapan */
/** @var int   $persen */
/** @var array $riwayat */

$title   = 'Status ' . $pemesanan['kode_unik'];
$scripts = ['/assets/js/modules/tracking.js'];

$status      = $pemesanan['status'];
$hasilSiap   = in_array($status, ['hasil_siap', 'selesai'], true);

ob_start();
?>

<div class="row justify-content-center"
     data-tracking
     data-kode="<?= F::e($pemesanan['kode_unik']) ?>"
     data-status="<?= F::e($status) ?>"
     data-pembayaran="<?= F::e($pemesanan['status_pembayaran'] ?? '') ?>">

    <div class="col-lg-8">

        <!-- Ringkasan -->
        <div class="card shadow-sm mb-3">
            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <div class="text-muted small">Kode Pemesanan</div>
                        <h1 class="h3 font-monospace fw-bold mb-0"><?= F::e($pemesanan['kode_unik']) ?></h1>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge text-bg-<?= F::e(F::statusPemesananBadge($status)) ?>">
                            <?= F::e(F::statusPemesanan($status)) ?>
                        </span>
                        <span class="badge text-bg-<?= F::e(F::statusPembayaranBadge($pemesanan['status_pembayaran'])) ?>">
                            Pembayaran: <?= F::e(F::statusPembayaran($pemesanan['status_pembayaran'])) ?>
                        </span>
                    </div>
                </div>

                <dl class="row small mb-0">
                    <dt class="col-sm-3 text-muted fw-normal">Nama</dt>
                    <dd class="col-sm-9"><?= F::e($pemesanan['pelanggan_nama']) ?></dd>

                    <dt class="col-sm-3 text-muted fw-normal">Layanan</dt>
                    <dd class="col-sm-9"><?= F::e($pemesanan['layanan_nama']) ?></dd>

                    <dt class="col-sm-3 text-muted fw-normal">Jadwal</dt>
                    <dd class="col-sm-9">
                        <?= F::e(F::namaHari($pemesanan['tanggal_jadwal'])) ?>,
                        <?= F::e(F::tanggalIndo($pemesanan['tanggal_jadwal'])) ?>,
                        <?= F::e(F::jam($pemesanan['jam_jadwal'])) ?> WIB
                    </dd>

                    <dt class="col-sm-3 text-muted fw-normal">Total Tagihan</dt>
                    <dd class="col-sm-9 mb-0"><?= F::e(F::rupiah($pemesanan['total_tagihan'])) ?></dd>
                </dl>

            </div>
        </div>

        <!-- Progres -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Progres Layanan</div>
            <div class="card-body p-4">

                <div class="progress mb-4" role="progressbar" aria-valuenow="<?= (int) $persen ?>"
                     aria-valuemin="0" aria-valuemax="100" style="height:10px">
                    <div class="progress-bar bg-dark" style="width:<?= (int) $persen ?>%"></div>
                </div>

                <ol class="list-unstyled mb-0">
                    <?php foreach ($tahapan as $t): ?>
                        <?php
                        $ikon = match ($t['state']) {
                            'selesai' => 'bi-check-circle-fill text-success',
                            'aktif'   => 'bi-record-circle-fill text-primary',
                            default   => 'bi-circle text-muted',
                        };
                        ?>
                        <li class="d-flex align-items-center gap-3 py-2 <?= $t['state'] === 'menunggu' ? 'text-muted' : '' ?>">
                            <i class="bi <?= $ikon ?> fs-5"></i>
                            <span class="<?= $t['state'] === 'aktif' ? 'fw-bold' : '' ?>">
                                <?= F::e($t['label']) ?>
                            </span>
                            <?php if ($t['state'] === 'aktif'): ?>
                                <span class="badge text-bg-primary">Sedang berjalan</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>

            </div>
        </div>

        <!-- Hasil foto (tombol unduh ditambahkan di Tahap 11) -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Hasil Foto</div>
            <div class="card-body">
                <?php if ($hasilSiap): ?>
                    <p class="mb-0"><i class="bi bi-check-circle text-success"></i> Hasil foto Anda sudah siap.</p>
                <?php else: ?>
                    <p class="text-muted mb-0">Hasil foto belum tersedia.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Riwayat -->
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Riwayat Status</div>
            <div class="card-body">
                <?php if (empty($riwayat)): ?>
                    <p class="text-muted mb-0">Belum ada riwayat.</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach (array_reverse($riwayat) as $r): ?>
                            <li class="border-start border-2 ps-3 pb-3">
                                <div class="fw-semibold"><?= F::e(F::statusPemesanan($r['status'])) ?></div>
                                <div class="small text-muted">
                                    <?= F::e(date('d/m/Y H:i', strtotime($r['created_at']))) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="/tracking" class="text-muted small">Lacak pesanan lain</a>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/guest.php';