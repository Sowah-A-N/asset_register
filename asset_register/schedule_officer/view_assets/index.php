<?php


session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../login/");
    exit;
}
$username = $_SESSION['username'];
require_once __DIR__ . "/../datacon.php"; // expects $conn from this file
if (!isset($conn)) die("DB connection missing");

// config
$limit = 50;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$assetClassFilter = isset($_GET['asset_class_filter']) && $_GET['asset_class_filter'] !== '' ? trim($_GET['asset_class_filter']) : null;

// helper
function esc($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// prefetch lists once
$locations = []; $users = []; $types = []; $assetClasses = [];
if ($r = mysqli_query($conn, "SELECT location FROM asset_location ORDER BY location")) {
    while ($row = mysqli_fetch_assoc($r)) $locations[] = $row['location'];
}
if ($r = mysqli_query($conn, "SELECT staff_first_name, staff_last_name FROM asset_users ORDER BY staff_first_name")) {
    while ($row = mysqli_fetch_assoc($r)) $users[] = trim($row['staff_first_name'] . ' ' . $row['staff_last_name']);
}
if ($r = mysqli_query($conn, "SELECT asset_type FROM asset_type ORDER BY asset_type")) {
    while ($row = mysqli_fetch_assoc($r)) $types[] = $row['asset_type'];
}
if ($r = mysqli_query($conn, "SELECT DISTINCT asset_class FROM asset_classes ORDER BY asset_class")) {
    while ($row = mysqli_fetch_assoc($r)) $assetClasses[] = $row['asset_class'];
}

// count total (with optional filter)
if ($assetClassFilter) {
    $countStmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM assets WHERE disposals = 0 AND asset_class = ?");
    mysqli_stmt_bind_param($countStmt, 's', $assetClassFilter);
    mysqli_stmt_execute($countStmt);
    $cres = mysqli_stmt_get_result($countStmt);
    $totalAssets = ($cres && $crow = mysqli_fetch_assoc($cres)) ? (int)$crow['total'] : 0;
    mysqli_stmt_close($countStmt);
} else {
    $cres = mysqli_query($conn, "SELECT COUNT(*) as total FROM assets WHERE disposals = 0");
    $totalAssets = ($cres && $crow = mysqli_fetch_assoc($cres)) ? (int)$crow['total'] : 0;
}
$totalPages = max(1, (int)ceil($totalAssets / $limit));

// fetch page rows (prepared)
$selectSql = "SELECT asset_id, asset_name, asset_class, sub_class, `user`, serial_number, grv_number, id_number, pv_number, disposals, asset_type, location, acquisition_date, additions, dollar_rate_used FROM assets WHERE disposals = 0";
$params = []; $types_sig = '';
if ($assetClassFilter) { $selectSql .= " AND asset_class = ?"; $params[] = $assetClassFilter; $types_sig .= 's'; }
$selectSql .= " ORDER BY asset_id ASC LIMIT ? OFFSET ?";
$params[] = $limit; $params[] = $offset; $types_sig .= 'ii';
$stmt = mysqli_prepare($conn, $selectSql);
if ($types_sig !== '') {
    // bind params dynamically (mysqli requires references)
    $refs = []; $refs[] = & $types_sig;
    for ($i = 0; $i < count($params); $i++) $refs[] = & $params[$i];
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $refs));
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ---------- POST handlers (archive/move/edit/dispose) using prepared statements ----------
// Archive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'archive' && isset($_POST['asset_id'])) {
    $archiveAssetId = (int)$_POST['asset_id'];
    mysqli_begin_transaction($conn);
    $moveSql = "INSERT INTO asset_register_new.assets_archive (asset_id, asset_name, grv_number, serial_number, pv_number, id_number, supplier_name, asset_class, sub_class, asset_type, location, user, acquisition_date, current_year, historical_cost, additions, disposals, disposed, active_res_value, dollar_rate_used, date_added) SELECT asset_id, asset_name, grv_number, serial_number, pv_number, id_number, supplier_name, asset_class, sub_class, asset_type, location, user, acquisition_date, current_year, historical_cost, additions, disposals, disposed, active_res_value, dollar_rate_used, date_added FROM assets WHERE asset_id = ?";
    $ms = mysqli_prepare($conn, $moveSql);
    mysqli_stmt_bind_param($ms, 'i', $archiveAssetId);
    $ok1 = mysqli_stmt_execute($ms);
    mysqli_stmt_close($ms);
    $ds = mysqli_prepare($conn, "DELETE FROM assets WHERE asset_id = ?");
    mysqli_stmt_bind_param($ds, 'i', $archiveAssetId);
    $ok2 = mysqli_stmt_execute($ds);
    mysqli_stmt_close($ds);
    if ($ok1 && $ok2) { mysqli_commit($conn); echo "<script>alert('Asset archived'); window.location='index.php';</script>"; exit; }
    else { mysqli_rollback($conn); echo "<script>alert('Archive failed'); window.location='index.php';</script>"; exit; }
}

// Move
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'move' && isset($_POST['serial_number'])) {
    $serial = $_POST['serial_number'];
    $new_location = $_POST['new_location'] ?? '';
    $new_user = $_POST['new_user'] ?? '';
    $notes = $_POST['notes'] ?? '';
    if (empty($new_location) || empty($new_user) || empty($notes)) { echo "<script>alert('Check details'); window.location='index.php';</script>"; exit; }
    $gs = mysqli_prepare($conn, "SELECT location, `user` FROM assets WHERE asset_id = ? LIMIT 1");
    mysqli_stmt_bind_param($gs, 's', $serial);
    mysqli_stmt_execute($gs);
    $gres = mysqli_stmt_get_result($gs);
    if (!$gres || mysqli_num_rows($gres) === 0) { mysqli_stmt_close($gs); echo "<script>alert('Asset not found'); window.location='index.php';</script>"; exit; }
    $crow = mysqli_fetch_assoc($gres);
    mysqli_stmt_close($gs);
    if ($crow['user'] === $new_user) { echo "<script>alert('Users are the same'); window.location='index.php';</script>"; exit; }
    if ($crow['location'] === $new_location) { echo "<script>alert('Locations are the same'); window.location='index1.php';</script>"; exit; }
    $us = mysqli_prepare($conn, "UPDATE assets SET location = ?, `user` = ? WHERE serial_number = ?");
    mysqli_stmt_bind_param($us, 'sss', $new_location, $new_user, $serial);
    $ok = mysqli_stmt_execute($us);
    mysqli_stmt_close($us);
    $ins = mysqli_prepare($conn, "INSERT INTO moved_assets (serial_number, notes, old_location, old_user, New_location, new_user) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'ssssss', $serial, $notes, $crow['location'], $crow['user'], $new_location, $new_user);
    $ok2 = mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    if ($ok) { echo "<script>alert('Asset Location Successfully Changed!'); window.location='index1.php';</script>"; exit; }
    else { echo "<script>alert('Error changing location'); window.location='index.php';</script>"; exit; }
}

// Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['serial_number'])) {
    $serial = $_POST['serial_number'];
    $asset_name = $_POST['asset_name'] ?? '';
    $dollar_rate = (float)($_POST['dollar_rate'] ?? 0);
    $historical_cost = (float)($_POST['historical_cost'] ?? 0);
    $pv_number = $_POST['pv_number'] ?? '';
    $grv_number = $_POST['grv_number'] ?? '';
    $location = $_POST['location'] ?? '';
    $user = $_POST['user'] ?? '';
    $asset_type = $_POST['asset_type'] ?? '';
    $asset_id = (int)($_POST['asset_id'] ?? 0); // Ensure this comes from the form

    $up = mysqli_prepare($conn, "UPDATE assets 
        SET asset_name = ?, dollar_rate_used = ?, additions = ?, location = ?, `user` = ?, 
            asset_type = ?, grv_number = ?, pv_number = ?, serial_number = ? 
        WHERE asset_id = ?");
    
    mysqli_stmt_bind_param($up, 'sddssssssi', 
        $asset_name, 
        $dollar_rate, 
        $historical_cost, 
        $location, 
        $user, 
        $asset_type, 
        $grv_number, 
        $pv_number, 
        $serial, 
        $asset_id
    );

    $ok = mysqli_stmt_execute($up);
    mysqli_stmt_close($up);

    if ($ok) {
        echo "<script>alert('Asset updated'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Update failed: " . esc(mysqli_error($conn)) . "'); window.location='index.php';</script>";
        exit;
    }
}


// Dispose
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'dispose' && isset($_POST['asset_id'])) {
    $assetId = (int)$_POST['asset_id'];
    $fs = mysqli_prepare($conn, "SELECT * FROM assets WHERE asset_id = ? LIMIT 1");
    mysqli_stmt_bind_param($fs, 'i', $assetId);
    mysqli_stmt_execute($fs);
    $fres = mysqli_stmt_get_result($fs);
    if (!$fres || mysqli_num_rows($fres) === 0) { echo "<script>alert('Asset not found'); window.location='index.php';</script>"; exit; }
    $rowAdditions = mysqli_fetch_assoc($fres);
    mysqli_stmt_close($fs);
    $disposalValue = isset($_POST['disposal_value']) ? (float)$_POST['disposal_value'] : (float)$rowAdditions['additions'];
    if ($disposalValue < 0) { echo "<script>alert('Disposal value cannot be negative'); window.location='index.php';</script>"; exit; }
    $currentYear = date('Y');
    $ins = mysqli_prepare($conn, "INSERT INTO disposals (asset_name, asset_class, sub_class, grv_number, serial_number, pv_number, id_number, supplier_name, asset_type, location, `user`, acquisition_date, current_year, additions, active_res_value, dollar_rate_used, disposal_value) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'sssssssssssssssss', $rowAdditions['asset_name'], $rowAdditions['asset_class'], $rowAdditions['sub_class'], $rowAdditions['grv_number'], $rowAdditions['serial_number'], $rowAdditions['pv_number'], $rowAdditions['id_number'], $rowAdditions['supplier_name'], $rowAdditions['asset_type'], $rowAdditions['location'], $rowAdditions['user'], $rowAdditions['acquisition_date'], $currentYear, $rowAdditions['additions'], $rowAdditions['active_res_value'], $rowAdditions['dollar_rate_used'], $disposalValue);
    $ok = mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    if (!$ok) { echo "<script>alert('Disposal insert failed'); window.location='index.php';</script>"; exit; }
    $up = mysqli_prepare($conn, "UPDATE assets SET disposals = 1 WHERE asset_id = ?");
    mysqli_stmt_bind_param($up, 'i', $assetId);
    $ok2 = mysqli_stmt_execute($up);
    mysqli_stmt_close($up);
    if ($ok2) { echo "<script>alert('Asset disposed'); window.location='index1.php';</script>"; exit; }
    else { echo "<script>alert('Failed to update disposal flag'); window.location='index.php';</script>"; exit; }
}

// AJAX endpoint to get a single asset
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_asset' && isset($_GET['asset_id'])) {
    $serial = $_GET['asset_id'];
    $gs = mysqli_prepare($conn, "SELECT * FROM assets WHERE asset_id = ? LIMIT 1");
    mysqli_stmt_bind_param($gs, 's', $serial);
    mysqli_stmt_execute($gs);
    $gres = mysqli_stmt_get_result($gs);
    if ($gres && mysqli_num_rows($gres) > 0) {
        header('Content-Type: application/json');
        echo json_encode(mysqli_fetch_assoc($gres));
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Asset not found']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>View Active Assets</title>
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .table-responsive { overflow-x:auto; }
  
    td, th { white-space: nowrap; }

    /* Pagination container */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    gap: 5px; /* spacing between page links */
}

/* Pagination links */
.pagination a, .pagination strong {
    display: inline-block;
    padding: 6px 12px;
    text-decoration: none;
    border: 1px solid #007bff;
    color: #007bff;
    border-radius: 4px;
    transition: background-color 0.2s, color 0.2s;
}

/* Hover effect for links */
.pagination a:hover {
    background-color: #007bff;
    color: #fff;
}

/* Current page styling */
.pagination strong {
    background-color: #007bff;
    color: #fff;
    cursor: default;
    border-color: #0056b3;
}

  </style>
</head>
<body>
   
  <div class="container-scroller">
    <?php include "../sidebar.html"; ?>
    <div class="main-panel">
        
      <div class="content-wrapper">
         <div class="d-xl-flex justify-content-between align-items-start">
            <h2 class="text-dark font-weight-bold mb-2"> Filter Active Assets by Classes </h2>
            <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
              <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
              </div>
            </div>
         </div>
     <form method="GET" action="index.php" style="margin-bottom:15px;">
  <select name="asset_class_filter" onchange="this.form.submit()">
    <option value="">All Asset Classes</option>
    <?php foreach ($assetClasses as $ac): ?>
      <option value="<?= esc($ac) ?>" <?= $assetClassFilter === $ac ? 'selected' : '' ?>><?= esc($ac) ?></option>
    <?php endforeach; ?>
  </select>
</form>


        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>S/n</th><th>Asset Name</th><th>Asset Class</th><th>Sub Class</th><th>Assigned User</th><th>Serial Number</th><th>GRV Number</th><th>ID Number</th><th>PV Number</th><th>Disposals</th><th>Asset Type</th><th>Location</th><th>Acquisition Date</th><th>Historical Cost (GHS)</th><th>Dollar Rate</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
<?php
      $sn = $offset + 1;
      while ($row = mysqli_fetch_assoc($result)):
      ?>
        <tr>
          <td><?= esc($sn++) ?></td>
          <td><?= esc($row['asset_name']) ?></td>
          <td><?= esc($row['asset_class']) ?></td>
          <td><?= esc($row['sub_class']) ?></td>
          <td><?= esc($row['user']) ?></td>
          <td><?= esc($row['serial_number']) ?></td>
          <td><?= esc($row['grv_number']) ?></td>
          <td><?= esc($row['id_number']) ?></td>
          <td><?= esc($row['pv_number']) ?></td>
          <td><?= esc($row['disposals']) ?></td>
          <td><?= esc($row['asset_type']) ?></td>
          <td><?= esc($row['location']) ?></td>
          <td><?= empty($row['acquisition_date']) ? '' : date('d F, Y', strtotime($row['acquisition_date'])) ?></td>
          <td><?= number_format((float)$row['additions'], 2, '.', ',') ?></td>
          <td><?= esc($row['dollar_rate_used']) ?></td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="openEditModal('<?= esc($row['asset_id'], ENT_QUOTES) ?>')">Edit</button>
            <button class="btn btn-primary btn-sm" onclick="openMoveModal('<?= esc($row['asset_id'], ENT_QUOTES) ?>')">Move</button>
            <form style="display:inline" method="POST" action="index.php" onsubmit="return confirm('Archive this asset?')">
              <input type="hidden" name="action" value="archive">
              <input type="hidden" name="asset_id" value="<?= esc($row['asset_id']) ?>">
              <button type="submit" class="btn btn-danger btn-sm">Archive</button>
            </form>
            <form style="display:inline" method="POST" action="index.php" onsubmit="return confirm('Dispose this asset?')">
              <input type="hidden" name="action" value="dispose">
              <input type="hidden" name="asset_id" value="<?= esc($row['asset_id']) ?>">
              <button type="submit" class="btn btn-success btn-sm">Dispose</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    

            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
<?php
$baseUrl = 'index.php?';
if ($assetClassFilter) $baseUrl .= 'asset_class_filter=' . urlencode($assetClassFilter) . '&';
if ($page > 1) echo '<a href="'.$baseUrl.'page='.($page-1).'">Previous</a> ';
$start = max(1, $page - 3);
$end = min($totalPages, $page + 3);
if ($start > 1) echo '<a href="'.$baseUrl.'page=1">1</a> ... ';
for ($i = $start; $i <= $end; $i++) {
    if ($i == $page) echo '<strong>'.$i.'</strong> ';
    else echo '<a href="'.$baseUrl.'page='.$i.'">'.$i.'</a> ';
}
if ($end < $totalPages) echo '... <a href="'.$baseUrl.'page='.$totalPages.'">'.$totalPages.'</a>';
if ($page < $totalPages) echo ' <a href="'.$baseUrl.'page='.($page+1).'">Next</a>';
?>
        </div>

     <!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Asset</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="editForm" method="POST" action="index.php">
          <input type="hidden" name="action" value="edit">
         <input type="hidden" name="asset_id" id="edit_asset_id">
          <div class="form-group">
            <label>Asset Name</label>
            <input type="text" name="asset_name" id="edit_asset_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Dollar Rate</label>
            <input type="text" name="dollar_rate" id="edit_dollar_rate" class="form-control">
          </div>
          <div class="form-group">
            <label>Historical Cost</label>
            <input type="number" step="0.01" name="historical_cost" id="edit_historical_cost" class="form-control">
          </div>

           <div class="form-group">
            <label>Serial number</label>
            <input type="text"  name="serial_number" id="edit_serial_number" class="form-control">
          </div>

           <div class="form-group">
            <label>GRV Number</label>
            <input type="text"  name="grv_number" id="edit_grv_number" class="form-control">
          </div>

           <div class="form-group">
            <label>PV Number</label>
            <input type="text"  name="pv_number" id="edit_pv_number" class="form-control">
          </div>
          <div class="form-group">
            <label>Location</label>
            <select name="location" id="edit_location" class="form-control">
              <?php foreach ($locations as $loc): ?>
                <option value="<?= esc($loc) ?>"><?= esc($loc) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>User</label>
            <select name="user" id="edit_user" class="form-control">
              <?php foreach ($users as $u): ?>
                <option value="<?= esc($u) ?>"><?= esc($u) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Asset Type</label>
            <select name="asset_type" id="edit_asset_type" class="form-control">
              <?php foreach ($types as $t): ?>
                <option value="<?= esc($t) ?>"><?= esc($t) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>


        <!-- Move Modal -->
        <div class="modal fade" id="moveModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Move Asset</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
              <div class="modal-body">
                <form id="moveForm" method="POST" action="index.php">
                  <input type="hidden" name="action" value="move">
                  <input type="hidden" name="serial_number" id="move_serial_number">
                  <div class="form-group"><label>Current Location</label><input readonly id="move_current_location" class="form-control"></div>
                  <div class="form-group"><label>Current User</label><input readonly id="move_current_user" class="form-control"></div>
                  <div class="form-group"><label>New Location</label><select name="new_location" id="move_new_location" class="form-control"><?php foreach ($locations as $loc) echo '<option value=\"'.esc($loc).'\">'.esc($loc).'</option>'; ?></select></div>
                  <div class="form-group"><label>New User</label><select name="new_user" id="move_new_user" class="form-control"><?php foreach ($users as $u) echo '<option value=\"'.esc($u).'\">'.esc($u).'</option>'; ?></select></div>
                  <div class="form-group"><label>Notes</label><input name="notes" id="move_notes" class="form-control" required oninput="capitalizeFirstLetter(this)"></div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div></div></div></div>

      </div>
    </div>
  </div>

  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <script>
    function capitalizeFirstLetter(input) { input.value = input.value.replace(/\b\w/g, c => c.toUpperCase()); }
    function openEditModal(serial) {
      fetch('index.php?action=get_asset&asset_id=' + encodeURIComponent(serial))
        .then(r => r.json())
        .then(data => {
          document.getElementById('edit_asset_id').value = data.asset_id;
          document.getElementById('edit_serial_number').value = data.serial_number || '';
          document.getElementById('edit_asset_name').value = data.asset_name || '';
          document.getElementById('edit_grv_number').value = data.grv_number || '';
          document.getElementById('edit_pv_number').value = data.pv_number || '';
          document.getElementById('edit_serial_number').value = data.serial_number || '';
          document.getElementById('edit_dollar_rate').value = data.dollar_rate_used || '';
          document.getElementById('edit_historical_cost').value = data.additions || '';
          document.getElementById('edit_location').value = data.location || '';
          document.getElementById('edit_user').value = data.user || '';
          document.getElementById('edit_asset_type').value = data.asset_type || '';
          $('#editModal').modal('show');
        })
        .catch(e => alert('Error loading asset'));
    }
    function openMoveModal(serial) {
      fetch('index.php?action=get_asset&asset_id=' + encodeURIComponent(serial))
        .then(r => r.json())
        .then(data => {
          document.getElementById('move_serial_number').value = data.serial_number || '';
          document.getElementById('move_current_location').value = data.location || '';
          document.getElementById('move_current_user').value = data.user || '';
          document.getElementById('move_new_location').value = '';
          document.getElementById('move_new_user').value = '';
          document.getElementById('move_notes').value = '';
          $('#moveModal').modal('show');
        })
        .catch(e => alert('Error loading asset'));
    }
  </script>
</body>
</html>
