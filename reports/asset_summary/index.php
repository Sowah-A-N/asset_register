<?php
/**
 * FILE:    schedule_officer/reports/asset_summary/index.php
 * PURPOSE: Asset Summary (GHS) — fixed-asset movement summary, ONE row per
 *          asset class, aggregated from the canonical depreciation engine.
 *
 * REPLACES the old version which read the manually-maintained (and
 * report-mutated) asset_class_opbal_year / asset_additions_year tables and
 * JOINed them — so classes silently vanished and numbers depended on who
 * last ran a class report.
 *
 * This version computes LIVE from `assets` via ../lib/depreciation.php, so
 * the summary and the per-class schedule (class_reports1) agree by design.
 * Read-only: no database writes.
 */

require_once '../../auth.php';
requirePermission('report.view', '../../login/');

include '../datacon.php';
require_once '../lib/depreciation.php';

$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));

// Shared, side-effect-free computation (also used by export.php).
require __DIR__ . '/compute.php';

function money($n) { return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asset Summary (GHS) — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
  <style>
    .report-topbar { background:#fff; border-bottom:1px solid var(--card-border); box-shadow:var(--topnav-shadow);
      padding:0 1.75rem; height:var(--topnav-h); display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:1030; }
    .rmu-content-full { padding:1.75rem; }
    .sum-table td, .sum-table th { white-space:nowrap; font-size:.8rem; }
    .sum-table .num { text-align:right; font-variant-numeric:tabular-nums; }
    @media print { .no-print, .report-topbar { display:none !important; } body { background:#fff; } }
  </style>
</head>
<body>

<header class="report-topbar no-print">
  <a href="../" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reports Hub</a>
  <div style="flex:1">
    <h1 style="font-size:1rem;font-weight:700;margin:0">Asset Summary — Movement (GHS)</h1>
    <small class="text-muted">One row per class · aggregated live from the depreciation engine</small>
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

  <div class="rmu-card mb-4 no-print">
    <div class="rmu-card-body" style="padding:1.1rem 1.5rem">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Report Year</label>
          <select name="year" class="form-select form-select-sm">
            <?php for ($y = 2024; $y <= $currentYear; $y++): ?>
              <option value="<?= $y ?>" <?= $selectedYear === $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-9">
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-play-fill me-1"></i>Generate</button>
          <?php if ($rows): ?>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
            <a class="btn btn-outline-success btn-sm" href="export.php?year=<?= $selectedYear ?>"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
            <a class="btn btn-outline-danger btn-sm" href="pdf.php?year=<?= $selectedYear ?>"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <?php if (!$rows): ?>
    <div class="rmu-card"><div class="rmu-card-body"><div class="rmu-empty">
      <i class="bi bi-inbox rmu-empty-icon"></i><h6>No assets in service for <?= $selectedYear ?></h6>
    </div></div></div>
  <?php else: ?>

    <!-- KPI header -->
    <div class="kpi-grid section-gap" style="grid-template-columns:repeat(4,1fr)">
      <div class="kpi-card kpi-blue"><div class="kpi-label">Total Cost</div><div class="kpi-value num" style="font-size:1.3rem">GH₵ <?= money($grand['cost']) ?></div><div class="kpi-sub"><?= $grand['count'] ?> assets · <?= count($rows) ?> classes</div></div>
      <div class="kpi-card kpi-amber"><div class="kpi-label">Depr. Charge <?= $selectedYear ?></div><div class="kpi-value num" style="font-size:1.3rem">GH₵ <?= money($grand['charge']) ?></div><div class="kpi-sub">Expense for the year</div></div>
      <div class="kpi-card kpi-slate"><div class="kpi-label">Accum. Depreciation</div><div class="kpi-value num" style="font-size:1.3rem">GH₵ <?= money($grand['accum_end']) ?></div><div class="kpi-sub">Carried forward</div></div>
      <div class="kpi-card kpi-green"><div class="kpi-label">Net Book Value</div><div class="kpi-value num" style="font-size:1.3rem">GH₵ <?= money($grand['nbv']) ?></div><div class="kpi-sub">Closing</div></div>
    </div>

    <div class="rmu-table-wrapper">
      <table class="rmu-table sum-table" id="summaryTable">
        <thead>
          <tr>
            <th>#</th><th>Asset Class</th><th class="num">Assets</th>
            <th class="num">Cost</th><th class="num">Additions (<?= $selectedYear ?>)</th><th class="num">Disposals (<?= $selectedYear ?>)</th>
            <th class="num">Accum. Depr. b/f</th><th class="num">Charge (<?= $selectedYear ?>)</th><th class="num">Accum. Depr. c/f</th>
            <th class="num">Net Book Value</th>
          </tr>
        </thead>
        <tbody>
          <?php $sn = 1; foreach ($rows as $r): ?>
            <tr>
              <td><?= $sn++ ?></td>
              <td><?= htmlspecialchars($r['name']) ?>
                <?= $r['wip'] ? '<span class="status-pill status-warning ms-1">WIP</span>' : '' ?></td>
              <td class="num"><?= $r['count'] ?></td>
              <td class="num"><?= money($r['cost']) ?></td>
              <td class="num"><?= $r['additions'] > 0 ? money($r['additions']) : '–' ?></td>
              <td class="num"><?= $r['disposals'] > 0 ? money($r['disposals']) : '–' ?></td>
              <td class="num"><?= money($r['accum_start']) ?></td>
              <td class="num"><?= money($r['charge']) ?></td>
              <td class="num"><?= money($r['accum_end']) ?></td>
              <td class="num"><strong><?= money($r['nbv']) ?></strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"><strong>Grand Total</strong></td>
            <td class="num"><strong><?= $grand['count'] ?></strong></td>
            <td class="num"><strong><?= money($grand['cost']) ?></strong></td>
            <td class="num"><strong><?= money($grand['additions']) ?></strong></td>
            <td class="num"><strong><?= money($grand['disposals']) ?></strong></td>
            <td class="num"><strong><?= money($grand['accum_start']) ?></strong></td>
            <td class="num"><strong><?= money($grand['charge']) ?></strong></td>
            <td class="num"><strong><?= money($grand['accum_end']) ?></strong></td>
            <td class="num"><strong><?= money($grand['nbv']) ?></strong></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <p class="text-muted mt-3" style="font-size:.75rem">
      <i class="bi bi-info-circle me-1"></i>
      Straight-line on (cost − residual) over each class's useful life. WIP classes are not depreciated.
      Computed live from the asset register — consistent with the per-class schedule. No stored data is altered.
    </p>
  <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
