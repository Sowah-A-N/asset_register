<?php
require_auth();
require_once SRC . '/queries/assets.php';

$per_page = 25;
$page     = max(1, get_int('page', 1));
$offset   = ($page - 1) * $per_page;

$count_row   = mysqli_fetch_row(mysqli_query($conn,
    'SELECT COUNT(*) FROM assets WHERE archived = 1'));
$total       = (int)$count_row[0];
$total_pages = (int)ceil($total / $per_page);

$stmt = mysqli_prepare($conn,
    'SELECT * FROM assets WHERE archived = 1 ORDER BY archived_at DESC LIMIT ? OFFSET ?');
mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$assets = [];
while ($row = mysqli_fetch_assoc($result)) $assets[] = $row;
mysqli_stmt_close($stmt);

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Archived Assets';
$content = function() use ($assets, $total, $total_pages, $page, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Archived Assets</h4>
        <small class="text-muted"><?= number_format($total) ?> records</small>
    </div>
</div>

<div class="table-responsive">
<table class="table table-hover table-sm align-middle">
    <thead>
        <tr>
            <th>Asset Name</th>
            <th>ID No.</th>
            <th>Class</th>
            <th>Location</th>
            <th class="text-end">Additions (GHS)</th>
            <th>Archived At</th>
            <?php if (in_array(current_role(), ['hod_ict','schedule_officer'], true)): ?>
            <th class="text-center">Restore</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php if ($assets): ?>
        <?php foreach ($assets as $a): ?>
        <tr>
            <td><?= esc($a['asset_name']) ?></td>
            <td><code class="small"><?= esc($a['id_number']) ?></code></td>
            <td><span class="badge bg-secondary"><?= esc($a['asset_class']) ?></span></td>
            <td><?= esc($a['location']) ?></td>
            <td class="text-end"><?= number_format((float)$a['additions'], 2) ?></td>
            <td class="text-nowrap text-muted small"><?= esc($a['archived_at']) ?></td>
            <?php if (in_array(current_role(), ['hod_ict','schedule_officer'], true)): ?>
            <td class="text-center">
                <form method="post" action="<?= $base_url ?>/" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_action" value="asset_restore">
                    <input type="hidden" name="asset_id" value="<?= (int)$a['asset_id'] ?>">
                    <button type="submit" class="btn btn-xs btn-outline-success"
                            data-confirm="Restore this asset to active?">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </form>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" class="text-center text-muted py-4">No archived assets.</td></tr>
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

<style>.btn-xs{padding:.15rem .4rem;font-size:.75rem;}</style>
<?php };
require SRC . '/templates/layouts/base.php';
