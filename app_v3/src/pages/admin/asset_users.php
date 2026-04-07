<?php
require_auth();
require_role('hod_ict', 'schedule_officer');

$res   = mysqli_query($conn,
    'SELECT * FROM asset_users ORDER BY staff_last_name, staff_first_name ASC');
$users = [];
while ($r = mysqli_fetch_assoc($res)) $users[] = $r;

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Asset Users';
$content = function() use ($users, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Asset Users (Staff)</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
        <i class="bi bi-person-plus me-1"></i>Add Staff
    </button>
</div>
<div class="table-responsive">
<table class="table table-hover table-sm">
    <thead><tr><th>Staff ID</th><th>Name</th><th>Department</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
    <tr>
        <td><code><?= esc($u['staff_id']) ?></code></td>
        <td><?= esc($u['staff_first_name'] . ' ' . $u['staff_last_name']) ?></td>
        <td><?= esc($u['department']) ?: '—' ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Staff Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= $base_url ?>/">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="asset_user_save">
        <div class="modal-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Staff ID <span class="text-danger">*</span></label>
                <input type="text" name="staff_id" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input type="text" name="staff_first_name" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" name="staff_last_name" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control">
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
