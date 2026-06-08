<?php
/**
 * FILE: schedule_officer/view_asset_class/index.php — Asset Classes (list + add), v2.
 *
 * MODEL (approved): asset_classes holds the class DEFINITION + depreciation
 * PARAMETERS (name, derived dep_rate = 1/life, estimated_life, depreciated).
 * asset_class_opbal_year holds the OPENING BALANCE for the base year.
 *
 * FIXES vs old version:
 *  - dep_rate is DERIVED (1/life) — keeps it consistent with the life-based engine.
 *  - opbal_year.rate now stores the DOLLAR rate (was wrongly fed the dep-rate,
 *    e.g. 0.20 — the source of the bad data in that column).
 *  - prepared statements + transaction; balance columns in asset_classes set
 *    to 0 (vestigial; the engine reads balances only from opbal_year).
 */
session_start();
require_once '../../auth.php';
requirePermission('catalog.view', '../login/');
$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
include "../datacon.php";

$BASE_YEAR  = 2024;
$activeRow  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
$activeRate = $activeRow ? (float)$activeRow['dollar_rate'] : 0;

if (isset($_POST['add'])) {
    if (!can('catalog.manage')) { echo "<script>alert('You do not have permission to modify the catalog.');window.location='index.php';</script>"; exit(); }

    $name        = trim($_POST['name'] ?? '');
    $life        = (int)($_POST['life'] ?? 0);
    $depreciates = (int)($_POST['depreciates'] ?? 1) === 1 ? 1 : 0;
    $opening     = (float)($_POST['opening_balance'] ?? 0);
    $accumStart  = (float)($_POST['accum_start'] ?? 0);
    $year        = (int)($_POST['base_year'] ?? $BASE_YEAR);

    if ($name === '' || $life <= 0) {
        echo "<script>alert('Please enter a class name and a useful life of at least 1 year.');window.location='index.php';</script>"; exit();
    }

    $depRate    = round(1 / $life, 6);   // derived: 1/life
    $lifeMonths = $life * 12;
    $rate       = $activeRate > 0 ? $activeRate : 1;   // dollar rate for the opening balance
    $openUsd    = $rate > 0 ? $opening / $rate : 0;
    $accumUsd   = $rate > 0 ? $accumStart / $rate : 0;

    // Duplicate?
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM asset_classes WHERE TRIM(asset_class) = ?");
    mysqli_stmt_bind_param($stmt, "s", $name); mysqli_stmt_execute($stmt);
    $dup = (int)(mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] ?? 0); mysqli_stmt_close($stmt);
    if ($dup > 0) { echo "<script>alert('Asset class already exists.');window.location='index.php';</script>"; exit(); }

    mysqli_begin_transaction($conn);
    $ok = true;

    // 1) Class definition + parameters (balance cols set to 0 — vestigial)
    $s1 = mysqli_prepare($conn,
        "INSERT INTO asset_classes (asset_class, account_depr_open_bal, opening_bal, opbal_plus_additions, dep_rate, estimated_life, depreciated)
         VALUES (?, 0, 0, 0, ?, ?, ?)");
    mysqli_stmt_bind_param($s1, "sdii", $name, $depRate, $life, $depreciates);
    $ok = $ok && mysqli_stmt_execute($s1); mysqli_stmt_close($s1);

    // 2) Opening balance for the base year (the pool)
    if ($ok) {
        $s2 = mysqli_prepare($conn,
            "INSERT INTO asset_class_opbal_year
                (asset_class, opening_balance, open_bal_usd, total_accum_depr_start, total_accum_start_usd,
                 total_depr_year_charge_usd, disposals_depr_usd, year, expected_life_months, rate, depreciated)
             VALUES (?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($s2, "sddddiidi",
            $name, $opening, $openUsd, $accumStart, $accumUsd, $year, $lifeMonths, $rate, $depreciates);
        $ok = $ok && mysqli_stmt_execute($s2); mysqli_stmt_close($s2);
    }

    if ($ok) { mysqli_commit($conn); echo "<script>alert('Asset class added successfully.');window.location='index.php';</script>"; }
    else { mysqli_rollback($conn); error_log('[RMU] add asset class failed: '.mysqli_error($conn)); echo "<script>alert('Could not add asset class. Please try again.');window.location='index.php';</script>"; }
    mysqli_autocommit($conn, true);
    exit();
}

/* ── Class list: one row per class (master) + base-year opening balance ── */
$rows = [];
$res = mysqli_query($conn,
    "SELECT c.asset_class, c.estimated_life, c.dep_rate, c.depreciated,
            o.opening_balance AS base_opening
     FROM asset_classes c
     LEFT JOIN asset_class_opbal_year o
       ON TRIM(o.asset_class) = TRIM(c.asset_class) AND o.year = $BASE_YEAR
     ORDER BY c.asset_class ASC");
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
$canManage = can('catalog.manage');
function money($n){ return number_format((float)$n, 2, '.', ','); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asset Classes — RMU Asset Register</title>
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
      <div class="topnav-breadcrumb"><h1>Asset Classes</h1><small><?= count($rows) ?> class<?= count($rows)!==1?'es':'' ?> defined</small></div>
      <div class="topnav-actions">
        <?php if ($canManage): ?><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#cModal"><i class="bi bi-plus-lg me-1"></i>Add Class</button><?php endif; ?>
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
      <div class="rmu-card">
        <div class="rmu-card-header"><div class="rmu-card-title">Asset Classes</div>
          <small class="text-muted">Per-year opening balances are in the Reports</small></div>
        <div class="rmu-card-body" style="padding:0">
          <?php if (empty($rows)): ?>
            <div class="rmu-empty"><i class="bi bi-tags rmu-empty-icon"></i><h6>No asset classes</h6></div>
          <?php else: ?>
            <table class="rmu-table" style="box-shadow:none;border:none">
              <thead><tr><th>S/N</th><th>Asset Class</th><th class="num" style="text-align:right">Useful Life</th><th class="num" style="text-align:right">Dep. Rate</th><th>Depreciates</th><th class="num" style="text-align:right">Opening Balance <?= $BASE_YEAR ?> (GHS)</th></tr></thead>
              <tbody>
                <?php $sn=1; foreach ($rows as $r): ?>
                  <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars(trim($r['asset_class'])) ?></td>
                    <td style="text-align:right"><?= (int)$r['estimated_life'] ?> yr<?= (int)$r['estimated_life']!==1?'s':'' ?></td>
                    <td style="text-align:right"><?= number_format((float)$r['dep_rate']*100, 2) ?>%</td>
                    <td><?= (int)$r['depreciated'] === 1 ? '<span class="status-pill status-active">Yes</span>' : '<span class="status-pill status-warning">No</span>' ?></td>
                    <td style="text-align:right;font-variant-numeric:tabular-nums"><?= $r['base_opening'] !== null ? money($r['base_opening']) : '—' ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </main>
    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Asset Classes</span><span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>
  </div>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="cModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="index.php">
      <div class="modal-header"><h5 class="modal-title">Add Asset Class</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label">Class Name</label>
            <input type="text" class="form-control" name="name" required></div>
          <div class="col-6"><label class="form-label">Useful Life (years)</label>
            <input type="number" class="form-control" name="life" id="lifeIn" min="1" step="1" required></div>
          <div class="col-6"><label class="form-label">Depreciation Rate</label>
            <input type="text" class="form-control" id="rateOut" value="—" readonly>
            <div class="form-text">Auto-derived (1 ÷ life).</div></div>
          <div class="col-12"><label class="form-label">Depreciates?</label>
            <select class="form-select" name="depreciates">
              <option value="1" selected>Yes — depreciate over its life</option>
              <option value="0">No — e.g. Land / Work-in-Progress</option>
            </select></div>
          <div class="col-6"><label class="form-label">Opening Balance (GHS)</label>
            <input type="number" class="form-control" name="opening_balance" step="0.01" value="0">
            <div class="form-text">Cost b/f. Leave 0 for a brand-new class.</div></div>
          <div class="col-6"><label class="form-label">Accum. Depreciation b/f (GHS)</label>
            <input type="number" class="form-control" name="accum_start" step="0.01" value="0"></div>
          <div class="col-6"><label class="form-label">Base Year</label>
            <input type="number" class="form-control" name="base_year" value="<?= $BASE_YEAR ?>" min="2000" max="2100"></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary btn-sm">Add Class</button></div>
    </form>
  </div></div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', ()=>document.body.classList.toggle('sidebar-open'));
// live derived rate
const lifeIn = document.getElementById('lifeIn'), rateOut = document.getElementById('rateOut');
lifeIn?.addEventListener('input', () => {
  const l = parseInt(lifeIn.value, 10);
  rateOut.value = (l > 0) ? (100 / l).toFixed(2) + '%  (' + l + '-yr straight-line)' : '—';
});
</script>
</body>
</html>
