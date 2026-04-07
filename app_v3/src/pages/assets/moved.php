<?php
require_auth();

$per_page = 25;
$page     = max(1, get_int('page', 1));
$offset   = ($page - 1) * $per_page;

$count_row   = mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM moved_assets'));
$total       = (int)$count_row[0];
$total_pages = (int)ceil($total / $per_page);

$stmt = mysqli_prepare($conn,
    'SELECT * FROM moved_assets ORDER BY date_of_action DESC LIMIT ? OFFSET ?');
mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rows   = [];
while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
mysqli_stmt_close($stmt);

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Moved Assets';
$content = function() use ($rows, $total, $total_pages, $page, $base_url) {
    $role = current_role();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Moved Assets</h4>
        <small class="text-muted"><?= number_format($total) ?> records</small>
    </div>
    <?php if (in_array($role, ['hod_ict','schedule_officer'], true)): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#moveModal">
        <i class="bi bi-arrow-repeat me-1"></i>Move Asset
    </button>
    <?php endif; ?>
</div>

<div class="table-responsive">
<table class="table table-hover table-sm align-middle">
    <thead>
        <tr>
            <th>Serial No.</th>
            <th>From Location</th>
            <th>From User</th>
            <th>To Location</th>
            <th>To User</th>
            <th>Notes</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($rows): ?>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><code><?= esc($r['serial_number']) ?></code></td>
            <td><?= esc($r['old_location']) ?></td>
            <td><?= esc($r['old_user']) ?></td>
            <td><?= esc($r['new_location']) ?></td>
            <td><?= esc($r['new_user']) ?></td>
            <td class="text-muted small"><?= esc($r['notes']) ?></td>
            <td class="text-nowrap"><?= esc($r['date_of_action']) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" class="text-center text-muted py-4">No moves recorded.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<?php if ($total_pages > 1): ?>
<nav><ul class="pagination pagination-sm flex-wrap">
    <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="?page=<?=$page-1?>">&laquo;</a></li>
    <?php for($p=max(1,$page-3);$p<=min($total_pages,$page+3);$p++): ?>
    <li class="page-item <?= $p===$page?'active':'' ?>"><a class="page-link" href="?page=<?=$p?>"><?=$p?></a></li>
    <?php endfor; ?>
    <li class="page-item <?= $page>=$total_pages?'disabled':'' ?>"><a class="page-link" href="?page=<?=$page+1?>">&raquo;</a></li>
</ul></nav>
<?php endif; ?>

<?php if (in_array($role, ['hod_ict','schedule_officer'], true)): ?>
<!-- Move Modal -->
<div class="modal fade" id="moveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i>Move Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= $base_url ?>/">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="asset_move">
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                    <input type="text" name="serial_number" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">From Location <span class="text-danger">*</span></label>
                    <input type="text" name="old_location" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">From User <span class="text-danger">*</span></label>
                    <input type="text" name="old_user" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">To Location <span class="text-danger">*</span></label>
                    <input type="text" name="new_location" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">To User <span class="text-danger">*</span></label>
                    <input type="text" name="new_user" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Record Move</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php };
require SRC . '/templates/layouts/base.php';
