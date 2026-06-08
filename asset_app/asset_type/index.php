<?php
/**
 * FILE: schedule_officer/asset_type/index.php — Asset Types (list + add), v2.
 * Add handler hardened (prepared stmt) + requires catalog.manage.
 */
session_start();
require_once '../../auth.php';
requirePermission('catalog.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

if (isset($_POST['add'])) {
    if (!can('catalog.manage')) { echo "<script>alert('You do not have permission to modify the catalog.');window.location='index.php';</script>"; exit(); }
    $name = trim($_POST['name'] ?? '');
    if ($name === '') { echo "<script>alert('Please enter an asset type.');window.location='index.php';</script>"; exit(); }
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM asset_type WHERE asset_type = ?");
    mysqli_stmt_bind_param($stmt, "s", $name); mysqli_stmt_execute($stmt);
    $dup = (int)(mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] ?? 0); mysqli_stmt_close($stmt);
    if ($dup > 0) { echo "<script>alert('Asset type already exists.');window.location='index.php';</script>"; exit(); }
    $stmt = mysqli_prepare($conn, "INSERT INTO asset_type (asset_type) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "s", $name);
    echo mysqli_stmt_execute($stmt)
       ? "<script>alert('Asset type added.');window.location='index.php';</script>"
       : "<script>alert('Could not add asset type.');window.location='index.php';</script>";
    exit();
}

$types = [];
$r = mysqli_query($conn, "SELECT type_id, asset_type FROM asset_type ORDER BY asset_type ASC");
while ($row = mysqli_fetch_assoc($r)) $types[] = $row;
$canManage = can('catalog.manage');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asset Types — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="rmu-layout">
  <?php require '../partials/sidebar.php'; ?>
  <div class="rmu-main">
    <header class="rmu-topnav">
      <button class="topnav-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <div class="topnav-breadcrumb"><h1>Asset Types</h1>
        <small><?= count($types) ?> type<?= count($types) !== 1 ? 's' : '' ?> defined</small></div>
      <div class="topnav-actions">
        <?php if ($canManage): ?><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tModal"><i class="bi bi-plus-lg me-1"></i>Add Type</button><?php endif; ?>
        <div class="topnav-divider"></div>
        <div class="dropdown">
          <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
            <div class="av-circle"><?= $userInitial ?></div>
            <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">Schedule Officer</div></div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../dashboard/"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
          </ul>
        </div>
      </div>
    </header>
    <main class="rmu-content">
      <div class="rmu-card" style="max-width:640px">
        <div class="rmu-card-header"><div class="rmu-card-title">Asset Types</div></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($types)): ?>
            <div class="rmu-empty"><i class="bi bi-layers rmu-empty-icon"></i><h6>No asset types</h6></div>
          <?php else: ?>
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Asset Type</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($types as $t): ?>
                  <tr><td><?= $sn++ ?></td><td><?= htmlspecialchars($t['asset_type']) ?></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </main>
    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Asset Types</span><span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>
<?php if ($canManage): ?>
<div class="modal fade" id="tModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="index.php">
      <div class="modal-header"><h5 class="modal-title">Add Asset Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><label class="form-label">Asset Type</label>
        <input type="text" class="form-control" name="name" placeholder="e.g. Owned, Leased" required></div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary btn-sm">Add Type</button></div>
    </form>
  </div></div>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
</script>
</body>
</html>
