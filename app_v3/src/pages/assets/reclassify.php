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

$classes  = get_all_asset_classes($conn);
$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

$page_title = 'Reclassify Asset';
$content = function() use ($asset, $classes, $base_url) {
?>
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= $base_url ?>/assets" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-bold">Reclassify Asset</h4>
</div>

<div class="card mb-3">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Asset</dt>
            <dd class="col-sm-9"><?= esc($asset['asset_name']) ?></dd>
            <dt class="col-sm-3">Current Class</dt>
            <dd class="col-sm-9">
                <span class="badge bg-secondary fs-6"><?= esc($asset['asset_class']) ?></span>
            </dd>
            <dt class="col-sm-3">ID Number</dt>
            <dd class="col-sm-9"><code><?= esc($asset['id_number']) ?></code></dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">
        <i class="bi bi-arrow-left-right me-2 text-primary"></i>Change Asset Class
    </div>
    <div class="card-body">
    <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="asset_reclassify">
        <input type="hidden" name="asset_id" value="<?= (int)$asset['asset_id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">New Asset Class <span class="text-danger">*</span></label>
                <select name="new_asset_class" class="form-select" required>
                    <option value="">Select new class…</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= esc($c['asset_class']) ?>"
                        <?= $asset['asset_class'] === $c['asset_class'] ? 'disabled' : '' ?>>
                        <?= esc($c['asset_class']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">New Sub-Class</label>
                <input type="text" name="new_sub_class" class="form-control"
                       value="<?= esc($asset['sub_class']) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" rows="3" required
                          placeholder="Explain why this asset is being reclassified…"></textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"
                    data-confirm="Reclassify this asset? The change will be permanently logged.">
                <i class="bi bi-arrow-left-right me-1"></i>Reclassify
            </button>
            <a href="<?= $base_url ?>/assets" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
    </div>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
