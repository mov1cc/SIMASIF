<?php
use App\Helpers\FormatHelper as F;

/** @var string $sbBrand */
?>
<header class="sf-topbar d-print-none">
    <button type="button" class="sf-topbar-brand" data-sf-toggle
            aria-controls="sfSidebar" aria-expanded="false"
            aria-label="Buka sidebar" title="Buka sidebar">
        <img src="/assets/images/logo.jpeg" alt="" class="sf-logo">
        <span><?= F::e($sbBrand) ?></span>
    </button>
</header>