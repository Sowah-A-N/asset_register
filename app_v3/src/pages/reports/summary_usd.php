<?php
require_auth();
require_once SRC . '/queries/reports.php';

$years    = get_report_years($conn);
$year     = get_int('year', $years[0] ?? (int)date('Y'));
$data     = get_asset_class_summary($conn, $year);
$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

// Get active dollar rate for display
$dr_row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
$rate = $dr_row ? (float)$dr_row['dollar_rate'] : 1.0;

$page_title = 'Asset Summary (USD)';
$content = function() use ($data, $years, $year, $rate, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Asset Summary — USD</h4>
        <small class="text-muted">Year <?= $year ?> &nbsp;|&nbsp; Rate: GH₵ <?= number_format($rate, 4) ?> / $1</small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form method="get" class="d-flex gap-2">
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="<?= $base_url ?>/exports/assets?report=summary-usd&year=<?= $year ?>&format=csv"
           class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle small">
    <thead class="table-dark">
        <tr>
            <th>Asset Class</th>
            <th class="text-end">Opening Bal ($)</th>
            <th class="text-end">Additions ($)</th>
            <th class="text-end">Disposals ($)</th>
            <th class="text-end">Depr Charge ($)</th>
            <th class="text-end">Accum Depr End ($)</th>
            <th class="text-end">Net Book Value ($)</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($data): foreach ($data as $r):
        $div = $rate ?: 1;
    ?>
    <tr>
        <td><?= esc($r['asset_class']) ?></td>
        <td class="text-end"><?= number_format((float)$r['opening_balance'] / $div, 2) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_additions_dollar'], 2) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_disposals_dollar'], 2) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_depr_year_charge'] / $div, 2) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_accum_depr_end'] / $div, 2) ?></td>
        <td class="text-end fw-bold"><?= number_format((float)$r['net_book_value'] / $div, 2) ?></td>
    </tr>
    <?php endforeach; else: ?>
        <tr><td colspan="7" class="text-center text-muted py-4">No data for year <?= $year ?>.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
