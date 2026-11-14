<?php
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

/** @var array $items */

$title = 'Kelola Pegawai';

ob_start();
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h3 class="mb-0">Kelola Pegawai</h3>
        <p class="text-muted small mb-0">
            Akun pegawai yang dapat login ke sistem. Akun nonaktif langsung tidak bisa mengakses sistem.
        </p>
    </div>
    <a href="/owner/users/create" class="btn btn-dark">
        <i class="bi bi-person-plus"></i> Tambah Pegawai
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No Telepon</th>
                    <th>Status</th>
                    <th>Login Terakhir</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada akun pegawai.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($items as $u): ?>
                    <?php $aktif = F::bool($u['is_active']); ?>
                    <tr class="<?= $aktif ? '' : 'text-muted' ?>">
                        <td class="fw-semibold"><?= F::e($u['nama']) ?></td>
                        <td><?= F::e($u['email']) ?></td>
                        <td><?= F::e($u['no_telepon'] ?? '-') ?></td>
                        <td>
                            <span class="badge text-bg-<?= $aktif ? 'success' : 'secondary' ?>">
                                <?= $aktif ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="small text-nowrap">
                            <?= $u['last_login_at']
                                ? F::e(date('d/m/Y H:i', strtotime($u['last_login_at'])))
                                : 'Belum pernah' ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="/owner/users/edit?id=<?= (int) $u['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <form action="/owner/users/toggle" method="POST" class="m-0"
                                      onsubmit="return confirm('<?= $aktif ? 'Nonaktifkan' : 'Aktifkan' ?> akun ini?');">
                                    <?= CsrfHelper::field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                    <input type="hidden" name="aktif" value="<?= $aktif ? '0' : '1' ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-<?= $aktif ? 'warning' : 'success' ?>">
                                        <?= $aktif ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>

                                <form action="/owner/users/delete" method="POST" class="m-0"
                                      onsubmit="return confirm('Hapus akun ini? Riwayat aktivitasnya tetap tersimpan tanpa nama pelaku. Menonaktifkan lebih disarankan.');">
                                    <?= CsrfHelper::field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/owner.php';