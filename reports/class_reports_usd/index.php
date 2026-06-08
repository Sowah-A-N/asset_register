<?php
/**
 * FILE:    schedule_officer/reports/class_reports_usd/index.php
 * PURPOSE: Class Depreciation Schedule (USD) — same engine as the GHS report,
 *          translated at historical rate per item (IAS 21).
 *
 * REPLACES the old version which queried USD columns that do not exist in
 * asset_class_opbal_year (open_bal_usd, …) and therefore always errored.
 *
 * Translation policy (approved):
 *   • Individual assets → each asset's own stored rate (dollar_rate_used)
 *   • Legacy pool       → its stored rate in asset_class_opbal_year
 *   • Rate ≤ 0 (assets) or < 1 (pool, i.e. a bad/dep-rate value) → fall back
 *     to the active dollar rate and flag the row.
 * Read-only: computes live, writes nothing.
 */

require_once '../../auth.php';
requirePermission('report.view', '../../login/');

include '../datacon.php';
require_once '../lib/depreciation.php';

$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));

// Shared, side-effect-free computation (also used by export.php).
require __DIR__ . '/compute.php';

function usd($n){ return number_format((float)$n, 2, '.', ','); }
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Class Depreciation Schedule (USD) — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
  <style>
    .report-topbar { background:#fff; border-bottom:1px solid var(--card-border); box-shadow:var(--topnav-shadow);
      padding:0 1.75rem; height:var(--topnav-h); display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:1030; }
    .rmu-content-full { padding:1.75rem; }
    .schedule-table td,.schedule-table th { white-space:nowrap; font-size:.78rem; }
    .schedule-table .num { text-align:right; font-variant-numeric:tabular-nums; }
    @media print { .no-print,.report-topbar { display:none !important; } body{background:#fff;} }
  </style>
</head>
<body>
<header class="report-topbar no-print">
  <a href="../" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reports Hub</a>
  <div style="flex:1">
    <h1 style="font-size:1rem;font-weight:700;margin:0">Class Depreciation Schedule (USD)</h1>
    <small class="text-muted">Translated at historical rate per item (IAS 21) · active rate $<?= number_format($activeRate,2) ?></small>
  </div>
  <?php $RPREFIX = '../../'; require __DIR__ . '/../partials/topbar_home.php'; ?>
  <div class="dropdown">
    <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
      <div class="av-circle"><?= $userInitial ?></div>
      <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">Reports</div></div>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="../../portal/"><i class="bi bi-grid-1x2 me-2"></i>Portal</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="../../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
    </ul>
  </div>
</header>

<main class="rmu-content-full">
  <div class="rmu-card mb-4 no-print"><div class="rmu-card-body" style="padding:1.1rem 1.5rem">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Asset Class</label>
        <select name="asset_class" class="form-select form-select-sm" required>
          <option value="">— Select asset class —</option>
          <?php foreach ($classes as $c): ?>
            <option value="<?= htmlspecialchars($c, ENT_QUOTES) ?>" <?= ($selectedClass!=='' && rtrim($selectedClass)===rtrim($c))?'selected':'' ?>><?= htmlspecialchars(trim($c)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Report Year</label>
        <select name="year" class="form-select form-select-sm" required>
          <option value="">— Year —</option>
          <?php for ($y=2024;$y<=$currentYear;$y++): ?><option value="<?= $y ?>" <?= $selectedYear===$y?'selected':'' ?>><?= $y ?></option><?php endfor; ?>
        </select>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-play-fill me-1"></i>Generate</button>
        <?php if ($hasData): ?>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
          <a class="btn btn-outline-success btn-sm" href="export.php?asset_class=<?= urlencode($selectedClass) ?>&year=<?= $selectedYear ?>"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
          <a class="btn btn-outline-danger btn-sm" href="pdf.php?asset_class=<?= urlencode($selectedClass) ?>&year=<?= $selectedYear ?>"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <?php endif; ?>
      </div>
    </form>
  </div></div>

  <?php if ($selectedClass==='' || $selectedYear===0): ?>
    <div class="rmu-card"><div class="rmu-card-body"><div class="rmu-empty">
      <i class="bi bi-bar-chart-steps rmu-empty-icon"></i><h6>Select a class and year</h6></div></div></div>
  <?php elseif (!$hasData): ?>
    <div class="rmu-card"><div class="rmu-card-body"><div class="rmu-empty">
      <i class="bi bi-inbox rmu-empty-icon"></i><h6>No data in service for <?= $selectedYear ?></h6></div></div></div>
  <?php else: $count = $report ? count($report['lines']) : 0; ?>
    <div class="rmu-card mb-3"><div class="rmu-card-body" style="padding:1.25rem 1.5rem">
      <div class="d-flex justify-content-between flex-wrap gap-3 align-items-center">
        <div>
          <h4 style="font-weight:700;margin:0"><?= htmlspecialchars(trim($selectedClass)) ?> <span class="text-muted" style="font-size:.9rem">· USD</span></h4>
          <small class="text-muted">Report year <?= $selectedYear ?> · <?= $count ?> itemised asset<?= $count!==1?'s':'' ?>
            <?php if ($pool): ?><span class="status-pill" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-bookmark-star me-1"></i>incl. brought-forward<?= $poolRateSub?' (rate substituted)':'' ?></span><?php endif; ?>
          </small>
        </div>
        <div class="d-flex gap-4 text-end flex-wrap">
          <div><div class="kpi-label">Total Cost</div><div class="fw-800 num" style="font-size:1.05rem">$ <?= usd($combined['cost']) ?></div></div>
          <div><div class="kpi-label">Depr. Expense <?= $selectedYear ?></div><div class="fw-800 num" style="font-size:1.05rem;color:var(--c-amber)">$ <?= usd($combined['expense']) ?></div></div>
          <div><div class="kpi-label">Accum. Depr.</div><div class="fw-800 num" style="font-size:1.05rem">$ <?= usd($combined['accum_end']) ?></div></div>
          <div><div class="kpi-label">Net Book Value</div><div class="fw-800 num" style="font-size:1.05rem;color:var(--c-green)">$ <?= usd($combined['nbv']) ?></div></div>
        </div>
      </div>
    </div></div>

    <div class="rmu-table-wrapper">
      <table class="rmu-table schedule-table" id="scheduleTable">
        <thead><tr>
          <th>#</th><th>Asset Name</th><th>Location</th><th>Tag / Serial</th><th>Acquired</th><th class="num">Rate</th>
          <th class="num">Cost</th><th class="num">Depr. Base</th><th class="num">Accum. Start</th>
          <th class="num">Expense <?= $selectedYear ?></th><th class="num">Accum. End</th><th class="num">NBV End</th>
          <?php foreach ($months as $m): ?><th class="num"><?= $m ?></th><?php endforeach; ?>
        </tr></thead>
        <tbody>
          <?php if ($pool): ?>
            <tr style="background:#eff6ff;font-weight:600">
              <td>—</td><td>Opening Balance <span class="text-muted" style="font-weight:400">(brought forward)</span></td>
              <td>—</td><td>—</td><td>1 Jan <?= RMU_POOL_BASE_YEAR ?></td>
              <td class="num"><?= number_format($poolRate,2) ?><?= $poolRateSub?' <i class="bi bi-exclamation-triangle text-warning" title="rate substituted"></i>':'' ?></td>
              <td class="num"><?= usd($pool['depreciable_base']) ?></td><td class="num"><?= usd($pool['depreciable_base']) ?></td>
              <td class="num"><?= usd($pool['accumulated_start']) ?></td><td class="num"><?= usd($pool['depreciation_expense']) ?></td>
              <td class="num"><?= usd($pool['accumulated_end']) ?></td><td class="num"><?= usd($pool['nbv_end']) ?></td>
              <?php for ($m=1;$m<=12;$m++): $v=$pool['monthly_breakdown'][$m]; ?><td class="num"><?= $v>0?usd($v):'–' ?></td><?php endfor; ?>
            </tr>
          <?php endif; ?>
          <?php $sn=1; foreach (($report['lines']??[]) as $ln): $a=$ln['asset']; ?>
            <tr<?= $ln['disposed_in_year']?' style="background:#fff7ed"':'' ?>>
              <td><?= $sn++ ?></td>
              <td style="white-space:normal;max-width:200px"><?= htmlspecialchars($a['asset_name']) ?></td>
              <td><?= htmlspecialchars($a['location']) ?></td>
              <td><code style="font-size:.74rem"><?= htmlspecialchars($a['serial_number']?:'—') ?></code></td>
              <td><?= date('d M Y', strtotime($a['acquisition_date'])) ?></td>
              <td class="num"><?= number_format($ln['rate_used'],2) ?><?= $ln['rate_substituted']?' <i class="bi bi-exclamation-triangle text-warning" title="rate substituted"></i>':'' ?></td>
              <td class="num"><?= usd($ln['cost_usd']) ?></td><td class="num"><?= usd($ln['depreciable_base']) ?></td>
              <td class="num"><?= usd($ln['accumulated_start']) ?></td><td class="num"><?= usd($ln['depreciation_expense']) ?></td>
              <td class="num"><?= usd($ln['accumulated_end']) ?></td><td class="num"><?= usd($ln['nbv_end']) ?></td>
              <?php for ($m=1;$m<=12;$m++): $v=$ln['monthly_breakdown'][$m]; ?><td class="num"><?= $v>0?usd($v):'–' ?></td><?php endfor; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot><tr>
          <td colspan="6"><strong>Totals (incl. brought-forward)</strong></td>
          <td class="num"><strong><?= usd($combined['cost']) ?></strong></td><td class="num"><strong><?= usd($combined['base']) ?></strong></td>
          <td class="num"><strong><?= usd($combined['accum_start']) ?></strong></td><td class="num"><strong><?= usd($combined['expense']) ?></strong></td>
          <td class="num"><strong><?= usd($combined['accum_end']) ?></strong></td><td class="num"><strong><?= usd($combined['nbv']) ?></strong></td>
          <?php for ($m=1;$m<=12;$m++): ?><td class="num"><strong><?= $combined['monthly'][$m]>0?usd($combined['monthly'][$m]):'–' ?></strong></td><?php endfor; ?>
        </tr></tfoot>
      </table>
    </div>
    <p class="text-muted mt-3" style="font-size:.75rem"><i class="bi bi-info-circle me-1"></i>
      USD figures translate each item at its historical rate (IAS 21). <i class="bi bi-exclamation-triangle text-warning"></i> marks a substituted rate (stored rate missing/invalid → active rate $<?= number_format($activeRate,2) ?>). Computed live; no stored data altered.</p>
  <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
