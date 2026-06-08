<?php
/**
 * FILE:    schedule_officer/set_rate/index.php
 * PURPOSE: View dollar-rate history and set a new active rate — v2 redesign.
 *
 * FIXES: the rate-history table had <td> cells with no <tr> wrapper and a
 * stray </form></tr> (broken markup) — corrected here. The "Set New Rate"
 * action is gated on the rate.set permission.
 */

session_start();
require_once '../../auth.php';
requirePermission('rate.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

$notice = '';
if (isset($_POST['add'])) {
    if (!can('rate.set')) {
        echo "<script>alert('You do not have permission to change the dollar rate.'); window.location='index.php';</script>";
        exit();
    }
    $dollar_rate = mysqli_real_escape_string($conn, $_POST['dollar_rate'] ?? '');
    if ($dollar_rate === '' || !is_numeric($dollar_rate) || (float)$dollar_rate <= 0) {
        echo "<script>alert('Please enter a valid positive dollar rate.'); window.location='index.php';</script>";
        exit();
    }
    mysqli_query($conn, "UPDATE dollar_rate SET rate_status='INACTIVE' WHERE rate_status='ACTIVE'");
    $stmt = mysqli_prepare($conn, "INSERT INTO dollar_rate (dollar_rate, rate_status, action_by) VALUES (?, 'ACTIVE', ?)");
    mysqli_stmt_bind_param($stmt, "ds", $dollar_rate, $username);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Dollar rate changed successfully.'); window.location='index.php';</script>";
        exit();
    } else {
        error_log('[RMU] set_rate insert failed: ' . mysqli_error($conn));
        echo "<script>alert('Could not save the rate. Please try again.'); window.location='index.php';</script>";
        exit();
    }
}

// Active rate
$activeRow  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT dollar_rate, date_added FROM dollar_rate WHERE rate_status='ACTIVE' ORDER BY table_ID DESC LIMIT 1"));
$activeRate = $activeRow ? (float)$activeRow['dollar_rate'] : 0;

// History (last 15)
$history = [];
$hr = mysqli_query($conn, "SELECT dollar_rate, date_added, rate_status, action_by FROM dollar_rate ORDER BY table_ID DESC LIMIT 15");
while ($r = mysqli_fetch_assoc($hr)) $history[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dollar Rate — RMU Asset Register</title>
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
        <h1>Dollar Rate</h1>
        <small>GHS / USD exchange rate used for asset valuation</small>
      </div>
      <div class="topnav-actions">
        <?php if (can('rate.set')): ?>
          <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#rateModal">
            <i class="bi bi-plus-lg me-1"></i>Set New Rate
          </button>
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
      <!-- Active rate highlight -->
      <div class="row g-3 section-gap">
        <div class="col-md-5 col-lg-4">
          <div class="kpi-card kpi-green" style="height:100%">
            <div class="kpi-label">Current Active Rate</div>
            <div class="kpi-value num"><?= $activeRate > 0 ? 'GH₵ '.number_format($activeRate, 4) : '<span style="font-size:1rem;color:var(--c-red)">NOT SET</span>' ?></div>
            <div class="kpi-sub"><?= $activeRow ? 'Since '.date('d M Y', strtotime($activeRow['date_added'])) : 'No active rate on record' ?></div>
            <i class="bi bi-currency-exchange kpi-bg-icon"></i>
          </div>
        </div>
        <div class="col-md-7 col-lg-8">
          <div class="rmu-card" style="height:100%">
            <div class="rmu-card-body d-flex align-items-center gap-3" style="height:100%">
              <span class="qa-icon" style="width:48px;height:48px;background:#dbeafe;color:var(--c-blue);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0"><i class="bi bi-info-circle"></i></span>
              <div>
                <div style="font-weight:600;color:var(--text-primary)">How the rate is used</div>
                <p class="text-muted mb-0" style="font-size:.83rem">New assets record the active rate at the time of entry (their historical rate). Setting a new rate marks the previous one inactive and applies the new rate to future entries — it does not re-translate existing assets.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- History -->
      <div class="rmu-card">
        <div class="rmu-card-header">
          <div class="rmu-card-title">Rate History</div>
          <small class="text-muted">Most recent 15 changes</small>
        </div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($history)): ?>
            <div class="rmu-empty"><i class="bi bi-clock-history rmu-empty-icon"></i><h6>No rate history</h6></div>
          <?php else: ?>
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th class="num" style="text-align:right">Rate (GH₵)</th><th>Status</th><th>Set By</th><th>Date</th></tr></thead>
              <tbody>
                <?php $sn = 1; foreach ($history as $h): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td style="text-align:right;font-variant-numeric:tabular-nums"><?= number_format((float)$h['dollar_rate'], 4) ?></td>
                    <td>
                      <?php if ($h['rate_status'] === 'ACTIVE'): ?>
                        <span class="status-pill status-active">Active</span>
                      <?php else: ?>
                        <span class="status-pill status-archived">Inactive</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($h['action_by'] ?? '—') ?></td>
                    <td><?= $h['date_added'] ? date('d M Y, H:i', strtotime($h['date_added'])) : '—' ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </main>

    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Dollar Rate</span>
      <span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>

<?php if (can('rate.set')): ?>
<!-- Set New Rate modal -->
<div class="modal fade" id="rateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="index.php">
        <div class="modal-header">
          <h5 class="modal-title">Set New Dollar Rate</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">New Rate (GH₵ per USD)</label>
          <div class="input-group">
            <span class="input-group-text">GH₵</span>
            <input type="number" step="0.0001" min="0.0001" class="form-control" name="dollar_rate" placeholder="e.g. 12.5000" required autofocus>
          </div>
          <div class="form-text">The current active rate will be marked inactive and this rate will become active.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add" class="btn btn-primary btn-sm">Save Rate</button>
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
</script>
</body>
</html>
