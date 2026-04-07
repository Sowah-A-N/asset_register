<?php
require_auth();
require_role('hod_ict', 'schedule_officer');
require_once SRC . '/queries/asset_classes.php';

$suppliers = get_all_suppliers($conn);
$base_url  = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Suppliers';
$content = function() use ($suppliers, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Suppliers</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supModal">
        <i class="bi bi-plus-lg me-1"></i>Add Supplier
    </button>
</div>
<div class="table-responsive">
<table class="table table-hover table-sm">
    <thead><tr><th>Name</th><th>Location</th><th>Phone</th></tr></thead>
    <tbody>
    <?php
    // Fetch full supplier rows
    $res = mysqli_query($GLOBALS['conn'],
        'SELECT name, location, number FROM suppliers ORDER BY name ASC');
    while ($s = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td><?= esc($s['name']) ?></td>
        <td><?= esc($s['location']) ?: '—' ?></td>
        <td><?= esc($s['number']) ?: '—' ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
<div class="modal fade" id="supModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="supplier_save">
        <div class="modal-body row g-3">
            <div class="col-12">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="number" class="form-control">
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
