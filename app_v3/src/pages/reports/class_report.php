<?php
require_auth();
require_once SRC . '/queries/reports.php';
require_once SRC . '/queries/asset_classes.php';

$years    = get_report_years($conn);
$year     = get_int('year', $years[0] ?? (int)date('Y'));
$class    = get('class');
$classes  = get_all_asset_classes($conn);
$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

// Load assets for the selected class + year
$rows = [];
if ($class) {
    $stmt = mysqli_prepare($conn,
        'SELECT * FROM assets WHERE asset_class = ? AND current_year <= ? AND disposed=0 AND archived=0
         ORDER BY acquisition_date');
    mysqli_stmt_bind_param($stmt, 'si', $class, $year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    mysqli_stmt_close($stmt);
}

// Also get the opbal summary for this class
$opbal = null;
if ($class) {
    $os = mysqli_prepare($conn,
        'SELECT * FROM asset_class_opbal_year WHERE asset_class = ? AND year = ? LIMIT 1');
    mysqli_stmt_bind_param($os, 'si', $class, $year);
    mysqli_stmt_execute($os);
    $opbal = mysqli_fetch_assoc(mysqli_stmt_get_result($os));
    mysqli_stmt_close($os);
}

$page_title = 'Class Report';
$content = function() use ($rows, $opbal, $classes, $class, $years, $year, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Class Report</h4>
        <?php if ($class): ?><small class="text-muted"><?= esc($class) ?> — <?= $year ?></small><?php endif; ?>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <form method="get" class="d-flex gap-2">
            <select name="class" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Select class…</option>
                <?php foreach ($classes as $c): ?>
                <option value="<?= esc($c['asset_class']) ?>"
                    <?= $class === $c['asset_class'] ? 'selected' : '' ?>>
                    <?= esc($c['asset_class']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php if ($class): ?>
        <a href="<?= $base_url ?>/exports/assets?report=class&class=<?= urlencode($class) ?>&year=<?= $year ?>&format=csv"
           class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer"></i>
        </button>
        <?php endif; ?>
    </div>
</div>

<?php if ($opbal): ?>
<div class="row g-3 mb-3">
    <?php $stats = [
        'Opening Balance' => $opbal['opening_balance'],
        'Depr Charge'     => $opbal['total_depr_year_charge'],
        'Accum Depr'      => $opbal['total_accum_depr_end'],
        'Net Book Value'  => $opbal['net_book_value'],
    ]; ?>
    <?php foreach ($stats as $label => $val): ?>
    <div class="col-sm-3">
        <div class="card border-0 bg-light">
            <div class="card-body py-2">
                <small class="text-muted"><?= $label ?></small>
                <div class="fw-bold"><?= number_format((float)$val, 2) ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($class && $rows): ?>
<div class="table-responsive">
<table class="table table-hover table-sm align-middle small">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Asset Name</th>
            <th>ID No.</th>
            <th>Sub-Class</th>
            <th>Location</th>
            <th>Acq. Date</th>
            <th class="text-end">Additions (GHS)</th>
            <th class="text-end">USD</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $i => $r):
        $usd = $r['dollar_rate_used'] > 0 ? (float)$r['additions']/(float)$r['dollar_rate_used'] : 0; ?>
    <tr>
        <td><?= $i+1 ?></td>
        <td><?= esc($r['asset_name']) ?></td>
        <td><code class="small"><?= esc($r['id_number']) ?></code></td>
        <td class="text-muted small"><?= esc($r['sub_class']) ?></td>
        <td><?= esc($r['location']) ?></td>
        <td class="text-nowrap small"><?= esc($r['acquisition_date']) ?></td>
        <td class="text-end"><?= number_format((float)$r['additions'],2) ?></td>
        <td class="text-end"><?= number_format($usd,2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot class="table-secondary fw-bold">
        <tr>
            <td colspan="6">TOTAL</td>
            <td class="text-end"><?= number_format(array_sum(array_column($rows,'additions')),2) ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>
</div>
<?php elseif ($class): ?>
<div class="alert alert-info">No assets found for this class in year <?= $year ?>.</div>
<?php else: ?>
<div class="alert alert-secondary">Select an asset class above to view the report.</div>
<?php endif; ?>
<?php };
require SRC . '/templates/layouts/base.php';
