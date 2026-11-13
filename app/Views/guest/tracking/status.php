<?php
use App\Helpers\FormatHelper as F;
use App\Helpers\CsrfHelper;

/** @var array $pemesanan */
/** @var array $tahapan */
/** @var int   $persen */
/** @var array $riwayat */
/** @var array $referensi */
/** @var array $hasil */
/** @var bool  $bolehUpload */

$scripts = [
    '/assets/js/modules/tracking.js',
    '/assets/js/modules/upload.js',
];

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

                <!-- Pembayaran -->
        <?php $sb = $pemesanan['status_pembayaran'] ?? null; ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Pembayaran</div>
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="badge text-bg-<?= F::e(F::statusPembayaranBadge($sb)) ?>">
                    <?= F::e(F::statusPembayaran($sb)) ?>
                </span>

                <?php if ($sb === null || $sb === 'ditolak'): ?>
                    <a href="/pembayaran?kode=<?= F::e(urlencode($pemesanan['kode_unik'])) ?>"
                       class="btn btn-dark btn-sm">
                        <i class="bi bi-wallet2"></i>
                        <?= $sb === 'ditolak' ? 'Kirim Ulang Bukti' : 'Bayar Sekarang' ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

                <!-- File pendukung dari pelanggan -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">File Pendukung (untuk editing)</div>
            <div class="card-body">

                <?php if (empty($referensi)): ?>
                    <p class="text-muted small">Belum ada file yang Anda unggah.</p>
                <?php else: ?>
                    <ul class="list-group mb-3">
                        <?php foreach ($referensi as $f): ?>
                            <li class="list-group-item d-flex justify-content-between small">
                                <span class="text-truncate me-2"><?= F::e($f['nama_file_asli']) ?></span>
                                <span class="text-muted text-nowrap"><?= F::e(F::ukuran($f['ukuran_file'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($bolehUpload): ?>
                    <form action="/file/upload?kode=<?= F::e(urlencode($pemesanan['kode_unik'])) ?>"
                          method="POST" enctype="multipart/form-data">
                        <?= CsrfHelper::field() ?>

                        <input type="file" name="berkas" class="form-control"
                               accept="image/jpeg,image/png,image/webp,application/zip"
                               data-upload-input data-maks-byte="<?= 10 * 1024 * 1024 ?>" required>
                        <div class="form-text">JPG, PNG, WebP, atau ZIP. Maksimal 10 MB per file.</div>
                        <div class="mt-2" data-upload-preview></div>

                        <button type="submit" class="btn btn-dark btn-sm mt-2">
                            <i class="bi bi-upload"></i> Unggah File
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-muted small mb-0">Pengunggahan file sudah ditutup karena hasil foto sudah siap.</p>
                <?php endif; ?>

            </div>
        </div>

        <!-- Hasil foto -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Hasil Foto</div>
            <div class="card-body">
                <?php if (!empty($hasil)): ?>
                    <ul class="list-group">
                        <?php foreach ($hasil as $f): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                                <div style="min-width:0">
                                    <div class="text-truncate"><?= F::e($f['nama_file_asli']) ?></div>
                                    <div class="small text-muted"><?= F::e(F::ukuran($f['ukuran_file'])) ?></div>
                                </div>
                                <a href="/file/download?kode=<?= F::e(urlencode($pemesanan['kode_unik'])) ?>&id=<?= (int) $f['id'] ?>"
                                   class="btn btn-sm btn-dark text-nowrap">
                                    <i class="bi bi-download"></i> Unduh
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif ($hasilSiap): ?>
                    <p class="mb-0 text-muted">
                        <i class="bi bi-hourglass-split"></i>
                        Hasil foto sudah siap, file sedang disiapkan oleh studio.
                    </p>
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