<?php
/**
 * FILE:    schedule_officer/new_asset/index.php
 * PURPOSE: Register a new asset — rebuilt as a 4-step wizard on the v2 design system.
 *
 * Preserves the exact field contract expected by index.inc.php and the
 * asset-ID generation (update_array.php) + opening-balance fetch mechanisms.
 *
 * FIXES APPLIED:
 *  - RBAC guard (asset.create) — was previously unguarded
 *  - Removed the redundant duplicate sub-class dropdown (M2)
 *  - Replaced do-while dropdown loops that crash on empty tables (M7) → while
 *  - Removed the extra </select> tag (H9) and the dead empty-URL XHR (H9)
 *  - session_start handled via requirePermission (L2)
 */

require_once '../../auth.php';
requirePermission('asset.create', '../login/');
include "../datacon.php";

$username    = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$userInitial = mb_strtoupper(mb_substr($username, 0, 1));
$year        = date('Y');

// Active dollar rate (for display on the financial step)
$rateRow    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
$activeRate = $rateRow ? (float)$rateRow['dollar_rate'] : 0;

/* Small helper to render <option> rows safely (replaces fragile do-while). */
function options(mysqli $conn, string $sql, string $valueCol, callable $label, array $dataAttr = []): string {
    $out = '';
    $res = mysqli_query($conn, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $val   = htmlspecialchars((string)$row[$valueCol], ENT_QUOTES);
            $text  = htmlspecialchars($label($row), ENT_QUOTES);
            $attrs = '';
            foreach ($dataAttr as $attr => $col) {
                $attrs .= ' ' . $attr . '="' . htmlspecialchars((string)($row[$col] ?? ''), ENT_QUOTES) . '"';
            }
            $out .= "<option value=\"$val\"$attrs>$text</option>";
        }
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Asset — RMU Asset Register</title>

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="../../assets/css/v2.css">
  <link rel="shortcut icon" href="../../assets/img/rmulog.png">

  <style>
    /* ── Wizard stepper ── */
    .wizard-steps {
      display: flex; gap: 0; margin-bottom: 2rem;
      counter-reset: step;
    }
    .wizard-step {
      flex: 1; text-align: center; position: relative;
      padding-top: 2.6rem;
      font-size: .78rem; font-weight: 600; color: var(--text-muted);
    }
    .wizard-step::before {
      counter-increment: step; content: counter(step);
      position: absolute; top: 0; left: 50%; transform: translateX(-50%);
      width: 36px; height: 36px; border-radius: 50%;
      background: #fff; border: 2px solid var(--card-border);
      color: var(--text-muted);
      display: flex; align-items: center; justify-content: center;
      font-size: .9rem; font-weight: 700; z-index: 2; transition: all var(--t-base);
    }
    .wizard-step::after {
      content: ''; position: absolute; top: 17px; left: -50%; width: 100%;
      height: 2px; background: var(--card-border); z-index: 1;
    }
    .wizard-step:first-child::after { display: none; }
    .wizard-step.active   { color: var(--rmu-blue); }
    .wizard-step.active::before {
      border-color: var(--rmu-blue); color: var(--rmu-blue);
      box-shadow: 0 0 0 4px var(--rmu-blue-light);
    }
    .wizard-step.done { color: var(--c-green); }
    .wizard-step.done::before {
      content: '\F26E'; font-family: 'bootstrap-icons';
      background: var(--c-green); border-color: var(--c-green); color: #fff;
    }
    .wizard-step.done::after { background: var(--c-green); }

    .wizard-panel { display: none; animation: fadeIn .25s ease; }
    .wizard-panel.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

    .review-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 1rem 2rem; }
    .review-item { border-bottom: 1px solid var(--card-border); padding-bottom: .6rem; }
    .review-item small { display: block; font-size: .72rem; color: var(--text-muted);
      text-transform: uppercase; letter-spacing: .05em; font-weight: 700; margin-bottom: .15rem; }
    .review-item span { font-size: .9rem; color: var(--text-primary); font-weight: 500; }
    @media (max-width: 575.98px){ .review-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="rmu-layout">

  <?php require '../partials/sidebar.php'; ?>

  <div class="rmu-main">

    <!-- TOP NAV -->
    <header class="rmu-topnav">
      <button class="topnav-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <div class="topnav-breadcrumb">
        <h1>Add New Asset</h1>
        <small>Register a new institutional asset</small>
      </div>
      <div class="topnav-actions">
        <a href="../view_assets/" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-list-ul me-1"></i>View Assets
        </a>
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

    <!-- CONTENT -->
    <main class="rmu-content">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

          <!-- Stepper -->
          <div class="wizard-steps">
            <div class="wizard-step active" data-step="1">Classification</div>
            <div class="wizard-step" data-step="2">Asset Information</div>
            <div class="wizard-step" data-step="3">Financial Details</div>
            <div class="wizard-step" data-step="4">Review &amp; Confirm</div>
          </div>

          <div class="rmu-card">
            <form action="index.inc.php" method="post" id="assetWizardForm" novalidate>

              <!-- ════ STEP 1 — Classification ════ -->
              <div class="wizard-panel active" data-panel="1">
                <div class="rmu-card-body">
                  <h5 class="rmu-form-section-title">Step 1 · Classification</h5>
                  <div class="row g-3">

                    <div class="col-md-6">
                      <label class="form-label">Asset Class <span class="text-danger">*</span></label>
                      <select name="asset_class_select" id="asset_class_select" class="form-select" required>
                        <option value="" hidden>Select asset class…</option>
                        <?= options($conn, "SELECT ast_id, asset_class FROM asset_classes ORDER BY asset_class ASC",
                                    'ast_id', fn($r) => $r['asset_class']) ?>
                      </select>
                      <div class="form-text">Determines depreciation rate and opening balance.</div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Sub-Class <span class="text-danger">*</span></label>
                      <select name="asset_class_sub_classes" id="asset_class_sub_classes"
                              class="form-select" data-array-id="item_specific_code" required>
                        <option value="" hidden>Select sub-class…</option>
                        <?= options($conn,
                              "SELECT T_id, sub_class, sub_class_code FROM asset_class_sub_classes ORDER BY sub_class ASC",
                              'T_id',
                              fn($r) => $r['sub_class'] . ' — ' . $r['sub_class_code'],
                              ['data-value' => 'sub_class_code']) ?>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Asset Type <span class="text-danger">*</span></label>
                      <select name="asset_type" id="asset_type" class="form-select" required>
                        <option value="" hidden>Select asset type…</option>
                        <?= options($conn, "SELECT type_id, asset_type FROM asset_type ORDER BY asset_type ASC",
                                    'type_id', fn($r) => $r['asset_type']) ?>
                      </select>
                    </div>

                  </div>
                </div>
                <div class="rmu-card-header" style="border-top:1px solid var(--card-border);border-bottom:none;justify-content:flex-end">
                  <button type="button" class="btn btn-primary btn-sm" data-next>Continue <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
              </div>

              <!-- ════ STEP 2 — Asset Information ════ -->
              <div class="wizard-panel" data-panel="2">
                <div class="rmu-card-body">
                  <h5 class="rmu-form-section-title">Step 2 · Asset Information</h5>
                  <div class="row g-3">

                    <div class="col-md-12">
                      <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                      <input type="text" name="asset_name" id="asset_name" class="form-control"
                             required oninput="capitalize(this)" placeholder="e.g. Toyota Hilux Pickup">
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">GRV Number <span class="text-danger">*</span></label>
                      <input type="text" name="grvNumber" id="grvNumber" class="form-control" required oninput="capitalize(this)">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Serial / Chassis No. <span class="text-danger">*</span></label>
                      <input type="text" name="identificationNumber" id="identificationNumber" class="form-control" required oninput="capitalize(this)">
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">PV Number <span class="text-danger">*</span></label>
                      <input type="text" name="pvNumber" id="pvNumber" class="form-control" required oninput="capitalize(this)">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Supplier <span class="text-danger">*</span></label>
                      <select name="supplier" id="supplier" class="form-select" required>
                        <option value="" hidden>Select supplier…</option>
                        <?= options($conn, "SELECT sup_id, name FROM suppliers ORDER BY name ASC",
                                    'sup_id', fn($r) => $r['name']) ?>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Location <span class="text-danger">*</span></label>
                      <select name="location" id="location" class="form-select"
                              data-array-id="name_of_department" required>
                        <option value="" hidden>Select location…</option>
                        <?= options($conn, "SELECT loc_id, location, loc_code FROM asset_location ORDER BY location ASC",
                                    'loc_id', fn($r) => $r['location'], ['data-value' => 'loc_code']) ?>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Assigned User <span class="text-danger">*</span></label>
                      <select name="user" id="user" class="form-select" required>
                        <option value="" hidden>Select user…</option>
                        <?= options($conn,
                              "SELECT t_id, staff_first_name, staff_last_name FROM asset_users ORDER BY staff_first_name ASC",
                              't_id', fn($r) => $r['staff_first_name'] . ' ' . $r['staff_last_name']) ?>
                      </select>
                    </div>

                  </div>
                </div>
                <div class="rmu-card-header" style="border-top:1px solid var(--card-border);border-bottom:none;justify-content:space-between">
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-back><i class="bi bi-arrow-left me-1"></i>Back</button>
                  <button type="button" class="btn btn-primary btn-sm" data-next>Continue <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
              </div>

              <!-- ════ STEP 3 — Financial Details ════ -->
              <div class="wizard-panel" data-panel="3">
                <div class="rmu-card-body">
                  <h5 class="rmu-form-section-title">Step 3 · Financial Details</h5>
                  <div class="row g-3">

                    <div class="col-md-6">
                      <label class="form-label">Historical Cost (GHS) <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text">GH₵</span>
                        <input type="number" step="0.01" min="0" name="additions" id="additions" class="form-control" required>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Active Residual Value (GHS) <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text">GH₵</span>
                        <input type="number" step="0.01" min="0" name="active_res_value" id="active_res_value" class="form-control" required>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Acquisition Date <span class="text-danger">*</span></label>
                      <input type="text" name="acquisition_date" id="acquisition_date" class="form-control"
                             placeholder="DD-MM-YYYY" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Opening Balance (GHS)</label>
                      <input type="text" id="opening_bal" name="opening_bal" class="form-control" readonly
                             placeholder="Auto-filled from class">
                      <div class="form-text">Pulled from the selected asset class.</div>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">Current Year</label>
                      <input type="text" id="current_year" name="current_year" class="form-control" value="<?= $year ?>" readonly>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">Active Dollar Rate</label>
                      <input type="text" class="form-control" value="<?= $activeRate > 0 ? 'GH₵ '.number_format($activeRate,4) : 'Not set' ?>" readonly>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">Generated Asset ID</label>
                      <input type="text" id="asset_id" name="asset_id" class="form-control" readonly
                             style="font-family:monospace;background:#f8fafc">
                    </div>

                  </div>
                </div>
                <div class="rmu-card-header" style="border-top:1px solid var(--card-border);border-bottom:none;justify-content:space-between">
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-back><i class="bi bi-arrow-left me-1"></i>Back</button>
                  <button type="button" class="btn btn-primary btn-sm" data-next>Review <i class="bi bi-arrow-right ms-1"></i></button>
                </div>
              </div>

              <!-- ════ STEP 4 — Review & Confirm ════ -->
              <div class="wizard-panel" data-panel="4">
                <div class="rmu-card-body">
                  <h5 class="rmu-form-section-title">Step 4 · Review &amp; Confirm</h5>
                  <p class="text-muted small mb-4">Please confirm the details below before submitting.</p>
                  <div class="review-grid" id="reviewGrid"></div>
                </div>
                <div class="rmu-card-header" style="border-top:1px solid var(--card-border);border-bottom:none;justify-content:space-between">
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-back><i class="bi bi-arrow-left me-1"></i>Back</button>
                  <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Register Asset
                  </button>
                </div>
              </div>

            </form>
          </div>

        </div>
      </div>
    </main>

    <footer style="padding:.85rem 1.75rem;border-top:1px solid var(--card-border);background:white;font-size:.75rem;color:var(--text-muted);display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
      <span>RMU Asset Register &nbsp;·&nbsp; Version 2.0</span>
      <span><?= date('Y') ?> &nbsp;·&nbsp; Regional Maritime University</span>
    </footer>

  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
/* ── Sidebar toggle ── */
document.getElementById('sidebarToggle')?.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
document.getElementById('sidebarOverlay')?.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));

/* ── Capitalise helper (preserves original behaviour) ── */
function capitalize(input) {
  input.value = input.value.replace(/\b\w/g, c => c.toUpperCase());
}

/* ── Wizard navigation ── */
let currentStep = 1;
const totalSteps = 4;
const panels = document.querySelectorAll('.wizard-panel');
const steps  = document.querySelectorAll('.wizard-step');

function showStep(n) {
  panels.forEach(p => p.classList.toggle('active', +p.dataset.panel === n));
  steps.forEach(s => {
    const sn = +s.dataset.step;
    s.classList.toggle('active', sn === n);
    s.classList.toggle('done', sn < n);
  });
  currentStep = n;
  if (n === 4) buildReview();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* Validate only the fields inside the current panel before advancing. */
function validateStep(n) {
  const panel = document.querySelector(`.wizard-panel[data-panel="${n}"]`);
  const fields = panel.querySelectorAll('input[required], select[required]');
  for (const f of fields) {
    if (!f.value || !f.value.trim()) {
      f.classList.add('is-invalid');
      f.focus();
      f.addEventListener('input', () => f.classList.remove('is-invalid'), { once: true });
      return false;
    }
  }
  return true;
}

document.querySelectorAll('[data-next]').forEach(btn =>
  btn.addEventListener('click', () => { if (validateStep(currentStep)) showStep(currentStep + 1); }));
document.querySelectorAll('[data-back]').forEach(btn =>
  btn.addEventListener('click', () => showStep(currentStep - 1)));

/* Allow clicking a completed step to jump back. */
steps.forEach(s => s.addEventListener('click', () => {
  const target = +s.dataset.step;
  if (target < currentStep) showStep(target);
}));

/* ── Review builder ── */
function labelOf(id) {
  const el = document.getElementById(id);
  if (!el) return '';
  if (el.tagName === 'SELECT') return el.options[el.selectedIndex]?.text || '';
  return el.value;
}
function buildReview() {
  const map = [
    ['Asset Name', 'asset_name'], ['Asset Class', 'asset_class_select'],
    ['Sub-Class', 'asset_class_sub_classes'], ['Asset Type', 'asset_type'],
    ['GRV Number', 'grvNumber'], ['Serial / Chassis', 'identificationNumber'],
    ['PV Number', 'pvNumber'], ['Supplier', 'supplier'],
    ['Location', 'location'], ['Assigned User', 'user'],
    ['Historical Cost', 'additions'], ['Residual Value', 'active_res_value'],
    ['Acquisition Date', 'acquisition_date'], ['Asset ID', 'asset_id'],
  ];
  document.getElementById('reviewGrid').innerHTML = map.map(([lbl, id]) =>
    `<div class="review-item"><small>${lbl}</small><span>${labelOf(id) || '—'}</span></div>`).join('');
}

/* ── Opening balance fetch on class change (preserved) ── */
document.querySelector('#asset_class_select').addEventListener('change', function () {
  fetch('index.inc.php?asset_class=' + encodeURIComponent(this.value))
    .then(r => r.ok ? r.text() : null)
    .then(data => { if (data !== null) document.querySelector('#opening_bal').value = data; })
    .catch(err => console.error('Opening balance fetch failed:', err));
});

/* ── Date picker ── */
flatpickr("#acquisition_date", {
  dateFormat: "d-m-Y",
  allowInput: true,
  maxDate: new Date().fp_incr(0)
});

/* ── Asset ID generation (preserved verbatim from original) ── */
document.addEventListener('DOMContentLoaded', function () {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'update_array.php', true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    if (xhr.status == 200) {
      var updatedArray = JSON.parse(xhr.responseText);
      var assetIdInput = document.getElementById('asset_id');
      assetIdInput.value = updatedArray['RMU_constant'] + '/' + updatedArray['name_of_department'] + '/'
                         + updatedArray['item_specific_code'] + '/' + updatedArray['dept_subclass_counter'] + '/' + updatedArray['year'];

      var selectElements = document.querySelectorAll('select[data-array-id]');
      selectElements.forEach(function (select) {
        select.addEventListener('change', function () {
          var selectedValue = select.options[select.selectedIndex].getAttribute('data-value');
          var selectName = select.getAttribute('data-array-id');
          var xhrUpdate = new XMLHttpRequest();
          xhrUpdate.open('POST', 'update_array.php', true);
          xhrUpdate.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
          xhrUpdate.onload = function () {
            if (xhrUpdate.status == 200) {
              updatedArray = JSON.parse(xhrUpdate.responseText);
              assetIdInput.value = updatedArray['RMU_constant'] + '/' + updatedArray['name_of_department'] + '/' + updatedArray['item_specific_code'] + '/'
                                 + updatedArray['dept_subclass_counter'] + '/' + updatedArray['year'];
            }
          };
          xhrUpdate.send('selected_value=' + encodeURIComponent(selectedValue) + '&select_name=' + encodeURIComponent(selectName));
        });
      });
    }
  };
  xhr.send();
});
</script>

</body>
</html>
