<?php
require_auth();
require_once SRC . '/queries/assets.php';

$per_page = 25;
$page     = max(1, get_int('page', 1));
$offset   = ($page - 1) * $per_page;

// Count + fetch disposals
$count_row = mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM disposals'));
$total     = (int)$count_row[0];
$total_pages = (int)ceil($total / $per_page);

$stmt = mysqli_prepare($conn,
    'SELECT * FROM disposals ORDER BY date_of_disposal DESC LIMIT ? OFFSET ?');
mysqli_stmt_bind_param($stmt, 'ii', $per_page, $offset);
mysqli_stmt_execute($stmt);
$result   = mysqli_stmt_get_result($stmt);
$disposals = [];
while ($row = mysqli_fetch_assoc($result)) $disposals[] = $row;
mysqli_stmt_close($stmt);

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Disposals';
$content = function() use ($disposals, $total, $total_pages, $page, $base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Disposed Assets</h4>
        <small class="text-muted"><?= number_format($total) ?> records</small>
    </div>
    <div class="export-bar d-flex gap-2">
        <a href="<?= $base_url ?>/exports/disposals?format=csv" class="btn btn-outline-success btn-sm">
            <i class="bi bi-filetype-csv me-1"></i>CSV
        </a>
    </div>
</div>

<div class="table-responsive">
<table class="table table-hover table-sm align-middle">
    <thead>
        <tr>
            <th>Asset Name</th>
            <th>Class</th>
            <th>Location</th>
            <th class="text-end">Additions (GHS)</th>
            <th class="text-end">Disposal Value</th>
            <th>Date Disposed</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($disposals): ?>
        <?php foreach ($disposals as $d): ?>
        <tr>
            <td><?= esc($d['asset_name']) ?></td>
            <td><span class="badge bg-secondary"><?= esc($d['asset_class']) ?></span></td>
            <td><?= esc($d['location']) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$d['additions'], 2) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$d['disposal_value'], 2) ?></td>
            <td><?= esc($d['date_of_disposal']) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No disposals recorded.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<?php if ($total_pages > 1): ?>
<nav>
<ul class="pagination pagination-sm flex-wrap">
    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a>
    </li>
    <?php for ($p = max(1, $page - 3); $p <= min($total_pages, $page + 3); $p++): ?>
    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
    </li>
    <?php endfor; ?>
    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a>
    </li>
</ul>
</nav>
<?php endif; ?>
<?php };

require SRC . '/templates/layouts/base.php';
