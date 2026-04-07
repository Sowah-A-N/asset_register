<?php
require_auth();
require_role('hod_ict', 'schedule_officer');
require_once SRC . '/queries/asset_classes.php';

$types    = get_all_asset_types($conn);
$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Asset Types';
$content = function() use ($types, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Asset Types</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#typeModal">
        <i class="bi bi-plus-lg me-1"></i>Add Type
    </button>
</div>
<div class="table-responsive">
<table class="table table-sm table-hover">
    <thead><tr><th>#</th><th>Type Name</th></tr></thead>
    <tbody>
    <?php foreach ($types as $i => $t): ?>
    <tr><td><?= $i+1 ?></td><td><?= esc($t['asset_type']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<div class="modal fade" id="typeModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Asset Type</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="asset_type_save">
        <div class="modal-body">
            <label class="form-label">Type Name <span class="text-danger">*</span></label>
            <input type="text" name="asset_type" class="form-control" required placeholder="e.g. Leased">
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
        </form>
    </div></div>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
