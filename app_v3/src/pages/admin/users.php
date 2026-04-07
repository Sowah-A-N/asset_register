<?php
require_auth();
require_role('hod_ict');

$result = mysqli_query($conn,
    'SELECT id, username, display_name, role, is_active, created_at FROM users ORDER BY display_name ASC');
$users    = [];
while ($row = mysqli_fetch_assoc($result)) $users[] = $row;

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Manage Users';
$content = function() use ($users, $base_url) {
    $roles_list = ['hod_ict','schedule_officer','budget_officer','accountant',
                   'director_finance','sia','dsu'];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Users</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
        <i class="bi bi-person-plus me-1"></i>Add User
    </button>
</div>

<div class="table-responsive">
<table class="table table-hover table-sm align-middle">
    <thead>
        <tr>
            <th>Display Name</th>
            <th>Username</th>
            <th>Role</th>
            <th class="text-center">Active</th>
            <th>Created</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= esc($u['display_name']) ?></td>
        <td><code><?= esc($u['username']) ?></code></td>
        <td><span class="badge bg-primary"><?= esc($u['role']) ?></span></td>
        <td class="text-center">
            <?php if ($u['is_active']): ?>
                <i class="bi bi-check-circle-fill text-success"></i>
            <?php else: ?>
                <i class="bi bi-x-circle-fill text-danger"></i>
            <?php endif; ?>
        </td>
        <td class="text-muted small text-nowrap"><?= esc($u['created_at']) ?></td>
        <td class="text-center text-nowrap">
            <button class="btn btn-xs btn-outline-primary"
                    data-bs-toggle="modal" data-bs-target="#editUserModal"
                    data-id="<?= (int)$u['id'] ?>"
                    data-username="<?= esc($u['username']) ?>"
                    data-display="<?= esc($u['display_name']) ?>"
                    data-role="<?= esc($u['role']) ?>"
                    data-active="<?= (int)$u['is_active'] ?>">
                <i class="bi bi-pencil"></i>
            </button>
            <?php if ((int)$u['id'] !== current_user_id()): ?>
            <form method="post" action="<?= $base_url ?>/" class="d-inline ms-1">
                <?= csrf_field() ?>
                <input type="hidden" name="_action" value="user_delete">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <button type="submit" class="btn btn-xs btn-outline-danger"
                        data-confirm="Delete user '<?= esc($u['username']) ?>'? This cannot be undone.">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= $base_url ?>/">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="user_create">
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required
                           autocomplete="off">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Display Name <span class="text-danger">*</span></label>
                    <input type="text" name="display_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">Select…</option>
                        <?php foreach ($roles_list as $r): ?>
                        <option value="<?= $r ?>"><?= $r ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required
                           autocomplete="new-password" minlength="8">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Create User</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= $base_url ?>/">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="user_edit">
            <input type="hidden" name="user_id" id="editUserId">
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" id="editUsername" name="username"
                           class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Display Name</label>
                    <input type="text" id="editDisplay" name="display_name"
                           class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select id="editRole" name="role" class="form-select">
                        <?php foreach ($roles_list as $r): ?>
                        <option value="<?= $r ?>"><?= $r ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Active</label>
                    <select id="editActive" name="is_active" class="form-select">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">New Password <span class="text-muted small">(leave blank to keep current)</span></label>
                    <input type="password" name="password" class="form-control"
                           autocomplete="new-password" minlength="8">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs{padding:.15rem .4rem;font-size:.75rem;}</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-bs-target="#editUserModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('editUserId').value   = this.dataset.id;
            document.getElementById('editUsername').value = this.dataset.username;
            document.getElementById('editDisplay').value  = this.dataset.display;
            document.getElementById('editRole').value     = this.dataset.role;
            document.getElementById('editActive').value   = this.dataset.active;
        });
    });
});
</script>
<?php };
require SRC . '/templates/layouts/base.php';
