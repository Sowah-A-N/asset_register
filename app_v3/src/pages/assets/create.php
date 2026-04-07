<?php
require_auth();
require_role('hod_ict', 'schedule_officer');
require_once SRC . '/queries/asset_classes.php';

$classes   = get_all_asset_classes($conn);
$locations = get_all_locations($conn);
$suppliers = get_all_suppliers($conn);
$types     = get_all_asset_types($conn);
$users     = get_all_asset_users($conn);
$base_url  = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

// Get active dollar rate
$dr_row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
$dollar_rate = $dr_row ? (float)$dr_row['dollar_rate'] : 1.0;

// Step from session (1 or 2)
$step = isset($_SESSION['_asset_create_step']) ? (int)$_SESSION['_asset_create_step'] : 1;

$page_title = 'Add Asset';
$content = function() use ($classes, $locations, $suppliers, $types, $users, $base_url, $dollar_rate, $step) {
?>
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= $base_url ?>/assets" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-bold">Add New Asset</h4>
</div>

<!-- ── Step indicator ─────────────────────────────────────────────────────── -->
<div class="d-flex gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill <?= $step === 1 ? 'bg-primary' : 'bg-success' ?> fs-6">1</span>
        <span class="<?= $step === 1 ? 'fw-semibold' : 'text-muted' ?>">Asset Details</span>
    </div>
    <div class="text-muted">→</div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill <?= $step === 2 ? 'bg-primary' : 'bg-secondary' ?> fs-6">2</span>
        <span class="<?= $step === 2 ? 'fw-semibold' : 'text-muted' ?>">Assign ID Numbers</span>
    </div>
</div>

<?php if ($step === 1): ?>

<!-- ════════════════════════════════════ STEP 1 ══════════════════════════ -->
<div class="card">
    <div class="card-header fw-semibold">
        <i class="bi bi-clipboard-data me-2 text-primary"></i>Step 1 — Asset Details
    </div>
    <div class="card-body">
    <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="asset_create_step1">

        <div class="row g-3">
            <!-- Asset Name -->
            <div class="col-12">
                <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                <input type="text" name="asset_name" class="form-control"
                       value="<?= esc($_SESSION['_asset_draft']['asset_name'] ?? '') ?>"
                       required placeholder="Full description of the asset">
            </div>

            <!-- Class + Sub-class -->
            <div class="col-md-6">
                <label class="form-label">Asset Class <span class="text-danger">*</span></label>
                <select name="asset_class" id="asset_class" class="form-select" required>
                    <option value="">Select class…</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= esc($c['asset_class']) ?>"
                        <?= (($_SESSION['_asset_draft']['asset_class'] ?? '') === $c['asset_class']) ? 'selected' : '' ?>>
                        <?= esc($c['asset_class']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sub-Class <span class="text-danger">*</span></label>
                <input type="text" name="sub_class" class="form-control"
                       value="<?= esc($_SESSION['_asset_draft']['sub_class'] ?? '') ?>"
                       required placeholder="Sub-classification">
            </div>

            <!-- Supplier -->
            <div class="col-md-6">
                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_name" class="form-select" required>
                    <option value="">Select supplier…</option>
                    <?php foreach ($suppliers as $s): ?>
                    <option value="<?= esc($s['name']) ?>"
                        <?= (($_SESSION['_asset_draft']['supplier_name'] ?? '') === $s['name']) ? 'selected' : '' ?>>
                        <?= esc($s['name']) ?>
                    </option>
                    <?php endforeach; ?>
                    <option value="__other__">Other (type below)</option>
                </select>
                <input type="text" name="supplier_name_other" class="form-control mt-1"
                       placeholder="Enter supplier name if not listed"
                       id="supplierOther" style="display:none;">
            </div>

            <!-- Asset Type -->
            <div class="col-md-6">
                <label class="form-label">Asset Type <span class="text-danger">*</span></label>
                <select name="asset_type" class="form-select" required>
                    <option value="">Select type…</option>
                    <?php foreach ($types as $t): ?>
                    <option value="<?= esc($t['asset_type']) ?>"><?= esc($t['asset_type']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Location -->
            <div class="col-md-6">
                <label class="form-label">Location <span class="text-danger">*</span></label>
                <select name="location" class="form-select" required>
                    <option value="">Select location…</option>
                    <?php foreach ($locations as $l): ?>
                    <option value="<?= esc($l['location']) ?>"><?= esc($l['location']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Asset User -->
            <div class="col-md-6">
                <label class="form-label">Asset User / Holder</label>
                <select name="asset_user" class="form-select">
                    <option value="">None / Unassigned</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= esc(trim($u['staff_first_name'] . ' ' . $u['staff_last_name'])) ?>">
                        <?= esc($u['staff_first_name'] . ' ' . $u['staff_last_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Acquisition Date + Year -->
            <div class="col-md-4">
                <label class="form-label">Acquisition Date <span class="text-danger">*</span></label>
                <input type="date" name="acquisition_date" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Current Year <span class="text-danger">*</span></label>
                <input type="number" name="current_year" class="form-control"
                       value="<?= date('Y') ?>" min="2000" max="2099" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Dollar Rate Used</label>
                <input type="number" name="dollar_rate_used" class="form-control"
                       value="<?= $dollar_rate ?>" step="0.01" min="0.01" required>
                <div class="form-text">Active rate: <?= $dollar_rate ?></div>
            </div>

            <!-- Financials -->
            <div class="col-md-4">
                <label class="form-label">Additions (GHS) <span class="text-danger">*</span></label>
                <input type="number" name="additions" class="form-control"
                       step="0.01" min="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Historical Cost (GHS)</label>
                <input type="number" name="historical_cost" class="form-control"
                       step="0.01" min="0" value="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Residual Value (GHS)</label>
                <input type="number" name="active_res_value" class="form-control"
                       step="0.01" min="0" value="0">
            </div>

            <!-- Reference numbers -->
            <div class="col-12">
                <hr class="my-1"><small class="text-muted text-uppercase">Reference Numbers (fill in Step 2 if unknown)</small>
            </div>
            <div class="col-md-3">
                <label class="form-label">GRV Number</label>
                <input type="text" name="grv_number" class="form-control" value="N/A">
            </div>
            <div class="col-md-3">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serial_number" class="form-control" value="N/A">
            </div>
            <div class="col-md-3">
                <label class="form-label">PV Number</label>
                <input type="text" name="pv_number" class="form-control" value="N/A">
            </div>
            <div class="col-md-3">
                <label class="form-label">ID Number</label>
                <input type="text" name="id_number" class="form-control" value="N/A">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                Next: Assign IDs <i class="bi bi-arrow-right ms-1"></i>
            </button>
            <a href="<?= $base_url ?>/assets" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
    </div>
</div>

<?php else: ?>

<!-- ════════════════════════════════════ STEP 2 ══════════════════════════ -->
<?php $draft = $_SESSION['_asset_draft'] ?? []; ?>
<div class="card mb-3">
    <div class="card-header fw-semibold bg-success text-white">
        <i class="bi bi-check-circle me-2"></i>Step 1 complete — reviewing draft
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Asset Name</dt>
            <dd class="col-sm-9"><?= esc($draft['asset_name'] ?? '') ?></dd>
            <dt class="col-sm-3">Class</dt>
            <dd class="col-sm-9"><?= esc($draft['asset_class'] ?? '') ?></dd>
            <dt class="col-sm-3">Additions (GHS)</dt>
            <dd class="col-sm-9"><?= number_format((float)($draft['additions'] ?? 0), 2) ?></dd>
            <dt class="col-sm-3">Acquisition Date</dt>
            <dd class="col-sm-9"><?= esc($draft['acquisition_date'] ?? '') ?></dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">
        <i class="bi bi-tag me-2 text-primary"></i>Step 2 — Assign ID Numbers
    </div>
    <div class="card-body">
    <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="asset_create_step2">

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">GRV Number</label>
                <input type="text" name="grv_number" class="form-control"
                       value="<?= esc($draft['grv_number'] ?? 'N/A') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serial_number" class="form-control"
                       value="<?= esc($draft['serial_number'] ?? 'N/A') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">PV Number</label>
                <input type="text" name="pv_number" class="form-control"
                       value="<?= esc($draft['pv_number'] ?? 'N/A') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Asset ID Number <span class="text-danger">*</span></label>
                <input type="text" name="id_number" class="form-control"
                       value="<?= esc($draft['id_number'] ?? '') ?>" required
                       placeholder="e.g. RMU///0/25">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-lg me-1"></i>Save Asset
            </button>
            <button type="submit" name="_action" value="asset_create_back"
                    class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Step 1
            </button>
            <a href="<?= $base_url ?>/assets" class="btn btn-outline-danger ms-auto">Cancel</a>
        </div>
    </form>
    </div>
</div>

<?php endif; ?>

<script>
// Show "Other supplier" text box
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.querySelector('select[name="supplier_name"]');
    const box = document.getElementById('supplierOther');
    if (sel && box) {
        sel.addEventListener('change', function() {
            box.style.display = this.value === '__other__' ? '' : 'none';
        });
    }
});
</script>

<?php };

require SRC . '/templates/layouts/base.php';
