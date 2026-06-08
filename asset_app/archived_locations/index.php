<?php
/** FILE: schedule_officer/archived_locations/index.php — Archived locations (read-only), v2. */
session_start();
require_once '../../auth.php';
requirePermission('asset.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";
$rows = [];
$r = mysqli_query($conn, "SELECT loc_id, location FROM asset_location_archive ORDER BY location ASC");
while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Archived Locations — RMU Asset Register</title>
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
      <div class="topnav-breadcrumb"><h1>Archived Locations</h1><small><?= count($rows) ?> archived</small></div>
      <div class="topnav-actions"><div class="dropdown">
        <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
          <div class="av-circle"><?= $userInitial ?></div>
          <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">Schedule Officer</div></div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="../dashboard/"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>
          <li><a class="dropdown-item" href="../view_asset_location/"><i class="bi bi-geo-alt me-2"></i>Active Locations</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
        </ul>
      </div></div>
    </header>
    <main class="rmu-content">
      <div class="rmu-card" style="max-width:640px">
        <div class="rmu-card-header"><div class="rmu-card-title">Archived Locations</div></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($rows)): ?>
            <div class="rmu-empty"><i class="bi bi-archive rmu-empty-icon"></i><h6>No archived locations</h6></div>
          <?php else: ?>
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Location</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($rows as $l): ?>
                  <tr><td><?= $sn++ ?></td><td><?= htmlspecialchars($l['location']) ?></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </main>
    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Archived Locations</span><span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
</script>
</body>
</html>
