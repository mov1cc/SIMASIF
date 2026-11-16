<?php
use App\Helpers\FormatHelper as F;

/** @var array $pemesanan */
/** @var array $addons */

$title   = 'Pemesanan Berhasil';
$scripts = ['/assets/js/modules/booking.js']; // handler tombol "Salin kode"

$kode    = $pemesanan['kode_unik'];
$tanggal = date('d/m/Y', strtotime($pemesanan['tanggal_jadwal']));
$jam     = substr((string) $pemesanan['jam_jadwal'], 0, 5);

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-6">

        <div class="card shadow-sm">
            <div class="card-body p-4">

                <div class="text-center mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i>
                    <h1 class="h4 fw-bold mt-2 mb-1">Pemesanan Berhasil</h1>
                    <p class="text-muted small mb-0">
                        Simpan kode di bawah ini untuk memantau status, mengunggah file,
                        dan mengunduh hasil foto.
                    </p>
                </div>

                <!-- Kode unik -->
                <div class="border rounded p-3 mb-4 text-center bg-light">
                    <div class="text-muted small">Kode Pemesanan</div>
                    <div class="fs-3 fw-bold font-monospace" id="kodeUnik"><?= F::e($kode) ?></div>
                    <button type="button" class="btn btn-sm btn-outline-dark mt-2"
                            data-copy-target="#kodeUnik">
                        <i class="bi bi-clipboard"></i> <span>Salin kode</span>
                    </button>
                </div>

                <!-- Ringkasan -->
                <dl class="row small mb-4">

                    <dt class="col-5 text-muted fw-normal">Nama</dt>
                    <dd class="col-7"><?= F::e($pemesanan['pelanggan_nama']) ?></dd>

                    <dt class="col-5 text-muted fw-normal">Nomor HP</dt>
                    <dd class="col-7"><?= F::e($pemesanan['pelanggan_no_hp']) ?></dd>

                    <dt class="col-5 text-muted fw-normal">Layanan</dt>
                    <dd class="col-7"><?= F::e($pemesanan['layanan_nama']) ?></dd>

                    <?php foreach ($addons as $a): ?>
                        <dt class="col-5 text-muted fw-normal">Add-on</dt>
                        <dd class="col-7">
                            <?= F::e($a['nama_saat_pesan']) ?>
                            <?= (int) $a['jumlah'] > 1 ? '× ' . (int) $a['jumlah'] : '' ?>
                            (<?= F::e(F::rupiah($a['subtotal'])) ?>)
                        </dd>
                    <?php endforeach; ?>

                    <dt class="col-5 text-muted fw-normal">Jadwal</dt>
                    <dd class="col-7"><?= F::e($tanggal) ?>, <?= F::e($jam) ?> WIB</dd>

                    <dt class="col-5 text-muted fw-normal">Estimasi durasi</dt>
                    <dd class="col-7"><?= F::e(F::durasi($pemesanan['durasi_total_menit'])) ?></dd>

                    <?php if (!empty($pemesanan['catatan_khusus'])): ?>
                        <dt class="col-5 text-muted fw-normal">Catatan</dt>
                        <dd class="col-7"><?= F::e($pemesanan['catatan_khusus']) ?></dd>
                    <?php endif; ?>

                    <dt class="col-5 text-muted fw-normal">Total Tagihan</dt>
                    <dd class="col-7 fw-semibold"><?= F::e(F::rupiah($pemesanan['total_tagihan'])) ?></dd>

                </dl>

                <a href="/pembayaran?kode=<?= F::e(urlencode($kode)) ?>" class="btn btn-dark w-100">
                    <i class="bi bi-wallet2"></i> Lanjut ke Pembayaran
                </a>

                <div class="text-center mt-3">
                    <a href="/tracking?kode=<?= F::e(urlencode($kode)) ?>" class="text-muted small">
                        Lihat status pesanan
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/guest.php';