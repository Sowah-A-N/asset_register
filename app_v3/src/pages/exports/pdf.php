<?php
// PDF export — generates a simple print-formatted HTML page that browsers
// can print/save as PDF via Ctrl+P (no server-side library needed for basic use).
// For full server-side PDF, FPDF can be added here in Phase 11.
require_auth();
require_once SRC . '/queries/reports.php';
require_once SRC . '/queries/assets.php';

$report   = get('report', 'summary');
$year     = get_int('year', (int)date('Y'));
$class    = get('class', '');
$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

switch ($report) {
    case 'additions':
        $data  = get_additions_by_year($conn, $year);
        $title = "Additions Report — {$year}";
        break;
    case 'all':
        $data  = get_all_active_assets($conn, $class);
        $title = 'All Active Assets' . ($class ? " — {$class}" : '');
        break;
    default:
        $data  = get_asset_class_summary($conn, $year);
        $title = "Asset Summary (GHS) — {$year}";
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
        }
        body { font-size: 11px; }
        .table th, .table td { padding: 3px 6px; }
    </style>
</head>
<body class="p-3">

<div class="no-print mb-3 d-flex gap-2">
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        <i class="bi bi-printer me-1"></i>Print / Save as PDF
    </button>
    <a href="<?= $base_url ?>/reports" class="btn btn-outline-secondary btn-sm">&larr; Reports</a>
</div>

<div class="text-center mb-3">
    <h5 class="fw-bold mb-0"><?= esc($title) ?></h5>
    <small class="text-muted">Generated: <?= date('d M Y H:i') ?></small>
</div>

<?php if ($report === 'summary' || $report === ''): ?>
<table class="table table-bordered table-sm small">
    <thead class="table-dark">
        <tr>
            <th>Asset Class</th>
            <th class="text-end">Opening Bal</th>
            <th class="text-end">Additions</th>
            <th class="text-end">Depr Charge</th>
            <th class="text-end">Accum Depr</th>
            <th class="text-end">NBV</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $r): ?>
    <tr>
        <td><?= esc($r['asset_class']) ?></td>
        <td class="text-end"><?= number_format((float)$r['opening_balance'],2) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_additions_cedi'],2) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_depr_year_charge'],2) ?></td>
        <td class="text-end"><?= number_format((float)$r['total_accum_depr_end'],2) ?></td>
        <td class="text-end fw-bold"><?= number_format((float)$r['net_book_value'],2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php elseif ($report === 'additions'): ?>
<table class="table table-bordered table-sm small">
    <thead class="table-dark">
        <tr>
            <th>#</th><th>Asset Name</th><th>Class</th>
            <th>Acq Date</th><th class="text-end">Additions (GHS)</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $i => $r): ?>
    <tr>
        <td><?= $i+1 ?></td>
        <td><?= esc($r['asset_name']) ?></td>
        <td><?= esc($r['asset_class']) ?></td>
        <td><?= esc($r['acquisition_date']) ?></td>
        <td class="text-end"><?= number_format((float)$r['additions'],2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
<table class="table table-bordered table-sm small">
    <thead class="table-dark">
        <tr>
            <th>#</th><th>Asset Name</th><th>ID</th><th>Class</th>
            <th>Location</th><th class="text-end">Additions (GHS)</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $i => $r): ?>
    <tr>
        <td><?= $i+1 ?></td>
        <td><?= esc($r['asset_name']) ?></td>
        <td><?= esc($r['id_number']) ?></td>
        <td><?= esc($r['asset_class']) ?></td>
        <td><?= esc($r['location']) ?></td>
        <td class="text-end"><?= number_format((float)$r['additions'],2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></script>
</body>
</html>
<?php exit;
