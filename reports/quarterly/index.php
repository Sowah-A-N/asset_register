<?php
/**
 * FILE: schedule_officer/reports/quarterly/index.php
 * PURPOSE: Quarterly depreciation report — per class, Q1–Q4 + year total.
 *          USD is the headline currency (IAS 21 historical rate per item);
 *          GHS is available via the currency toggle. Pool + individual,
 *          computed live from the engine's monthly breakdown. Read-only.
 */
require_once '../../auth.php';
requirePermission('report.view', '../../login/');
include '../datacon.php';
require_once '../lib/depreciation.php';

$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
// Shared, side-effect-free computation (also used by export.php).
require __DIR__ . '/compute.php';

function n2($v){ return number_format((float)$v, 2, '.', ','); }
$Q = ['Q1 (Jan–Mar)','Q2 (Apr–Jun)','Q3 (Jul–Sep)','Q4 (Oct–Dec)'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quarterly Depreciation — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
  <style>
    .report-topbar{background:#fff;border-bottom:1px solid var(--card-border);box-shadow:var(--topnav-shadow);padding:0 1.75rem;height:var(--topnav-h);display:flex;align-items:center;gap:1rem;position:sticky;top:0;z-index:1030}
    .rmu-content-full{padding:1.75rem}
    .q-table td,.q-table th{white-space:nowrap;font-size:.82rem}
    .q-table .num{text-align:right;font-variant-numeric:tabular-nums}
    @media print{.no-print,.report-topbar{display:none !important}body{background:#fff}}
  </style>
</head>
<body>
<header class="report-topbar no-print">
  <a href="../" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Reports Hub</a>
  <div style="flex:1">
    <h1 style="font-size:1rem;font-weight:700;margin:0">Quarterly Depreciation</h1>
    <small class="text-muted">USD headline (historical rate, IAS 21) · GHS available · pool + individual</small>
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
      <div class="col-md-3">
        <label class="form-label">Report Year</label>
        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
          <?php for ($y=2024;$y<=$currentYear;$y++): ?><option value="<?= $y ?>" <?= $selectedYear===$y?'selected':'' ?>><?= $y ?></option><?php endfor; ?>
        </select>
      </div>
      <div class="col-md-9 d-flex align-items-end gap-2">
        <div class="btn-group btn-group-sm" role="group">
          <input type="radio" class="btn-check" name="cur" id="curUsd" checked>
          <label class="btn btn-outline-primary" for="curUsd">USD ($)</label>
          <input type="radio" class="btn-check" name="cur" id="curGhs">
          <label class="btn btn-outline-primary" for="curGhs">GHS (₵)</label>
        </div>
        <?php if ($rows): ?>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
          <a class="btn btn-outline-success btn-sm" href="export.php?year=<?= $selectedYear ?>"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
          <a class="btn btn-outline-danger btn-sm" href="pdf.php?year=<?= $selectedYear ?>"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <?php endif; ?>
      </div>
    </form>
  </div></div>

  <?php if (!$rows): ?>
    <div class="rmu-card"><div class="rmu-card-body"><div class="rmu-empty">
      <i class="bi bi-calendar3 rmu-empty-icon"></i><h6>No depreciation in <?= $selectedYear ?></h6></div></div></div>
  <?php else: ?>
    <div class="kpi-grid section-gap" style="grid-template-columns:repeat(4,1fr)">
      <?php foreach ([0,1,2,3] as $q): ?>
        <div class="kpi-card <?= ['kpi-blue','kpi-cyan','kpi-amber','kpi-green'][$q] ?>">
          <div class="kpi-label"><?= 'Q'.($q+1) ?> Depreciation</div>
          <div class="kpi-value num" style="font-size:1.25rem">
            <span class="cur-usd">$ <?= n2($grand['usd'][$q]) ?></span><span class="cur-ghs" style="display:none">GH₵ <?= n2($grand['ghs'][$q]) ?></span>
          </div>
          <div class="kpi-sub"><?= $Q[$q] ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="rmu-table-wrapper">
      <table class="rmu-table q-table" id="qTable">
        <thead><tr>
          <th>#</th><th>Asset Class</th>
          <th class="num">Q1</th><th class="num">Q2</th><th class="num">Q3</th><th class="num">Q4</th>
          <th class="num">Year Total</th>
        </tr></thead>
        <tbody>
          <?php $sn=1; foreach ($rows as $r): ?>
            <tr>
              <td><?= $sn++ ?></td>
              <td><?= htmlspecialchars($r['name']) ?></td>
              <?php for ($q=0;$q<4;$q++): ?>
                <td class="num"><span class="cur-usd"><?= n2($r['usd'][$q]) ?></span><span class="cur-ghs" style="display:none"><?= n2($r['ghs'][$q]) ?></span></td>
              <?php endfor; ?>
              <td class="num"><strong><span class="cur-usd"><?= n2($r['usdYear']) ?></span><span class="cur-ghs" style="display:none"><?= n2($r['ghsYear']) ?></span></strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot><tr>
          <td colspan="2"><strong>Total</strong></td>
          <?php for ($q=0;$q<4;$q++): ?>
            <td class="num"><strong><span class="cur-usd"><?= n2($grand['usd'][$q]) ?></span><span class="cur-ghs" style="display:none"><?= n2($grand['ghs'][$q]) ?></span></strong></td>
          <?php endfor; ?>
          <td class="num"><strong><span class="cur-usd"><?= n2($grandUsdYear) ?></span><span class="cur-ghs" style="display:none"><?= n2($grandGhsYear) ?></span></strong></td>
        </tr></tfoot>
      </table>
    </div>
    <p class="text-muted mt-3" style="font-size:.75rem"><i class="bi bi-info-circle me-1"></i>
      Quarterly depreciation = sum of the months in each quarter, from the same engine as the annual schedules (pool + individual). USD at each item's historical rate (IAS 21). Computed live; no stored data altered.</p>
  <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
function setCur(cur){
  document.querySelectorAll('.cur-usd').forEach(e=>e.style.display = cur==='usd'?'':'none');
  document.querySelectorAll('.cur-ghs').forEach(e=>e.style.display = cur==='ghs'?'':'none');
}
document.getElementById('curUsd')?.addEventListener('change',()=>setCur('usd'));
document.getElementById('curGhs')?.addEventListener('change',()=>setCur('ghs'));
</script>
</body>
</html>
