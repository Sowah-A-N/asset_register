<?php
/**
 * FILE:    schedule_officer/view_assets/index.php
 * PURPOSE: View, filter, edit, move, archive and dispose active assets.
 *
 * FIXES APPLIED:
 *  - CRITICAL: $rate_used = (line 195 in original) → fatal parse error fixed
 *  - Archive query had hardcoded asset_register_new. prefix → removed
 *  - Archive query included 'disposed' column not in assets_archive → removed
 *  - Move handler used deprecated mysqli_escape_string → mysqli_real_escape_string
 *  - Edit/Move modals generated per-asset in PHP loop (N+1 queries) → single
 *    JS-populated modal; locations/users/types loaded once before loop
 *  - Missing <tr> wrapper around table cells in loop → fixed
 *  - Logout link pointed to non-existent logout.php in same dir → ../logout/
 *  - Bootstrap 4 vendor bundle → Bootstrap 5.3 CDN + DataTables
 *  - Old sidebar.html → new partials/sidebar.php
 */

session_start();
require_once '../../auth.php';
requirePermission('asset.view', '../login/');   // RBAC: must hold asset.view

$username    = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));

include '../datacon.php';

/* Per-action permission guard — friendly redirect if not allowed. */
function guard(string $perm): void {
    if (!can($perm)) {
        echo "<script>alert('You do not have permission to perform this action.');"
           . "window.location='index.php';</script>";
        exit();
    }
}

if (!$conn) {
    error_log('[RMU] view_assets DB connection failed');
    die('Database connection error. Please try again.');
}

/* ══════════════════════════════════════════════════════════
   PRE-LOAD DROPDOWN DATA ONCE (eliminates N+1 per-row queries)
   ══════════════════════════════════════════════════════════ */
$locationsList = [];
$r = mysqli_query($conn, "SELECT location FROM asset_location ORDER BY location ASC");
while ($row = mysqli_fetch_assoc($r)) $locationsList[] = $row['location'];

$usersList = [];
$r = mysqli_query($conn,
    "SELECT CONCAT(staff_first_name,' ',staff_last_name) AS fullname
     FROM asset_users ORDER BY staff_first_name ASC");
while ($row = mysqli_fetch_assoc($r)) $usersList[] = $row['fullname'];

$assetTypesList = [];
$r = mysqli_query($conn, "SELECT asset_type FROM asset_type ORDER BY asset_type ASC");
while ($row = mysqli_fetch_assoc($r)) $assetTypesList[] = $row['asset_type'];

/* ══════════════════════════════════════════════════════════
   POST HANDLERS
   ══════════════════════════════════════════════════════════ */

/* ── Edit asset ─────────────────────────────────────────── */
if (isset($_POST['submit_edit'])) {
    guard('asset.edit');
    $asset_id       = mysqli_real_escape_string($conn, $_POST['asset_id']       ?? '');
    $asset_name     = mysqli_real_escape_string($conn, $_POST['asset_name']     ?? '');
    $dollar_rate    = mysqli_real_escape_string($conn, $_POST['dollar_rate']    ?? '');
    $historical_cost = mysqli_real_escape_string($conn, $_POST['historical_cost'] ?? '');
    $location       = mysqli_real_escape_string($conn, $_POST['location']       ?? '');
    $user           = mysqli_real_escape_string($conn, $_POST['user']           ?? '');
    $asset_type     = mysqli_real_escape_string($conn, $_POST['asset_type']     ?? '');

    $sql = "UPDATE assets
            SET asset_name       = '$asset_name',
                dollar_rate_used = '$dollar_rate',
                additions        = '$historical_cost',
                location         = '$location',
                user             = '$user',
                asset_type       = '$asset_type'
            WHERE serial_number  = '$asset_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('Asset updated successfully.');
            window.location.href = 'index.php';
        </script>";
    } else {
        error_log('[RMU] Asset edit failed: ' . mysqli_error($conn));
        echo "<script>alert('Error updating asset. Please try again.'); window.location.href='index.php';</script>";
    }
    exit();
}

/* ── Archive asset ──────────────────────────────────────── */
if (isset($_POST['submit_archive'])) {
    guard('asset.archive');
    $archiveId = mysqli_real_escape_string($conn, $_POST['asset_id'] ?? '');

    mysqli_autocommit($conn, false);

    // FIX: removed hardcoded 'asset_register_new.' prefix and 'disposed' column
    // (assets_archive does not have a 'disposed' column)
    $copyQuery = "INSERT INTO assets_archive (
                    asset_name, grv_number, serial_number, pv_number, id_number,
                    supplier_name, asset_class, sub_class, asset_type, location, user,
                    acquisition_date, current_year, historical_cost, additions,
                    disposals, active_res_value, dollar_rate_used, date_added
                  )
                  SELECT
                    asset_name, grv_number, serial_number, pv_number, id_number,
                    supplier_name, asset_class, sub_class, asset_type, location, user,
                    acquisition_date, current_year, historical_cost, additions,
                    disposals, active_res_value, dollar_rate_used, date_added
                  FROM assets
                  WHERE asset_id = '$archiveId'";

    $deleteQuery = "DELETE FROM assets WHERE asset_id = '$archiveId'";

    $ok = mysqli_query($conn, $copyQuery) && mysqli_query($conn, $deleteQuery);

    if ($ok) {
        mysqli_commit($conn);
        echo "<script>alert('Asset archived successfully.'); window.location.href='index.php';</script>";
    } else {
        mysqli_rollback($conn);
        error_log('[RMU] Archive failed: ' . mysqli_error($conn));
        echo "<script>alert('Error archiving asset. Please try again.'); window.location.href='index.php';</script>";
    }
    mysqli_autocommit($conn, true);
    exit();
}

/* ── Move asset ─────────────────────────────────────────── */
if (isset($_POST['submit_move'])) {
    guard('asset.move');
    $serial_number = mysqli_real_escape_string($conn, $_POST['asset_id']     ?? '');
    $new_location  = mysqli_real_escape_string($conn, $_POST['new_location'] ?? '');
    $new_user      = mysqli_real_escape_string($conn, $_POST['new_user']     ?? '');
    $notes         = mysqli_real_escape_string($conn, $_POST['notes']        ?? '');

    if (empty($new_location) || empty($new_user) || empty($notes)) {
        echo "<script>alert('All move fields are required.'); window.location.href='index.php';</script>";
        exit();
    }

    // Fetch current values
    $current = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT location, user FROM assets WHERE serial_number = '$serial_number'")
    );

    if (!$current) {
        echo "<script>alert('Asset not found.'); window.location.href='index.php';</script>";
        exit();
    }

    $old_location = $current['location'];
    $old_user     = $current['user'];

    if ($old_location === $new_location && $old_user === $new_user) {
        echo "<script>alert('Location and user are the same — no change made.'); window.location.href='index.php';</script>";
        exit();
    }

    $updateSql = "UPDATE assets
                  SET location = '$new_location', user = '$new_user'
                  WHERE serial_number = '$serial_number'";

    $insertSql = "INSERT INTO moved_assets
                    (serial_number, notes, old_location, old_user, New_location, new_user)
                  VALUES ('$serial_number','$notes','$old_location','$old_user','$new_location','$new_user')";

    if (mysqli_query($conn, $updateSql) && mysqli_query($conn, $insertSql)) {
        echo "<script>alert('Asset location updated successfully.'); window.location.href='index.php';</script>";
    } else {
        error_log('[RMU] Move failed: ' . mysqli_error($conn));
        echo "<script>alert('Error moving asset. Please try again.'); window.location.href='index.php';</script>";
    }
    exit();
}

/* ── Dispose asset ──────────────────────────────────────── */
if (isset($_POST['submit_disposal'])) {
    guard('asset.dispose');
    $assetId = mysqli_real_escape_string($conn, $_POST['asset_id'] ?? '');

    $stmt = mysqli_prepare($conn, "SELECT * FROM assets WHERE asset_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $assetId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (!$res || mysqli_num_rows($res) === 0) {
        echo "<script>alert('Asset not found.'); window.location.href='index.php';</script>";
        exit();
    }

    $asset = mysqli_fetch_assoc($res);

    $disposalValue  = (float)$asset['additions'];
    $currentYear    = (int)date('Y');
    $rate_used      = $asset['dollar_rate_used']; // FIX: was "$rate_used =" (fatal parse error)

    if ($disposalValue < 0) {
        echo "<script>alert('Disposal value cannot be negative.'); window.location.href='index.php';</script>";
        exit();
    }

    $insertStmt = mysqli_prepare($conn,
        "INSERT INTO disposals
            (asset_name, asset_class, sub_class, grv_number, serial_number, pv_number,
             id_number, supplier_name, asset_type, location, user, acquisition_date,
             current_year, additions, active_res_value, dollar_rate_used, disposal_value)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    mysqli_stmt_bind_param($insertStmt, 'ssssssssssssissdd',
        $asset['asset_name'],
        $asset['asset_class'],
        $asset['sub_class'],
        $asset['grv_number'],
        $asset['serial_number'],
        $asset['pv_number'],
        $asset['id_number'],
        $asset['supplier_name'],
        $asset['asset_type'],
        $asset['location'],
        $asset['user'],
        $asset['acquisition_date'],
        $currentYear,
        $asset['additions'],
        $asset['active_res_value'],
        $rate_used,
        $disposalValue
    );

    if (!mysqli_stmt_execute($insertStmt)) {
        error_log('[RMU] Disposal insert failed: ' . mysqli_error($conn));
        echo "<script>alert('Error recording disposal. Please try again.'); window.location.href='index.php';</script>";
        exit();
    }

    $updateStmt = mysqli_prepare($conn, "UPDATE assets SET disposals = 1 WHERE asset_id = ?");
    mysqli_stmt_bind_param($updateStmt, "s", $assetId);

    if (mysqli_stmt_execute($updateStmt)) {
        echo "<script>alert('Asset disposed successfully.'); window.location.href='index.php';</script>";
    } else {
        error_log('[RMU] Disposal flag update failed: ' . mysqli_error($conn));
        echo "<script>alert('Disposal recorded but asset flag not updated. Please contact admin.'); window.location.href='index.php';</script>";
    }
    exit();
}

/* ══════════════════════════════════════════════════════════
   LOAD ASSET LIST
   ══════════════════════════════════════════════════════════ */
$classFilter = mysqli_real_escape_string($conn, $_GET['asset_class_filter'] ?? '');

if ($classFilter !== '') {
    $sql = "SELECT * FROM assets WHERE asset_class='$classFilter' AND disposals=0 ORDER BY date_added DESC";
} else {
    $sql = "SELECT * FROM assets WHERE disposals=0 ORDER BY date_added DESC";
}

$result     = mysqli_query($conn, $sql);
$totalCount = mysqli_num_rows($result);

// Asset classes for filter dropdown
$assetClasses = [];
$r = mysqli_query($conn, "SELECT DISTINCT asset_class FROM asset_classes ORDER BY asset_class ASC");
while ($row = mysqli_fetch_assoc($r)) $assetClasses[] = $row['asset_class'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Active Assets — RMU Asset Register</title>

  <!-- Bootstrap 5.3 -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- DataTables + Bootstrap 5 integration -->
  <link rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

  <!-- RMU Design System -->
  <link rel="stylesheet" href="../../assets/css/v2.css">

  <link rel="shortcut icon" href="../../assets/img/rmulog.png">

  <style>
    /* Table action dropdown */
    .action-menu .dropdown-toggle::after { display: none; }
    .action-menu .btn {
      padding: .3rem .65rem;
      font-size: .78rem;
    }
    /* Compact table */
    .rmu-table td, .rmu-table th { white-space: nowrap; }
    .asset-name-cell { max-width: 240px; white-space: normal !important; }
    .cost-cell { font-variant-numeric: tabular-nums; text-align: right; }
  </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="rmu-layout">

  <!-- ═══ SIDEBAR ═══════════════════════════════════════ -->
  <?php require '../partials/sidebar.php'; ?>

  <!-- ═══ MAIN ═══════════════════════════════════════════ -->
  <div class="rmu-main">

    <!-- TOP NAV -->
    <header class="rmu-topnav">
      <button class="topnav-toggle" id="sidebarToggle">
        <i class="bi bi-list"></i>
      </button>

      <div class="topnav-breadcrumb">
        <h1>Active Assets</h1>
        <small>
          <?php if ($classFilter): ?>
            Filtered by: <strong><?= htmlspecialchars($classFilter) ?></strong> &mdash;
          <?php endif; ?>
          <?= number_format($totalCount) ?> asset<?= $totalCount !== 1 ? 's' : '' ?>
        </small>
      </div>

      <div class="topnav-actions">
        <?php if (can('asset.create')): ?>
        <a href="../new_asset/" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Add Asset
        </a>
        <?php endif; ?>
        <div class="topnav-divider"></div>
        <div class="dropdown">
          <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
            <div class="av-circle"><?= $userInitial ?></div>
            <div class="av-info">
              <div class="av-name"><?= $username ?></div>
              <div class="av-role">Schedule Officer</div>
            </div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="../dashboard/"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
          </ul>
        </div>
      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="rmu-content">

      <!-- Filter bar -->
      <div class="rmu-card mb-4">
        <div class="rmu-card-body" style="padding: 1rem 1.5rem;">
          <form method="GET" id="filterForm" class="d-flex align-items-center gap-3 flex-wrap">
            <label class="form-label mb-0 fw-600" style="white-space:nowrap;font-size:.82rem;">
              Filter by Class
            </label>
            <select name="asset_class_filter" class="form-select form-select-sm" style="max-width:280px;" id="classFilter">
              <option value="">— All Asset Classes —</option>
              <?php foreach ($assetClasses as $cls): ?>
                <option value="<?= htmlspecialchars($cls) ?>"
                  <?= $classFilter === $cls ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cls) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-funnel me-1"></i>Apply
            </button>
            <?php if ($classFilter): ?>
              <a href="index.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Clear
              </a>
            <?php endif; ?>
          </form>
        </div>
      </div>

      <!-- Asset Table -->
      <?php if ($totalCount === 0): ?>
        <div class="rmu-card">
          <div class="rmu-card-body">
            <div class="rmu-empty">
              <i class="bi bi-inbox rmu-empty-icon"></i>
              <h6>No active assets found</h6>
              <p><?= $classFilter ? 'No assets in this class.' : 'Add your first asset to get started.' ?></p>
              <a href="../new_asset/" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-plus-lg me-1"></i>Add Asset
              </a>
            </div>
          </div>
        </div>

      <?php else: ?>
      <div class="rmu-table-wrapper">
        <table class="rmu-table" id="assetsTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Asset Name</th>
              <th>Class</th>
              <th>Sub-Class</th>
              <th>Location</th>
              <th>User</th>
              <th>Serial No.</th>
              <th>GRV No.</th>
              <th>PV No.</th>
              <th>Acquired</th>
              <th class="text-end">Cost (GHS)</th>
              <th>Rate</th>
              <th style="width:60px">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sn = 1;
          while ($row = mysqli_fetch_assoc($result)):
            // Build JSON payload for JS modal population (XSS-safe)
            $assetJson = htmlspecialchars(json_encode([
              'asset_id'    => $row['asset_id'],
              'serial'      => $row['serial_number'],
              'name'        => $row['asset_name'],
              'rate'        => $row['dollar_rate_used'],
              'cost'        => $row['additions'],
              'location'    => $row['location'],
              'user'        => $row['user'],
              'asset_type'  => $row['asset_type'],
            ], JSON_UNESCAPED_UNICODE), ENT_QUOTES);

            $acqDate = ($row['acquisition_date'] && $row['acquisition_date'] !== '0000-00-00')
                     ? date('d M Y', strtotime($row['acquisition_date']))
                     : '—';
          ?>
            <tr>
              <td><?= $sn++ ?></td>
              <td class="asset-name-cell">
                <div class="fw-600" style="font-size:.85rem;"><?= htmlspecialchars($row['asset_name']) ?></div>
              </td>
              <td><?= htmlspecialchars($row['asset_class']) ?></td>
              <td><?= htmlspecialchars($row['sub_class'] ?: '—') ?></td>
              <td><?= htmlspecialchars($row['location']) ?></td>
              <td><?= htmlspecialchars($row['user'] ?: '—') ?></td>
              <td><code style="font-size:.78rem"><?= htmlspecialchars($row['serial_number'] ?: '—') ?></code></td>
              <td><?= htmlspecialchars($row['grv_number'] ?: '—') ?></td>
              <td><?= htmlspecialchars($row['pv_number'] ?: '—') ?></td>
              <td><?= $acqDate ?></td>
              <td class="cost-cell">
                <?= number_format((float)$row['additions'], 2, '.', ',') ?>
              </td>
              <td><?= htmlspecialchars($row['dollar_rate_used']) ?></td>
              <td class="action-menu">
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                          data-bs-toggle="dropdown" title="Actions">
                    <i class="bi bi-three-dots"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <?php if (can('asset.edit')): ?>
                    <li>
                      <button class="dropdown-item btn-edit"
                              data-asset="<?= $assetJson ?>">
                        <i class="bi bi-pencil me-2 text-warning"></i>Edit
                      </button>
                    </li>
                    <?php endif; ?>
                    <?php if (can('asset.move')): ?>
                    <li>
                      <button class="dropdown-item btn-move"
                              data-asset="<?= $assetJson ?>">
                        <i class="bi bi-arrow-left-right me-2 text-primary"></i>Move
                      </button>
                    </li>
                    <?php endif; ?>
                    <?php if (can('asset.archive') || can('asset.dispose')): ?>
                    <li><hr class="dropdown-divider"></li>
                    <?php endif; ?>
                    <?php if (can('asset.archive')): ?>
                    <li>
                      <button class="dropdown-item btn-archive"
                              data-asset-id="<?= $row['asset_id'] ?>"
                              data-asset-name="<?= htmlspecialchars($row['asset_name'], ENT_QUOTES) ?>">
                        <i class="bi bi-archive me-2 text-secondary"></i>Archive
                      </button>
                    </li>
                    <?php endif; ?>
                    <?php if (can('asset.dispose')): ?>
                    <li>
                      <button class="dropdown-item btn-dispose text-danger"
                              data-asset-id="<?= $row['asset_id'] ?>"
                              data-asset-name="<?= htmlspecialchars($row['asset_name'], ENT_QUOTES) ?>">
                        <i class="bi bi-trash3 me-2"></i>Dispose
                      </button>
                    </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

    </main>

    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Version 2.0</span>
      <span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>

  </div><!-- /rmu-main -->
</div><!-- /rmu-layout -->

<!-- ══════════════════════════════════════════════════════
     EDIT MODAL (single — populated by JS)
     ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">
          <i class="bi bi-pencil me-2 text-warning"></i>Edit Asset
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="index.php" method="POST">
        <input type="hidden" name="submit_edit" value="1">
        <input type="hidden" name="asset_id" id="edit_asset_id">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Asset Name</label>
              <input type="text" class="form-control" name="asset_name"
                     id="edit_asset_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Dollar Rate Used</label>
              <div class="input-group">
                <span class="input-group-text">GH₵</span>
                <input type="number" step="0.0001" class="form-control"
                       name="dollar_rate" id="edit_dollar_rate" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Historical Cost (GHS)</label>
              <div class="input-group">
                <span class="input-group-text">GH₵</span>
                <input type="number" step="0.01" class="form-control"
                       name="historical_cost" id="edit_historical_cost" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <select name="location" id="edit_location" class="form-select" required>
                <option value="">Select location…</option>
                <?php foreach ($locationsList as $loc): ?>
                  <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Assigned User</label>
              <select name="user" id="edit_user" class="form-select" required>
                <option value="">Select user…</option>
                <?php foreach ($usersList as $u): ?>
                  <option value="<?= htmlspecialchars($u) ?>"><?= htmlspecialchars($u) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label">Asset Type</label>
              <select name="asset_type" id="edit_asset_type" class="form-select" required>
                <option value="">Select type…</option>
                <?php foreach ($assetTypesList as $t): ?>
                  <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MOVE MODAL (single — populated by JS)
     ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="moveModal" tabindex="-1" aria-labelledby="moveModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="moveModalLabel">
          <i class="bi bi-arrow-left-right me-2 text-primary"></i>Move Asset
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="index.php" method="POST">
        <input type="hidden" name="submit_move" value="1">
        <input type="hidden" name="asset_id" id="move_asset_id">
        <div class="modal-body">
          <div class="mb-3 p-3 rounded" style="background:var(--page-bg);">
            <div class="row g-2">
              <div class="col-6">
                <small class="text-muted d-block">Current Location</small>
                <strong id="move_current_location" class="small">—</strong>
              </div>
              <div class="col-6">
                <small class="text-muted d-block">Current User</small>
                <strong id="move_current_user" class="small">—</strong>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">New Location</label>
            <select name="new_location" id="move_new_location" class="form-select" required>
              <option value="">Select new location…</option>
              <?php foreach ($locationsList as $loc): ?>
                <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">New Assigned User</label>
            <select name="new_user" id="move_new_user" class="form-select" required>
              <option value="">Select new user…</option>
              <?php foreach ($usersList as $u): ?>
                <option value="<?= htmlspecialchars($u) ?>"><?= htmlspecialchars($u) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason / Notes</label>
            <input type="text" name="notes" class="form-control"
                   placeholder="Reason for move…" required
                   oninput="this.value=this.value.replace(/\b\w/g,c=>c.toUpperCase())">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>Confirm Move
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     ARCHIVE CONFIRM MODAL
     ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-archive me-2"></i>Archive Asset</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">Archive <strong id="archive_asset_name"></strong>?</p>
        <p class="text-muted small mb-0">The asset will be moved to the archive and removed from the active register.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <form action="index.php" method="POST" style="display:inline">
          <input type="hidden" name="submit_archive" value="1">
          <input type="hidden" name="asset_id" id="archive_asset_id">
          <button type="submit" class="btn btn-secondary btn-sm">
            <i class="bi bi-archive me-1"></i>Archive
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     DISPOSE CONFIRM MODAL
     ══════════════════════════════════════════════════════ -->
<div class="modal fade" id="disposeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-trash3 me-2"></i>Dispose Asset</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">Dispose of <strong id="dispose_asset_name"></strong>?</p>
        <p class="text-muted small mb-0">This records a permanent write-off. The asset will be flagged as disposed and a record created in the disposals register.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <form action="index.php" method="POST" style="display:inline">
          <input type="hidden" name="submit_disposal" value="1">
          <input type="hidden" name="asset_id" id="dispose_asset_id">
          <button type="submit" class="btn btn-danger btn-sm">
            <i class="bi bi-trash3 me-1"></i>Confirm Disposal
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     SCRIPTS
     ══════════════════════════════════════════════════════ -->
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
/* ── Sidebar toggle ─────────────────────────────────── */
const sbToggle  = document.getElementById('sidebarToggle');
const sbOverlay = document.getElementById('sidebarOverlay');
function toggleSb() { document.body.classList.toggle('sidebar-open'); }
if (sbToggle)  sbToggle.addEventListener('click', toggleSb);
if (sbOverlay) sbOverlay.addEventListener('click', toggleSb);

/* ── DataTables ─────────────────────────────────────── */
$(document).ready(function() {
  if ($('#assetsTable').length) {
    $('#assetsTable').DataTable({
      pageLength: 25,
      lengthMenu: [10, 25, 50, 100],
      order: [],
      columnDefs: [
        { orderable: false, targets: [12] },   // Actions column
        { searchable: false, targets: [0] }    // # column
      ],
      language: {
        search: '',
        searchPlaceholder: 'Search assets…',
        lengthMenu: 'Show _MENU_ assets',
        info: 'Showing _START_ to _END_ of _TOTAL_ assets',
        infoFiltered: '(filtered from _MAX_ total)',
        zeroRecords: 'No matching assets found.',
        emptyTable: 'No active assets.'
      },
      dom: '<"d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2"lf>rtip'
    });
  }
});

/* ── Action dropdowns: escape the overflow-x:auto table wrapper ──
   The table wrapper scrolls horizontally (overflow-x:auto), which per the
   CSS spec also clips vertically — so a Popper-positioned dropdown menu was
   being cut off (button appeared to "do nothing"). Forcing Popper's `fixed`
   strategy positions the menu against the viewport so it escapes the clip. */
document.querySelectorAll('.action-menu [data-bs-toggle="dropdown"]').forEach(el => {
  bootstrap.Dropdown.getOrCreateInstance(el, {
    popperConfig(defaultConfig) {
      return Object.assign({}, defaultConfig, { strategy: 'fixed' });
    }
  });
});

/* ── EDIT modal — populate from data-asset JSON ─────── */
document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', function () {
    const a = JSON.parse(this.dataset.asset);

    document.getElementById('edit_asset_id').value       = a.serial;  // WHERE serial_number = ?
    document.getElementById('edit_asset_name').value     = a.name;
    document.getElementById('edit_dollar_rate').value    = a.rate;
    document.getElementById('edit_historical_cost').value = a.cost;

    // Set select values
    setSelectValue('edit_location',   a.location);
    setSelectValue('edit_user',       a.user);
    setSelectValue('edit_asset_type', a.asset_type);

    new bootstrap.Modal(document.getElementById('editModal')).show();
  });
});

/* ── MOVE modal — populate from data-asset JSON ─────── */
document.querySelectorAll('.btn-move').forEach(btn => {
  btn.addEventListener('click', function () {
    const a = JSON.parse(this.dataset.asset);

    document.getElementById('move_asset_id').value       = a.serial;
    document.getElementById('move_current_location').textContent = a.location || '—';
    document.getElementById('move_current_user').textContent     = a.user     || '—';
    document.getElementById('move_new_location').value   = '';
    document.getElementById('move_new_user').value       = '';

    new bootstrap.Modal(document.getElementById('moveModal')).show();
  });
});

/* ── ARCHIVE confirm modal ──────────────────────────── */
document.querySelectorAll('.btn-archive').forEach(btn => {
  btn.addEventListener('click', function () {
    document.getElementById('archive_asset_id').value   = this.dataset.assetId;
    document.getElementById('archive_asset_name').textContent = this.dataset.assetName;
    new bootstrap.Modal(document.getElementById('archiveModal')).show();
  });
});

/* ── DISPOSE confirm modal ──────────────────────────── */
document.querySelectorAll('.btn-dispose').forEach(btn => {
  btn.addEventListener('click', function () {
    document.getElementById('dispose_asset_id').value   = this.dataset.assetId;
    document.getElementById('dispose_asset_name').textContent = this.dataset.assetName;
    new bootstrap.Modal(document.getElementById('disposeModal')).show();
  });
});

/* ── Helper: set a select element's value ───────────── */
function setSelectValue(id, value) {
  const sel = document.getElementById(id);
  if (!sel || !value) return;
  for (let i = 0; i < sel.options.length; i++) {
    if (sel.options[i].value === value) {
      sel.selectedIndex = i;
      return;
    }
  }
  // Value not in list — add it temporarily so form doesn't lose data
  const opt = new Option(value, value, true, true);
  sel.add(opt);
}
</script>

</body>
</html>
