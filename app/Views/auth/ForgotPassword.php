<?php
use App\Core\Session;
use App\Helpers\CsrfHelper;
use App\Helpers\FormatHelper as F;

$errors = Session::flash('errors') ?? [];
$old    = Session::flash('old') ?? [];

$title = 'Lupa Password';

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <h4 class="mb-1">Lupa Password</h4>
                <p class="text-muted small mb-4">
                    Masukkan email akun Anda. Kami akan mengirim tautan untuk membuat password baru.
                </p>

                <form action="/forgot-password" method="POST" novalidate>
                    <?= CsrfHelper::field() ?>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" autofocus
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               value="<?= F::e($old['email'] ?? '') ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= F::e($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Kirim Tautan Reset</button>
                </form>

                <div class="text-center mt-3">
                    <a href="/login" class="small text-muted">Kembali ke login</a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__) . '/layouts/guest.php';