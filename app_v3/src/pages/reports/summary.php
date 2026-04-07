<?php
require_auth();
require_once SRC . '/queries/reports.php';
require_once SRC . '/queries/asset_classes.php';

$years      = get_report_years($conn);
$year       = get_int('year', $years[0] ?? (int)date('Y'));
$data       = get_asset_class_summary($conn, $year);
$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Asset Summary (GHS)';

// Totals
$tot = ['opening_balance'=>0,'total_additions_cedi'=>0,'total_depr_year_charge'=>0,
        'total_accum_depr_end'=>0,'net_book_value'=>0,'total_disposals_cedi'=>0];
foreach ($data as $r) foreach ($tot as $k => &$v) $v += (float)$r[$k];

$content = function() use ($data, $tot, $years, $year, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Asset Summary — GH₵</h4>
        <small class="text-muted">Financial Year <?= $year ?></small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <!-- Year selector -->
        <form method="get" class="d-flex gap-2">
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= $y === $year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <!-- Export bar at top -->
        <a href="<?= $base_url ?>/exports/assets?report=summary&year=<?= $year ?>&format=csv"
           class="btn btn-outline-success btn-sm">
            <i class="bi bi-filetype-csv me-1"></i>CSV
        </a>
        <a href="<?= $base_url ?>/exports/pdf?report=summary&year=<?= $year ?>"
           class="btn btn-outline-danger btn-sm">
            <i class="bi bi-filetype-pdf me-1"></i>PDF
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle small" id="summaryTable">
    <thead class="table-dark">
        <tr>
            <th>Asset Class</th>
            <th class="text-end">Opening Balance</th>
            <th class="text-end">Additions</th>
            <th class="text-end">Disposals</th>
            <th class="text-end">Cost Closing</th>
            <th class="text-end">Accum Depr (Start)</th>
            <th class="text-end">Depr Charge</th>
            <th class="text-end">Disposal Depr</th>
            <th class="text-end">Accum Depr (End)</th>
            <th class="text-end">Net Book Value</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($data): ?>
        <?php foreach ($data as $r): ?>
        <?php $cost_closing = (float)$r['opening_balance'] + (float)$r['total_additions_cedi'] - (float)$r['total_disposals_cedi']; ?>
        <tr>
            <td><?= esc($r['asset_class']) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$r['opening_balance'], 2) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$r['total_additions_cedi'], 2) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$r['total_disposals_cedi'], 2) ?></td>
            <td class="text-end fw-semibold fmt-number"><?= number_format($cost_closing, 2) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$r['total_accum_depr_start'], 2) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$r['total_depr_year_charge'], 2) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$r['disposals_depr'], 2) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$r['total_accum_depr_end'], 2) ?></td>
            <td class="text-end fw-bold <?= (float)$r['net_book_value'] < 0 ? 'text-danger' : '' ?> fmt-number">
                <?= number_format((float)$r['net_book_value'], 2) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="10" class="text-center text-muted py-4">No data for year <?= $year ?>.</td></tr>
    <?php endif; ?>
    </tbody>
    <?php if ($data): ?>
    <tfoot class="table-secondary fw-bold">
        <?php $cost_closing_tot = $tot['opening_balance'] + $tot['total_additions_cedi'] - $tot['total_disposals_cedi']; ?>
        <tr>
            <td>TOTAL</td>
            <td class="text-end"><?= number_format($tot['opening_balance'], 2) ?></td>
            <td class="text-end"><?= number_format($tot['total_additions_cedi'], 2) ?></td>
            <td class="text-end"><?= number_format($tot['total_disposals_cedi'], 2) ?></td>
            <td class="text-end"><?= number_format($cost_closing_tot, 2) ?></td>
            <td class="text-end">—</td>
            <td class="text-end"><?= number_format($tot['total_depr_year_charge'], 2) ?></td>
            <td class="text-end">—</td>
            <td class="text-end"><?= number_format($tot['total_accum_depr_end'], 2) ?></td>
            <td class="text-end"><?= number_format($tot['net_book_value'], 2) ?></td>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
