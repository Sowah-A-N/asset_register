<?php
/**
 * FILE: schedule_officer/disposals/index.php — Disposed assets (read-only), v2.
 */
session_start();
require_once '../../auth.php';
requirePermission('asset.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

$filter = trim($_GET['asset_class'] ?? '');
$classes = [];
$cr = mysqli_query($conn, "SELECT DISTINCT asset_class FROM disposals ORDER BY asset_class ASC");
while ($row = mysqli_fetch_assoc($cr)) if ($row['asset_class'] !== '') $classes[] = $row['asset_class'];

if ($filter !== '') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM disposals WHERE asset_class = ? ORDER BY date_of_disposal DESC");
    mysqli_stmt_bind_param($stmt, "s", $filter); mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
} else {
    $res = mysqli_query($conn, "SELECT * FROM disposals ORDER BY date_of_disposal DESC");
}
$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
function money($n){ return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Disposed Assets — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
</head>
<body>
<?php
  $pageTitle    = 'Disposed Assets';
  $pageSubtitle = count($rows) . ' record' . (count($rows) !== 1 ? 's' : '');
  require '../partials/chrome_open.php';
?>
      <div class="rmu-card">
        <div class="rmu-card-header">
          <div class="rmu-card-title">Disposed Assets</div>
          <form method="GET" class="d-flex gap-2">
            <select name="asset_class" class="form-select form-select-sm" style="max-width:220px" onchange="this.form.submit()">
              <option value="">All classes</option>
              <?php foreach ($classes as $c): ?>
                <option value="<?= htmlspecialchars($c, ENT_QUOTES) ?>" <?= $filter===$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($rows)): ?>
            <div class="rmu-empty"><i class="bi bi-trash3 rmu-empty-icon"></i><h6>No disposed assets</h6>
              <p>Assets you dispose from the active register will appear here.</p></div>
          <?php else: ?>
            <div class="rmu-table-wrapper" style="box-shadow:none;border:none">
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Asset Name</th><th>Class</th><th>GRV</th><th>ID No.</th><th>PV</th><th>Type</th><th>Location</th><th>Acquired</th><th class="num" style="text-align:right">Cost (GHS)</th><th class="num" style="text-align:right">Disposal Value</th><th>Rate</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($rows as $r): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($r['asset_name']) ?></td>
                    <td><?= htmlspecialchars($r['asset_class']) ?></td>
                    <td><?= htmlspecialchars($r['grv_number']) ?></td>
                    <td><?= htmlspecialchars($r['id_number']) ?></td>
                    <td><?= htmlspecialchars($r['pv_number']) ?></td>
                    <td><?= htmlspecialchars($r['asset_type']) ?></td>
                    <td><?= htmlspecialchars($r['location']) ?></td>
                    <td><?= $r['acquisition_date'] && $r['acquisition_date']!=='0000-00-00' ? date('d M Y', strtotime($r['acquisition_date'])) : '—' ?></td>
                    <td style="text-align:right;font-variant-numeric:tabular-nums"><?= money($r['additions']) ?></td>
                    <td style="text-align:right;font-variant-numeric:tabular-nums"><?= money($r['disposal_value']) ?></td>
                    <td><?= htmlspecialchars($r['dollar_rate_used']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
<?php require '../partials/chrome_close.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
</script>
</body>
</html>
