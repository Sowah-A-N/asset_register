<?php
/* ═══════════════════════════════════════════════════════════
   RMU Asset Register v2 — Schedule Officer Dashboard
   ═══════════════════════════════════════════════════════════ */
require_once '../../auth.php';
requireAuth('../login/');
// Operational dashboard — only roles that can create assets or manage the
// catalogue belong here; read-only/oversight roles use the shared Portal.
if (!can('asset.create') && !can('catalog.manage')) { header('Location: ../../portal/'); exit(); }
$username    = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));

include '../datacon.php';   // provides $conn → asset_register_new

/* ── Safe query helper ─────────────────────────────────── */
function qs(mysqli $c, string $sql): array|false {
    $r = mysqli_query($c, $sql);
    if (!$r) return false;
    return mysqli_fetch_assoc($r) ?: [];
}

/* ── KPI queries ──────────────────────────────────────── */
$activeAssets  = (int)(qs($conn, "SELECT COUNT(*) c FROM assets WHERE disposals=0")['c'] ?? 0);
$disposedCount = (int)(qs($conn, "SELECT COUNT(*) c FROM disposals")['c'] ?? 0);
$archivedCount = (int)(qs($conn, "SELECT COUNT(*) c FROM assets_archive")['c'] ?? 0);

$ghs = (float)(qs($conn, "SELECT COALESCE(SUM(additions),0) v FROM assets WHERE disposals=0")['v'] ?? 0);
$row = qs($conn, "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1");
$dollarRate = $row ? (float)$row['dollar_rate'] : 0;
$usd        = ($dollarRate > 0) ? $ghs / $dollarRate : 0;

$classCount    = (int)(qs($conn, "SELECT COUNT(*) c FROM asset_classes")['c'] ?? 0);
$locationCount = (int)(qs($conn, "SELECT COUNT(*) c FROM asset_location")['c'] ?? 0);
$supplierCount = (int)(qs($conn, "SELECT COUNT(*) c FROM suppliers")['c'] ?? 0);

/* ── Chart 1: asset distribution by class (donut) ───── */
$qDist = mysqli_query($conn,
    "SELECT asset_class, COUNT(*) cnt
     FROM assets WHERE disposals=0
     GROUP BY asset_class ORDER BY cnt DESC LIMIT 8");
$distLabels = []; $distValues = [];
while ($r = mysqli_fetch_assoc($qDist)) {
    $distLabels[] = $r['asset_class'];
    $distValues[] = (int)$r['cnt'];
}

/* ── Chart 2: monthly acquisitions last 12 months (bar) */
$qMon = mysqli_query($conn,
    "SELECT DATE_FORMAT(date_added,'%b %Y') mo, COUNT(*) cnt
     FROM assets
     WHERE date_added >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
       AND date_added NOT IN ('0000-00-00','0000-00-00 00:00:00')
     GROUP BY DATE_FORMAT(date_added,'%Y-%m')
     ORDER BY MIN(date_added) ASC");
$monLabels = []; $monValues = [];
while ($r = mysqli_fetch_assoc($qMon)) {
    $monLabels[] = $r['mo'];
    $monValues[] = (int)$r['cnt'];
}

/* ── Activity feed: recent 10 events ─────────────────── */
$activities = [];
$qAct = mysqli_query($conn,
    "SELECT asset_name lbl, 'added' kind, date_added ts
     FROM assets
     WHERE date_added NOT IN ('0000-00-00','0000-00-00 00:00:00')
     ORDER BY date_added DESC LIMIT 6");
while ($r = mysqli_fetch_assoc($qAct)) {
    $activities[] = $r;
}
$qMov = mysqli_query($conn,
    "SELECT CONCAT('SN ',serial_number,' → ',New_location) lbl, 'moved' kind, date_of_action ts
     FROM moved_assets ORDER BY date_of_action DESC LIMIT 5");
while ($r = mysqli_fetch_assoc($qMov)) {
    $activities[] = $r;
}
$qDis = mysqli_query($conn,
    "SELECT asset_name lbl, 'disposed' kind, date_of_disposal ts
     FROM disposals ORDER BY date_of_disposal DESC LIMIT 4");
while ($r = mysqli_fetch_assoc($qDis)) {
    $activities[] = $r;
}
usort($activities, fn($a,$b) => strtotime($b['ts']) - strtotime($a['ts']));
$activities = array_slice($activities, 0, 10);

/* ── Helpers ──────────────────────────────────────────── */
function fmtMoney(float $n): string {
    if ($n >= 1_000_000) return 'GH₵ ' . number_format($n/1_000_000, 2) . 'M';
    if ($n >= 1_000)     return 'GH₵ ' . number_format($n/1_000, 2)     . 'K';
    return 'GH₵ ' . number_format($n, 2);
}
function fmtUSD(float $n): string {
    if ($n >= 1_000_000) return '$ ' . number_format($n/1_000_000, 2) . 'M';
    if ($n >= 1_000)     return '$ ' . number_format($n/1_000, 2)     . 'K';
    return '$ ' . number_format($n, 2);
}
function timeAgo(string $ts): string {
    $diff = time() - strtotime($ts);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff/60) . 'm ago';
    if ($diff < 86400)  return floor($diff/3600) . 'h ago';
    if ($diff < 604800) return floor($diff/86400) . 'd ago';
    return date('d M Y', strtotime($ts));
}
function activityIcon(string $kind): string {
    return match($kind) {
        'added'    => '<span class="activity-dot dot-add"><i class="bi bi-plus-lg"></i></span>',
        'moved'    => '<span class="activity-dot dot-move"><i class="bi bi-arrow-left-right"></i></span>',
        'disposed' => '<span class="activity-dot dot-dispose"><i class="bi bi-trash3"></i></span>',
        default    => '<span class="activity-dot dot-edit"><i class="bi bi-pencil"></i></span>',
    };
}
function activityVerb(string $kind): string {
    return match($kind) {
        'added'    => 'Asset added:',
        'moved'    => 'Asset relocated:',
        'disposed' => 'Asset disposed:',
        default    => 'Asset updated:',
    };
}

$today = date('l, d F Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — RMU Asset Register</title>

  <!-- Bootstrap 5.3 -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- RMU Design System -->
  <link rel="stylesheet" href="../../assets/css/v2.css">

  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
</head>
<body>

<!-- ── SIDEBAR OVERLAY (mobile) ─────────────────────── -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="rmu-layout">

  <!-- ═══ SIDEBAR ══════════════════════════════════════ -->
  <?php require '../partials/sidebar.php'; ?>

  <!-- ═══ MAIN PANEL ═══════════════════════════════════ -->
  <div class="rmu-main">

    <!-- TOP NAV -->
    <header class="rmu-topnav">
      <button class="topnav-toggle" id="sidebarToggle" title="Toggle sidebar">
        <i class="bi bi-list"></i>
      </button>

      <div class="topnav-breadcrumb">
        <h1>Dashboard</h1>
        <small><?= $today ?></small>
      </div>

      <div class="topnav-actions">
        <!-- Refresh -->
        <button class="topnav-icon-btn" title="Refresh data" onclick="location.reload()">
          <i class="bi bi-arrow-clockwise"></i>
        </button>

        <div class="topnav-divider"></div>

        <!-- User dropdown -->
        <div class="dropdown">
          <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="av-circle"><?= $userInitial ?></div>
            <div class="av-info">
              <div class="av-name"><?= $username ?></div>
              <div class="av-role">Schedule Officer</div>
            </div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <span class="dropdown-item-text">
                <div class="fw-600" style="font-size:.85rem"><?= $username ?></div>
                <small class="text-muted">Schedule Officer</small>
              </span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="../set_rate/">
                <i class="bi bi-currency-dollar"></i> Set Dollar Rate
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="../../account/">
                <i class="bi bi-shield-lock"></i> Change Password
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="../logout/">
                <i class="bi bi-box-arrow-right"></i> Sign Out
              </a>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="rmu-content">

      <!-- Page header -->
      <div class="page-header">
        <div>
          <h2 class="page-header-title">Overview</h2>
          <p class="page-header-subtitle">Real-time summary of all institutional assets</p>
        </div>
        <div class="page-header-actions">
          <a href="../new_asset/" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Asset
          </a>
          <a href="../../reports/" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-bar-chart-line me-1"></i>Reports
          </a>
        </div>
      </div>

      <!-- ── KPI CARDS ─────────────────────────────────── -->
      <div class="kpi-grid section-gap">

        <div class="kpi-card kpi-blue">
          <div class="kpi-label">Active Assets</div>
          <div class="kpi-value num"><?= number_format($activeAssets) ?></div>
          <div class="kpi-sub">Currently in service</div>
          <i class="bi bi-box-seam kpi-bg-icon"></i>
        </div>

        <div class="kpi-card kpi-green">
          <div class="kpi-label">Total Value (GHS)</div>
          <div class="kpi-value num" style="font-size:1.4rem"><?= fmtMoney($ghs) ?></div>
          <div class="kpi-sub">Historical cost of active assets</div>
          <i class="bi bi-cash-stack kpi-bg-icon"></i>
        </div>

        <div class="kpi-card kpi-cyan">
          <div class="kpi-label">Total Value (USD)</div>
          <div class="kpi-value num" style="font-size:1.4rem"><?= fmtUSD($usd) ?></div>
          <div class="kpi-sub">At active rate of <?= $dollarRate > 0 ? 'GH₵'.number_format($dollarRate,2) : 'N/A' ?></div>
          <i class="bi bi-currency-dollar kpi-bg-icon"></i>
        </div>

        <div class="kpi-card kpi-amber">
          <div class="kpi-label">Dollar Rate</div>
          <div class="kpi-value num">
            <?= $dollarRate > 0 ? 'GH₵'.number_format($dollarRate,4) : '<span style="font-size:1rem;color:var(--c-red)">NOT SET</span>' ?>
          </div>
          <div class="kpi-sub">Active exchange rate</div>
          <i class="bi bi-currency-exchange kpi-bg-icon"></i>
        </div>

        <div class="kpi-card kpi-red">
          <div class="kpi-label">Disposed Assets</div>
          <div class="kpi-value num"><?= number_format($disposedCount) ?></div>
          <div class="kpi-sub">Written off or retired</div>
          <i class="bi bi-trash3 kpi-bg-icon"></i>
        </div>

        <div class="kpi-card kpi-slate">
          <div class="kpi-label">Archived Assets</div>
          <div class="kpi-value num"><?= number_format($archivedCount) ?></div>
          <div class="kpi-sub">Removed from active register</div>
          <i class="bi bi-archive kpi-bg-icon"></i>
        </div>

        <div class="kpi-card kpi-purple">
          <div class="kpi-label">Asset Classes</div>
          <div class="kpi-value num"><?= number_format($classCount) ?></div>
          <div class="kpi-sub"><?= number_format($locationCount) ?> locations registered</div>
          <i class="bi bi-tags kpi-bg-icon"></i>
        </div>

        <div class="kpi-card kpi-indigo">
          <div class="kpi-label">Suppliers</div>
          <div class="kpi-value num"><?= number_format($supplierCount) ?></div>
          <div class="kpi-sub">Registered vendors</div>
          <i class="bi bi-truck kpi-bg-icon"></i>
        </div>

      </div>
      <!-- /KPI CARDS -->

      <!-- ── CHARTS ROW ─────────────────────────────────── -->
      <div class="row g-4 section-gap">

        <!-- Donut: Asset Distribution -->
        <div class="col-lg-5">
          <div class="rmu-card h-100">
            <div class="rmu-card-header">
              <div>
                <div class="rmu-card-title">Asset Distribution</div>
                <div class="rmu-card-subtitle">Active assets by class</div>
              </div>
              <a href="../view_assets/" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="rmu-card-body">
              <?php if (empty($distValues)): ?>
                <div class="rmu-empty">
                  <i class="bi bi-pie-chart rmu-empty-icon"></i>
                  <h6>No asset data</h6>
                  <p>Add assets to see the distribution chart.</p>
                </div>
              <?php else: ?>
                <div class="chart-wrap" style="min-height:280px">
                  <canvas id="chartDist"></canvas>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Bar: Monthly Acquisitions -->
        <div class="col-lg-7">
          <div class="rmu-card h-100">
            <div class="rmu-card-header">
              <div>
                <div class="rmu-card-title">Acquisition Trend</div>
                <div class="rmu-card-subtitle">Assets added — last 12 months</div>
              </div>
              <a href="../../reports/" class="btn btn-sm btn-outline-primary">Full Report</a>
            </div>
            <div class="rmu-card-body">
              <?php if (empty($monValues)): ?>
                <div class="rmu-empty">
                  <i class="bi bi-bar-chart rmu-empty-icon"></i>
                  <h6>No acquisition data</h6>
                  <p>Asset additions will appear here.</p>
                </div>
              <?php else: ?>
                <div class="chart-wrap" style="min-height:280px">
                  <canvas id="chartAcq"></canvas>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
      <!-- /CHARTS ROW -->

      <!-- ── ACTIVITY + QUICK ACTIONS ──────────────────── -->
      <div class="row g-4">

        <!-- Activity Feed -->
        <div class="col-lg-7">
          <div class="rmu-card">
            <div class="rmu-card-header">
              <div>
                <div class="rmu-card-title">Recent Activity</div>
                <div class="rmu-card-subtitle">Latest events across the asset register</div>
              </div>
            </div>
            <div class="rmu-card-body" style="padding:0 1.5rem">
              <?php if (empty($activities)): ?>
                <div class="rmu-empty">
                  <i class="bi bi-clock-history rmu-empty-icon"></i>
                  <h6>No recent activity</h6>
                </div>
              <?php else: ?>
                <ul class="activity-list">
                  <?php foreach ($activities as $act): ?>
                  <li class="activity-item">
                    <?= activityIcon($act['kind']) ?>
                    <div class="activity-body">
                      <div class="activity-text">
                        <strong><?= activityVerb($act['kind']) ?></strong>
                        <?= htmlspecialchars(mb_strimwidth($act['lbl'], 0, 80, '…'), ENT_QUOTES) ?>
                      </div>
                      <div class="activity-time">
                        <i class="bi bi-clock me-1"></i><?= timeAgo($act['ts']) ?>
                      </div>
                    </div>
                  </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-5">
          <div class="rmu-card">
            <div class="rmu-card-header">
              <div class="rmu-card-title">Quick Actions</div>
            </div>
            <div class="rmu-card-body">
              <div class="quick-actions">

                <a href="../new_asset/" class="quick-action-btn qa-blue">
                  <span class="qa-icon"><i class="bi bi-plus-circle-dotted"></i></span>
                  Add New Asset
                </a>

                <a href="../view_assets/" class="quick-action-btn qa-cyan">
                  <span class="qa-icon"><i class="bi bi-list-columns-reverse"></i></span>
                  View Active Assets
                </a>

                <a href="../supplier/" class="quick-action-btn qa-purple">
                  <span class="qa-icon"><i class="bi bi-truck"></i></span>
                  Manage Suppliers
                </a>

                <a href="../set_rate/" class="quick-action-btn qa-amber">
                  <span class="qa-icon"><i class="bi bi-currency-dollar"></i></span>
                  Update Dollar Rate
                </a>

                <a href="../asset_users/" class="quick-action-btn qa-green">
                  <span class="qa-icon"><i class="bi bi-people"></i></span>
                  Manage Users
                </a>

                <a href="../../reports/" class="quick-action-btn qa-red">
                  <span class="qa-icon"><i class="bi bi-bar-chart-line"></i></span>
                  Reports Hub
                </a>

              </div>
            </div>
          </div>
        </div>

      </div>
      <!-- /ACTIVITY + QUICK ACTIONS -->

    </main>
    <!-- /PAGE CONTENT -->

    <!-- Footer -->
    <footer style="padding:1rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Version 2.0</span>
      <span>Regional Maritime University &nbsp;·&nbsp; <?= date('Y') ?></span>
    </footer>

  </div><!-- /rmu-main -->
</div><!-- /rmu-layout -->

<!-- ═══ SCRIPTS ══════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
/* ── Sidebar toggle ─────────────────────────────────── */
const toggle  = document.getElementById('sidebarToggle');
const overlay = document.getElementById('sidebarOverlay');
function toggleSidebar() { document.body.classList.toggle('sidebar-open'); }
if (toggle)  toggle.addEventListener('click', toggleSidebar);
if (overlay) overlay.addEventListener('click', toggleSidebar);

/* ── Chart palette ──────────────────────────────────── */
const palette = [
  '#2563eb','#7c3aed','#059669','#d97706',
  '#dc2626','#0891b2','#4338ca','#64748b'
];

/* ── Donut: Asset Distribution ──────────────────────── */
<?php if (!empty($distValues)): ?>
(function(){
  const ctx = document.getElementById('chartDist');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: <?= json_encode($distLabels, JSON_UNESCAPED_UNICODE) ?>,
      datasets: [{
        data: <?= json_encode($distValues) ?>,
        backgroundColor: palette,
        borderColor: '#ffffff',
        borderWidth: 3,
        hoverBorderWidth: 0,
      }]
    },
    options: {
      cutout: '68%',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            font: { size: 11 },
            padding: 14,
            usePointStyle: true,
            pointStyleWidth: 8,
            boxHeight: 8,
          }
        },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.label}: ${ctx.formattedValue} assets`
          }
        }
      }
    }
  });
})();
<?php endif; ?>

/* ── Bar: Acquisition Trend ─────────────────────────── */
<?php if (!empty($monValues)): ?>
(function(){
  const ctx = document.getElementById('chartAcq');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($monLabels) ?>,
      datasets: [{
        label: 'Assets Added',
        data: <?= json_encode($monValues) ?>,
        backgroundColor: 'rgba(37,99,235,0.15)',
        borderColor: '#2563eb',
        borderWidth: 2,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ` ${ctx.formattedValue} assets added`
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { size: 11 } }
        },
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1, font: { size: 11 } },
          grid: { color: 'rgba(0,0,0,0.04)' }
        }
      }
    }
  });
})();
<?php endif; ?>

/* ── Active sidebar link ────────────────────────────── */
(function(){
  const path = window.location.pathname;
  document.querySelectorAll('.sidebar-link[href]').forEach(a => {
    const href = a.getAttribute('href');
    if (href && href !== '#' && path.includes(href.replace(/^\.\.\//, ''))) {
      a.classList.add('active');
      const parent = a.closest('.collapse');
      if (parent) {
        parent.classList.add('show');
        const trigger = document.querySelector(`[data-bs-target="#${parent.id}"]`);
        if (trigger) trigger.setAttribute('aria-expanded','true');
      }
    }
  });
})();
</script>

</body>
</html>
