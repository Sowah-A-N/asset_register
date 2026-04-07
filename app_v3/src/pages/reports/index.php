<?php
require_auth();
$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Reports';
$content = function() use ($base_url) {
    $reports = [
        ['url' => 'reports/summary',           'icon' => 'table',              'title' => 'Asset Summary (GHS)',        'desc' => 'Opening balance, additions, depreciation, NBV by class'],
        ['url' => 'reports/summary-usd',        'icon' => 'currency-dollar',    'title' => 'Asset Summary (USD)',        'desc' => 'Same as above in US dollars'],
        ['url' => 'reports/class',              'icon' => 'diagram-3',          'title' => 'Class Report',               'desc' => 'Detailed breakdown per asset class'],
        ['url' => 'reports/additions',          'icon' => 'calendar-plus',      'title' => 'Additions Report',           'desc' => 'Assets acquired in a selected year'],
        ['url' => 'reports/all-assets',         'icon' => 'boxes',              'title' => 'All Active Assets',          'desc' => 'Full register of all non-disposed assets'],
        ['url' => 'reports/depreciation',       'icon' => 'graph-down',         'title' => 'Depreciation Charge',        'desc' => 'Annual depreciation per asset'],
        ['url' => 'reports/accum-depreciation', 'icon' => 'bar-chart-steps',    'title' => 'Accumulated Depreciation',   'desc' => 'Cumulative depreciation by class'],
        ['url' => 'reports/fully-depreciated',  'icon' => 'check2-all',         'title' => 'Fully Depreciated',          'desc' => 'Classes where NBV has reached zero'],
        ['url' => 'reports/disposals',          'icon' => 'trash',              'title' => 'Disposals Report',           'desc' => 'All disposed assets with disposal values'],
        ['url' => 'reports/by-location',        'icon' => 'geo-alt',            'title' => 'Assets by Location',         'desc' => 'Count and value grouped by location'],
        ['url' => 'reports/quarterly-class',    'icon' => 'calendar3-range',    'title' => 'Quarterly by Class',         'desc' => 'Quarterly additions breakdown per class'],
        ['url' => 'reports/historical-cost',    'icon' => 'clock-history',      'title' => 'Historical Cost',            'desc' => 'Original cost register'],
    ];
?>
<h4 class="fw-bold mb-4">Reports</h4>
<div class="row g-3">
<?php foreach ($reports as $r): ?>
<div class="col-md-4 col-sm-6">
    <a href="<?= $base_url ?>/<?= $r['url'] ?>"
       class="card text-decoration-none text-dark h-100 stat-card">
        <div class="card-body d-flex align-items-start gap-3">
            <i class="bi bi-<?= $r['icon'] ?> fs-3 text-primary mt-1"></i>
            <div>
                <div class="fw-semibold"><?= $r['title'] ?></div>
                <small class="text-muted"><?= $r['desc'] ?></small>
            </div>
        </div>
    </a>
</div>
<?php endforeach; ?>
</div>
<?php };
require SRC . '/templates/layouts/base.php';
