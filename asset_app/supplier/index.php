<?php
/**
 * FILE:    schedule_officer/supplier/index.php
 * PURPOSE: View, add and archive suppliers — v2 redesign.
 *
 * FIXES: archive handler referenced the wrong database
 * (asset_register.suppliers_archive) — corrected to the current DB; both
 * handlers now use prepared statements and require supplier.manage.
 */

session_start();
require_once '../../auth.php';
requirePermission('supplier.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

/* ── Add supplier ─────────────────────────────────────────── */
if (isset($_POST['add'])) {
    if (!can('supplier.manage')) { echo "<script>alert('You do not have permission to manage suppliers.');window.location='index.php';</script>"; exit(); }
    $name = trim($_POST['name'] ?? ''); $loc = trim($_POST['location'] ?? ''); $num = trim($_POST['number'] ?? '');
    if ($name === '' || $loc === '' || $num === '') {
        echo "<script>alert('Please fill all fields.');window.location='index.php';</script>"; exit();
    }
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM suppliers WHERE name = ?");
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $dup = (int)(mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] ?? 0);
    mysqli_stmt_close($stmt);
    if ($dup > 0) {
        echo "<script>alert('Supplier already exists.');window.location='index.php';</script>"; exit();
    }
    $stmt = mysqli_prepare($conn, "INSERT INTO suppliers (name, location, number) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $name, $loc, $num);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Supplier added successfully.');window.location='index.php';</script>";
    } else {
        error_log('[RMU] supplier insert failed: ' . mysqli_error($conn));
        echo "<script>alert('Could not add supplier. Please try again.');window.location='index.php';</script>";
    }
    exit();
}

/* ── Archive supplier (move to suppliers_archive, current DB) ── */
if (isset($_POST['archive_supplier'])) {
    if (!can('supplier.manage')) { echo "<script>alert('You do not have permission to manage suppliers.');window.location='index.php';</script>"; exit(); }
    $id = (int)($_POST['id'] ?? 0);
    mysqli_autocommit($conn, false);
    $ok1 = false; $ok2 = false;
    if ($stmt = mysqli_prepare($conn, "INSERT INTO suppliers_archive (sup_id, name, location, number) SELECT sup_id, name, location, number FROM suppliers WHERE sup_id = ?")) {
        mysqli_stmt_bind_param($stmt, "i", $id); $ok1 = mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    }
    if ($ok1 && ($stmt = mysqli_prepare($conn, "DELETE FROM suppliers WHERE sup_id = ?"))) {
        mysqli_stmt_bind_param($stmt, "i", $id); $ok2 = mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    }
    if ($ok1 && $ok2) { mysqli_commit($conn); echo "<script>alert('Supplier archived.');window.location='index.php';</script>"; }
    else { mysqli_rollback($conn); echo "<script>alert('Could not archive supplier.');window.location='index.php';</script>"; }
    mysqli_autocommit($conn, true);
    exit();
}

/* ── Supplier list ── */
$suppliers = [];
$r = mysqli_query($conn, "SELECT sup_id, name, location, number FROM suppliers ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($r)) $suppliers[] = $row;
$canManage = can('supplier.manage');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers — RMU Asset Register</title>
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
      <div class="topnav-breadcrumb">
        <h1>Suppliers</h1>
        <small><?= count($suppliers) ?> registered vendor<?= count($suppliers) !== 1 ? 's' : '' ?></small>
      </div>
      <div class="topnav-actions">
        <?php if ($canManage): ?>
          <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#supModal"><i class="bi bi-plus-lg me-1"></i>Add Supplier</button>
        <?php endif; ?>
        <div class="topnav-divider"></div>
        <div class="dropdown">
          <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
            <div class="av-circle"><?= $userInitial ?></div>
            <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">Schedule Officer</div></div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../dashboard/"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>
            <li><a class="dropdown-item" href="../archived_suppliers/"><i class="bi bi-archive me-2"></i>Archived Suppliers</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
          </ul>
        </div>
      </div>
    </header>

    <main class="rmu-content">
      <div class="rmu-card">
        <div class="rmu-card-header">
          <div class="rmu-card-title">Supplier Register</div>
          <input type="search" class="form-control form-control-sm" id="supSearch" placeholder="Search suppliers…" style="max-width:240px">
        </div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($suppliers)): ?>
            <div class="rmu-empty"><i class="bi bi-truck rmu-empty-icon"></i><h6>No suppliers yet</h6>
              <?php if ($canManage): ?><p>Click “Add Supplier” to register one.</p><?php endif; ?></div>
          <?php else: ?>
            <table class="rmu-table" id="supTable" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Name</th><th>Location</th><th>Contact</th><?php if ($canManage): ?><th></th><?php endif; ?></tr></thead>
              <tbody>
                <?php $sn = 1; foreach ($suppliers as $s): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['location']) ?></td>
                    <td><?= htmlspecialchars($s['number']) ?></td>
                    <?php if ($canManage): ?>
                    <td style="text-align:right">
                      <form action="index.php" method="POST" onsubmit="return confirm('Archive supplier &quot;<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>&quot;?');" style="margin:0">
                        <input type="hidden" name="id" value="<?= (int)$s['sup_id'] ?>">
                        <button type="submit" name="archive_supplier" class="btn btn-sm btn-outline-danger"><i class="bi bi-archive me-1"></i>Archive</button>
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
      <span>RMU Asset Register &nbsp;·&nbsp; Suppliers</span>
      <span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="supModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="index.php">
        <div class="modal-header"><h5 class="modal-title">Add Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" required oninput="cap(this)"></div>
          <div class="mb-3"><label class="form-label">Location</label>
            <input type="text" class="form-control" name="location" required oninput="cap(this)"></div>
          <div class="mb-3"><label class="form-label">Contact Number</label>
            <input type="text" class="form-control" name="number" minlength="10" maxlength="15" required></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add" class="btn btn-primary btn-sm">Add Supplier</button>
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
// client-side filter
document.getElementById('supSearch')?.addEventListener('input', function(){
  const q = this.value.toLowerCase();
  document.querySelectorAll('#supTable tbody tr').forEach(tr =>
    tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none');
});
</script>
</body>
</html>
