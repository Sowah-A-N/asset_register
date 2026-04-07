<?php
require_auth();
require_role('hod_ict', 'schedule_officer', 'accountant');
require_once SRC . '/queries/assets.php';
require_once SRC . '/queries/asset_classes.php';

$id    = get_int('id');
$asset = $id ? get_asset_by_id($conn, $id) : null;
if (!$asset) {
    set_flash('error', 'Asset not found.');
    $base = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
    header('Location: ' . $base . '/assets');
    exit;
}

$classes   = get_all_asset_classes($conn);
$locations = get_all_locations($conn);
$suppliers = get_all_suppliers($conn);
$types     = get_all_asset_types($conn);
$base_url  = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

$page_title = 'Edit Asset';
$content = function() use ($asset, $classes, $locations, $suppliers, $types, $base_url) {
?>
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= $base_url ?>/assets" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-bold">Edit Asset</h4>
    <small class="text-muted ms-2"><?= esc($asset['id_number']) ?></small>
</div>

<div class="card">
    <div class="card-body">
    <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="asset_edit">
        <input type="hidden" name="asset_id" value="<?= (int)$asset['asset_id'] ?>">

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                <input type="text" name="asset_name" class="form-control"
                       value="<?= esc($asset['asset_name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Asset Class <span class="text-danger">*</span></label>
                <select name="asset_class" class="form-select" required>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= esc($c['asset_class']) ?>"
                        <?= $asset['asset_class'] === $c['asset_class'] ? 'selected' : '' ?>>
                        <?= esc($c['asset_class']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sub-Class</label>
                <input type="text" name="sub_class" class="form-control"
                       value="<?= esc($asset['sub_class']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Supplier</label>
                <select name="supplier_name" class="form-select">
                    <?php foreach ($suppliers as $s): ?>
                    <option value="<?= esc($s['name']) ?>"
                        <?= $asset['supplier_name'] === $s['name'] ? 'selected' : '' ?>>
                        <?= esc($s['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Asset Type</label>
                <select name="asset_type" class="form-select">
                    <?php foreach ($types as $t): ?>
                    <option value="<?= esc($t['asset_type']) ?>"
                        <?= $asset['asset_type'] === $t['asset_type'] ? 'selected' : '' ?>>
                        <?= esc($t['asset_type']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Location</label>
                <select name="location" class="form-select">
                    <?php foreach ($locations as $l): ?>
                    <option value="<?= esc($l['location']) ?>"
                        <?= $asset['location'] === $l['location'] ? 'selected' : '' ?>>
                        <?= esc($l['location']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Asset User</label>
                <input type="text" name="asset_user" class="form-control"
                       value="<?= esc($asset['user'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Acquisition Date</label>
                <input type="date" name="acquisition_date" class="form-control"
                       value="<?= esc($asset['acquisition_date']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Additions (GHS)</label>
                <input type="number" name="additions" class="form-control"
                       value="<?= (float)$asset['additions'] ?>" step="0.01" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Dollar Rate Used</label>
                <input type="number" name="dollar_rate_used" class="form-control"
                       value="<?= (float)$asset['dollar_rate_used'] ?>" step="0.01" min="0.01">
            </div>
            <div class="col-md-3">
                <label class="form-label">GRV Number</label>
                <input type="text" name="grv_number" class="form-control"
                       value="<?= esc($asset['grv_number']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serial_number" class="form-control"
                       value="<?= esc($asset['serial_number']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">PV Number</label>
                <input type="text" name="pv_number" class="form-control"
                       value="<?= esc($asset['pv_number']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">ID Number</label>
                <input type="text" name="id_number" class="form-control"
                       value="<?= esc($asset['id_number']) ?>">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy me-1"></i>Save Changes
            </button>
            <a href="<?= $base_url ?>/assets" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
    </div>
</div>
<?php };

require SRC . '/templates/layouts/base.php';
