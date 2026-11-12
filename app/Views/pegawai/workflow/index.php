<?php
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;
use App\Services\WorkflowService;

/** @var array<string, array> $kolom */

$title = 'Workflow Layanan';

ob_start();
?>

<div class="mb-3">
    <h3 class="mb-0">Workflow Layanan</h3>
    <p class="text-muted small mb-0">
        Pantau tahap setiap pemesanan. Status hanya bisa maju satu tahap berurutan.
    </p>
</div>

<div class="d-flex gap-3 pb-3" style="overflow-x:auto;align-items:flex-start">

    <?php foreach ($kolom as $kode => $items): ?>
        <?php $berikutnya = WorkflowService::statusBerikutnya($kode); ?>

        <div class="flex-shrink-0" style="width:280px">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-bg-<?= F::e(F::statusPemesananBadge($kode)) ?>">
                    <?= F::e(F::statusPemesanan($kode)) ?>
                </span>
                <span class="text-muted small"><?= count($items) ?></span>
            </div>

            <div class="d-grid gap-2">

                <?php if (empty($items)): ?>
                    <div class="border rounded p-3 text-center text-muted small bg-white">
                        Kosong
                    </div>
                <?php endif; ?>

                <?php foreach ($items as $row): ?>
                    <div class="card shadow-sm">
                        <div class="card-body p-3">

                            <div class="d-flex justify-content-between align-items-start">
                                <a href="/pegawai/pemesanan/detail?id=<?= (int) $row['id'] ?>"
                                   class="font-monospace fw-semibold text-decoration-none">
                                    <?= F::e($row['kode_unik']) ?>
                                </a>
                                <span class="badge text-bg-<?= F::e(F::statusPembayaranBadge($row['status_pembayaran'])) ?>">
                                    <?= F::e(F::statusPembayaran($row['status_pembayaran'])) ?>
                                </span>
                            </div>

                            <div class="mt-1"><?= F::e($row['pelanggan_nama']) ?></div>
                            <div class="small text-muted"><?= F::e($row['layanan_nama']) ?></div>
                            <div class="small text-muted mb-2">
                                <i class="bi bi-calendar3"></i>
                                <?= F::e(date('d/m/Y', strtotime($row['tanggal_jadwal']))) ?>,
                                <?= F::e(F::jam($row['jam_jadwal'])) ?>
                            </div>

                            <?php if ($berikutnya !== null): ?>
                                <form action="/pegawai/workflow/update" method="POST" class="m-0"
                                      onsubmit="return confirm('Ubah status ke <?= F::e(F::statusPemesanan($berikutnya)) ?>?');">
                                    <?= CsrfHelper::field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <input type="hidden" name="status" value="<?= F::e($berikutnya) ?>">
                                    <input type="hidden" name="kembali" value="board">
                                    <button type="submit" class="btn btn-sm btn-dark w-100">
                                        Lanjut: <?= F::e(F::statusPemesanan($berikutnya)) ?>
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="small text-success">
                                    <i class="bi bi-check-circle-fill"></i> Layanan selesai
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

    <?php endforeach; ?>

</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/pegawai.php';