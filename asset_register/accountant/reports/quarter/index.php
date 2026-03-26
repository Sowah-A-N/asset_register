<?php
session_start();
include "./datacon.php";
include "functions.php";

/**
 * Resolve quarter date range
 */
function getQuarterRange($year, $quarter)
{
    switch ($quarter) {
        case 'Q1':
            return [$year . '-01-01', $year . '-03-31'];
        case 'Q2':
            return [$year . '-04-01', $year . '-06-30'];
        case 'Q3':
            return [$year . '-07-01', $year . '-09-30'];
        case 'Q4':
            return [$year . '-10-01', $year . '-12-31'];
        default:
            return [null, null];
    }
}

/**
 * Fetch quarterly summary by asset class
 */
function quarterlyClassSummary($conn, $assetClass, $year, $quarter)
{
    [$startDate, $endDate] = getQuarterRange($year, $quarter);

    // Opening balances (from yearly opening, pro-rated)
    $opSql = "
        SELECT
            open_bal_usd,
            total_accum_start_usd
        FROM asset_class_opbal_year
        WHERE asset_class = ?
        AND year = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($opSql);
    $stmt->bind_param("si", $assetClass, $year);
    $stmt->execute();
    $opening = $stmt->get_result()->fetch_assoc();

    $openingBalance = $opening['open_bal_usd'] ?? 0;
    $openingAccum = $opening['total_accum_start_usd'] ?? 0;

    // Additions in quarter
    $addSql = "
        SELECT SUM(additions_dollar) AS total_additions
        FROM assets
        WHERE asset_class = ?
        AND acquisition_date BETWEEN ? AND ?
        AND disposed = 0
    ";

    $stmt = $conn->prepare($addSql);
    $stmt->bind_param("sss", $assetClass, $startDate, $endDate);
    $stmt->execute();
    $additions = $stmt->get_result()->fetch_assoc()['total_additions'] ?? 0;

    // Disposals in quarter
    $dispSql = "
        SELECT SUM(disposal_value / dollar_rate_used) AS total_disposals
        FROM disposals
        WHERE asset_class = ?
        AND date_of_disposal BETWEEN ? AND ?
    ";

    $stmt = $conn->prepare($dispSql);
    $stmt->bind_param("sss", $assetClass, $startDate, $endDate);
    $stmt->execute();
    $disposals = $stmt->get_result()->fetch_assoc()['total_disposals'] ?? 0;

    // Depreciation (quarterly = annual / 4)
    $depSql = "
        SELECT dep_rate
        FROM asset_classes
        WHERE asset_class = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($depSql);
    $stmt->bind_param("s", $assetClass);
    $stmt->execute();
    $rate = $stmt->get_result()->fetch_assoc()['dep_rate'] ?? 0;

    $quarterlyDep = (($openingBalance + $additions - $disposals) * ($rate / 100)) / 4;

    $accumEnd = $openingAccum + $quarterlyDep;
    $nbv = ($openingBalance + $additions - $disposals) - $accumEnd;

    return [
        'opening_balance' => round($openingBalance, 2),
        'additions' => round($additions, 2),
        'disposals' => round($disposals, 2),
        'depreciation' => round($quarterlyDep, 2),
        'accumulated_depr' => round($accumEnd, 2),
        'net_book_value' => round($nbv, 2),
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quarterly Asset Class Report (USD)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container-fluid p-4">

<a href="../" class="btn btn-outline-primary btn-sm mb-3">Back To Reports</a>

<form method="POST" class="card shadow-sm p-3 mb-4 needs-validation" novalidate>
    <div class="row">
        <div class="col-md-4">
            <label class="form-label">Asset Class</label>
            <select name="asset_class" class="form-select form-select-sm" required>
                <option value="">--Select--</option>
                <?php
                $res = mysqli_query($conn, "SELECT asset_class FROM asset_classes");
                while ($r = mysqli_fetch_assoc($res)) {
                    echo "<option value='{$r['asset_class']}'>{$r['asset_class']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Year</label>
            <select name="year" class="form-select form-select-sm" required>
                <option value="">--Select--</option>
                <?php
                for ($y = 2024; $y <= date('Y'); $y++) {
                    echo "<option value='$y'>$y</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Quarter</label>
            <select name="quarter" class="form-select form-select-sm" required>
                <option value="">--Select--</option>
                <option>Q1</option>
                <option>Q2</option>
                <option>Q3</option>
                <option>Q4</option>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary btn-sm w-100">Generate</button>
        </div>
    </div>
</form>

<?php
if (!empty($_POST['asset_class']) && !empty($_POST['year']) && !empty($_POST['quarter'])) {

    $summary = quarterlyClassSummary(
        $conn,
        $_POST['asset_class'],
        $_POST['year'],
        $_POST['quarter']
    );
    ?>

    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Quarterly Asset Class Report – <?= htmlspecialchars($_POST['asset_class']) ?>
            (<?= $_POST['quarter'] ?> <?= $_POST['year'] ?>)
        </div>

        <div class="card-body">
            <table class="table table-bordered table-sm text-end">
                <thead class="table-light">
                    <tr>
                        <th class="text-start">Metric</th>
                        <th>USD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="text-start">Opening Balance</td><td><?= number_format($summary['opening_balance'], 2) ?></td></tr>
                    <tr><td class="text-start">Additions</td><td><?= number_format($summary['additions'], 2) ?></td></tr>
                    <tr><td class="text-start">Disposals</td><td><?= number_format($summary['disposals'], 2) ?></td></tr>
                    <tr><td class="text-start">Depreciation (Quarter)</td><td><?= number_format($summary['depreciation'], 2) ?></td></tr>
                    <tr class="table-secondary">
                        <td class="text-start">Accumulated Depreciation</td>
                        <td><?= number_format($summary['accumulated_depr'], 2) ?></td>
                    </tr>
                    <tr class="table-success fw-bold">
                        <td class="text-start">Net Book Value</td>
                        <td><?= number_format($summary['net_book_value'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php } ?>

<script>
(function () {
    'use strict'
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', e => {
            if (!form.checkValidity()) {
                e.preventDefault()
                e.stopPropagation()
            }
            form.classList.add('was-validated')
        })
    })
})();
</script>

</body>
</html>
