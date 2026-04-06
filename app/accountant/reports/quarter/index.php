<?php
require_once '../../init.php';

/**
 * Resolve quarter date range
 */
function getQuarterRange($year, $quarter)
{
    switch ($quarter) {
        case 'Q1': return [$year . '-01-01', $year . '-03-31'];
        case 'Q2': return [$year . '-04-01', $year . '-06-30'];
        case 'Q3': return [$year . '-07-01', $year . '-09-30'];
        case 'Q4': return [$year . '-10-01', $year . '-12-31'];
        default:   return [null, null];
    }
}

/**
 * Fetch quarterly summary for one asset class (USD values)
 */
function quarterlyClassSummary($conn, $assetClass, $year, $quarter)
{
    [$startDate, $endDate] = getQuarterRange($year, $quarter);

    // Opening balances for the year — use correct column names
    $stmt = $conn->prepare(
        "SELECT opening_balance, total_accum_depr_start, rate
         FROM asset_class_opbal_year
         WHERE asset_class = ? AND year = ?
         LIMIT 1"
    );
    $stmt->bind_param('si', $assetClass, $year);
    $stmt->execute();
    $opening = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // opening_balance is stored in GHS; convert to USD using the active rate
    $rateRow = $conn->query(
        "SELECT dollar_rate FROM dollar_rate WHERE rate_status = 'ACTIVE' LIMIT 1"
    )->fetch_assoc();
    $activeRate = $rateRow ? (float)$rateRow['dollar_rate'] : 1;

    $openingBalanceGHS  = (float)($opening['opening_balance']       ?? 0);
    $accumDeprStartGHS  = (float)($opening['total_accum_depr_start'] ?? 0);
    $depRate            = (float)($opening['rate']                   ?? 0); // already a decimal e.g. 0.20
    $openingBalance     = $activeRate > 0 ? $openingBalanceGHS  / $activeRate : 0;
    $openingAccum       = $activeRate > 0 ? $accumDeprStartGHS  / $activeRate : 0;

    // Additions in quarter — use additions_dollar (already USD) and correct disposals filter
    $stmt = $conn->prepare(
        "SELECT COALESCE(SUM(additions_dollar), 0) AS total_additions
         FROM assets
         WHERE asset_class = ?
           AND acquisition_date BETWEEN ? AND ?
           AND disposals = 0"
    );
    $stmt->bind_param('sss', $assetClass, $startDate, $endDate);
    $stmt->execute();
    $additions = (float)$stmt->get_result()->fetch_assoc()['total_additions'];
    $stmt->close();

    // Disposals in quarter (convert GHS disposal value to USD at rate used)
    $stmt = $conn->prepare(
        "SELECT COALESCE(SUM(disposal_value / NULLIF(dollar_rate_used, 0)), 0) AS total_disposals
         FROM disposals
         WHERE asset_class = ?
           AND date_of_disposal BETWEEN ? AND ?"
    );
    $stmt->bind_param('sss', $assetClass, $startDate, $endDate);
    $stmt->execute();
    $disposals = (float)$stmt->get_result()->fetch_assoc()['total_disposals'];
    $stmt->close();

    // Quarterly depreciation = annual rate applied to closing cost balance / 4
    $closingCostBalance = $openingBalance + $additions - $disposals;
    $quarterlyDep       = ($closingCostBalance * $depRate) / 4;
    $accumEnd           = $openingAccum + $quarterlyDep;
    $nbv                = max($closingCostBalance - $accumEnd, 0);

    return [
        'opening_balance'  => round($openingBalance,  2),
        'additions'        => round($additions,        2),
        'disposals'        => round($disposals,        2),
        'total_cost'       => round($closingCostBalance, 2),
        'depreciation'     => round($quarterlyDep,     2),
        'accumulated_depr' => round($accumEnd,         2),
        'net_book_value'   => round($nbv,              2),
    ];
}

// Dynamic year range from DB
$minYearRow  = $conn->query("SELECT MIN(year) AS min_y FROM asset_class_opbal_year")->fetch_assoc();
$minYear     = ($minYearRow && $minYearRow['min_y']) ? (int)$minYearRow['min_y'] : 2020;
$currentYear = (int)date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quarterly Asset Class Report (USD)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>@media print { .no-print { display:none !important; } }</style>
</head>
<body class="container-fluid p-4">

<div class="no-print d-flex align-items-center gap-2 mb-3">
    <a href="../" class="btn btn-outline-secondary btn-sm">&larr; Back To Reports</a>
    <h5 class="mb-0 ms-2">Quarterly Asset Class Report (USD)</h5>
</div>

<form method="POST" class="no-print card shadow-sm p-3 mb-4 needs-validation" novalidate>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Asset Class</label>
            <select name="asset_class" class="form-select form-select-sm" required>
                <option value="">-- Select --</option>
                <?php
                $res = mysqli_query($conn, "SELECT asset_class FROM asset_classes ORDER BY asset_class");
                $selectedClass = $_POST['asset_class'] ?? '';
                while ($r = mysqli_fetch_assoc($res)) {
                    $sel = ($r['asset_class'] === $selectedClass) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($r['asset_class']) . "' $sel>"
                       . htmlspecialchars($r['asset_class']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Year</label>
            <select name="year" class="form-select form-select-sm" required>
                <option value="">-- Select --</option>
                <?php
                $selectedYear = isset($_POST['year']) ? (int)$_POST['year'] : 0;
                for ($y = $currentYear; $y >= $minYear; $y--) {
                    $sel = ($y === $selectedYear) ? 'selected' : '';
                    echo "<option value='$y' $sel>$y</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Quarter</label>
            <select name="quarter" class="form-select form-select-sm" required>
                <option value="">-- Select --</option>
                <?php
                $selectedQ = $_POST['quarter'] ?? '';
                foreach (['Q1','Q2','Q3','Q4'] as $q) {
                    $sel = ($q === $selectedQ) ? 'selected' : '';
                    echo "<option $sel>$q</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end gap-2">
            <button class="btn btn-primary btn-sm w-100">Generate</button>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">Print</button>
        </div>
    </div>
</form>

<?php
if (!empty($_POST['asset_class']) && !empty($_POST['year']) && !empty($_POST['quarter'])):
    $postedClass   = $_POST['asset_class'];
    $postedYear    = (int)$_POST['year'];
    $postedQuarter = $_POST['quarter'];

    $summary = quarterlyClassSummary($conn, $postedClass, $postedYear, $postedQuarter);
    $quarterLabels = [
        'Q1' => 'January – March',
        'Q2' => 'April – June',
        'Q3' => 'July – September',
        'Q4' => 'October – December',
    ];
?>

    <!-- Report header -->
    <div class="text-center mb-3">
        <h4 class="fw-bold text-uppercase mb-0">REGIONAL MARITIME UNIVERSITY</h4>
        <h5 class="fw-bold">
            QUARTERLY FIXED ASSET REPORT — <?= htmlspecialchars($postedQuarter) ?>
            <?= htmlspecialchars($postedYear) ?>
            (<?= $quarterLabels[$postedQuarter] ?? '' ?>)
        </h5>
        <p class="text-muted small mb-0">
            Asset Class: <strong><?= htmlspecialchars($postedClass) ?></strong>
            &nbsp;&bull;&nbsp; All values in USD
        </p>
        <hr>
    </div>

    <div class="row justify-content-center">
    <div class="col-md-6">
    <table class="table table-bordered table-sm text-end">
        <thead class="table-primary">
            <tr>
                <th class="text-start">Metric</th>
                <th>USD</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="text-start">Opening Balance (NBV)</td>
                <td>$ <?= number_format($summary['opening_balance'], 2) ?></td></tr>
            <tr><td class="text-start">Additions (Quarter)</td>
                <td>$ <?= number_format($summary['additions'], 2) ?></td></tr>
            <tr><td class="text-start">Disposals (Quarter)</td>
                <td>$ <?= number_format($summary['disposals'], 2) ?></td></tr>
            <tr class="table-light fw-semibold">
                <td class="text-start">Total Cost (Closing)</td>
                <td>$ <?= number_format($summary['total_cost'], 2) ?></td></tr>
            <tr><td class="text-start">Depreciation Charge (Quarter)</td>
                <td>$ <?= number_format($summary['depreciation'], 2) ?></td></tr>
            <tr class="table-secondary">
                <td class="text-start">Accumulated Depreciation (Closing)</td>
                <td>$ <?= number_format($summary['accumulated_depr'], 2) ?></td></tr>
            <tr class="table-success fw-bold">
                <td class="text-start">Net Book Value (Closing)</td>
                <td>$ <?= number_format($summary['net_book_value'], 2) ?></td></tr>
        </tbody>
    </table>
    <p class="text-muted small text-end mt-1">
        Generated: <?= date('d M Y, H:i') ?>
    </p>
    </div>
    </div>

<?php endif; ?>

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
