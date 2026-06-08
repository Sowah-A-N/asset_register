<?php
/**
 * FILE: schedule_officer/moved_assets/index.php — Asset movement log (read-only), v2.
 */
session_start();
require_once '../../auth.php';
requirePermission('asset.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

$rows = [];
$res = mysqli_query($conn,
    "SELECT m.serial_number, m.notes, m.old_location, m.old_user, m.New_location, m.new_user, m.date_of_action, a.asset_name
     FROM moved_assets m
     LEFT JOIN assets a ON a.serial_number = m.serial_number
     ORDER BY m.date_of_action DESC");
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asset Movements — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">
</head>
<body>
<?php
  $pageTitle    = 'Asset Movements';
  $pageSubtitle = count($rows) . ' transfer' . (count($rows) !== 1 ? 's' : '') . ' logged';
  require '../partials/chrome_open.php';
?>
      <div class="rmu-card">
        <div class="rmu-card-header"><div class="rmu-card-title">Movement History</div>
          <small class="text-muted">Location / custodian transfers</small></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($rows)): ?>
            <div class="rmu-empty"><i class="bi bi-arrow-left-right rmu-empty-icon"></i><h6>No movements logged</h6></div>
          <?php else: ?>
            <div class="rmu-table-wrapper" style="box-shadow:none;border:none">
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Asset</th><th>Serial</th><th>From → To (Location)</th><th>From → To (User)</th><th>Notes</th><th>Date</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($rows as $r): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($r['asset_name'] ?? '—') ?></td>
                    <td><code style="font-size:.74rem"><?= htmlspecialchars($r['serial_number']) ?></code></td>
                    <td><?= htmlspecialchars($r['old_location']) ?> <i class="bi bi-arrow-right text-muted"></i> <strong><?= htmlspecialchars($r['New_location']) ?></strong></td>
                    <td><?= htmlspecialchars($r['old_user']) ?> <i class="bi bi-arrow-right text-muted"></i> <strong><?= htmlspecialchars($r['new_user']) ?></strong></td>
                    <td style="max-width:220px;white-space:normal"><?= htmlspecialchars($r['notes']) ?></td>
                    <td><?= $r['date_of_action'] ? date('d M Y', strtotime($r['date_of_action'])) : '—' ?></td>
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
