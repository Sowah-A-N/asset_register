<?php
require_auth();
require_once SRC . '/queries/reports.php';

$years      = get_report_years($conn);
$year       = get_int('year', $years[0] ?? (int)date('Y'));
$data       = get_asset_class_summary($conn, $year);
$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Accumulated Depreciation';

$content = function() use ($data, $years, $year, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Accumulated Depreciation</h4>
        <small class="text-muted">Year <?= $year ?></small>
    </div>
    <div class="d-flex gap-2">
        <form method="get" class="d-flex gap-2">
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= $y===$year?'selected':'' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="<?= $base_url ?>/exports/assets?report=accum-dep&year=<?= $year ?>&format=csv"
           class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-printer"></i>
        </button>
    </div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle small">
    <thead class="table-dark">
        <tr>
            <th>Asset Class</th>
            <th class="text-end">Accum Depr (Start)</th>
            <th class="text-end">Depr Charge</th>
            <th class="text-end">Disposal Depr</th>
            <th class="text-end">Accum Depr (End)</th>
            <th class="text-end">Net Book Value</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $tot_start=0; $tot_charge=0; $tot_disp=0; $tot_end=0; $tot_nbv=0;
    foreach ($data as $r):
        $tot_start  += (float)$r['total_accum_depr_start'];
        $tot_charge += (float)$r['total_depr_year_charge'];
        $tot_disp   += (float)$r['disposals_depr'];
        $tot_end    += (float)$r['total_accum_depr_end'];
        $tot_nbv    += (float)$r['net_book_value'];
    ?>
    <tr>
        <td><?= esc($r['asset_class']) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_accum_depr_start'],2) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_depr_year_charge'],2) ?></td>
        <td class="text-end"><?= number_format((float)$r['disposals_depr'],2) ?></td>
        <td class="text-end fw-semibold"><?= number_format((float)$r['total_accum_depr_end'],2) ?></td>
        <td class="text-end fw-bold <?= (float)$r['net_book_value']<0?'text-danger':'' ?>">
            <?= number_format((float)$r['net_book_value'],2) ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <?php if ($data): ?>
    <tfoot class="table-secondary fw-bold">
        <tr>
            <td>TOTAL</td>
            <td class="text-end"><?= number_format($tot_start,2) ?></td>
            <td class="text-end"><?= number_format($tot_charge,2) ?></td>
            <td class="text-end"><?= number_format($tot_disp,2) ?></td>
            <td class="text-end"><?= number_format($tot_end,2) ?></td>
            <td class="text-end"><?= number_format($tot_nbv,2) ?></td>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
