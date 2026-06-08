<?php
/**
 * FILE: asset_app/partials/chrome_open.php
 * PURPOSE: Adaptive page chrome (open). Operational roles (Schedule Officer /
 *          System Admin) get the full sidebar layout; read-only / oversight
 *          roles reaching the page from the Portal get a neutral, sidebar-free
 *          top-bar with a "Portal" back link.
 *
 * Set BEFORE including:  $pageTitle, $pageSubtitle (HTML ok), $username,
 *                        $userInitial, and optionally $pageActions (HTML for
 *                        topbar buttons, shown in the operational layout).
 * Pair with chrome_close.php.
 */
$RMU_OP       = function_exists('can') && (can('asset.create') || can('catalog.manage'));
$pageTitle    = $pageTitle    ?? '';
$pageSubtitle = $pageSubtitle ?? '';
$username     = $username     ?? '';
$userInitial  = $userInitial  ?? '';
$pageActions  = $pageActions  ?? '';
if ($RMU_OP):
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="rmu-layout">
  <?php require __DIR__ . '/sidebar.php'; ?>
  <div class="rmu-main">
    <header class="rmu-topnav">
      <button class="topnav-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <div class="topnav-breadcrumb"><h1><?= htmlspecialchars($pageTitle) ?></h1><small><?= $pageSubtitle ?></small></div>
      <div class="topnav-actions">
        <?= $pageActions ?>
        <?php if ($pageActions): ?><div class="topnav-divider"></div><?php endif; ?>
        <div class="dropdown">
          <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
            <div class="av-circle"><?= $userInitial ?></div>
            <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">Schedule Officer</div></div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../dashboard/"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>
            <li><a class="dropdown-item" href="../../account/"><i class="bi bi-shield-lock me-2"></i>Change Password</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
          </ul>
        </div>
      </div>
    </header>
    <main class="rmu-content">
<?php else: ?>
<header class="report-topbar">
  <img src="../../assets/img/rmu.png" alt="RMU" style="height:34px;width:34px;object-fit:contain;background:#fff;border-radius:6px">
  <div class="topnav-breadcrumb" style="flex:1">
    <h1 style="font-size:1rem;font-weight:700;margin:0"><?= htmlspecialchars($pageTitle) ?></h1>
    <small class="text-muted"><?= $pageSubtitle ?></small>
  </div>
  <a href="../../portal/" class="btn btn-outline-secondary btn-sm"><i class="bi bi-grid-1x2 me-1"></i>Portal</a>
  <div class="dropdown">
    <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
      <div class="av-circle"><?= $userInitial ?></div>
      <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">Portal</div></div>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="../../portal/"><i class="bi bi-grid-1x2 me-2"></i>Portal</a></li>
      <li><a class="dropdown-item" href="../../account/"><i class="bi bi-shield-lock me-2"></i>Change Password</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="../../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
    </ul>
  </div>
</header>
<main class="rmu-content-full" style="max-width:1150px;margin:0 auto">
<?php endif; ?>
