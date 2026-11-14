<?php
use App\Helpers\FormatHelper as F;

/** @var array $items */
/** @var array{user_id:int,modul:string,dari:string,sampai:string} $filter */
/** @var string[] $daftarModul */
/** @var array $daftarUser */
/** @var int $total */
/** @var int $halaman */
/** @var int $halamanTotal */

$title = 'Audit Log';

$adaFilter = $filter['user_id'] > 0 || $filter['modul'] !== ''
    || $filter['dari'] !== '' || $filter['sampai'] !== '';

// Link halaman: pertahankan filter, ganti page
$link = function (int $p) use ($filter): string {
    $q = array_filter(
        $filter + ['page' => $p],
        fn ($v) => $v !== '' && $v !== 0 && $v !== null
    );

    return '/owner/audit-log?' . http_build_query($q);
};

$warnaModul = [
    'Auth'       => 'secondary',
    'Workflow'   => 'primary',
    'Pembayaran' => 'success',
    'User'       => 'warning',
    'Profil'     => 'info',
];

ob_start();
?>

<div class="mb-3">
    <h3 class="mb-0">Audit Log</h3>
    <p class="text-muted small mb-0">
        Riwayat aktivitas pengguna internal. Total: <?= (int) $total ?> catatan.
    </p>
</div>

<form method="GET" action="/owner/audit-log" class="row g-2 align-items-end mb-3">
    <div class="col-md-3">
        <label for="user_id" class="form-label small mb-1">Pengguna</label>
        <select id="user_id" name="user_id" class="form-select">
            <option value="">Semua pengguna</option>
            <?php foreach ($daftarUser as $u): ?>
                <option value="<?= (int) $u['id'] ?>" <?= $filter['user_id'] === (int) $u['id'] ? 'selected' : '' ?>>
                    <?= F::e($u['nama']) ?> (<?= F::e($u['role']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <label for="modul" class="form-label small mb-1">Modul</label>
        <select id="modul" name="modul" class="form-select">
            <option value="">Semua modul</option>
            <?php foreach ($daftarModul as $m): ?>
                <option value="<?= F::e($m) ?>" <?= $filter['modul'] === $m ? 'selected' : '' ?>>
                    <?= F::e($m) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label for="dari" class="form-label small mb-1">Dari</label>
        <input type="date" id="dari" name="dari" class="form-control" value="<?= F::e($filter['dari']) ?>">
    </div>
    <div class="col-6 col-md-2">
        <label for="sampai" class="form-label small mb-1">Sampai</label>
        <input type="date" id="sampai" name="sampai" class="form-control" value="<?= F::e($filter['sampai']) ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-secondary">Filter</button>
        <?php if ($adaFilter): ?>
            <a href="/owner/audit-log" class="btn btn-link">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Modul</th>
                    <th>Aktivitas</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <?= $adaFilter ? 'Tidak ada catatan yang cocok dengan filter.' : 'Belum ada aktivitas tercatat.' ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($items as $r): ?>
                    <?php
                    $detail = !empty($r['detail']) ? json_decode((string) $r['detail'], true) : null;
                    ?>
                    <tr>
                        <td class="text-nowrap small">
                            <?= F::e(date('d/m/Y H:i:s', strtotime($r['created_at']))) ?>
                        </td>
                        <td>
                            <?php if ($r['user_nama'] !== null): ?>
                                <div><?= F::e($r['user_nama']) ?></div>
                                <div class="small text-muted"><?= F::e($r['user_role']) ?></div>
                            <?php else: ?>
                                <span class="text-muted small">Tidak diketahui / akun dihapus</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge text-bg-<?= F::e($warnaModul[$r['modul']] ?? 'secondary') ?>">
                                <?= F::e($r['modul']) ?>
                            </span>
                        </td>
                        <td>
                            <?= F::e($r['aktivitas']) ?>
                            <?php if (!empty($detail)): ?>
                                <details class="small mt-1">
                                    <summary class="text-muted">Detail</summary>
                                    <pre class="mb-0 bg-light border rounded p-2"><?= F::e(
                                        json_encode($detail, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                    ) ?></pre>
                                </details>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted"><?= F::e($r['ip_address'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($halamanTotal > 1): ?>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="small text-muted">Halaman <?= (int) $halaman ?> dari <?= (int) $halamanTotal ?></span>
            <div class="btn-group">
                <a class="btn btn-sm btn-outline-secondary <?= $halaman <= 1 ? 'disabled' : '' ?>"
                   href="<?= F::e($link(max(1, $halaman - 1))) ?>">&laquo; Sebelumnya</a>
                <a class="btn btn-sm btn-outline-secondary <?= $halaman >= $halamanTotal ? 'disabled' : '' ?>"
                   href="<?= F::e($link(min($halamanTotal, $halaman + 1))) ?>">Berikutnya &raquo;</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/owner.php';