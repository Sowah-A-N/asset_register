<?php
require_auth();
require_role('hod_ict', 'schedule_officer', 'accountant', 'dsu');
require_once SRC . '/queries/asset_classes.php';

$locations = get_all_locations($conn);
$base_url  = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Locations';
$content = function() use ($locations, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Locations</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#locModal">
        <i class="bi bi-plus-lg me-1"></i>Add Location
    </button>
</div>
<div class="table-responsive">
<table class="table table-hover table-sm">
    <thead><tr><th>#</th><th>Location</th><th>Code</th></tr></thead>
    <tbody>
    <?php foreach ($locations as $i => $l): ?>
    <tr>
        <td class="text-muted small"><?= $i + 1 ?></td>
        <td><?= esc($l['location']) ?></td>
        <td><code class="small"><?= esc($l['loc_code']) ?: '—' ?></code></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<div class="modal fade" id="locModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Location</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="location_save">
        <div class="modal-body row g-3">
            <div class="col-12">
                <label class="form-label">Location Name <span class="text-danger">*</span></label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Code</label>
                <input type="text" name="loc_code" class="form-control">
            </div>
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
