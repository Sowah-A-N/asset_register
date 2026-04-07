<?php
require_auth();
require_role('hod_ict', 'schedule_officer');
require_once SRC . '/queries/assets.php';

// "Untracked" = assets with id_number still set to 'N/A' or empty
$per_page = 25;
$page     = max(1, get_int('page', 1));
$offset   = ($page - 1) * $per_page;

$count_row = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COUNT(*) FROM assets
     WHERE (id_number = 'N/A' OR id_number = '' OR id_number IS NULL)
       AND disposed = 0 AND archived = 0"));
$total       = (int)$count_row[0];
$total_pages = (int)ceil($total / $per_page);

$stmt = mysqli_prepare($conn,
    "SELECT * FROM assets
     WHERE (id_number = 'N/A' OR id_number = '' OR id_number IS NULL)
       AND disposed = 0 AND archived = 0
     ORDER BY date_added DESC LIMIT ? OFFSET ?");
mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$assets = [];
while ($row = mysqli_fetch_assoc($result)) $assets[] = $row;
mysqli_stmt_close($stmt);

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Untracked Assets';
$content = function() use ($assets, $total, $total_pages, $page, $base_url) {
?>
<div class="mb-3">
    <h4 class="fw-bold mb-0">Untracked Assets</h4>
    <small class="text-muted"><?= number_format($total) ?> assets missing an ID number</small>
</div>

<div class="table-responsive">
<table class="table table-hover table-sm align-middle">
    <thead>
        <tr>
            <th>Asset Name</th>
            <th>Class</th>
            <th>Serial No.</th>
            <th>GRV No.</th>
            <th class="text-end">Additions (GHS)</th>
            <th class="text-center">Assign ID</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($assets): ?>
        <?php foreach ($assets as $a): ?>
        <tr>
            <td><?= esc($a['asset_name']) ?></td>
            <td><span class="badge bg-secondary"><?= esc($a['asset_class']) ?></span></td>
            <td><code class="small"><?= esc($a['serial_number']) ?></code></td>
            <td><?= esc($a['grv_number']) ?></td>
            <td class="text-end"><?= number_format((float)$a['additions'], 2) ?></td>
            <td class="text-center">
                <a href="<?= $base_url ?>/assets/add-id?id=<?= (int)$a['asset_id'] ?>"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-tag me-1"></i>Assign
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted py-4">
            <i class="bi bi-check-circle text-success me-1"></i>All assets have ID numbers.
        </td></tr>
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
<?php };
require SRC . '/templates/layouts/base.php';
