<?php
use App\Helpers\FormatHelper as F;

/** @var string $dari */
/** @var string $sampai */
/** @var string $status */
/** @var array $rows */
/** @var array $ringkasan */

$title = 'Laporan Pemesanan';
$tab   = 'pemesanan';

$daftarStatus = F::daftarStatusPemesanan();

$kartu = [
    [
        'label' => 'Total Pemesanan',
        'value' => $ringkasan['total'],
        'icon'  => 'bi-calendar-check',
        'sub'   => 'Dibuat pada periode ini',
        'tone'  => 'dark',
    ],
    [
        'label' => 'Total Nilai Tagihan',
        'value' => F::rupiah($ringkasan['nilai']),
        'icon'  => 'bi-receipt',
        'sub'   => 'Belum tentu sudah dibayar',
        'tone'  => 'primary',
    ],
    [
        'label' => 'Layanan Selesai',
        'value' => $ringkasan['per_status']['selesai'] ?? 0,
        'icon'  => 'bi-check-circle',
        'sub'   => 'Berstatus selesai',
        'tone'  => 'success',
    ],
];

ob_start();
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <h3 class="mb-0">Laporan Pemesanan</h3>
        <p class="text-muted small mb-0">
            Periode: <?= F::e(F::tanggalIndo($dari)) ?> – <?= F::e(F::tanggalIndo($sampai)) ?>
            <?= $status !== '' ? ' · Status: ' . F::e(F::statusPemesanan($status)) : '' ?>
        </p>
    </div>
</div>

<?php require __DIR__ . '/_filter.php'; ?>

<div class="row g-3 mb-4">
    <?php foreach ($kartu as $card): ?>
        <div class="col-md-4">
            <?php require dirname(__DIR__, 2) . '/components/card.php'; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Per Status</div>
            <table class="table table-sm align-middle mb-0">
                <tbody>
                    <?php foreach ($daftarStatus as $kode => $label): ?>
                        <?php $n = $ringkasan['per_status'][$kode] ?? 0; ?>
                        <?php if ($n === 0) { continue; } ?>
                        <tr>
                            <td>
                                <span class="badge text-bg-<?= F::e(F::statusPemesananBadge($kode)) ?>">
                                    <?= F::e($label) ?>
                                </span>
                            </td>
                            <td class="text-end fw-semibold"><?= (int) $n ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ringkasan['per_status'])): ?>
                        <tr><td class="text-center text-muted py-3">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Per Layanan</div>
            <table class="table table-sm align-middle mb-0">
                <tbody>
                    <?php foreach ($ringkasan['per_layanan'] as $nama => $n): ?>
                        <tr>
                            <td><?= F::e($nama) ?></td>
                            <td class="text-end fw-semibold"><?= (int) $n ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ringkasan['per_layanan'])): ?>
                        <tr><td class="text-center text-muted py-3">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Rincian (<?= count($rows) ?>)</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Dibuat</th>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Tidak ada pemesanan pada periode ini.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="text-nowrap"><?= F::e(date('d/m/Y', strtotime($r['created_at']))) ?></td>
                        <td class="font-monospace"><?= F::e($r['kode_unik']) ?></td>
                        <td><?= F::e($r['pelanggan_nama']) ?></td>
                        <td><?= F::e($r['layanan_nama']) ?></td>
                        <td class="text-nowrap">
                            <?= F::e(date('d/m/Y', strtotime($r['tanggal_jadwal']))) ?>
                            <?= F::e(F::jam($r['jam_jadwal'])) ?>
                        </td>
                        <td><?= F::e(F::statusPemesanan($r['status'])) ?></td>
                        <td><?= F::e(F::statusPembayaran($r['status_pembayaran'])) ?></td>
                        <td class="text-end text-nowrap"><?= F::e(F::rupiah($r['total_tagihan'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if (!empty($rows)): ?>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="7" class="text-end">Total nilai tagihan</th>
                        <th class="text-end text-nowrap"><?= F::e(F::rupiah($ringkasan['nilai'])) ?></th>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/owner.php';