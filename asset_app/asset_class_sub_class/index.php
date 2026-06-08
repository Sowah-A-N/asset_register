<?php
/**
 * FILE: schedule_officer/asset_class_sub_class/index.php — Asset Sub-Classes (list + add), v2.
 * Add hardened (prepared stmts) + requires catalog.manage. Resolves the parent
 * class name from the selected ast_id. sub_class_code captured (used by the
 * Add-Asset asset-ID generator); provided explicitly (column is NOT NULL).
 */
session_start();
require_once '../../auth.php';
requirePermission('catalog.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

if (isset($_POST['add'])) {
    if (!can('catalog.manage')) { echo "<script>alert('You do not have permission to modify the catalog.');window.location='index.php';</script>"; exit(); }
    $subClass = trim($_POST['sub_class'] ?? '');
    $code     = trim($_POST['sub_class_code'] ?? '');
    $classId  = (int)($_POST['asset_class'] ?? 0);
    if ($subClass === '' || $classId <= 0) { echo "<script>alert('Please enter a sub-class and select a parent class.');window.location='index.php';</script>"; exit(); }

    // Resolve parent class name
    $stmt = mysqli_prepare($conn, "SELECT asset_class FROM asset_classes WHERE ast_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $classId); mysqli_stmt_execute($stmt);
    $className = mysqli_stmt_get_result($stmt)->fetch_assoc()['asset_class'] ?? '';
    mysqli_stmt_close($stmt);
    if ($className === '') { echo "<script>alert('Selected class not found.');window.location='index.php';</script>"; exit(); }

    // Duplicate sub-class?
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM asset_class_sub_classes WHERE sub_class = ?");
    mysqli_stmt_bind_param($stmt, "s", $subClass); mysqli_stmt_execute($stmt);
    $dup = (int)(mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] ?? 0); mysqli_stmt_close($stmt);
    if ($dup > 0) { echo "<script>alert('Sub-class already exists.');window.location='index.php';</script>"; exit(); }

    $stmt = mysqli_prepare($conn, "INSERT INTO asset_class_sub_classes (sub_class, sub_class_code, asset_class) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "sss", $subClass, $code, $className);
    echo mysqli_stmt_execute($stmt) ? "<script>alert('Sub-class added.');window.location='index.php';</script>"
                                    : "<script>alert('Could not add sub-class.');window.location='index.php';</script>";
    exit();
}

$classes = [];
$cr = mysqli_query($conn, "SELECT ast_id, asset_class FROM asset_classes ORDER BY asset_class ASC");
while ($row = mysqli_fetch_assoc($cr)) $classes[] = $row;

$rows = [];
$r = mysqli_query($conn, "SELECT sub_class, sub_class_code, asset_class FROM asset_class_sub_classes ORDER BY asset_class, sub_class ASC");
while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
$canManage = can('catalog.manage');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asset Sub-Classes — RMU Asset Register</title>
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
      <div class="topnav-breadcrumb"><h1>Asset Sub-Classes</h1><small><?= count($rows) ?> sub-class<?= count($rows)!==1?'es':'' ?></small></div>
      <div class="topnav-actions">
        <?php if ($canManage): ?><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scModal"><i class="bi bi-plus-lg me-1"></i>Add Sub-Class</button><?php endif; ?>
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
      <div class="rmu-card">
        <div class="rmu-card-header"><div class="rmu-card-title">Asset Sub-Classes</div>
          <input type="search" class="form-control form-control-sm" id="scSearch" placeholder="Search…" style="max-width:220px"></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($rows)): ?>
            <div class="rmu-empty"><i class="bi bi-diagram-2 rmu-empty-icon"></i><h6>No sub-classes</h6></div>
          <?php else: ?>
            <table class="rmu-table" id="scTable" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Sub-Class</th><th>Code</th><th>Parent Class</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($rows as $s): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($s['sub_class']) ?></td>
                    <td><?= $s['sub_class_code'] !== '' ? '<code style="font-size:.74rem">'.htmlspecialchars($s['sub_class_code']).'</code>' : '<span class="text-muted">—</span>' ?></td>
                    <td><?= htmlspecialchars(trim($s['asset_class'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </main>
    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Asset Sub-Classes</span><span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>
<?php if ($canManage): ?>
<div class="modal fade" id="scModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="index.php">
      <div class="modal-header"><h5 class="modal-title">Add Sub-Class</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Parent Asset Class</label>
          <select class="form-select" name="asset_class" required>
            <option value="" hidden>Select class…</option>
            <?php foreach ($classes as $c): ?>
              <option value="<?= (int)$c['ast_id'] ?>"><?= htmlspecialchars(trim($c['asset_class'])) ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="mb-3"><label class="form-label">Sub-Class Name</label>
          <input type="text" class="form-control" name="sub_class" required></div>
        <div class="mb-3"><label class="form-label">Sub-Class Code <span class="text-muted">(optional)</span></label>
          <input type="text" class="form-control" name="sub_class_code" placeholder="Used in the generated Asset ID"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary btn-sm">Add Sub-Class</button></div>
    </form>
  </div></div>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('scSearch')?.addEventListener('input', function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#scTable tbody tr').forEach(tr=>tr.style.display=tr.innerText.toLowerCase().includes(q)?'':'none');
});
</script>
</body>
</html>
