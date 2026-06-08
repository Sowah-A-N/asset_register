<?php
/** FILE: schedule_officer/archived_assets/index.php — Archived assets (read-only), v2. */
session_start();
require_once '../../auth.php';
requirePermission('asset.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";
$rows = [];
$r = mysqli_query($conn,
    "SELECT asset_name, asset_class, grv_number, id_number, pv_number, asset_type, location,
            acquisition_date, additions, dollar_rate_used
     FROM assets_archive ORDER BY asset_name ASC");
while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;
function money($n){ return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Archived Assets — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
</head>
<body>
<?php
  $pageTitle    = 'Archived Assets';
  $pageSubtitle = count($rows) . ' archived';
  require '../partials/chrome_open.php';
?>
      <div class="rmu-card">
        <div class="rmu-card-header"><div class="rmu-card-title">Archived Assets</div>
          <input type="search" class="form-control form-control-sm" id="aSearch" placeholder="Search…" style="max-width:240px"></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($rows)): ?>
            <div class="rmu-empty"><i class="bi bi-archive rmu-empty-icon"></i><h6>No archived assets</h6></div>
          <?php else: ?>
            <div class="rmu-table-wrapper" style="box-shadow:none;border:none">
            <table class="rmu-table" id="aTable" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Asset Name</th><th>Class</th><th>GRV</th><th>ID No.</th><th>PV</th><th>Type</th><th>Location</th><th>Acquired</th><th class="num" style="text-align:right">Cost (GHS)</th><th>Rate</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($rows as $a): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td style="max-width:240px;white-space:normal"><?= htmlspecialchars($a['asset_name']) ?></td>
                    <td><?= htmlspecialchars(trim($a['asset_class'])) ?></td>
                    <td><?= htmlspecialchars($a['grv_number']) ?></td>
                    <td><?= htmlspecialchars($a['id_number']) ?></td>
                    <td><?= htmlspecialchars($a['pv_number']) ?></td>
                    <td><?= htmlspecialchars($a['asset_type']) ?></td>
                    <td><?= htmlspecialchars($a['location']) ?></td>
                    <td><?= $a['acquisition_date'] && $a['acquisition_date']!=='0000-00-00' ? date('d M Y', strtotime($a['acquisition_date'])) : '—' ?></td>
                    <td style="text-align:right;font-variant-numeric:tabular-nums"><?= money($a['additions']) ?></td>
                    <td><?= htmlspecialchars($a['dollar_rate_used']) ?></td>
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
document.getElementById('aSearch')?.addEventListener('input', function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#aTable tbody tr').forEach(tr=>tr.style.display=tr.innerText.toLowerCase().includes(q)?'':'none');
});
</script>
</body>
</html>
