<?php
require_once '../../init.php';

$assetClass = $_GET['asset_class'] ?? '';
$year       = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$quarter    = $_GET['quarter'] ?? '';

$quarters = [
    'Q1' => [1, 2, 3],
    'Q2' => [4, 5, 6],
    'Q3' => [7, 8, 9],
    'Q4' => [10, 11, 12],
];

// Dynamic year range
$minYearRow  = $conn->query("SELECT MIN(year) AS min_y FROM asset_class_opbal_year")->fetch_assoc();
$minYear     = ($minYearRow && $minYearRow['min_y']) ? (int)$minYearRow['min_y'] : 2016;
$currentYear = (int)date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quarterly Assets by Class (USD)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>@media print { .no-print { display:none !important; } }</style>
</head>
<body class="container-fluid my-4">

<div class="no-print d-flex align-items-center gap-2 mb-3">
    <a href="../" class="btn btn-outline-secondary btn-sm">&larr; Back To Reports</a>
    <h5 class="mb-0 ms-2">Quarterly Assets Report (USD)</h5>
</div>

<!-- Filter form (GET so URL is shareable/printable) -->
<form method="GET" class="no-print card p-3 mb-4 shadow-sm needs-validation" novalidate>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Asset Class</label>
            <select name="asset_class" class="form-select form-select-sm" required>
                <option value="">-- Select --</option>
                <?php
                $cls = mysqli_query($conn, "SELECT asset_class FROM asset_classes ORDER BY asset_class");
                while ($c = mysqli_fetch_assoc($cls)) {
                    $sel = ($c['asset_class'] === $assetClass) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($c['asset_class']) . "' $sel>"
                       . htmlspecialchars($c['asset_class']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Year</label>
            <select name="year" class="form-select form-select-sm" required>
                <option value="">-- Select --</option>
                <?php for ($y = $currentYear; $y >= $minYear; $y--): ?>
                    <option value="<?= $y ?>" <?= ($y === $year) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Quarter</label>
            <select name="quarter" class="form-select form-select-sm" required>
                <option value="">-- Select --</option>
                <?php foreach (array_keys($quarters) as $q): ?>
                    <option <?= ($q === $quarter) ? 'selected' : '' ?>><?= $q ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end gap-2">
            <button class="btn btn-primary btn-sm w-100">Generate</button>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">Print</button>
        </div>
    </div>
</form>

<?php if ($assetClass && $year && $quarter && isset($quarters[$quarter])): ?>

<?php
    $qMonths = $quarters[$quarter];

    // Fetch active assets only (disposals = 0) for the class acquired on or before year-end
    $stmt = $conn->prepare(
        "SELECT a.asset_id, a.asset_name, a.acquisition_date,
                a.additions, a.dollar_rate_used,
                c.dep_rate
         FROM assets a
         JOIN asset_classes c ON c.asset_class = a.asset_class
         WHERE a.asset_class = ?
           AND YEAR(a.acquisition_date) <= ?
           AND a.disposals = 0
         ORDER BY a.acquisition_date ASC"
    );
    $stmt->bind_param('si', $assetClass, $year);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    $quarterLabels = [
        'Q1' => 'Jan – Mar', 'Q2' => 'Apr – Jun',
        'Q3' => 'Jul – Sep', 'Q4' => 'Oct – Dec',
    ];
?>

<!-- Report header (visible on print) -->
<div class="text-center mb-3">
    <h4 class="fw-bold text-uppercase mb-0">REGIONAL MARITIME UNIVERSITY</h4>
    <h5 class="fw-bold">
        QUARTERLY ASSET DETAIL — <?= htmlspecialchars($quarter) ?> <?= $year ?>
        (<?= $quarterLabels[$quarter] ?? '' ?>)
    </h5>
    <p class="text-muted small mb-0">
        Class: <strong><?= htmlspecialchars($assetClass) ?></strong>
        &nbsp;&bull;&nbsp; All values in USD
    </p>
    <hr>
</div>

<div class="card shadow-sm mb-4">
<div class="table-responsive">
<table class="table table-bordered table-striped table-sm mb-0" style="font-size:0.85rem">
<thead class="table-primary">
<tr>
    <th>#</th>
    <th>Asset Name</th>
    <th>Acquired</th>
    <th class="text-end">Cost (USD)</th>
    <th class="text-end">Opening NBV</th>
    <th class="text-end">Quarter Depr.</th>
    <th class="text-end fw-bold">Closing NBV</th>
    <th class="no-print"></th>
</tr>
</thead>
<tbody>

<?php
$modals  = [];
$counter = 1;
$totalCost = $totalQDep = $totalClosingNBV = 0;

while ($a = $res->fetch_assoc()):
    $rate = (float)$a['dollar_rate_used'];
    if ($rate <= 0) continue;

    $costUSD    = (float)$a['additions'] / $rate;
    $depRate    = (float)$a['dep_rate'];        // decimal e.g. 0.25
    $monthlyDep = ($costUSD * $depRate) / 12;

    $acqYear  = (int)date('Y', strtotime($a['acquisition_date']));
    $acqMonth = (int)date('m', strtotime($a['acquisition_date']));

    // Count full months depreciated before this quarter starts
    $monthsBefore = 0;
    $qStart = min($qMonths);
    for ($y = $acqYear; $y <= $year; $y++) {
        for ($m = 1; $m <= 12; $m++) {
            if ($y === $acqYear && $m < $acqMonth) continue;
            if ($y === $year   && $m >= $qStart)   break 2;
            $monthsBefore++;
        }
    }

    $accumBefore = min($monthsBefore * $monthlyDep, $costUSD);
    $openingNBV  = max($costUSD - $accumBefore, 0);

    $qDep = 0;
    $nbv  = $openingNBV;
    $acc  = $accumBefore;
    $rows = [];

    foreach ($qMonths as $m) {
        // Skip months before acquisition in the acquisition year
        if ($year === $acqYear && $m < $acqMonth) continue;
        if ($nbv <= 0) break;

        $dep  = min($monthlyDep, $nbv);
        $nbv  = max($nbv - $dep, 0);
        $acc += $dep;
        $qDep += $dep;

        $rows[] = [
            'month' => date('F', mktime(0, 0, 0, $m, 1)),
            'dep'   => round($dep, 2),
            'acc'   => round($acc, 2),
            'nbv'   => round($nbv, 2),
        ];
    }

    $modalId = 'assetModal' . $counter;
    $modals[] = ['id' => $modalId, 'name' => $a['asset_name'], 'rows' => $rows,
                 'year' => $year, 'q' => $quarter];

    $totalCost       += $costUSD;
    $totalQDep       += $qDep;
    $totalClosingNBV += $nbv;
?>
<tr>
    <td><?= $counter ?></td>
    <td><?= htmlspecialchars($a['asset_name']) ?></td>
    <td><?= date('d M Y', strtotime($a['acquisition_date'])) ?></td>
    <td class="text-end">$ <?= number_format($costUSD, 2) ?></td>
    <td class="text-end">$ <?= number_format($openingNBV, 2) ?></td>
    <td class="text-end">$ <?= number_format($qDep, 2) ?></td>
    <td class="text-end fw-bold">$ <?= number_format($nbv, 2) ?></td>
    <td class="no-print">
        <button class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
            Breakdown
        </button>
    </td>
</tr>
<?php $counter++; endwhile; ?>

</tbody>
<tfoot class="table-secondary fw-bold">
<tr>
    <td colspan="3" class="text-end">TOTAL</td>
    <td class="text-end">$ <?= number_format($totalCost, 2) ?></td>
    <td></td>
    <td class="text-end">$ <?= number_format($totalQDep, 2) ?></td>
    <td class="text-end">$ <?= number_format($totalClosingNBV, 2) ?></td>
    <td class="no-print"></td>
</tr>
</tfoot>
</table>
</div>
</div>

<p class="text-muted small">
    Generated: <?= date('d M Y, H:i') ?>
    &bull; <?= $counter - 1 ?> asset(s) &bull; Active assets only
</p>

<!-- Per-asset breakdown modals -->
<?php foreach ($modals as $modal): ?>
<div class="modal fade" id="<?= $modal['id'] ?>" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title">
        <?= htmlspecialchars($modal['name']) ?>
        — <?= $modal['q'] ?> <?= $modal['year'] ?>
    </h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<table class="table table-bordered table-sm">
<thead class="table-secondary">
<tr>
    <th>Month</th>
    <th class="text-end">Depreciation</th>
    <th class="text-end">Accumulated Depr.</th>
    <th class="text-end">NBV End</th>
</tr>
</thead>
<tbody>
<?php foreach ($modal['rows'] as $r): ?>
<tr>
    <td><?= $r['month'] ?></td>
    <td class="text-end">$ <?= number_format($r['dep'], 2) ?></td>
    <td class="text-end">$ <?= number_format($r['acc'], 2) ?></td>
    <td class="text-end">$ <?= number_format($r['nbv'], 2) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    'use strict';
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', e => {
            if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
            form.classList.add('was-validated');
        });
    });
})();
</script>
</body>
</html>
