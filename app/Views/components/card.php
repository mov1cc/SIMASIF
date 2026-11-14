<?php
use App\Helpers\FormatHelper as F;

/**
 * @var array{label:string, value:string|int, icon?:string, sub?:string,
 *            href?:string, tone?:string} $card
 */

$tone = $card['tone'] ?? 'dark';
$tag  = !empty($card['href']) ? 'a' : 'div';
?>
<<?= $tag ?>
    <?= !empty($card['href']) ? 'href="' . F::e($card['href']) . '"' : '' ?>
    class="card shadow-sm h-100 text-decoration-none text-body">
    <div class="card-body d-flex justify-content-between align-items-start gap-3">
        <div>
            <div class="text-muted small"><?= F::e($card['label']) ?></div>
            <div class="fs-3 fw-bold lh-sm"><?= F::e($card['value']) ?></div>
            <?php if (!empty($card['sub'])): ?>
                <div class="small text-muted mt-1"><?= F::e($card['sub']) ?></div>
            <?php endif; ?>
        </div>
        <?php if (!empty($card['icon'])): ?>
            <span class="rounded-3 text-bg-<?= F::e($tone) ?> d-inline-flex align-items-center justify-content-center"
                  style="width:44px;height:44px;flex:0 0 44px">
                <i class="bi <?= F::e($card['icon']) ?> fs-5"></i>
            </span>
        <?php endif; ?>
    </div>
</<?= $tag ?>>