<?php
include "../datacon.php";

$assetClass = $_GET['asset_class'] ?? '';
$year       = isset($_GET['year']) ? (int)$_GET['year'] : '';
$quarter    = $_GET['quarter'] ?? '';

$quarters = [
    'Q1' => [1,2,3],
    'Q2' => [4,5,6],
    'Q3' => [7,8,9],
    'Q4' => [10,11,12],
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quarterly Assets by Class (USD)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container my-4">

<h3>Quarterly Assets Report (USD)</h3>

<!-- FILTER -->
<form method="get" class="card p-3 mb-4 shadow-sm">
    <div class="row g-3">
        <div class="col-md-4">
            <label>Asset Class</label>
            <select name="asset_class" class="form-select" required>
                <option value="">-- Select --</option>
                <?php
                $cls = mysqli_query($conn, "SELECT asset_class FROM asset_classes");
                while ($c = mysqli_fetch_assoc($cls)) {
                    $sel = ($c['asset_class'] === $assetClass) ? 'selected' : '';
                    echo "<option $sel>{$c['asset_class']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label>Year</label>
            <select name="year" class="form-select" required>
                <option value="">-- Select --</option>
                <?php
                for ($y = 2016; $y <= date('Y'); $y++) {
                    $sel = ($y == $year) ? 'selected' : '';
                    echo "<option value='$y' $sel>$y</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label>Quarter</label>
            <select name="quarter" class="form-select" required>
                <option value="">-- Select --</option>
                <?php foreach ($quarters as $q => $_): ?>
                    <option <?= ($q === $quarter) ? 'selected' : '' ?>><?= $q ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Generate</button>
        </div>
    </div>
</form>

<?php
if ($assetClass && $year && $quarter):

$qMonths = $quarters[$quarter];

$sql = "
    SELECT a.*, c.dep_rate
    FROM assets a
    JOIN asset_classes c ON c.asset_class = a.asset_class
    WHERE a.asset_class = ?
      AND YEAR(a.acquisition_date) <= ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $assetClass, $year);
$stmt->execute();
$res = $stmt->get_result();

$modals = [];
$index  = 0;
?>

<div class="card shadow-sm">
<div class="table-responsive">
<table class="table table-bordered table-striped mb-0">
<thead class="table-primary">
<tr>
    <th>Asset</th>
    <th>Acquired</th>
    <th>Cost (USD)</th>
    <th>Opening NBV</th>
    <th>Quarter Depreciation</th>
    <th>Closing NBV</th>
    <th></th>
</tr>
</thead>
<tbody>

<?php
while ($a = $res->fetch_assoc()) {

    if ($a['dollar_rate_used'] <= 0) continue;

    $costUSD   = $a['additions'] / $a['dollar_rate_used'];
    $monthlyDep = ($costUSD * $a['dep_rate']) / 12;

    $acqDate  = strtotime($a['acquisition_date']);
    $acqYear  = (int)date('Y', $acqDate);
    $acqMonth = (int)date('m', $acqDate);

    $monthsBefore = 0;
    for ($y = $acqYear; $y <= $year; $y++) {
        for ($m = 1; $m <= 12; $m++) {
            if ($y == $acqYear && $m < $acqMonth) continue;
            if ($y == $year && $m >= min($qMonths)) break;
            $monthsBefore++;
        }
    }

    $accumBefore = min($monthsBefore * $monthlyDep, $costUSD);
    $openingNBV  = $costUSD - $accumBefore;

    $qDep = 0;
    $nbv  = $openingNBV;
    $acc  = $accumBefore;
    $rows = [];

    foreach ($qMonths as $m) {
        if ($year == $acqYear && $m < $acqMonth) continue;
        if ($nbv <= 0) break;

        $nbv -= $monthlyDep;
        $acc += $monthlyDep;
        $qDep += $monthlyDep;

        $rows[] = [
            'month' => date('F', mktime(0,0,0,$m,1)),
            'dep'   => $monthlyDep,
            'acc'   => $acc,
            'nbv'   => max($nbv,0)
        ];
    }

    $modalId = "assetModal{$index}";
    $modals[] = [
        'id'    => $modalId,
        'name'  => $a['asset_name'],
        'rows'  => $rows,
        'year'  => $year,
        'q'     => $quarter
    ];
    $index++;
?>

<tr>
    <td><?= htmlspecialchars($a['asset_name']) ?></td>
    <td><?= date('d M Y', strtotime($a['acquisition_date'])) ?></td>
    <td>$<?= number_format($costUSD,2) ?></td>
    <td>$<?= number_format($openingNBV,2) ?></td>
    <td>$<?= number_format($qDep,2) ?></td>
    <td class="fw-bold">$<?= number_format(max($nbv,0),2) ?></td>
    <td>
        <button class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#<?= $modalId ?>">
            Breakdown
        </button>
    </td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>

<!-- ================= MODALS RENDERED AFTER TABLE ================= -->
<?php foreach ($modals as $m): ?>
<div class="modal fade" id="<?= $m['id'] ?>" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title">
        <?= htmlspecialchars($m['name']) ?> — <?= $m['q'] ?> <?= $m['year'] ?>
    </h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<table class="table table-bordered">
<thead class="table-secondary">
<tr>
    <th>Month</th>
    <th>Depreciation</th>
    <th>Accumulated</th>
    <th>NBV End</th>
</tr>
</thead>
<tbody>
<?php foreach ($m['rows'] as $r): ?>
<tr>
    <td><?= $r['month'] ?></td>
    <td>$<?= number_format($r['dep'],2) ?></td>
    <td>$<?= number_format($r['acc'],2) ?></td>
    <td>$<?= number_format($r['nbv'],2) ?></td>
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
</body>
</html>
