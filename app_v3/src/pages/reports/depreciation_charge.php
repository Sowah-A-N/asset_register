<?php
require_auth();
require_once SRC . '/queries/reports.php';

$years      = get_report_years($conn);
$year       = get_int('year', $years[0] ?? (int)date('Y'));
$data       = get_depreciation_charge($conn, $year);
$per_page   = 50;
$page       = max(1, get_int('page', 1));
$total      = count($data);
$total_pages= (int)ceil($total / $per_page);
$paged      = array_slice($data, ($page - 1) * $per_page, $per_page);
$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Depreciation Charge';
$total_annual = array_sum(array_column($data, 'annual_dep'));

$content = function() use ($paged, $total, $total_annual, $total_pages, $page, $years, $year, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Depreciation Charge</h4>
        <small class="text-muted">Year <?= $year ?> — <?= number_format($total) ?> assets</small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form method="get" class="d-flex gap-2">
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="<?= $base_url ?>/exports/assets?report=depreciation&year=<?= $year ?>&format=csv"
           class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer"></i>
        </button>
    </div>
</div>

<div class="card border-0 bg-light mb-3 d-inline-block px-3 py-2">
    <small class="text-muted">Total Annual Depreciation (GHS)</small>
    <div class="fw-bold fs-5"><?= number_format($total_annual, 2) ?></div>
</div>

<div class="table-responsive">
<table class="table table-hover table-sm align-middle small">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Asset Name</th>
            <th>Class</th>
            <th>Acq. Date</th>
            <th class="text-end">Cost (GHS)</th>
            <th class="text-center">Life (mo)</th>
            <th class="text-end">Monthly Dep</th>
            <th class="text-end">Annual Dep</th>
        </tr>
    </thead>
    <tbody>
    <?php $offset = ($page-1)*50; ?>
    <?php foreach ($paged as $i => $r): ?>
    <tr>
        <td class="text-muted"><?= $offset+$i+1 ?></td>
        <td><?= esc($r['asset_name']) ?></td>
        <td><span class="badge bg-secondary"><?= esc($r['asset_class']) ?></span></td>
        <td class="text-nowrap small"><?= esc($r['acquisition_date']) ?></td>
        <td class="text-end"><?= number_format((float)$r['additions'],2) ?></td>
        <td class="text-center"><?= (int)$r['estimated_life_months'] ?></td>
        <td class="text-end"><?= number_format((float)$r['monthly_dep'],2) ?></td>
        <td class="text-end fw-semibold"><?= number_format((float)$r['annual_dep'],2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php if ($total_pages > 1): $qs = 'year='.$year.'&'; ?>
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
