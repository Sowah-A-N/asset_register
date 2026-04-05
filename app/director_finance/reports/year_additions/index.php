<?php
require_once '../../init.php';

$currentYear  = (int)date('Y');
$selectedYear = isset($_POST['asset_year_select']) && $_POST['asset_year_select'] !== ''
    ? (int)$_POST['asset_year_select']
    : $currentYear;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Additions Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <style>
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="p-3">

    <div class="no-print d-flex align-items-center gap-2 mb-3">
        <a href="../" class="btn btn-outline-secondary btn-sm">&larr; Back To Reports</a>
        <h5 class="mb-0 ms-2">Yearly Additions Report</h5>
    </div>

    <form action="" method="POST" class="no-print row g-2 align-items-center mb-4">
        <div class="col-auto">
            <label for="asset_year_select" class="form-label mb-0">Acquisition Year:</label>
        </div>
        <div class="col-auto">
            <select name="asset_year_select" id="asset_year_select" class="form-select form-select-sm">
                <option value="">-- Select Year --</option>
                <?php for ($y = $currentYear; $y >= $currentYear - 51; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo ($y === $selectedYear && isset($_POST['asset_year_select'])) ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">Print</button>
        </div>
    </form>

    <?php if (isset($_POST['asset_year_select']) && $_POST['asset_year_select'] !== ''): ?>

    <!-- Report header -->
    <div class="text-center mb-3">
        <h4 class="fw-bold text-uppercase mb-0">REGIONAL MARITIME UNIVERSITY</h4>
        <h5 class="fw-bold">ASSETS ADDITIONS REPORT — YEAR <?php echo htmlspecialchars($selectedYear); ?></h5>
        <p class="text-muted small mb-0">Assets acquired during the financial year (GH₵)</p>
        <hr>
    </div>

    <?php
    $stmt = mysqli_prepare($conn,
        "SELECT asset_id, asset_name, asset_class, location, acquisition_date,
                dollar_rate_used, additions, additions_dollar
         FROM assets
         WHERE YEAR(acquisition_date) = ?
         ORDER BY asset_class, acquisition_date");

    $rows       = [];
    $additionSum = 0.0;

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $selectedYear);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[]      = $row;
            $additionSum += (float)$row['additions'];
        }
        mysqli_stmt_close($stmt);
    }
    ?>

    <?php if (empty($rows)): ?>
        <div class="alert alert-warning">No assets acquired in <?php echo htmlspecialchars($selectedYear); ?>.</div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-sm" style="font-size:0.85rem">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Asset Name</th>
                <th>Asset Class</th>
                <th>Location</th>
                <th>Acquisition Date</th>
                <th class="text-end">FX Rate</th>
                <th class="text-end">Additions (GH₵)</th>
                <th class="text-end">Additions (USD)</th>
                <th class="no-print">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $counter = 1; foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($row['asset_name']); ?></td>
                <td><?php echo htmlspecialchars($row['asset_class']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['acquisition_date'])); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['dollar_rate_used'], 4); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['additions'], 2); ?></td>
                <td class="text-end">
                    <?php
                    $rate = (float)$row['dollar_rate_used'];
                    echo $rate > 0
                        ? number_format((float)$row['additions'] / $rate, 2)
                        : '-';
                    ?>
                </td>
                <td class="no-print">
                    <a href="../individual_assets/index.php?asset_id=<?php echo (int)$row['asset_id']; ?>"
                       class="btn btn-sm btn-outline-primary">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td colspan="6" class="text-end">Total Additions for <?php echo htmlspecialchars($selectedYear); ?>:</td>
                <td class="text-end"><?php echo number_format($additionSum, 2); ?></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    </div>
    <p class="text-muted small mt-2">
        Report generated on <?php echo date('d M Y, H:i'); ?> &bull; <?php echo count($rows); ?> asset(s) found
    </p>
    <?php endif; ?>

    <?php endif; ?>

</body>
</html>
