<?php
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

/** @var array $user */

$errProfil = Session::flash('errors_profil') ?? [];
$oldProfil = Session::flash('old_profil') ?? [];
$errPw     = Session::flash('errors_password') ?? [];

$title = 'Profil Saya';

$nilai = fn (string $key) => $oldProfil[$key] ?? ($user[$key] ?? '');

ob_start();
?>

<div class="mb-3">
    <h3 class="mb-0">Profil Saya</h3>
    <p class="text-muted small mb-0">
        Peran: Owner
        <?php if (!empty($user['last_login_at'])): ?>
            · Login terakhir <?= F::e(date('d/m/Y H:i', strtotime($user['last_login_at']))) ?>
        <?php endif; ?>
    </p>
</div>

<div class="row g-3">

    <!-- Data profil -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Data Profil</div>
            <div class="card-body">

                <form action="/owner/profil/update" method="POST" novalidate>
                    <?= CsrfHelper::field() ?>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" maxlength="100"
                               class="form-control <?= isset($errProfil['nama']) ? 'is-invalid' : '' ?>"
                               value="<?= F::e($nilai('nama')) ?>">
                        <?php if (isset($errProfil['nama'])): ?>
                            <div class="invalid-feedback"><?= F::e($errProfil['nama']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" maxlength="100"
                               class="form-control <?= isset($errProfil['email']) ? 'is-invalid' : '' ?>"
                               value="<?= F::e($nilai('email')) ?>">
                        <?php if (isset($errProfil['email'])): ?>
                            <div class="invalid-feedback"><?= F::e($errProfil['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="no_telepon" class="form-label">
                            No Telepon <span class="text-muted">(opsional)</span>
                        </label>
                        <input type="tel" id="no_telepon" name="no_telepon" maxlength="20"
                               class="form-control <?= isset($errProfil['no_telepon']) ? 'is-invalid' : '' ?>"
                               value="<?= F::e($nilai('no_telepon')) ?>">
                        <?php if (isset($errProfil['no_telepon'])): ?>
                            <div class="invalid-feedback"><?= F::e($errProfil['no_telepon']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="password_saat_ini" class="form-label">
                            Password saat ini <span class="text-muted">(hanya jika mengganti email)</span>
                        </label>
                        <input type="password" id="password_saat_ini" name="password_saat_ini"
                               autocomplete="current-password"
                               class="form-control <?= isset($errProfil['password_saat_ini']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errProfil['password_saat_ini'])): ?>
                            <div class="invalid-feedback"><?= F::e($errProfil['password_saat_ini']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Profil</button>
                </form>

            </div>
        </div>
    </div>

    <!-- Ganti password -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Ganti Password</div>
            <div class="card-body">

                <form action="/password/change" method="POST" novalidate>
                    <?= CsrfHelper::field() ?>

                    <div class="mb-3">
                        <label for="password_lama" class="form-label">Password saat ini</label>
                        <input type="password" id="password_lama" name="password_lama"
                               autocomplete="current-password"
                               class="form-control <?= isset($errPw['password_lama']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errPw['password_lama'])): ?>
                            <div class="invalid-feedback"><?= F::e($errPw['password_lama']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password baru</label>
                        <input type="password" id="password" name="password" autocomplete="new-password"
                               class="form-control <?= isset($errPw['password']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errPw['password'])): ?>
                            <div class="invalid-feedback"><?= F::e($errPw['password']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label">Ulangi password baru</label>
                        <input type="password" id="password_confirm" name="password_confirm"
                               autocomplete="new-password"
                               class="form-control <?= isset($errPw['password_confirm']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errPw['password_confirm'])): ?>
                            <div class="invalid-feedback"><?= F::e($errPw['password_confirm']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-dark">Ganti Password</button>
                </form>

            </div>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__) . '/layouts/owner.php';