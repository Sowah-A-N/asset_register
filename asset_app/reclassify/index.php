<?php
/**
 * FILE: schedule_officer/reclassify/index.php
 * PURPOSE: Reclassify an asset from one class to another (e.g. Work-in-Progress
 *          → Buildings on capitalization). For now the SOURCE is restricted to
 *          non-depreciating classes (asset_classes.depreciated = 0).
 *
 * Policy (approved): depreciation starts from the reclassification date — the
 * asset's in_service_date is set to that date (the reports use COALESCE(
 * in_service_date, acquisition_date) as the depreciation start). Every
 * reclassification is written to asset_reclass_log (audit trail). Any target
 * class is allowed. The original acquisition_date is preserved.
 */
session_start();
require_once '../../auth.php';
requirePermission('asset.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

if (isset($_POST['reclassify'])) {
    if (!can('asset.edit')) { echo "<script>alert('You do not have permission to reclassify assets.');window.location='index.php';</script>"; exit(); }
    $assetId = (int)($_POST['asset_id'] ?? 0);
    $toClass = trim($_POST['to_class'] ?? '');
    $toSub   = trim($_POST['to_sub_class'] ?? '');
    $date    = trim($_POST['reclass_date'] ?? '');
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if ($assetId <= 0 || $toClass === '' || !$d || $d->format('Y-m-d') !== $date) {
        echo "<script>alert('Please choose a target class and a valid reclassification date.');window.location='index.php';</script>"; exit();
    }
    // Current asset
    $stmt = mysqli_prepare($conn, "SELECT asset_name, serial_number, asset_class, sub_class FROM assets WHERE asset_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $assetId); mysqli_stmt_execute($stmt);
    $cur = mysqli_stmt_get_result($stmt)->fetch_assoc(); mysqli_stmt_close($stmt);
    if (!$cur) { echo "<script>alert('Asset not found.');window.location='index.php';</script>"; exit(); }
    $newSub = $toSub !== '' ? $toSub : $cur['sub_class'];   // keep existing sub-class if none chosen

    mysqli_begin_transaction($conn); $ok = true;
    $s = mysqli_prepare($conn, "UPDATE assets SET asset_class = ?, sub_class = ?, in_service_date = ? WHERE asset_id = ?");
    mysqli_stmt_bind_param($s, "sssi", $toClass, $newSub, $date, $assetId);
    $ok = mysqli_stmt_execute($s); mysqli_stmt_close($s);
    if ($ok) {
        $s = mysqli_prepare($conn,
            "INSERT INTO asset_reclass_log (asset_id, serial_number, asset_name, from_class, from_sub_class, to_class, to_sub_class, reclass_date, action_by)
             VALUES (?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($s, "issssssss", $assetId, $cur['serial_number'], $cur['asset_name'], $cur['asset_class'], $cur['sub_class'], $toClass, $newSub, $date, $username);
        $ok = mysqli_stmt_execute($s); mysqli_stmt_close($s);
    }
    if ($ok) { mysqli_commit($conn); echo "<script>alert('Asset reclassified to ".addslashes($toClass)." (depreciation starts ".$date.").');window.location='index.php';</script>"; }
    else { mysqli_rollback($conn); error_log('[RMU] reclassify failed: '.mysqli_error($conn)); echo "<script>alert('Could not reclassify. Please try again.');window.location='index.php';</script>"; }
    mysqli_autocommit($conn, true);
    exit();
}

/* Assets currently in non-depreciating classes (the eligible source set) */
$assets = [];
$ar = mysqli_query($conn,
    "SELECT a.asset_id, a.asset_name, a.serial_number, a.asset_class, a.sub_class,
            a.additions, a.acquisition_date, a.in_service_date
     FROM assets a
     JOIN asset_classes c ON TRIM(c.asset_class) = TRIM(a.asset_class)
     WHERE c.depreciated = 0
     ORDER BY a.asset_class, a.asset_name");
while ($r = mysqli_fetch_assoc($ar)) $assets[] = $r;

$classes = [];
$cr = mysqli_query($conn, "SELECT asset_class FROM asset_classes ORDER BY asset_class ASC");
while ($r = mysqli_fetch_assoc($cr)) $classes[] = trim($r['asset_class']);

$subs = [];
$sr = mysqli_query($conn, "SELECT sub_class, asset_class FROM asset_class_sub_classes ORDER BY sub_class ASC");
while ($r = mysqli_fetch_assoc($sr)) $subs[] = $r;

$log = [];
$lr = mysqli_query($conn, "SELECT * FROM asset_reclass_log ORDER BY logged_at DESC LIMIT 20");
while ($r = mysqli_fetch_assoc($lr)) $log[] = $r;

$canEdit = can('asset.edit');
function money($n){ return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reclassify Assets — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
</head>
<body>
<?php
  $pageTitle    = 'Reclassify Assets';
  $pageSubtitle = 'Move assets between classes — e.g. Work-in-Progress → Buildings on capitalization';
  require '../partials/chrome_open.php';
?>
      <div class="rmu-card mb-4">
        <div class="rmu-card-header"><div>
          <div class="rmu-card-title">Eligible Assets — Non-Depreciating Classes</div>
          <small class="text-muted">Capitalising Work-in-Progress or reclassifying Land. <?= count($assets) ?> asset<?= count($assets)!==1?'s':'' ?>.</small>
        </div></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($assets)): ?>
            <div class="rmu-empty"><i class="bi bi-arrow-left-right rmu-empty-icon"></i><h6>No eligible assets</h6>
              <p>Only assets in non-depreciating classes can be reclassified for now.</p></div>
          <?php else: ?>
            <div class="rmu-table-wrapper" style="box-shadow:none;border:none">
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>#</th><th>Asset</th><th>Serial</th><th>Current Class</th><th>Sub-Class</th><th class="num" style="text-align:right">Cost (GHS)</th><th>Acquired</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($assets as $a): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td style="max-width:240px;white-space:normal"><?= htmlspecialchars($a['asset_name']) ?>
                      <?= $a['in_service_date'] ? '<span class="status-pill status-active ms-1" title="in service '.htmlspecialchars($a['in_service_date']).'">reclassified</span>' : '' ?></td>
                    <td><code style="font-size:.74rem"><?= htmlspecialchars($a['serial_number'] ?: '—') ?></code></td>
                    <td><?= htmlspecialchars(trim($a['asset_class'])) ?></td>
                    <td><?= htmlspecialchars($a['sub_class'] ?: '—') ?></td>
                    <td style="text-align:right;font-variant-numeric:tabular-nums"><?= money($a['additions']) ?></td>
                    <td><?= $a['acquisition_date'] && $a['acquisition_date']!=='0000-00-00' ? date('d M Y', strtotime($a['acquisition_date'])) : '—' ?></td>
                    <?php if ($canEdit): ?>
                    <td style="text-align:right">
                      <button class="btn btn-sm btn-outline-primary btn-reclass"
                              data-id="<?= (int)$a['asset_id'] ?>"
                              data-name="<?= htmlspecialchars($a['asset_name'], ENT_QUOTES) ?>"
                              data-class="<?= htmlspecialchars(trim($a['asset_class']), ENT_QUOTES) ?>">
                        <i class="bi bi-arrow-left-right me-1"></i>Reclassify
                      </button>
                    </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Reclassification log -->
      <div class="rmu-card">
        <div class="rmu-card-header"><div class="rmu-card-title">Reclassification History</div>
          <small class="text-muted">Audit trail — most recent 20</small></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($log)): ?>
            <div class="rmu-empty"><i class="bi bi-clock-history rmu-empty-icon"></i><h6>No reclassifications yet</h6></div>
          <?php else: ?>
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>Asset</th><th>From → To (Class)</th><th>Reclass Date</th><th>By</th><th>Logged</th></tr></thead>
              <tbody>
                <?php foreach ($log as $l): ?>
                  <tr>
                    <td><?= htmlspecialchars($l['asset_name']) ?></td>
                    <td><?= htmlspecialchars(trim($l['from_class'])) ?> <i class="bi bi-arrow-right text-muted"></i> <strong><?= htmlspecialchars(trim($l['to_class'])) ?></strong></td>
                    <td><?= $l['reclass_date'] ? date('d M Y', strtotime($l['reclass_date'])) : '—' ?></td>
                    <td><?= htmlspecialchars($l['action_by'] ?? '—') ?></td>
                    <td><?= $l['logged_at'] ? date('d M Y, H:i', strtotime($l['logged_at'])) : '—' ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
<?php require '../partials/chrome_close.php'; ?>

<?php if ($canEdit): ?>
<div class="modal fade" id="rcModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="index.php">
      <div class="modal-header"><h5 class="modal-title">Reclassify Asset</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="asset_id" id="rcAssetId">
        <div class="mb-3"><label class="form-label">Asset</label>
          <input type="text" class="form-control" id="rcAssetName" readonly></div>
        <div class="mb-3"><label class="form-label">Current Class</label>
          <input type="text" class="form-control" id="rcFromClass" readonly></div>
        <div class="mb-3"><label class="form-label">Reclassify To <span class="text-danger">*</span></label>
          <select class="form-select" name="to_class" id="rcToClass" required>
            <option value="" hidden>Select target class…</option>
            <?php foreach ($classes as $c): ?><option value="<?= htmlspecialchars($c, ENT_QUOTES) ?>"><?= htmlspecialchars($c) ?></option><?php endforeach; ?>
          </select></div>
        <div class="mb-3"><label class="form-label">Target Sub-Class <span class="text-muted">(optional)</span></label>
          <select class="form-select" name="to_sub_class">
            <option value="">— keep current —</option>
            <?php foreach ($subs as $s): ?>
              <option value="<?= htmlspecialchars($s['sub_class'], ENT_QUOTES) ?>"><?= htmlspecialchars($s['sub_class']) ?> <?= $s['asset_class'] ? '· '.htmlspecialchars(trim($s['asset_class'])) : '' ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="mb-1"><label class="form-label">Reclassification Date <span class="text-danger">*</span></label>
          <input type="date" class="form-control" name="reclass_date" value="<?= date('Y-m-d') ?>" required>
          <div class="form-text">Depreciation in the new class starts from this date (capitalization date).</div></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="reclassify" class="btn btn-primary btn-sm">Reclassify</button></div>
    </form>
  </div></div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
<?php if ($canEdit): ?>
const rcModal = new bootstrap.Modal(document.getElementById('rcModal'));
document.querySelectorAll('.btn-reclass').forEach(b => b.addEventListener('click', () => {
  document.getElementById('rcAssetId').value = b.dataset.id;
  document.getElementById('rcAssetName').value = b.dataset.name;
  document.getElementById('rcFromClass').value = b.dataset.class;
  // prevent selecting the same class as target
  [...document.getElementById('rcToClass').options].forEach(o => o.disabled = (o.value === b.dataset.class));
  rcModal.show();
}));
<?php endif; ?>
</script>
</body>
</html>
