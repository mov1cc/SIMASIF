<div class="card mx-auto" style="max-width: 420px">
    <div class="card-body">
        <h1 class="h4 mb-3">Login</h1>

        <form method="post" action="<?= e(url('/login')) ?>" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label" for="identifier">Email / Username</label>
                <input type="text" id="identifier" name="identifier"
                       class="form-control <?= error('identifier') ? 'is-invalid' : '' ?>"
                       value="<?= e(old('identifier')) ?>" autocomplete="username" autofocus>
                <div class="invalid-feedback"><?= e(error('identifier')) ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control <?= error('password') ? 'is-invalid' : '' ?>"
                       autocomplete="current-password">
                <div class="invalid-feedback"><?= e(error('password')) ?></div>
            </div>

            <button type="submit" class="btn btn-dark w-100">Masuk</button>
        </form>
    </div>
</div>