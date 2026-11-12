<?php
use App\Helpers\FormatHelper as F;

$title = 'Lacak Pesanan';

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">

        <div class="card shadow-sm">
            <div class="card-body p-4">

                <h1 class="h4 fw-bold mb-1">Lacak Pesanan</h1>
                <p class="text-muted small mb-4">
                    Masukkan kode pemesanan Anda (contoh: SF-ABC234) untuk melihat status layanan.
                </p>

                <form action="/tracking" method="GET" novalidate>
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Pemesanan</label>
                        <input type="text" id="kode" name="kode" maxlength="20"
                               class="form-control font-monospace text-uppercase"
                               placeholder="SF-XXXXXX" autocomplete="off" autofocus required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-search"></i> Cek Status
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/guest.php';