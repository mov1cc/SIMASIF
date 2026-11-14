<?php
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

/** @var array|null $user  null = mode tambah */
/** @var string $action */

$errors = Session::flash('errors') ?? [];
$old    = Session::flash('old') ?? [];

$isEdit = $user !== null;
$title  = $isEdit ? 'Edit Pegawai' : 'Tambah Pegawai';

// Prioritas nilai: input lama (gagal validasi) > data database > kosong
$nilai = fn (string $key) => $old[$key] ?? ($user[$key] ?? '');

if (!empty($old)) {
    $aktif = !empty($old['is_active']);
} elseif ($isEdit) {
    $aktif = F::bool($user['is_active']);
} else {
    $aktif = true;
}

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-6">

        <h3 class="mb-3"><?= F::e($title) ?></h3>

        <div class="card shadow-sm">
            <div class="card-body p-4">

                <form action="<?= F::e($action) ?>" method="POST" novalidate autocomplete="off">
                    <?= CsrfHelper::field() ?>

                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" maxlength="100" autofocus
                               class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                               value="<?= F::e($nilai('nama')) ?>">
                        <?php if (isset($errors['nama'])): ?>
                            <div class="invalid-feedback"><?= F::e($errors['nama']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" maxlength="100"
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               value="<?= F::e($nilai('email')) ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= F::e($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="no_telepon" class="form-label">
                            No Telepon <span class="text-muted">(opsional)</span>
                        </label>
                        <input type="tel" id="no_telepon" name="no_telepon" maxlength="20"
                               class="form-control <?= isset($errors['no_telepon']) ? 'is-invalid' : '' ?>"
                               value="<?= F::e($nilai('no_telepon')) ?>">
                        <?php if (isset($errors['no_telepon'])): ?>
                            <div class="invalid-feedback"><?= F::e($errors['no_telepon']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                            <?php if ($isEdit): ?>
                                <span class="text-muted">(kosongkan jika tidak diganti)</span>
                            <?php endif; ?>
                        </label>
                        <input type="password" id="password" name="password" maxlength="72"
                               autocomplete="new-password"
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= F::e($errors['password']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="is_active" name="is_active" value="1" <?= $aktif ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Akun aktif (boleh login)</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="/owner/users" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/owner.php';