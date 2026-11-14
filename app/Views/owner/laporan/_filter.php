<?php
use App\Helpers\FormatHelper as F;

/**
 * @var string $tab           'pemesanan' | 'pendapatan'
 * @var string $dari
 * @var string $sampai
 * @var array<string,string>|null $daftarStatus  hanya untuk laporan pemesanan
 * @var string $status
 */

$tab    = $tab ?? 'pemesanan';
$status = $status ?? '';
$qs     = '?dari=' . urlencode($dari) . '&sampai=' . urlencode($sampai);
?>

<ul class="nav nav-tabs mb-3 d-print-none">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'pemesanan' ? 'active' : '' ?>"
           href="/owner/laporan/pemesanan<?= F::e($qs) ?>">Laporan Pemesanan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'pendapatan' ? 'active' : '' ?>"
           href="/owner/laporan/pendapatan<?= F::e($qs) ?>">Laporan Pendapatan</a>
    </li>
</ul>

<form method="GET" action="/owner/laporan/<?= F::e($tab) ?>"
      class="row g-2 align-items-end mb-4 d-print-none">
    <div class="col-6 col-md-3">
        <label for="dari" class="form-label small mb-1">Dari tanggal</label>
        <input type="date" id="dari" name="dari" class="form-control" value="<?= F::e($dari) ?>">
    </div>
    <div class="col-6 col-md-3">
        <label for="sampai" class="form-label small mb-1">Sampai tanggal</label>
        <input type="date" id="sampai" name="sampai" class="form-control" value="<?= F::e($sampai) ?>">
    </div>

    <?php if (!empty($daftarStatus)): ?>
        <div class="col-md-3">
            <label for="status" class="form-label small mb-1">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">Semua status</option>
                <?php foreach ($daftarStatus as $kode => $label): ?>
                    <option value="<?= F::e($kode) ?>" <?= $status === $kode ? 'selected' : '' ?>>
                        <?= F::e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="col-auto d-flex gap-2">
        <button type="submit" class="btn btn-dark">Tampilkan</button>
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak
        </button>
    </div>
</form>