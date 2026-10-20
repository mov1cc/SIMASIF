<?php $user = auth_user(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? config('app.name')) ?> | <?= e(config('app.name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand bg-dark navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= e(url('/')) ?>"><?= e(config('app.name')) ?></a>
        <?php if ($user): ?>
            <form method="post" action="<?= e(url('/logout')) ?>" class="d-flex align-items-center gap-2 m-0">
                <?= csrf_field() ?>
                <span class="navbar-text"><?= e($user[config('auth.role')] ?? '') ?></span>
                <button class="btn btn-outline-light btn-sm" type="submit">Keluar</button>
            </form>
        <?php else: ?>
            <a class="btn btn-outline-light btn-sm" href="<?= e(url('/login')) ?>">Login</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container py-4">
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert alert-danger"><?= e($message) ?></div>
    <?php endif; ?>

    <?= $content ?>
</main>
</body>
</html>