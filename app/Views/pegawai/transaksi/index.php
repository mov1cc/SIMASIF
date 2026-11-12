<?php
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

/** @var array $items */
/** @var array{q:string,status:string} $filter */

$title = 'Transaksi Pembayaran';

$adaFilter = $filter['q'] !== '' || $filter['status'] !== '';

$daftarStatus = [
    'menunggu_verifikasi' => 'Menunggu Verifikasi',
    'lunas'               => 'Lunas',
    'ditolak'             => 'Ditolak',
];

$menunggu = count(array_filter($items, fn ($r) => $r['status_pembayaran'] === 'menunggu_verifikasi'));

ob_start();
?>

<div class="mb-3">
    <h3 class="mb-0">Transaksi Pembayaran</h3>
    <p class="text-muted small mb-0">
        Periksa bukti pembayaran lalu verifikasi atau tolak.
        Total: <?= count($items) ?> transaksi, <?= $menunggu ?> menunggu verifikasi.
    </p>
</div>

<form method="GET" action="/pegawai/transaksi" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="search" name="q" class="form-control"
               placeholder="Cari kode, nama, atau nomor HP..."
               value="<?= F::e($filter['q']) ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            <?php foreach ($daftarStatus as $kode => $label): ?>
                <option value="<?= F::e($kode) ?>" <?= $filter['status'] === $kode ? 'selected' : '' ?>>
                    <?= F::e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-secondary">Filter</button>
        <?php if ($adaFilter): ?>
            <a href="/pegawai/transaksi" class="btn btn-link">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Metode</th>
                    <th class="text-end">Jumlah</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <?= $adaFilter
                                ? 'Tidak ada transaksi yang cocok dengan filter.'
                                : 'Belum ada transaksi pembayaran.' ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($items as $row): ?>
                    <?php
                    $adaBukti = !empty($row['bukti_transfer']);
                    $urlBukti = '/pegawai/transaksi/bukti?id=' . (int) $row['transaksi_id'];
                    $isPdf    = $adaBukti && str_ends_with(strtolower($row['bukti_transfer']), '.pdf');
                    $pending  = $row['status_pembayaran'] === 'menunggu_verifikasi';
                    ?>
                    <tr>
                        <td class="font-monospace fw-semibold">
                            <a href="/pegawai/pemesanan/detail?id=<?= (int) $row['pemesanan_id'] ?>"
                               class="text-decoration-none">
                                <?= F::e($row['kode_unik']) ?>
                            </a>
                        </td>
                        <td>
                            <div><?= F::e($row['pelanggan_nama']) ?></div>
                            <div class="text-muted small"><?= F::e($row['pelanggan_no_hp']) ?></div>
                        </td>
                        <td><?= F::e($row['layanan_nama']) ?></td>
                        <td>
                            <?= $row['metode_pembayaran'] === 'transfer' ? 'Transfer' : 'Langsung' ?>
                            <?php if (!empty($row['nomor_referensi'])): ?>
                                <div class="text-muted small">Ref: <?= F::e($row['nomor_referensi']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap"><?= F::e(F::rupiah($row['jumlah_bayar'])) ?></td>
                        <td>
                            <?php if (!$adaBukti): ?>
                                <span class="text-muted small">-</span>
                            <?php elseif ($isPdf): ?>
                                <a href="<?= F::e($urlBukti) ?>" target="_blank" rel="noopener"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            <?php else: ?>
                                <a href="<?= F::e($urlBukti) ?>" target="_blank" rel="noopener">
                                    <img src="<?= F::e($urlBukti) ?>" alt="Bukti transfer"
                                         class="rounded border"
                                         style="width:56px;height:56px;object-fit:cover" loading="lazy">
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge text-bg-<?= F::e(F::statusPembayaranBadge($row['status_pembayaran'])) ?>">
                                <?= F::e(F::statusPembayaran($row['status_pembayaran'])) ?>
                            </span>
                            <?php if (!empty($row['diverifikator_nama'])): ?>
                                <div class="text-muted small">oleh <?= F::e($row['diverifikator_nama']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($pending): ?>
                                <div class="d-inline-flex gap-1">
                                    <form action="/pegawai/transaksi/verify" method="POST" class="m-0"
                                          onsubmit="return confirm('Verifikasi pembayaran ini sebagai LUNAS?');">
                                        <?= CsrfHelper::field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $row['transaksi_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-lg"></i> Verifikasi
                                        </button>
                                    </form>

                                    <form action="/pegawai/transaksi/reject" method="POST" class="m-0"
                                          onsubmit="return confirm('Tolak pembayaran ini?');">
                                        <?= CsrfHelper::field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $row['transaksi_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">
                                    <?= $row['diverifikasi_at']
                                        ? F::e(date('d/m/Y H:i', strtotime($row['diverifikasi_at'])))
                                        : '-' ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/pegawai.php';