<?php
require_auth();

// Load query helpers (not in bootstrap — loaded on demand)
require_once SRC . '/queries/assets.php';

// ── Stat data ─────────────────────────────────────────────────────────────
$active_count    = count_active_assets($conn);
$disposed_count  = count_disposed_assets($conn);
$archived_count  = count_archived_assets($conn);
$class_count     = count_asset_classes($conn);
$location_count  = count_locations($conn);
$supplier_count  = count_suppliers($conn);
$dollar_rate     = get_active_dollar_rate($conn);

// ── Chart: total additions GHS by asset class ────────────────────────────
$chart_data   = get_assets_by_class_totals($conn);
$chart_labels = json_encode(array_column($chart_data, 'asset_class'));
$chart_values = json_encode(array_map('floatval', array_column($chart_data, 'total')));

$page_title   = 'Dashboard';
$load_chartjs = true;
$base_url     = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

$content = function() use (
    $active_count, $disposed_count, $archived_count,
    $class_count, $location_count, $supplier_count,
    $dollar_rate, $chart_labels, $chart_values, $base_url
) { ?>

<!-- ── Page header ──────────────────────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Dashboard</h4>
        <small class="text-muted">Overview &mdash; <?= date('d M Y') ?></small>
    </div>
    <?php if (in_array(current_role(), ['hod_ict','schedule_officer'], true)): ?>
    <a href="<?= $base_url ?>/assets/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Asset
    </a>
    <?php endif; ?>
</div>

<!-- ── Stat cards ────────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card border-start border-4 border-primary">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Active Assets</div>
                    <h3 class="fw-bold mb-0"><?= number_format($active_count) ?></h3>
                </div>
                <i class="bi bi-boxes stat-icon text-primary"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card border-start border-4 border-danger">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Disposed</div>
                    <h3 class="fw-bold mb-0"><?= number_format($disposed_count) ?></h3>
                </div>
                <i class="bi bi-trash stat-icon text-danger"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card border-start border-4 border-secondary">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Archived</div>
                    <h3 class="fw-bold mb-0"><?= number_format($archived_count) ?></h3>
                </div>
                <i class="bi bi-archive stat-icon text-secondary"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card border-start border-4 border-info">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Asset Classes</div>
                    <h3 class="fw-bold mb-0"><?= number_format($class_count) ?></h3>
                </div>
                <i class="bi bi-tags stat-icon text-info"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card border-start border-4 border-warning">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Locations</div>
                    <h3 class="fw-bold mb-0"><?= number_format($location_count) ?></h3>
                </div>
                <i class="bi bi-geo-alt stat-icon text-warning"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card stat-card border-start border-4 border-success">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase">Dollar Rate (Active)</div>
                    <h3 class="fw-bold mb-0">
                        <?= $dollar_rate !== null
                            ? 'GH₵&nbsp;' . number_format($dollar_rate, 2)
                            : '<span class="text-muted fs-6">Not set</span>' ?>
                    </h3>
                </div>
                <i class="bi bi-currency-dollar stat-icon text-success"></i>
            </div>
        </div>
    </div>

</div>

<!-- ── Chart: total value by asset class ─────────────────────────────────── -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            <i class="bi bi-bar-chart-fill me-2 text-primary"></i>Total Additions by Asset Class (GHS)
        </span>
        <small class="text-muted">Active assets only</small>
    </div>
    <div class="card-body">
        <canvas id="assetClassChart" style="max-height:360px;"></canvas>
    </div>
</div>

<!-- ── Quick links ─────────────────────────────────────────────────────────── -->
<div class="row g-3">
    <div class="col-md-4">
        <a href="<?= $base_url ?>/assets" class="card text-decoration-none text-dark h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-list-ul fs-2 text-primary mb-2 d-block"></i>
                <strong>View All Assets</strong>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= $base_url ?>/reports" class="card text-decoration-none text-dark h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-file-earmark-bar-graph fs-2 text-success mb-2 d-block"></i>
                <strong>Reports</strong>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= $base_url ?>/assets/disposals" class="card text-decoration-none text-dark h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-trash fs-2 text-danger mb-2 d-block"></i>
                <strong>Disposals</strong>
            </div>
        </a>
    </div>
</div>

<script>
(function () {
    const labels = <?= $chart_labels ?>;
    const values = <?= $chart_values ?>;

    const colours = labels.map((_, i) => `hsl(${(i * 37) % 360},65%,55%)`);

    new Chart(document.getElementById('assetClassChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Additions (GHS)',
                data: values,
                backgroundColor: colours,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' GH₵ ' + Number(ctx.parsed.y).toLocaleString('en-GH', {
                            minimumFractionDigits: 2
                        })
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: val => 'GH₵' + Number(val).toLocaleString('en-GH', {
                            notation: 'compact', compactDisplay: 'short'
                        })
                    }
                },
                x: {
                    ticks: { maxRotation: 35, minRotation: 15 }
                }
            }
        }
    });
}());
</script>

<?php };

require SRC . '/templates/layouts/base.php';
