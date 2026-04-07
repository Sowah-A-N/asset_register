<?php
require_auth();
require_once SRC . '/queries/assets.php';
require_once SRC . '/queries/asset_classes.php';

$per_page = 25;
$page     = max(1, get_int('page', 1));
$offset   = ($page - 1) * $per_page;

$filters = [
    'class'    => get('class'),
    'location' => get('location'),
    'search'   => get('search'),
];

$data        = get_assets_paginated($conn, $per_page, $offset, $filters);
$total       = $data['total'];
$assets      = $data['rows'];
$total_pages = (int)ceil($total / $per_page);

$classes   = get_all_asset_classes($conn);
$locations = get_all_locations($conn);
$base_url  = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

$page_title = 'All Assets';
$content = function() use ($assets, $total, $total_pages, $page, $filters, $classes, $locations, $base_url, $per_page) {
    $role = current_role();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Assets</h4>
        <small class="text-muted"><?= number_format($total) ?> records</small>
    </div>
    <?php if (in_array($role, ['hod_ict','schedule_officer'], true)): ?>
    <a href="<?= $base_url ?>/assets/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Asset
    </a>
    <?php endif; ?>
</div>

<!-- ── Filters ─────────────────────────────────────────────────────────────── -->
<form method="get" action="" class="row g-2 mb-3">
    <div class="col-sm-4">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search name / ID / serial…"
               value="<?= esc($filters['search']) ?>">
    </div>
    <div class="col-sm-3">
        <select name="class" class="form-select form-select-sm">
            <option value="">All Classes</option>
            <?php foreach ($classes as $c): ?>
            <option value="<?= esc($c['asset_class']) ?>"
                <?= $filters['class'] === $c['asset_class'] ? 'selected' : '' ?>>
                <?= esc($c['asset_class']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-3">
        <select name="location" class="form-select form-select-sm">
            <option value="">All Locations</option>
            <?php foreach ($locations as $l): ?>
            <option value="<?= esc($l['location']) ?>"
                <?= $filters['location'] === $l['location'] ? 'selected' : '' ?>>
                <?= esc($l['location']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-2 d-flex gap-1">
        <button type="submit" class="btn btn-secondary btn-sm flex-grow-1">Filter</button>
        <a href="<?= $base_url ?>/assets" class="btn btn-outline-secondary btn-sm">Clear</a>
    </div>
</form>

<!-- ── Export bar ──────────────────────────────────────────────────────────── -->
<div class="export-bar d-flex gap-2 flex-wrap mb-3">
    <span class="text-muted small align-self-center">Export:</span>
    <?php
    $qs = http_build_query(array_filter($filters) + ['format' => 'csv']);
    ?>
    <a href="<?= $base_url ?>/exports/assets?<?= $qs ?>" class="btn btn-outline-success btn-sm">
        <i class="bi bi-filetype-csv me-1"></i>CSV
    </a>
    <a href="<?= $base_url ?>/exports/assets-pdf?<?= $qs ?>" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-filetype-pdf me-1"></i>PDF
    </a>
</div>

<!-- ── Table ───────────────────────────────────────────────────────────────── -->
<div class="table-responsive">
<table class="table table-hover table-sm align-middle mb-3">
    <thead>
        <tr>
            <th>#</th>
            <th>ID No.</th>
            <th>Asset Name</th>
            <th>Class</th>
            <th>Location</th>
            <th>Acquisition Date</th>
            <th class="text-end">Additions (GHS)</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($assets): ?>
        <?php foreach ($assets as $i => $a): ?>
        <tr>
            <td class="text-muted small"><?= $offset + $i + 1 ?></td>
            <td><code class="small"><?= esc($a['id_number']) ?></code></td>
            <td><?= esc($a['asset_name']) ?></td>
            <td><span class="badge bg-secondary text-wrap text-start" style="max-width:140px;"><?= esc($a['asset_class']) ?></span></td>
            <td><?= esc($a['location']) ?></td>
            <td class="text-nowrap"><?= esc($a['acquisition_date']) ?></td>
            <td class="text-end fmt-number"><?= number_format((float)$a['additions'], 2) ?></td>
            <td class="text-center text-nowrap">
                <a href="<?= $base_url ?>/assets/edit?id=<?= (int)$a['asset_id'] ?>"
                   class="btn btn-xs btn-outline-primary" title="Edit">
                    <i class="bi bi-pencil"></i>
                </a>
                <?php if (in_array(current_role(), ['hod_ict','schedule_officer'], true)): ?>
                <a href="<?= $base_url ?>/assets/dispose?id=<?= (int)$a['asset_id'] ?>"
                   class="btn btn-xs btn-outline-danger ms-1" title="Dispose">
                    <i class="bi bi-trash"></i>
                </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No assets found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- ── Pagination ──────────────────────────────────────────────────────────── -->
<?php if ($total_pages > 1): ?>
<?php
$qs_base = http_build_query(array_filter($filters));
if ($qs_base) $qs_base .= '&';
?>
<nav aria-label="Assets pagination">
<ul class="pagination pagination-sm flex-wrap">
    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?<?= $qs_base ?>page=<?= $page - 1 ?>">&laquo;</a>
    </li>
    <?php for ($p = max(1, $page - 3); $p <= min($total_pages, $page + 3); $p++): ?>
    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
        <a class="page-link" href="?<?= $qs_base ?>page=<?= $p ?>"><?= $p ?></a>
    </li>
    <?php endfor; ?>
    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
        <a class="page-link" href="?<?= $qs_base ?>page=<?= $page + 1 ?>">&raquo;</a>
    </li>
</ul>
</nav>
<?php endif; ?>

<style>
.btn-xs { padding: 0.15rem 0.4rem; font-size: 0.75rem; }
</style>
<?php };

require SRC . '/templates/layouts/base.php';
