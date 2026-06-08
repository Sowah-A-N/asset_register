<?php
/**
 * FILE:    schedule_officer/asset_users/index.php
 * PURPOSE: View and add asset users (staff) — v2 redesign.
 * FIXES: add handler now uses prepared statements (was SQLi) and requires
 * assetuser.manage; department dropdown uses a safe while loop.
 */

session_start();
require_once '../../auth.php';
requirePermission('assetuser.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

if (isset($_POST['add'])) {
    if (!can('assetuser.manage')) { echo "<script>alert('You do not have permission to manage users.');window.location='index.php';</script>"; exit(); }
    $fn = trim($_POST['F_name'] ?? ''); $ln = trim($_POST['L_name'] ?? '');
    $sid = trim($_POST['Staff_ID'] ?? ''); $depId = (int)($_POST['department'] ?? 0);
    if ($fn === '' || $ln === '' || $sid === '' || $depId <= 0) {
        echo "<script>alert('Please fill all fields.');window.location='index.php';</script>"; exit();
    }
    // Resolve department name
    $stmt = mysqli_prepare($conn, "SELECT dep_name FROM department WHERE t_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $depId); mysqli_stmt_execute($stmt);
    $depName = mysqli_stmt_get_result($stmt)->fetch_assoc()['dep_name'] ?? '';
    mysqli_stmt_close($stmt);
    // Duplicate staff ID?
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM asset_users WHERE staff_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $sid); mysqli_stmt_execute($stmt);
    $dup = (int)(mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] ?? 0);
    mysqli_stmt_close($stmt);
    if ($dup > 0) { echo "<script>alert('Staff ID already exists.');window.location='index.php';</script>"; exit(); }
    $stmt = mysqli_prepare($conn, "INSERT INTO asset_users (staff_id, staff_first_name, staff_last_name, department) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssss", $sid, $fn, $ln, $depName);
    if (mysqli_stmt_execute($stmt)) echo "<script>alert('User added successfully.');window.location='index.php';</script>";
    else { error_log('[RMU] asset_user insert failed: '.mysqli_error($conn)); echo "<script>alert('Could not add user.');window.location='index.php';</script>"; }
    exit();
}

$users = [];
$r = mysqli_query($conn, "SELECT staff_id, staff_first_name, staff_last_name, department FROM asset_users ORDER BY staff_first_name ASC");
while ($row = mysqli_fetch_assoc($r)) $users[] = $row;

$depts = [];
$dr = mysqli_query($conn, "SELECT t_id, dep_name FROM department ORDER BY dep_name ASC");
while ($row = mysqli_fetch_assoc($dr)) $depts[] = $row;

$canManage = can('assetuser.manage');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asset Users — RMU Asset Register</title>
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
      <div class="topnav-breadcrumb"><h1>Asset Users</h1>
        <small><?= count($users) ?> staff member<?= count($users) !== 1 ? 's' : '' ?></small></div>
      <div class="topnav-actions">
        <?php if ($canManage): ?>
          <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModal"><i class="bi bi-person-plus me-1"></i>Add User</button>
        <?php endif; ?>
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
        <div class="rmu-card-header">
          <div class="rmu-card-title">Staff Register</div>
          <input type="search" class="form-control form-control-sm" id="uSearch" placeholder="Search users…" style="max-width:240px">
        </div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($users)): ?>
            <div class="rmu-empty"><i class="bi bi-people rmu-empty-icon"></i><h6>No users yet</h6></div>
          <?php else: ?>
            <table class="rmu-table" id="uTable" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Staff ID</th><th>First Name</th><th>Last Name</th><th>Department</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($users as $u): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($u['staff_id']) ?></td>
                    <td><?= htmlspecialchars($u['staff_first_name']) ?></td>
                    <td><?= htmlspecialchars($u['staff_last_name']) ?></td>
                    <td><?= htmlspecialchars($u['department'] ?? '—') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </main>
    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Asset Users</span><span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="index.php">
        <div class="modal-header"><h5 class="modal-title">Add Asset User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-6"><label class="form-label">First Name</label>
              <input type="text" class="form-control" name="F_name" required oninput="cap(this)"></div>
            <div class="col-6"><label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="L_name" required oninput="cap(this)"></div>
            <div class="col-12"><label class="form-label">Staff ID</label>
              <input type="text" class="form-control" name="Staff_ID" required></div>
            <div class="col-12"><label class="form-label">Department</label>
              <select class="form-select" name="department" required>
                <option value="" hidden>Select department…</option>
                <?php foreach ($depts as $d): ?>
                  <option value="<?= (int)$d['t_id'] ?>"><?= htmlspecialchars($d['dep_name']) ?></option>
                <?php endforeach; ?>
              </select></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add" class="btn btn-primary btn-sm">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
function cap(i){ i.value = i.value.replace(/\b\w/g, c => c.toUpperCase()); }
document.getElementById('uSearch')?.addEventListener('input', function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#uTable tbody tr').forEach(tr=>tr.style.display=tr.innerText.toLowerCase().includes(q)?'':'none');
});
</script>
</body>
</html>
