<?php
/**
 * FILE: schedule_officer/view_asset_location/index.php — Asset Locations (list/add/archive), v2.
 * FIXES: archive referenced the wrong database (asset_register.asset_location_archive)
 * → corrected to the current DB; prepared statements + transaction; catalog.manage gating.
 */
session_start();
require_once '../../auth.php';
requirePermission('catalog.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

if (isset($_POST['add'])) {
    if (!can('catalog.manage')) { echo "<script>alert('You do not have permission to modify the catalog.');window.location='index.php';</script>"; exit(); }
    $loc = trim($_POST['location'] ?? '');
    if ($loc === '') { echo "<script>alert('Please enter a location.');window.location='index.php';</script>"; exit(); }
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM asset_location WHERE location = ?");
    mysqli_stmt_bind_param($stmt, "s", $loc); mysqli_stmt_execute($stmt);
    $dup = (int)(mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] ?? 0); mysqli_stmt_close($stmt);
    if ($dup > 0) { echo "<script>alert('Location already exists.');window.location='index.php';</script>"; exit(); }
    $stmt = mysqli_prepare($conn, "INSERT INTO asset_location (location) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "s", $loc);
    echo mysqli_stmt_execute($stmt) ? "<script>alert('Location added.');window.location='index.php';</script>"
                                    : "<script>alert('Could not add location.');window.location='index.php';</script>";
    exit();
}

if (isset($_POST['archive_location'])) {
    if (!can('catalog.manage')) { echo "<script>alert('You do not have permission to modify the catalog.');window.location='index.php';</script>"; exit(); }
    $id = (int)($_POST['id'] ?? 0);
    mysqli_begin_transaction($conn); $ok = true;
    if ($s = mysqli_prepare($conn, "INSERT INTO asset_location_archive (loc_id, location, loc_code) SELECT loc_id, location, loc_code FROM asset_location WHERE loc_id = ?")) {
        mysqli_stmt_bind_param($s, "i", $id); $ok = mysqli_stmt_execute($s); mysqli_stmt_close($s);
    } else $ok = false;
    if ($ok && ($s = mysqli_prepare($conn, "DELETE FROM asset_location WHERE loc_id = ?"))) {
        mysqli_stmt_bind_param($s, "i", $id); $ok = mysqli_stmt_execute($s); mysqli_stmt_close($s);
    } else $ok = false;
    if ($ok) { mysqli_commit($conn); echo "<script>alert('Location archived.');window.location='index.php';</script>"; }
    else { mysqli_rollback($conn); echo "<script>alert('Could not archive location.');window.location='index.php';</script>"; }
    mysqli_autocommit($conn, true); exit();
}

$rows = [];
$r = mysqli_query($conn, "SELECT loc_id, location FROM asset_location ORDER BY location ASC");
while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
$canManage = can('catalog.manage');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asset Locations — RMU Asset Register</title>
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
      <div class="topnav-breadcrumb"><h1>Asset Locations</h1><small><?= count($rows) ?> location<?= count($rows)!==1?'s':'' ?></small></div>
      <div class="topnav-actions">
        <?php if ($canManage): ?><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#lModal"><i class="bi bi-plus-lg me-1"></i>Add Location</button><?php endif; ?>
        <div class="topnav-divider"></div>
        <div class="dropdown">
          <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
            <div class="av-circle"><?= $userInitial ?></div>
            <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">Schedule Officer</div></div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../dashboard/"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>
            <li><a class="dropdown-item" href="../archived_locations/"><i class="bi bi-archive me-2"></i>Archived Locations</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
          </ul>
        </div>
      </div>
    </header>
    <main class="rmu-content">
      <div class="rmu-card" style="max-width:720px">
        <div class="rmu-card-header"><div class="rmu-card-title">Asset Locations</div>
          <input type="search" class="form-control form-control-sm" id="lSearch" placeholder="Search…" style="max-width:200px"></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($rows)): ?>
            <div class="rmu-empty"><i class="bi bi-geo-alt rmu-empty-icon"></i><h6>No locations</h6></div>
          <?php else: ?>
            <table class="rmu-table" id="lTable" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Location</th><?php if ($canManage): ?><th></th><?php endif; ?></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($rows as $l): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($l['location']) ?></td>
                    <?php if ($canManage): ?>
                    <td style="text-align:right">
                      <form action="index.php" method="POST" onsubmit="return confirm('Archive location &quot;<?= htmlspecialchars($l['location'], ENT_QUOTES) ?>&quot;?');" style="margin:0">
                        <input type="hidden" name="id" value="<?= (int)$l['loc_id'] ?>">
                        <button type="submit" name="archive_location" class="btn btn-sm btn-outline-danger"><i class="bi bi-archive me-1"></i>Archive</button>
                      </form>
                    </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </main>
    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Asset Locations</span><span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>
<?php if ($canManage): ?>
<div class="modal fade" id="lModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="index.php">
      <div class="modal-header"><h5 class="modal-title">Add Location</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><label class="form-label">Location</label>
        <input type="text" class="form-control" name="location" placeholder="e.g. ICT Lab" required></div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary btn-sm">Add Location</button></div>
    </form>
  </div></div>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('lSearch')?.addEventListener('input', function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#lTable tbody tr').forEach(tr=>tr.style.display=tr.innerText.toLowerCase().includes(q)?'':'none');
});
</script>
</body>
</html>
