<?php
use App\Helpers\FormatHelper as F;

/** @var string $dari */
/** @var string $sampai */
/** @var array $rows */
/** @var array $ringkasan */

$title = 'Laporan Pendapatan';
$tab   = 'pendapatan';

$kartu = [
    [
        'label' => 'Total Pendapatan',
        'value' => F::rupiah($ringkasan['total']),
        'icon'  => 'bi-cash-stack',
        'sub'   => 'Pembayaran lunas terverifikasi',
        'tone'  => 'success',
    ],
    [
        'label' => 'Jumlah Transaksi',
        'value' => $ringkasan['jumlah'],
        'icon'  => 'bi-receipt',
        'sub'   => 'Transaksi lunas',
        'tone'  => 'dark',
    ],
    [
        'label' => 'Rata-rata per Transaksi',
        'value' => F::rupiah($ringkasan['rata_rata']),
        'icon'  => 'bi-calculator',
        'tone'  => 'primary',
    ],
];

ob_start();
?>

<div class="mb-3">
    <h3 class="mb-0">Laporan Pendapatan</h3>
    <p class="text-muted small mb-0">
        Periode: <?= F::e(F::tanggalIndo($dari)) ?> – <?= F::e(F::tanggalIndo($sampai)) ?>
        · dihitung dari tanggal verifikasi pembayaran
    </p>
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

    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Per Metode</div>
            <table class="table table-sm align-middle mb-0">
                <tbody>
                    <?php foreach ($ringkasan['per_metode'] as $m => $v): ?>
                        <tr>
                            <td>
                                <?= $m === 'transfer' ? 'Transfer bank' : 'Bayar langsung' ?>
                                <span class="text-muted small">(<?= (int) $v['n'] ?>)</span>
                            </td>
                            <td class="text-end text-nowrap fw-semibold"><?= F::e(F::rupiah($v['total'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ringkasan['per_metode'])): ?>
                        <tr><td class="text-center text-muted py-3">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Per Layanan</div>
            <table class="table table-sm align-middle mb-0">
                <tbody>
                    <?php foreach ($ringkasan['per_layanan'] as $nama => $v): ?>
                        <tr>
                            <td>
                                <?= F::e($nama) ?>
                                <span class="text-muted small">(<?= (int) $v['n'] ?>)</span>
                            </td>
                            <td class="text-end text-nowrap fw-semibold"><?= F::e(F::rupiah($v['total'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ringkasan['per_layanan'])): ?>
                        <tr><td class="text-center text-muted py-3">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Per Hari</div>
            <table class="table table-sm align-middle mb-0">
                <tbody>
                    <?php foreach ($ringkasan['per_hari'] as $tgl => $total): ?>
                        <tr>
                            <td><?= F::e(date('d/m/Y', strtotime($tgl))) ?></td>
                            <td class="text-end text-nowrap fw-semibold"><?= F::e(F::rupiah($total)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ringkasan['per_hari'])): ?>
                        <tr><td class="text-center text-muted py-3">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Rincian Transaksi (<?= count($rows) ?>)</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Diverifikasi</th>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Metode</th>
                    <th>Verifikator</th>
                    <th class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada pembayaran lunas pada periode ini.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="text-nowrap"><?= F::e(date('d/m/Y H:i', strtotime($r['diverifikasi_at']))) ?></td>
                        <td class="font-monospace"><?= F::e($r['kode_unik']) ?></td>
                        <td><?= F::e($r['pelanggan_nama']) ?></td>
                        <td><?= F::e($r['layanan_nama']) ?></td>
                        <td><?= $r['metode_pembayaran'] === 'transfer' ? 'Transfer' : 'Langsung' ?></td>
                        <td><?= F::e($r['diverifikator_nama'] ?? '-') ?></td>
                        <td class="text-end text-nowrap"><?= F::e(F::rupiah($r['jumlah_bayar'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if (!empty($rows)): ?>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6" class="text-end">Total pendapatan</th>
                        <th class="text-end text-nowrap"><?= F::e(F::rupiah($ringkasan['total'])) ?></th>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/owner.php';