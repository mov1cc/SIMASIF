<?php
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

/** @var array $pemesanan */
/** @var array|null $transaksi */
/** @var array $rekening */

$old = Session::flash('old') ?? [];

$title       = 'Pembayaran ' . $pemesanan['kode_unik'];
$statusBayar = $transaksi['status_pembayaran'] ?? null;
$bolehKirim  = $statusBayar === null || $statusBayar === 'ditolak';
$metode      = $old['metode'] ?? 'transfer';

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-6">

        <div class="card shadow-sm mb-3">
            <div class="card-body p-4">

                <div class="text-muted small">Kode Pemesanan</div>
                <h1 class="h4 font-monospace fw-bold mb-3"><?= F::e($pemesanan['kode_unik']) ?></h1>

                <dl class="row small mb-0">
                    <dt class="col-5 text-muted fw-normal">Layanan</dt>
                    <dd class="col-7"><?= F::e($pemesanan['layanan_nama']) ?></dd>

                    <dt class="col-5 text-muted fw-normal">Jadwal</dt>
                    <dd class="col-7">
                        <?= F::e(F::tanggalIndo($pemesanan['tanggal_jadwal'])) ?>,
                        <?= F::e(F::jam($pemesanan['jam_jadwal'])) ?> WIB
                    </dd>

                    <dt class="col-5 text-muted fw-normal">Total Tagihan</dt>
                    <dd class="col-7 fw-bold mb-0"><?= F::e(F::rupiah($pemesanan['total_tagihan'])) ?></dd>
                </dl>

            </div>
        </div>

        <?php if ($statusBayar === 'lunas'): ?>

            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                Pembayaran Anda sudah <strong>lunas</strong>. Terima kasih!
            </div>

        <?php elseif ($statusBayar === 'menunggu_verifikasi'): ?>

            <div class="alert alert-warning">
                <i class="bi bi-hourglass-split"></i>
                Pembayaran Anda <strong>sedang menunggu verifikasi</strong> dari studio.
                Silakan cek status secara berkala.
            </div>

        <?php else: ?>

            <?php if ($statusBayar === 'ditolak'): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle-fill"></i>
                    Pembayaran sebelumnya <strong>ditolak</strong>. Silakan kirim ulang bukti pembayaran
                    atau hubungi studio melalui WhatsApp.
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h2 class="h5 fw-bold mb-3">Pilih Metode Pembayaran</h2>

                    <form action="/pembayaran/upload?kode=<?= F::e(urlencode($pemesanan['kode_unik'])) ?>"
                          method="POST" enctype="multipart/form-data" novalidate>
                        <?= CsrfHelper::field() ?>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="metode" id="m_transfer"
                                   value="transfer" <?= $metode === 'transfer' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="m_transfer">Transfer bank</label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="metode" id="m_langsung"
                                   value="langsung" <?= $metode === 'langsung' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="m_langsung">Bayar langsung di studio</label>
                        </div>

                        <!-- Panel transfer -->
                        <div id="panelTransfer">

                            <div class="border rounded p-3 mb-3 bg-light small">
                                <div class="text-muted">Transfer ke:</div>
                                <div class="fw-bold fs-5"><?= F::e($rekening['bank']) ?> — <?= F::e($rekening['nomor']) ?></div>
                                <div>a.n. <?= F::e($rekening['atas_nama']) ?></div>
                                <div class="mt-2">
                                    Nominal: <strong><?= F::e(F::rupiah($pemesanan['total_tagihan'])) ?></strong>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="bukti" class="form-label">Bukti transfer</label>
                                <input type="file" id="bukti" name="bukti" class="form-control"
                                       accept="image/jpeg,image/png,image/webp,application/pdf">
                                <div class="form-text">JPG, PNG, WebP, atau PDF. Maksimal 5 MB.</div>
                            </div>

                            <div class="mb-3">
                                <label for="nomor_referensi" class="form-label">
                                    Nomor referensi <span class="text-muted">(opsional)</span>
                                </label>
                                <input type="text" id="nomor_referensi" name="nomor_referensi" maxlength="100"
                                       class="form-control" value="<?= F::e($old['nomor_referensi'] ?? '') ?>">
                            </div>

                        </div>

                        <!-- Panel langsung -->
                        <div id="panelLangsung" class="alert alert-info small d-none">
                            Silakan bayar sebesar <strong><?= F::e(F::rupiah($pemesanan['total_tagihan'])) ?></strong>
                            langsung di studio. Pegawai akan memverifikasi pembayaran Anda.
                        </div>

                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-send"></i> Kirim Pembayaran
                        </button>
                    </form>

                </div>
            </div>

        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="/tracking?kode=<?= F::e(urlencode($pemesanan['kode_unik'])) ?>" class="text-muted small">
                Kembali ke status pesanan
            </a>
        </div>

    </div>
</div>

<script>
(function () {
    const radios = document.querySelectorAll('input[name="metode"]');
    const panelTransfer = document.getElementById('panelTransfer');
    const panelLangsung = document.getElementById('panelLangsung');
    if (!panelTransfer || !panelLangsung) return;

    function sync() {
        const transfer = document.getElementById('m_transfer').checked;
        panelTransfer.classList.toggle('d-none', !transfer);
        panelLangsung.classList.toggle('d-none', transfer);
    }

    radios.forEach(function (r) { r.addEventListener('change', sync); });
    sync();
})();
</script>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/guest.php';