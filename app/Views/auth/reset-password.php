<?php
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

/** @var string $token */

$errors = Session::flash('errors') ?? [];

$title = 'Reset Password';

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <h4 class="mb-1">Buat Password Baru</h4>
                <p class="text-muted small mb-4">Minimal 8 karakter.</p>

                <form action="/reset-password" method="POST" novalidate>
                    <?= CsrfHelper::field() ?>
                    <input type="hidden" name="token" value="<?= F::e($token) ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" id="password" name="password" autofocus
                               autocomplete="new-password"
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= F::e($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label">Ulangi Password Baru</label>
                        <input type="password" id="password_confirm" name="password_confirm"
                               autocomplete="new-password"
                               class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errors['password_confirm'])): ?>
                            <div class="invalid-feedback"><?= F::e($errors['password_confirm']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Simpan Password</button>
                </form>

            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__) . '/layouts/guest.php';