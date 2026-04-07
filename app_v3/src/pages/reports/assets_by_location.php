<?php
require_auth();
require_once SRC . '/queries/reports.php';

$data       = get_assets_by_location($conn);
$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Assets by Location';
$total_val  = array_sum(array_column($data, 'total_value'));
$total_cnt  = array_sum(array_column($data, 'asset_count'));

$content = function() use ($data, $total_val, $total_cnt, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Assets by Location</h4>
        <small class="text-muted"><?= number_format($total_cnt) ?> assets, GH₵ <?= number_format($total_val, 2) ?> total</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= $base_url ?>/exports/assets?report=by-location&format=csv"
           class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer"></i>
        </button>
    </div>
</div>

<div class="table-responsive">
<table class="table table-hover table-sm align-middle">
    <thead class="table-dark">
        <tr>
            <th>Location</th>
            <th class="text-center">No. of Assets</th>
            <th class="text-end">Total Value (GHS)</th>
            <th class="text-end">% of Total</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $r): ?>
    <tr>
        <td><?= esc($r['location']) ?></td>
        <td class="text-center"><?= number_format((int)$r['asset_count']) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_value'], 2) ?></td>
        <td class="text-end">
            <?= $total_val > 0 ? number_format((float)$r['total_value'] / $total_val * 100, 1) : 0 ?>%
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot class="table-secondary fw-bold">
        <tr>
            <td>TOTAL</td>
            <td class="text-center"><?= number_format($total_cnt) ?></td>
            <td class="text-end"><?= number_format($total_val, 2) ?></td>
            <td class="text-end">100%</td>
        </tr>
    </tfoot>
</table>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
