<?php
require_auth();
require_role('hod_ict', 'schedule_officer');
require_once SRC . '/queries/assets.php';

$id    = get_int('id');
$asset = $id ? get_asset_by_id($conn, $id) : null;
if (!$asset) {
    set_flash('error', 'Asset not found.');
    $base = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
    header('Location: ' . $base . '/assets/untracked');
    exit;
}

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Assign Asset ID';
$content = function() use ($asset, $base_url) {
?>
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= $base_url ?>/assets/untracked" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-bold">Assign ID Number</h4>
</div>

<div class="card mb-3">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Asset</dt>
            <dd class="col-sm-9"><?= esc($asset['asset_name']) ?></dd>
            <dt class="col-sm-3">Class</dt>
            <dd class="col-sm-9"><?= esc($asset['asset_class']) ?></dd>
            <dt class="col-sm-3">Additions</dt>
            <dd class="col-sm-9">GH₵ <?= number_format((float)$asset['additions'], 2) ?></dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-body">
    <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="asset_add_id">
        <input type="hidden" name="asset_id" value="<?= (int)$asset['asset_id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Asset ID Number <span class="text-danger">*</span></label>
                <input type="text" name="id_number" class="form-control" required
                       placeholder="e.g. RMU///0/25">
            </div>
            <div class="col-md-6">
                <label class="form-label">GRV Number</label>
                <input type="text" name="grv_number" class="form-control"
                       value="<?= esc($asset['grv_number']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serial_number" class="form-control"
                       value="<?= esc($asset['serial_number']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">PV Number</label>
                <input type="text" name="pv_number" class="form-control"
                       value="<?= esc($asset['pv_number']) ?>">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-tag me-1"></i>Save ID
            </button>
            <a href="<?= $base_url ?>/assets/untracked" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
    </div>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
