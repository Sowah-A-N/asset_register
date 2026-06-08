<?php
/**
 * FILE:    reports/index.php
 * PURPOSE: Reports Hub — neutral, RBAC-gated launcher for all asset reports.
 *          Now lives at the top level (role-neutral); uses the sidebar-free
 *          report-topbar chrome like the individual report pages. Read-only.
 */

session_start();
require_once '../auth.php';
requirePermission('report.view', '../login/');

include __DIR__ . '/datacon.php';

$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));

/* ── Assets by class (count + cost) ── */
$byClass = [];
$r = mysqli_query($conn,
    "SELECT asset_class, COUNT(*) cnt, COALESCE(SUM(additions),0) cost
     FROM assets WHERE disposals = 0
     GROUP BY asset_class ORDER BY cost DESC");
while ($row = mysqli_fetch_assoc($r)) $byClass[] = $row;

/* ── Assets by location (count + cost) ── */
$byLocation = [];
$r = mysqli_query($conn,
    "SELECT location, COUNT(*) cnt, COALESCE(SUM(additions),0) cost
     FROM assets WHERE disposals = 0
     GROUP BY location ORDER BY cost DESC");
while ($row = mysqli_fetch_assoc($r)) $byLocation[] = $row;

/* ── Chart data (top 8 classes by cost) ── */
$chartLabels = [];
$chartCosts  = [];
foreach (array_slice($byClass, 0, 8) as $c) {
    $chartLabels[] = $c['asset_class'];
    $chartCosts[]  = (float)$c['cost'];
}

function money($n) { return number_format((float)$n, 2, '.', ','); }

/* ── Report catalogue (the 5 live, engine-backed reports) ── */
$reports = [
    ['Class Depreciation Schedule', 'GHS · per asset + brought-forward pool', 'class_reports1/',     'bi-bar-chart-steps', 'qa-blue'],
    ['Asset Summary',               'GHS · movement, one row per class',      'asset_summary/',      'bi-table',          'qa-green'],
    ['Class Depreciation Schedule', 'USD · historical rate (IAS 21)',         'class_reports_usd/',  'bi-bar-chart-steps','qa-cyan'],
    ['Asset Summary',               'USD · movement, one row per class',      'asset_summary_usd/',  'bi-table',          'qa-purple'],
    ['Quarterly Depreciation',      'USD + GHS · Q1–Q4 by class',             'quarterly/',          'bi-calendar3',      'qa-amber'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports Hub — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/v2.css">
  <link rel="shortcut icon" href="../assets/img/rmulog.png">
  <style>
    .report-topbar { background:#fff; border-bottom:1px solid var(--card-border); box-shadow:var(--topnav-shadow);
      padding:0 1.75rem; height:var(--topnav-h); display:flex; align-items:center; gap:1rem; position:sticky; top:0; z-index:1030; }
    .rmu-content-full { padding:1.75rem; }
  </style>
</head>
<body class="bg-page">

<header class="report-topbar">
  <img src="../assets/img/rmulog.png" alt="RMU" style="height:34px;width:34px;object-fit:contain;background:#fff;border-radius:6px">
  <div class="topnav-breadcrumb" style="flex:1">
    <h1 style="font-size:1rem;font-weight:700;margin:0">Reports Hub</h1>
    <small class="text-muted">Financial and asset reports · all figures computed live</small>
  </div>
  <?php $RPREFIX = '../'; require __DIR__ . '/partials/topbar_home.php'; ?>
  <div class="dropdown">
    <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
      <div class="av-circle"><?= $userInitial ?></div>
      <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">Reports</div></div>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="../portal/"><i class="bi bi-grid-1x2 me-2"></i>Portal</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
    </ul>
  </div>
</header>

<main class="rmu-content-full" style="max-width:1200px;margin:0 auto">

  <!-- Report catalogue -->
  <div class="page-header">
    <div><h2 class="page-header-title">Report Library</h2>
      <p class="page-header-subtitle">Select a report to generate</p></div>
  </div>

  <div class="row g-3 section-gap">
    <?php foreach ($reports as [$title, $sub, $href, $icon, $cls]): ?>
      <div class="col-md-6 col-xl-4">
        <a href="<?= $href ?>" class="quick-action-btn <?= $cls ?>" style="align-items:flex-start">
          <span class="qa-icon"><i class="bi <?= $icon ?>"></i></span>
          <span>
            <span style="display:block"><?= htmlspecialchars($title) ?></span>
            <small class="text-muted" style="font-weight:400"><?= htmlspecialchars($sub) ?></small>
          </span>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Chart + by-class breakdown -->
  <div class="row g-4 section-gap">
    <div class="col-lg-6">
      <div class="rmu-card h-100">
        <div class="rmu-card-header"><div class="rmu-card-title">Cost by Asset Class (GHS)</div></div>
        <div class="rmu-card-body">
          <?php if (empty($chartCosts)): ?>
            <div class="rmu-empty"><i class="bi bi-pie-chart rmu-empty-icon"></i><h6>No active assets</h6></div>
          <?php else: ?>
            <div class="chart-wrap" style="min-height:300px"><canvas id="costChart"></canvas></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="rmu-card h-100">
        <div class="rmu-card-header">
          <div class="rmu-card-title">Assets by Class</div>
          <small class="text-muted">Active assets · click a class for its schedule</small>
        </div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($byClass)): ?>
            <div class="rmu-empty"><i class="bi bi-inbox rmu-empty-icon"></i><h6>No active assets</h6></div>
          <?php else: ?>
            <div style="max-height:300px;overflow-y:auto">
              <table class="rmu-table" style="box-shadow:none;border:none">
                <thead><tr><th>Class</th><th class="num" style="text-align:right">Assets</th><th class="num" style="text-align:right">Cost (GHS)</th></tr></thead>
                <tbody>
                  <?php foreach ($byClass as $c): ?>
                    <tr style="cursor:pointer" onclick="location.href='class_reports1/?asset_class=<?= urlencode($c['asset_class']) ?>'">
                      <td><?= htmlspecialchars($c['asset_class']) ?></td>
                      <td style="text-align:right"><?= (int)$c['cnt'] ?></td>
                      <td style="text-align:right;font-variant-numeric:tabular-nums"><?= money($c['cost']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- By-location breakdown -->
  <div class="rmu-card">
    <div class="rmu-card-header">
      <div class="rmu-card-title">Assets by Location</div>
      <small class="text-muted">Active assets grouped by location</small>
    </div>
    <div class="rmu-card-body" style="padding:0">
      <?php if (empty($byLocation)): ?>
        <div class="rmu-empty"><i class="bi bi-geo-alt rmu-empty-icon"></i><h6>No active assets</h6></div>
      <?php else: ?>
        <div style="max-height:360px;overflow-y:auto">
          <table class="rmu-table" style="box-shadow:none;border:none">
            <thead><tr><th>Location</th><th style="text-align:right">Assets</th><th style="text-align:right">Cost (GHS)</th></tr></thead>
            <tbody>
              <?php foreach ($byLocation as $l): ?>
                <tr>
                  <td><?= htmlspecialchars($l['location']) ?></td>
                  <td style="text-align:right"><?= (int)$l['cnt'] ?></td>
                  <td style="text-align:right;font-variant-numeric:tabular-nums"><?= money($l['cost']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <footer style="margin-top:1.5rem;padding:.85rem 0;border-top:1px solid var(--card-border);font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
    <span>RMU Asset Register &nbsp;·&nbsp; Reports Hub</span>
    <span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
  </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
<?php if (!empty($chartCosts)): ?>
(function(){
  const ctx = document.getElementById('costChart'); if(!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>,
      datasets: [{
        label: 'Cost (GHS)',
        data: <?= json_encode($chartCosts) ?>,
        backgroundColor: 'rgba(37,99,235,0.15)', borderColor: '#2563eb',
        borderWidth: 2, borderRadius: 6, borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y', responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display:false },
        tooltip: { callbacks: { label: c => ' GH₵ ' + c.formattedValue } } },
      scales: { x: { beginAtZero:true, ticks:{ font:{size:10} }, grid:{ color:'rgba(0,0,0,0.04)' } },
                y: { ticks:{ font:{size:11} }, grid:{ display:false } } }
    }
  });
})();
<?php endif; ?>
</script>
</body>
</html>
