<?php
require_once '../../init.php';

$currentYear  = (int)date('Y');
$minYearResult = mysqli_query($conn, "SELECT MIN(year) AS min_year FROM asset_class_opbal_year");
$minYearRow    = $minYearResult ? mysqli_fetch_assoc($minYearResult) : null;
$minYear       = ($minYearRow && $minYearRow['min_year']) ? (int)$minYearRow['min_year'] : 2020;
$selectedYear  = isset($_POST['report_year']) ? (int)$_POST['report_year'] : $currentYear;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accumulated Depreciation Report</title>
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
        <h5 class="mb-0 ms-2">Accumulated Depreciation Report</h5>
    </div>

    <form method="POST" class="no-print row g-2 align-items-center mb-4">
        <div class="col-auto">
            <label for="report_year" class="form-label mb-0">Financial Year:</label>
        </div>
        <div class="col-auto">
            <select name="report_year" id="report_year" class="form-select form-select-sm">
                <?php for ($y = $currentYear; $y >= $minYear; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo ($y === $selectedYear) ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">Generate</button>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">Print</button>
        </div>
    </form>

    <?php
    $stmt = mysqli_prepare($conn,
        "SELECT
            a.asset_class,
            a.year,
            a.opening_balance,
            a.total_accum_depr_start,
            a.total_depr_year_charge,
            a.disposals_depr,
            a.rate,
            a.expected_life_months,
            COALESCE(b.total_additions_cedi,  0) AS total_additions_cedi,
            COALESCE(b.total_disposals_cedi,  0) AS total_disposals_cedi
         FROM asset_class_opbal_year a
         LEFT JOIN asset_additions_year b
            ON a.asset_class COLLATE utf8mb4_general_ci = b.asset_class COLLATE utf8mb4_general_ci
            AND a.year = b.year
         WHERE a.year = ?
         ORDER BY a.asset_class");

    $rows          = [];
    $totalAccumEnd = 0.0;

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $selectedYear);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $row['accum_depr_end'] = (float)$row['total_accum_depr_start']
                                   + (float)$row['total_depr_year_charge']
                                   - (float)$row['disposals_depr'];
            $rows[]          = $row;
            $totalAccumEnd  += $row['accum_depr_end'];
        }
        mysqli_stmt_close($stmt);
    }
    ?>

    <div class="text-center mb-3">
        <h4 class="fw-bold text-uppercase mb-0">REGIONAL MARITIME UNIVERSITY</h4>
        <h5 class="fw-bold">ACCUMULATED DEPRECIATION SCHEDULE — YEAR <?php echo htmlspecialchars($selectedYear); ?></h5>
        <hr>
    </div>

    <?php if (empty($rows)): ?>
        <div class="alert alert-warning">No data found for year <?php echo htmlspecialchars($selectedYear); ?>.</div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-sm" style="font-size:0.85rem">
        <thead class="table-primary">
            <tr>
                <th>Asset Class</th>
                <th class="text-end">Opening Balance<br><small class="fw-normal">(Cost)</small></th>
                <th class="text-end">Additions</th>
                <th class="text-end">Disposals</th>
                <th class="text-end">Accum. Depr<br>(Opening)</th>
                <th class="text-end">Depr. Charge<br>(Year)</th>
                <th class="text-end">Depr. on<br>Disposals</th>
                <th class="text-end fw-bold">Accum. Depr<br>(Closing)</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Life<br>(Mths)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['asset_class']); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['opening_balance'], 2); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['total_additions_cedi'], 2); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['total_disposals_cedi'], 2); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['total_accum_depr_start'], 2); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['total_depr_year_charge'], 2); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['disposals_depr'], 2); ?></td>
                <td class="text-end fw-bold"><?php echo number_format($row['accum_depr_end'], 2); ?></td>
                <td class="text-end"><?php echo number_format((float)$row['rate'] * 100, 0); ?>%</td>
                <td class="text-end"><?php echo htmlspecialchars($row['expected_life_months']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td colspan="7" class="text-end">Total Accumulated Depreciation (Closing):</td>
                <td class="text-end"><?php echo number_format($totalAccumEnd, 2); ?></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    </div>
    <p class="text-muted small mt-2">
        Report generated on <?php echo date('d M Y, H:i'); ?> &bull; Financial Year: <?php echo htmlspecialchars($selectedYear); ?>
    </p>
    <?php endif; ?>

</body>
</html>
