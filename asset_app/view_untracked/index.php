<?php
/**
 * FILE: schedule_officer/view_untracked/index.php — Untracked asset disposals (list + add), v2.
 * Consolidates the old separate "Log Untracked" form into a modal here.
 * Add handler hardened (prepared stmts) + gated on asset.create.
 */
session_start();
require_once '../../auth.php';
requirePermission('asset.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

if (isset($_POST['add_untracked'])) {
    if (!can('asset.create')) { echo "<script>alert('You do not have permission to add untracked assets.');window.location='index.php';</script>"; exit(); }
    $name = trim($_POST['asset_name'] ?? '');
    $value = (float)($_POST['asset_value'] ?? 0);
    $classId = (int)($_POST['asset_class'] ?? 0);
    $acq = trim($_POST['acquisition_date'] ?? '');
    $dis = trim($_POST['disposal_date'] ?? '');
    $rate = (float)($_POST['dollar_rate'] ?? 0);
    $da = DateTime::createFromFormat('Y-m-d', $acq);
    if ($name === '' || $classId <= 0 || !$da) { echo "<script>alert('Please fill name, class and a valid acquisition date.');window.location='index.php';</script>"; exit(); }
    $disVal = ($dis !== '' && DateTime::createFromFormat('Y-m-d', $dis)) ? $dis : null;
    // resolve class name
    $stmt = mysqli_prepare($conn, "SELECT asset_class FROM asset_classes WHERE ast_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $classId); mysqli_stmt_execute($stmt);
    $className = mysqli_stmt_get_result($stmt)->fetch_assoc()['asset_class'] ?? '';
    mysqli_stmt_close($stmt);
    if ($className === '') { echo "<script>alert('Selected class not found.');window.location='index.php';</script>"; exit(); }
    $stmt = mysqli_prepare($conn, "INSERT INTO untracked_asset_disposals (class, name, value, acquisition_date, date_of_disposal, dollar_rate) VALUES (?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssdssd", $className, $name, $value, $acq, $disVal, $rate);
    echo mysqli_stmt_execute($stmt) ? "<script>alert('Untracked asset recorded.');window.location='index.php';</script>"
                                    : "<script>alert('Could not save. Please try again.');window.location='index.php';</script>";
    exit();
}

$rows = [];
$r = mysqli_query($conn, "SELECT name, class, value, acquisition_date, date_of_disposal, dollar_rate FROM untracked_asset_disposals ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;

$classes = [];
$cr = mysqli_query($conn, "SELECT ast_id, asset_class FROM asset_classes ORDER BY asset_class ASC");
while ($row = mysqli_fetch_assoc($cr)) $classes[] = $row;

$activeRate = (float)(mysqli_fetch_assoc(mysqli_query($conn, "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"))['dollar_rate'] ?? 0);
$canCreate = can('asset.create');
function money($n){ return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Untracked Assets — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
</head>
<body>
<?php
  $pageTitle    = 'Untracked Assets';
  $pageSubtitle = count($rows) . ' record' . (count($rows) !== 1 ? 's' : '') . ' · assets not in the main register';
  $pageActions  = $canCreate ? '<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#utModal"><i class="bi bi-plus-lg me-1"></i>Log Untracked Asset</button>' : '';
  require '../partials/chrome_open.php';
?>
      <div class="rmu-card">
        <div class="rmu-card-header"><div class="rmu-card-title">Untracked Asset Disposals</div>
          <small class="text-muted">Assets recorded outside the itemised register</small></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($rows)): ?>
            <div class="rmu-empty"><i class="bi bi-box rmu-empty-icon"></i><h6>No untracked assets</h6></div>
          <?php else: ?>
            <div class="rmu-table-wrapper" style="box-shadow:none;border:none">
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Asset Name</th><th>Class</th><th class="num" style="text-align:right">Value (GHS)</th><th>Acquired</th><th>Disposed</th><th>Rate</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($rows as $r): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars(trim($r['class'])) ?></td>
                    <td style="text-align:right;font-variant-numeric:tabular-nums"><?= money($r['value']) ?></td>
                    <td><?= $r['acquisition_date'] && $r['acquisition_date']!=='0000-00-00' ? date('d M Y', strtotime($r['acquisition_date'])) : '—' ?></td>
                    <td><?= $r['date_of_disposal'] && $r['date_of_disposal']!=='0000-00-00' ? date('d M Y', strtotime($r['date_of_disposal'])) : '—' ?></td>
                    <td><?= htmlspecialchars($r['dollar_rate']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
<?php require '../partials/chrome_close.php'; ?>
<?php if ($canCreate): ?>
<div class="modal fade" id="utModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="index.php">
      <div class="modal-header"><h5 class="modal-title">Log Untracked Asset</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><div class="row g-3">
        <div class="col-12"><label class="form-label">Asset Name</label>
          <input type="text" class="form-control" name="asset_name" required></div>
        <div class="col-6"><label class="form-label">Asset Class</label>
          <select class="form-select" name="asset_class" required>
            <option value="" hidden>Select class…</option>
            <?php foreach ($classes as $c): ?><option value="<?= (int)$c['ast_id'] ?>"><?= htmlspecialchars(trim($c['asset_class'])) ?></option><?php endforeach; ?>
          </select></div>
        <div class="col-6"><label class="form-label">Value (GHS)</label>
          <input type="number" step="0.01" min="0" class="form-control" name="asset_value" required></div>
        <div class="col-6"><label class="form-label">Acquisition Date</label>
          <input type="date" class="form-control" name="acquisition_date" required></div>
        <div class="col-6"><label class="form-label">Disposal Date <span class="text-muted">(optional)</span></label>
          <input type="date" class="form-control" name="disposal_date"></div>
        <div class="col-6"><label class="form-label">Dollar Rate</label>
          <input type="number" step="0.0001" class="form-control" name="dollar_rate" value="<?= $activeRate ?>"></div>
      </div></div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="add_untracked" class="btn btn-primary btn-sm">Save</button></div>
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
