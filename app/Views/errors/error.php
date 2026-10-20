<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Error') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="text-center">
        <h1 class="display-3 fw-bold"><?= e($status ?? 500) ?></h1>
        <p class="lead"><?= e($message ?? 'Terjadi kesalahan.') ?></p>
        <a class="btn btn-dark" href="<?= e(url('/')) ?>">Kembali ke Beranda</a>
    </div>
</body>
</html>