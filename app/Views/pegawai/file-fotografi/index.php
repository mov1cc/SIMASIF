<?php
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

/** @var array $p */
/** @var array $referensi */
/** @var array $hasil */
/** @var int|float $maksMb */

$title = 'File ' . $p['kode_unik'];

$adalahGambar = fn (array $f) => str_starts_with((string) $f['mime_type'], 'image/');

ob_start();
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="/pegawai/pemesanan">Pemesanan</a></li>
        <li class="breadcrumb-item">
            <a href="/pegawai/pemesanan/detail?id=<?= (int) $p['id'] ?>"><?= F::e($p['kode_unik']) ?></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">File Fotografi</li>
    </ol>
</nav>

<div class="mb-3">
    <h3 class="mb-1">File Fotografi</h3>
    <p class="text-muted small mb-0">
        <span class="font-monospace"><?= F::e($p['kode_unik']) ?></span> &middot;
        <?= F::e($p['pelanggan_nama']) ?> &middot;
        <?= F::e($p['layanan_nama']) ?> &middot;
        <span class="badge text-bg-<?= F::e(F::statusPemesananBadge($p['status'])) ?>">
            <?= F::e(F::statusPemesanan($p['status'])) ?>
        </span>
    </p>
</div>

<div class="row g-3">

    <!-- File dari pelanggan -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                File dari Pelanggan (<?= count($referensi) ?>)
            </div>

            <?php if (empty($referensi)): ?>
                <div class="card-body text-center text-muted py-5">
                    Pelanggan belum mengunggah file.
                </div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($referensi as $f): ?>
                        <li class="list-group-item d-flex align-items-center gap-3">
                            <?php if ($adalahGambar($f)): ?>
                                <a href="/pegawai/file/unduh?id=<?= (int) $f['id'] ?>&tampil=1"
                                   target="_blank" rel="noopener">
                                    <img src="/pegawai/file/unduh?id=<?= (int) $f['id'] ?>&tampil=1"
                                         alt="" class="rounded border" loading="lazy"
                                         style="width:56px;height:56px;object-fit:cover">
                                </a>
                            <?php else: ?>
                                <i class="bi bi-file-earmark-zip fs-1 text-muted"></i>
                            <?php endif; ?>

                            <div class="flex-grow-1" style="min-width:0">
                                <div class="text-truncate fw-semibold"><?= F::e($f['nama_file_asli']) ?></div>
                                <div class="small text-muted">
                                    <?= F::e(F::ukuran($f['ukuran_file'])) ?> &middot;
                                    <?= F::e(date('d/m/Y H:i', strtotime($f['created_at']))) ?>
                                </div>
                            </div>

                            <div class="d-flex gap-1">
                                <a href="/pegawai/file/unduh?id=<?= (int) $f['id'] ?>"
                                   class="btn btn-sm btn-outline-dark" title="Unduh">
                                    <i class="bi bi-download"></i>
                                </a>
                                <form action="/pegawai/file/delete" method="POST" class="m-0"
                                      onsubmit="return confirm('Hapus file ini?');">
                                    <?= CsrfHelper::field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hasil foto -->
    <div class="col-lg-6">

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Unggah Hasil Foto</div>
            <div class="card-body">
                <form action="/pegawai/file/upload" method="POST" enctype="multipart/form-data">
                    <?= CsrfHelper::field() ?>
                    <input type="hidden" name="pemesanan_id" value="<?= (int) $p['id'] ?>">

                    <div class="mb-2">
                        <input type="file" name="berkas" class="form-control"
                               accept="image/jpeg,image/png,image/webp,application/zip"
                               data-upload-input
                               data-maks-byte="<?= (int) ($maksMb * 1048576) ?>" required>
                        <div class="form-text">
                            JPG, PNG, WebP, atau ZIP. Maksimal <?= F::e((string) $maksMb) ?> MB per file.
                        </div>
                        <div class="mt-2" data-upload-preview></div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="langsung_tersedia"
                               name="langsung_tersedia" value="1">
                        <label class="form-check-label" for="langsung_tersedia">
                            Langsung tersedia untuk pelanggan
                        </label>
                    </div>

                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-upload"></i> Unggah
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">
                Hasil Foto (<?= count($hasil) ?>)
            </div>

            <?php if (empty($hasil)): ?>
                <div class="card-body text-center text-muted py-4">Belum ada hasil foto.</div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($hasil as $f): ?>
                        <?php $tersedia = $f['status_file'] === 'tersedia'; ?>
                        <li class="list-group-item d-flex align-items-center gap-3">
                            <?php if ($adalahGambar($f)): ?>
                                <a href="/pegawai/file/unduh?id=<?= (int) $f['id'] ?>&tampil=1"
                                   target="_blank" rel="noopener">
                                    <img src="/pegawai/file/unduh?id=<?= (int) $f['id'] ?>&tampil=1"
                                         alt="" class="rounded border" loading="lazy"
                                         style="width:56px;height:56px;object-fit:cover">
                                </a>
                            <?php else: ?>
                                <i class="bi bi-file-earmark-zip fs-1 text-muted"></i>
                            <?php endif; ?>

                            <div class="flex-grow-1" style="min-width:0">
                                <div class="text-truncate fw-semibold"><?= F::e($f['nama_file_asli']) ?></div>
                                <div class="small text-muted">
                                    <?= F::e(F::ukuran($f['ukuran_file'])) ?>
                                    <span class="badge text-bg-<?= $tersedia ? 'success' : 'secondary' ?>">
                                        <?= $tersedia ? 'Tersedia' : 'Belum tersedia' ?>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex gap-1">
                                <form action="/pegawai/file/toggle" method="POST" class="m-0">
                                    <?= CsrfHelper::field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                    <input type="hidden" name="status"
                                           value="<?= $tersedia ? 'belum_tersedia' : 'tersedia' ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                                            title="<?= $tersedia ? 'Sembunyikan' : 'Sediakan untuk pelanggan' ?>">
                                        <i class="bi bi-<?= $tersedia ? 'eye-slash' : 'eye' ?>"></i>
                                    </button>
                                </form>
                                <a href="/pegawai/file/unduh?id=<?= (int) $f['id'] ?>"
                                   class="btn btn-sm btn-outline-dark" title="Unduh">
                                    <i class="bi bi-download"></i>
                                </a>
                                <form action="/pegawai/file/delete" method="POST" class="m-0"
                                      onsubmit="return confirm('Hapus file ini?');">
                                    <?= CsrfHelper::field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php
$scripts = ['/assets/js/modules/upload.js'];
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/pegawai.php';