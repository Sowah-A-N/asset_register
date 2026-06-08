<?php
/**
 * FILE: /portal/index.php — Role-aware landing (Phase C).
 *
 * The home for oversight / finance / audit / admin roles after sign-in.
 * Shows only the tiles the user's RBAC permissions allow. The financial
 * reports are linked directly (they live under schedule_officer/reports/ but
 * are report.view-guarded, so any permitted role can open them).
 */
require_once '../auth.php';
requireAuth('../login/');
require_once '../config.php';   // $conn

$username = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$initial  = mb_strtoupper(mb_substr($username, 0, 1));
$roles    = userRoles();

$ROLE_NAMES = [
  'schedule_officer'=>'Schedule Officer','dsu'=>'DSU','accountant'=>'Accountant',
  'budget_officer'=>'Budget Officer','sia'=>'Senior Internal Auditor',
  'director_finance'=>'Director of Finance','system_admin'=>'System Administrator',
];
$roleLabel = implode(' · ', array_map(fn($r)=>$ROLE_NAMES[$r] ?? $r, $roles)) ?: 'User';

/* Light KPI summary (shown to anyone who can view reports or assets) */
$kpi = ['assets'=>0,'cost'=>0.0,'rate'=>0.0,'classes'=>0];
if (can('report.view') || can('asset.view')) {
    $kpi['assets']  = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM assets WHERE disposals=0"))['c'] ?? 0);
    $kpi['cost']    = (float)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(additions),0) s FROM assets WHERE disposals=0"))['s'] ?? 0);
    $kpi['rate']    = (float)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"))['dollar_rate'] ?? 0);
    $kpi['classes'] = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM asset_classes"))['c'] ?? 0);
}
function money($n){ if($n>=1e6) return number_format($n/1e6,2).'M'; if($n>=1e3) return number_format($n/1e3,2).'K'; return number_format($n,2); }

$SO = '../asset_app';   // where the report/audit pages physically live
/* Tile = [title, subtitle, href, icon, colorClass, show?] */
$tiles = [];
if (can('report.view')) {
    $tiles[] = ['Class Depreciation (GHS)','Per-asset schedule + brought-forward pool',"../reports/class_reports1/",'bi-bar-chart-steps','qa-blue'];
    $tiles[] = ['Class Depreciation (USD)','Historical rate (IAS 21)',"../reports/class_reports_usd/",'bi-bar-chart-steps','qa-cyan'];
    $tiles[] = ['Asset Summary (GHS)','Movement, one row per class',"../reports/asset_summary/",'bi-table','qa-green'];
    $tiles[] = ['Asset Summary (USD)','Movement, one row per class',"../reports/asset_summary_usd/",'bi-table','qa-purple'];
    $tiles[] = ['Quarterly Depreciation','Q1–Q4 · USD + GHS',"../reports/quarterly/",'bi-calendar3','qa-amber'];
}
if (can('asset.view')) {
    $tiles[] = ['Asset Register','Browse active assets (read-only)',"$SO/view_assets/",'bi-list-ul','qa-blue'];
    $tiles[] = ['Untracked Assets','Assets pending tagging',"$SO/view_untracked/",'bi-eye','qa-slate'];
    $tiles[] = ['Asset Movements','Location / custodian transfer log',"$SO/moved_assets/",'bi-arrow-left-right','qa-cyan'];
    $tiles[] = ['Reclassification Log','Class changes audit trail',"$SO/reclassify/",'bi-shuffle','qa-purple'];
    $tiles[] = ['Disposals','Disposed assets',"$SO/disposals/",'bi-trash3','qa-red'];
    $tiles[] = ['Archived Assets','Retired from the register',"$SO/archived_assets/",'bi-archive','qa-amber'];
}
if (can('catalog.view')) {
    $tiles[] = ['Asset Classes','Classes & useful lives',"$SO/view_asset_class/",'bi-tags','qa-green'];
    $tiles[] = ['Locations','Asset locations',"$SO/view_asset_location/",'bi-geo-alt','qa-cyan'];
    $tiles[] = ['Asset Types','Type catalogue',"$SO/asset_type/",'bi-layers','qa-purple'];
}
if (can('asset.create') || can('catalog.manage')) {
    $tiles[] = ['Asset Management','Full operational app',"$SO/dashboard/",'bi-grid-1x2','qa-green'];
}
if (can('rate.view')) {
    $tiles[] = ['Dollar Rate','Exchange-rate history',"$SO/set_rate/",'bi-currency-exchange','qa-blue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/v2.css">
  <link rel="shortcut icon" href="../assets/img/rmulog.png">
</head>
<body class="bg-page">
  <header class="rmu-topnav" style="margin:0">
    <div class="sidebar-brand" style="border:none;height:auto;padding:0">
      <img src="../assets/img/rmulog.png" alt="RMU" class="sidebar-brand-logo" style="background:#fff;object-fit:contain;padding:3px">
      <div><div class="sidebar-brand-name" style="color:var(--text-primary)">Asset Register</div>
        <div class="sidebar-brand-sub">Portal</div></div>
    </div>
    <div style="flex:1"></div>
    <div class="topnav-actions">
      <div class="dropdown">
        <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
          <div class="av-circle"><?= $initial ?></div>
          <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role"><?= htmlspecialchars($roleLabel) ?></div></div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><span class="dropdown-item-text"><small class="text-muted"><?= htmlspecialchars($roleLabel) ?></small></span></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="../account/"><i class="bi bi-shield-lock me-2"></i>Change Password</a></li>
          <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
        </ul>
      </div>
    </div>
  </header>

  <main class="rmu-content" style="max-width:1100px;margin:0 auto">
    <div class="page-header">
      <div>
        <h2 class="page-header-title">Welcome, <?= $username ?></h2>
        <p class="page-header-subtitle"><?= htmlspecialchars($roleLabel) ?> · choose a destination below</p>
      </div>
    </div>

    <?php if (can('report.view') || can('asset.view')): ?>
    <div class="kpi-grid section-gap" style="grid-template-columns:repeat(4,1fr)">
      <div class="kpi-card kpi-blue"><div class="kpi-label">Active Assets</div><div class="kpi-value num"><?= number_format($kpi['assets']) ?></div></div>
      <div class="kpi-card kpi-green"><div class="kpi-label">Total Cost (GHS)</div><div class="kpi-value num" style="font-size:1.3rem">GH₵ <?= money($kpi['cost']) ?></div></div>
      <div class="kpi-card kpi-cyan"><div class="kpi-label">Active Dollar Rate</div><div class="kpi-value num"><?= $kpi['rate']>0?'GH₵'.number_format($kpi['rate'],2):'—' ?></div></div>
      <div class="kpi-card kpi-purple"><div class="kpi-label">Asset Classes</div><div class="kpi-value num"><?= number_format($kpi['classes']) ?></div></div>
    </div>
    <?php endif; ?>

    <?php if (empty($tiles)): ?>
      <div class="rmu-card"><div class="rmu-card-body"><div class="rmu-empty">
        <i class="bi bi-shield-lock rmu-empty-icon"></i><h6>No modules available</h6>
        <p>Your account has no permissions assigned yet. Contact your administrator.</p></div></div></div>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach ($tiles as [$t,$s,$href,$icon,$cls]): ?>
          <div class="col-md-6 col-lg-4">
            <a href="<?= $href ?>" class="quick-action-btn <?= $cls ?>" style="align-items:flex-start;height:100%">
              <span class="qa-icon"><i class="bi <?= $icon ?>"></i></span>
              <span><span style="display:block"><?= htmlspecialchars($t) ?></span>
                <small class="text-muted" style="font-weight:400"><?= htmlspecialchars($s) ?></small></span>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (can('user.manage')): ?>
      <a href="../admin/" class="rmu-card mt-4 d-block" style="text-decoration:none">
        <div class="rmu-card-body d-flex align-items-center gap-3">
          <span class="qa-icon" style="width:44px;height:44px;background:#ede9fe;color:var(--c-purple);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem"><i class="bi bi-people-fill"></i></span>
          <div style="flex:1"><div style="font-weight:600;color:var(--text-primary)">User &amp; Role Management</div>
            <small class="text-muted">Create accounts, assign roles &amp; permissions.</small></div>
          <i class="bi bi-chevron-right text-muted"></i>
        </div>
      </a>
    <?php endif; ?>
  </main>

  <footer style="padding:1rem 1.75rem;text-align:center;font-size:.75rem;color:var(--text-muted)">
    RMU Asset Register · Version 2.0 · <?= date('Y') ?> · Regional Maritime University
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
