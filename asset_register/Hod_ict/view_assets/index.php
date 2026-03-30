<?php
require_once '../init.php';

if (!$conn) {
    error_log('DB connection failed: ' . mysqli_connect_error());
    die('Service temporarily unavailable.');
}

// ── Pre-fetch reference lists once (fixes N+1 query problem) ──────────────
$locations   = [];
$users       = [];
$assetClasses = [];

if ($r = mysqli_query($conn, "SELECT location FROM asset_location ORDER BY location")) {
    while ($row = mysqli_fetch_assoc($r)) $locations[] = $row['location'];
}
if ($r = mysqli_query($conn, "SELECT staff_first_name, staff_last_name FROM asset_users ORDER BY staff_first_name")) {
    while ($row = mysqli_fetch_assoc($r)) $users[] = trim($row['staff_first_name'] . ' ' . $row['staff_last_name']);
}
if ($r = mysqli_query($conn, "SELECT DISTINCT asset_class FROM asset_classes ORDER BY asset_class")) {
    while ($row = mysqli_fetch_assoc($r)) $assetClasses[] = $row['asset_class'];
}

// ── POST handlers ──────────────────────────────────────────────────────────

// Archive asset
if (isset($_POST['submit_archive']) && isset($_POST['asset_id'])) {
    csrf_verify();
    $archiveAssetId = (int)$_POST['asset_id'];

    mysqli_begin_transaction($conn);
    $ms = mysqli_prepare($conn, "INSERT INTO asset_register_new.assets_archive SELECT * FROM assets WHERE asset_id = ?");
    mysqli_stmt_bind_param($ms, 'i', $archiveAssetId);
    $ok1 = mysqli_stmt_execute($ms);
    mysqli_stmt_close($ms);

    $ds = mysqli_prepare($conn, "DELETE FROM assets WHERE asset_id = ?");
    mysqli_stmt_bind_param($ds, 'i', $archiveAssetId);
    $ok2 = mysqli_stmt_execute($ds);
    mysqli_stmt_close($ds);

    if ($ok1 && $ok2) {
        mysqli_commit($conn);
        echo '<script>alert("Asset successfully archived!"); window.location.href="index.php";</script>';
    } else {
        mysqli_rollback($conn);
        error_log('Archive failed for asset_id=' . $archiveAssetId . ': ' . mysqli_error($conn));
        echo '<script>alert("Archive failed. Please try again."); window.location.href="index.php";</script>';
    }
    exit();
}

// Move asset
if (isset($_POST['submit_move']) && isset($_POST['asset_id'])) {
    csrf_verify();
    $identification_number = $_POST['asset_id'];
    $new_location          = $_POST['new_location'] ?? '';
    $new_user              = $_POST['new_user'] ?? '';
    $notes                 = $_POST['notes'] ?? '';

    if (empty($new_location) || empty($notes) || empty($new_user)) {
        echo "<script>alert('Check Details'); window.location='index.php';</script>";
        exit();
    }

    // Fetch current location and user using a prepared statement
    $gs = mysqli_prepare($conn, "SELECT location, `user` FROM assets WHERE asset_id = ? LIMIT 1");
    mysqli_stmt_bind_param($gs, 's', $identification_number);
    mysqli_stmt_execute($gs);
    $gres = mysqli_stmt_get_result($gs);
    mysqli_stmt_close($gs);

    if (!$gres || mysqli_num_rows($gres) === 0) {
        echo "<script>alert('Asset not found.'); window.location='index.php';</script>";
        exit();
    }

    $current = mysqli_fetch_assoc($gres);
    $old_user     = $current['user'];
    $old_location = $current['location'];

    if ($old_user === $new_user) {
        echo "<script>alert('Users are the same'); window.location='index.php';</script>";
        exit();
    }
    if ($old_location === $new_location) {
        echo "<script>alert('Locations are the same'); window.location='index.php';</script>";
        exit();
    }

    $us = mysqli_prepare($conn, "UPDATE assets SET location = ?, `user` = ? WHERE asset_id = ?");
    mysqli_stmt_bind_param($us, 'sss', $new_location, $new_user, $identification_number);
    $ok = mysqli_stmt_execute($us);
    mysqli_stmt_close($us);

    $ins = mysqli_prepare($conn, "INSERT INTO moved_assets (serial_number, notes, old_location, old_user, New_location, new_user) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'ssssss', $identification_number, $notes, $old_location, $old_user, $new_location, $new_user);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);

    if ($ok) {
        echo '<script>alert("Asset Location Successfully Changed!"); window.location.href="index.php";</script>';
    } else {
        error_log('Move failed: ' . mysqli_error($conn));
        echo '<script>alert("Error changing location. Please try again."); window.location.href="index.php";</script>';
    }
    exit();
}

// Dispose asset
if (isset($_POST['submit_disposal']) && isset($_POST['asset_id'])) {
    csrf_verify();
    $assetId = (int)$_POST['asset_id'];

    $fs = mysqli_prepare($conn, "SELECT * FROM assets WHERE asset_id = ? LIMIT 1");
    mysqli_stmt_bind_param($fs, 'i', $assetId);
    mysqli_stmt_execute($fs);
    $fres = mysqli_stmt_get_result($fs);
    mysqli_stmt_close($fs);

    if (!$fres || mysqli_num_rows($fres) === 0) {
        echo '<script>alert("Asset not found."); window.location.href="index.php";</script>';
        exit();
    }

    $rowAdditions = mysqli_fetch_assoc($fres);
    $disposalValue = (float)$rowAdditions['additions'];

    if ($disposalValue < 0) {
        echo '<script>alert("Disposal value cannot be negative."); window.location.href="index.php";</script>';
        exit();
    }

    // Fetch active dollar rate
    $rateRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
    $rate_used   = $rateRow ? $rateRow['dollar_rate'] : 0;
    $currentYear = date('Y');

    $ins = mysqli_prepare($conn, "INSERT INTO disposals (asset_name, asset_class, sub_class, grv_number, serial_number, pv_number, id_number, supplier_name, asset_type, location, `user`, acquisition_date, current_year, additions, active_res_value, dollar_rate_used, disposal_value) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'sssssssssssssssss',
        $rowAdditions['asset_name'], $rowAdditions['asset_class'], $rowAdditions['sub_class'],
        $rowAdditions['grv_number'], $rowAdditions['serial_number'], $rowAdditions['pv_number'],
        $rowAdditions['id_number'], $rowAdditions['supplier_name'], $rowAdditions['asset_type'],
        $rowAdditions['location'], $rowAdditions['user'], $rowAdditions['acquisition_date'],
        $currentYear, $rowAdditions['additions'], $rowAdditions['active_res_value'],
        $rate_used, $disposalValue
    );

    if (!mysqli_stmt_execute($ins)) {
        error_log('Disposal insert failed: ' . mysqli_error($conn));
        echo '<script>alert("Failed to record disposal. Please try again."); window.location.href="index.php";</script>';
        exit();
    }
    mysqli_stmt_close($ins);

    $up = mysqli_prepare($conn, "UPDATE assets SET disposals = 1 WHERE asset_id = ?");
    mysqli_stmt_bind_param($up, 'i', $assetId);

    if (mysqli_stmt_execute($up)) {
        echo '<script>alert("Asset successfully disposed!"); window.location.href="index.php";</script>';
    } else {
        error_log('Disposal flag update failed: ' . mysqli_error($conn));
        echo '<script>alert("Failed to update disposal flag. Please try again."); window.location.href="index.php";</script>';
    }
    mysqli_stmt_close($up);
    exit();
}

// ── Build main asset query with optional class filter (prepared statement) ─

$assetClassFilter = isset($_GET['asset_class_filter']) && $_GET['asset_class_filter'] !== ''
    ? trim($_GET['asset_class_filter']) : null;

if ($assetClassFilter) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM assets WHERE disposals = 0 AND asset_class = ?");
    mysqli_stmt_bind_param($stmt, 's', $assetClassFilter);
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM assets WHERE disposals = 0");
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>View Active Assets</title>
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="assets/images/favicon.png">
</head>
<body>
  <div class="container-scroller">
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center"></div>
      <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown">
              <div class="nav-profile-img">
                <img src="assets/images/favicon.png" alt="User avatar">
              </div>
              <div class="nav-profile-text">
                <p class="mb-1 text-black"><?= esc($username) ?></p>
              </div>
            </a>
            <div class="dropdown-menu navbar-dropdown dropdown-menu-right p-0 border-0 font-size-sm" aria-labelledby="profileDropdown">
              <div class="p-3 text-center bg-light">
                <img class="img-avatar img-avatar48 img-avatar-thumb" src="assets/images/favicon.png" alt="">
              </div>
              <div class="p-2">
                <h5 class="dropdown-header text-uppercase pl-2 text-dark">User Options</h5>
                <div role="separator" class="dropdown-divider"></div>
                <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="logout.php">
                  <span>Log Out</span>
                  <i class="mdi mdi-logout ml-1"></i>
                </a>
              </div>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>

    <div class="container-fluid page-body-wrapper">
      <?php include "../sidebar.html" ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="d-xl-flex justify-content-between align-items-start">
            <h2 class="text-dark font-weight-bold mb-2">Filter Active Assets by Classes</h2>
          </div>

          <!-- Asset class filter form -->
          <form method="GET" id="filterForm" style="margin-bottom:15px;">
            <select name="asset_class_filter" onchange="document.getElementById('filterForm').submit()">
              <option value="">All Asset Classes</option>
              <?php foreach ($assetClasses as $ac): ?>
                <option value="<?= esc($ac) ?>" <?= $assetClassFilter === $ac ? 'selected' : '' ?>>
                  <?= esc($ac) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </form>

          <div class="row">
            <div class="col-md-12">
              <div class="tab-content tab-transparent-content overflow-auto">
                <div class="tab-pane fade show active">
                  <div class="row">
                    <table class="table">
                      <caption>Active Assets</caption>
                      <thead>
                        <tr>
                          <th scope="col">S/n</th>
                          <th scope="col">Asset Name</th>
                          <th scope="col">Asset Class</th>
                          <th scope="col">Sub Class</th>
                          <th scope="col">Assigned User</th>
                          <th scope="col">Serial Number</th>
                          <th scope="col">GRV Number</th>
                          <th scope="col">ID Number</th>
                          <th scope="col">PV Number</th>
                          <th scope="col">Disposals</th>
                          <th scope="col">Asset Type</th>
                          <th scope="col">Location</th>
                          <th scope="col">Acquisition Date</th>
                          <th scope="col">Historical Cost (GHS)</th>
                          <th scope="col">Dollar Rate</th>
                          <th scope="col">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $serial_number = 1;
                        while ($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                          <td><?= esc($serial_number++) ?></td>
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
                            <!-- Move button -->
                            <button type="button" class="btn btn-primary btn-sm"
                              data-toggle="modal"
                              data-target="#moveModal<?= esc($row['asset_id']) ?>">
                              Move Asset
                            </button>

                            <!-- Archive form -->
                            <form style="display:inline" action="index.php" method="POST"
                              onsubmit="return confirm('Archive this asset?')">
                              <?= csrf_field() ?>
                              <input type="hidden" name="submit_archive" value="1">
                              <input type="hidden" name="asset_id" value="<?= esc($row['asset_id']) ?>">
                              <button type="submit" class="btn btn-danger btn-sm">Archive</button>
                            </form>

                            <!-- Dispose form -->
                            <form style="display:inline" action="index.php" method="POST"
                              onsubmit="return confirm('Dispose this asset?')">
                              <?= csrf_field() ?>
                              <input type="hidden" name="submit_disposal" value="1">
                              <input type="hidden" name="asset_id" value="<?= esc($row['asset_id']) ?>">
                              <button type="submit" class="btn btn-success btn-sm">Dispose</button>
                            </form>
                          </td>
                        </tr>

                        <!-- Move Modal (one per row, using prefetched location/user lists) -->
                        <div class="modal fade" id="moveModal<?= esc($row['asset_id']) ?>"
                          tabindex="-1" role="dialog" aria-labelledby="moveModalLabel<?= esc($row['asset_id']) ?>"
                          aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="moveModalLabel<?= esc($row['asset_id']) ?>">
                                  Move Asset Location
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <form action="index.php" method="POST">
                                  <?= csrf_field() ?>
                                  <input type="hidden" name="asset_id" value="<?= esc($row['asset_id']) ?>">
                                  <input type="hidden" name="submit_move" value="1">

                                  <div class="form-group">
                                    <label for="location_<?= esc($row['asset_id']) ?>">Current Location:</label>
                                    <input type="text" id="location_<?= esc($row['asset_id']) ?>"
                                      value="<?= esc($row['location']) ?>" class="form-control" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label for="user_<?= esc($row['asset_id']) ?>">Current User:</label>
                                    <input type="text" id="user_<?= esc($row['asset_id']) ?>"
                                      value="<?= esc($row['user']) ?>" class="form-control" readonly>
                                  </div>
                                  <div class="form-group">
                                    <label for="new_location_<?= esc($row['asset_id']) ?>">
                                      Select New Asset Location:
                                    </label>
                                    <select id="new_location_<?= esc($row['asset_id']) ?>"
                                      name="new_location" class="form-control" required>
                                      <option hidden value="">Select New Asset Location</option>
                                      <?php foreach ($locations as $loc): ?>
                                        <option value="<?= esc($loc) ?>"><?= esc($loc) ?></option>
                                      <?php endforeach; ?>
                                    </select>
                                  </div>
                                  <div class="form-group">
                                    <label for="new_user_<?= esc($row['asset_id']) ?>">
                                      Select New User:
                                    </label>
                                    <select id="new_user_<?= esc($row['asset_id']) ?>"
                                      name="new_user" class="form-control" required>
                                      <option hidden value="">Select New Asset User</option>
                                      <?php foreach ($users as $u): ?>
                                        <option value="<?= esc($u) ?>"><?= esc($u) ?></option>
                                      <?php endforeach; ?>
                                    </select>
                                  </div>
                                  <div class="form-group">
                                    <label for="notes_<?= esc($row['asset_id']) ?>">Notes:</label>
                                    <input type="text" id="notes_<?= esc($row['asset_id']) ?>"
                                      name="notes" class="form-control" required
                                      oninput="capitalizeFirstLetter(this)">
                                  </div>
                                  <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div><!-- content-wrapper -->

        <footer class="footer">
          <div class="footer-inner-wraper">
            <div class="d-sm-flex justify-content-center justify-content-sm-between"></div>
          </div>
        </footer>
      </div><!-- main-panel -->
    </div><!-- page-body-wrapper -->
  </div><!-- container-scroller -->

  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <script src="assets/vendors/chart.js/Chart.min.js"></script>
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/hoverable-collapse.js"></script>
  <script src="assets/js/misc.js"></script>
  <script src="assets/js/dashboard.js"></script>
  <script>
    function capitalizeFirstLetter(input) {
      input.value = input.value.replace(/\b\w/g, function(c) { return c.toUpperCase(); });
    }
  </script>
</body>
</html>
