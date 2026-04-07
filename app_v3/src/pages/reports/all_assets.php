<?php
require_auth();
require_once SRC . '/queries/reports.php';
require_once SRC . '/queries/asset_classes.php';

$class_filter = get('class');
$data         = get_all_active_assets($conn, $class_filter);
$classes      = get_all_asset_classes($conn);
$per_page     = 50;
$page         = max(1, get_int('page', 1));
$total        = count($data);
$total_pages  = (int)ceil($total / $per_page);
$paged        = array_slice($data, ($page - 1) * $per_page, $per_page);
$base_url     = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title   = 'All Active Assets';

$content = function() use ($paged, $total, $total_pages, $page, $classes, $class_filter, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">All Active Assets</h4>
        <small class="text-muted"><?= number_format($total) ?> assets</small>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="get" class="d-flex gap-2">
            <select name="class" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Classes</option>
                <?php foreach ($classes as $c): ?>
                <option value="<?= esc($c['asset_class']) ?>"
                    <?= $class_filter === $c['asset_class'] ? 'selected' : '' ?>>
                    <?= esc($c['asset_class']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="<?= $base_url ?>/exports/assets?report=all&class=<?= urlencode($class_filter) ?>&format=csv"
           class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
        <a href="<?= $base_url ?>/exports/pdf?report=all&class=<?= urlencode($class_filter) ?>"
           class="btn btn-outline-danger btn-sm"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle small">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Asset Name</th>
            <th>ID No.</th>
            <th>Class</th>
            <th>Sub-Class</th>
            <th>Location</th>
            <th>User</th>
            <th>Acq. Date</th>
            <th class="text-end">Additions (GHS)</th>
            <th class="text-end">USD</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($paged): ?>
        <?php $offset = ($page - 1) * 50; ?>
        <?php foreach ($paged as $i => $r):
            $usd = $r['dollar_rate_used'] > 0 ? (float)$r['additions'] / (float)$r['dollar_rate_used'] : 0;
        ?>
        <tr>
            <td class="text-muted"><?= $offset + $i + 1 ?></td>
            <td><?= esc($r['asset_name']) ?></td>
            <td><code class="small"><?= esc($r['id_number']) ?></code></td>
            <td><span class="badge bg-secondary"><?= esc($r['asset_class']) ?></span></td>
            <td class="small text-muted"><?= esc($r['sub_class']) ?></td>
            <td><?= esc($r['location']) ?></td>
            <td class="small"><?= esc($r['user'] ?? '—') ?></td>
            <td class="text-nowrap small"><?= esc($r['acquisition_date']) ?></td>
            <td class="text-end"><?= number_format((float)$r['additions'], 2) ?></td>
            <td class="text-end"><?= number_format($usd, 2) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="10" class="text-center text-muted py-4">No active assets found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<?php if ($total_pages > 1):
    $qs = $class_filter ? 'class=' . urlencode($class_filter) . '&' : ''; ?>
<nav><ul class="pagination pagination-sm flex-wrap">
    <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="?<?= $qs ?>page=<?=$page-1?>">&laquo;</a></li>
    <?php for($p=max(1,$page-3);$p<=min($total_pages,$page+3);$p++): ?>
    <li class="page-item <?= $p===$page?'active':'' ?>"><a class="page-link" href="?<?= $qs ?>page=<?=$p?>"><?=$p?></a></li>
    <?php endfor; ?>
    <li class="page-item <?= $page>=$total_pages?'disabled':'' ?>"><a class="page-link" href="?<?= $qs ?>page=<?=$page+1?>">&raquo;</a></li>
</ul></nav>
<?php endif; ?>
<?php };
require SRC . '/templates/layouts/base.php';
