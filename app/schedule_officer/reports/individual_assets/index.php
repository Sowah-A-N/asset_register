<?php
require_once '../../init.php';

$assetId = isset($_GET['asset_id']) ? (int)$_GET['asset_id'] : 0;

if ($assetId <= 0) {
    echo "<p>Invalid asset ID.</p>";
    exit;
}

// Fetch asset details
$stmt = mysqli_prepare($conn, "SELECT * FROM assets WHERE asset_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $assetId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$rowAsset = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$rowAsset) {
    echo "<p>Asset not found.</p>";
    exit;
}

$assetClass = $rowAsset['asset_class'];

// Fetch asset class details (opening_bal, rate, life)
$stmt2 = mysqli_prepare($conn, "SELECT opening_bal, opbal_plus_additions, estimated_life, dep_rate FROM asset_classes WHERE asset_class = ? LIMIT 1");
mysqli_stmt_bind_param($stmt2, 's', $assetClass);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);
$rowAssetClasses = mysqli_fetch_assoc($res2);
mysqli_stmt_close($stmt2);

$opbalPlusAdditions = is_numeric($rowAssetClasses['opbal_plus_additions'] ?? null) ? (float)$rowAssetClasses['opbal_plus_additions'] : 0;
$openingBalance     = is_numeric($rowAssetClasses['opening_bal']           ?? null) ? (float)$rowAssetClasses['opening_bal'] : 0;
$estimatedLife      = (int)($rowAssetClasses['estimated_life'] ?? 0);
$depRate            = (float)($rowAssetClasses['dep_rate'] ?? 0);

$assetCostOpeningBalance          = (float)$rowAsset['active_res_value'];
$assetCostClosingBalance          = $assetCostOpeningBalance + (float)$rowAsset['additions'] - (float)$rowAsset['disposals'];
$depreciationCost                 = $assetCostClosingBalance * $depRate;

// Fetch acc_depr_opening_bal
$res3 = mysqli_query($conn, "SELECT acc_depr_opening_bal FROM other_values LIMIT 1");
$rowAccDepr = $res3 ? mysqli_fetch_assoc($res3) : null;
$accDeprOpeningBal = (is_array($rowAccDepr) && is_numeric($rowAccDepr['acc_depr_opening_bal'] ?? null))
    ? (float)$rowAccDepr['acc_depr_opening_bal'] : 0;

$totalAccumulatedDepreciation       = $accDeprOpeningBal + $depreciationCost;
$accountDepreciationClosingBalance  = $totalAccumulatedDepreciation - (float)$rowAsset['active_res_value'];
$closingCarryingValue               = $assetCostClosingBalance - $accountDepreciationClosingBalance;

$acquisitionYear  = (int)date('Y', strtotime($rowAsset['acquisition_date']));
$currentLifetime  = (int)$rowAsset['current_year'] - $acquisitionYear + 1;
$unexpiredLifetime = $estimatedLife - $currentLifetime;

$a_name = $rowAsset['asset_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Individual Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

<?php
// Asset details table
$stmt3 = mysqli_prepare($conn, "SELECT * FROM assets WHERE asset_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt3, 'i', $assetId);
mysqli_stmt_execute($stmt3);
$detailRes = mysqli_stmt_get_result($stmt3);

if ($detailRes && mysqli_num_rows($detailRes) > 0) {
    echo "<div class='h-full overflow-y-scroll mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";
    echo "<table class='table-auto mb-8'>";
    echo "<thead><tr class='bg-gray-200'>
            <th class='border px-4 py-2'>Asset Name</th>
            <th class='border px-4 py-2'>Asset Class</th>
            <th class='border px-4 py-2'>Asset Type</th>
            <th class='border px-4 py-2'>Location</th>
            <th class='border px-4 py-2'>Acquisition Date</th>
            <th class='border px-4 py-2'>Active Res Value</th>
            <th class='border px-4 py-2'>Dollar Rate Used</th>
            <th class='border px-4 py-2'>Date Added</th>
          </tr></thead><tbody>";

    while ($row = mysqli_fetch_assoc($detailRes)) {
        echo "<tr>
            <td class='border px-4 py-2'>" . esc($row['asset_name']) . "</td>
            <td class='border px-4 py-2'>" . esc($row['asset_class']) . "</td>
            <td class='border px-4 py-2'>" . esc($row['asset_type']) . "</td>
            <td class='border px-4 py-2'>" . esc($row['location']) . "</td>
            <td class='border px-4 py-2'>" . esc(date('d-m-Y', strtotime($row['acquisition_date']))) . "</td>
            <td class='border px-4 py-2'>" . esc($row['active_res_value']) . "</td>
            <td class='border px-4 py-2'>" . esc($row['dollar_rate_used']) . "</td>
            <td class='border px-4 py-2'>" . esc(date('d-m-Y', strtotime($row['date_added']))) . "</td>
        </tr>";
    }
    echo "</tbody></table></div>";
}
mysqli_stmt_close($stmt3);
?>

<?php // Calculations table ?>
<h2 class='text-2xl font-bold mb-4'>Calculations for <?= esc($a_name) ?></h2>
<div class='overflow-x-auto'>
<table class='table-auto min-w-full bg-white border border-gray-300'>
    <thead class='bg-gray-200'>
        <tr>
            <th class='py-2 px-4 border-b'>Asset Cost Opening Balance (GHC)</th>
            <th class='py-2 px-4 border-b'>Asset Cost Closing Balance (GHC)</th>
            <th class='py-2 px-4 border-b'>Depreciation Cost (GHC)</th>
            <th class='py-2 px-4 border-b'>Total Accumulated Depreciation (GHC)</th>
            <th class='py-2 px-4 border-b'>Acc. Depreciation Closing Balance (GHC)</th>
            <th class='py-2 px-4 border-b'>Closing Carrying Value (GHC)</th>
            <th class='py-2 px-4 border-b'>Current Lifetime</th>
            <th class='py-2 px-4 border-b'>Unexpired Lifetime</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class='py-2 px-4 border-b'><?= esc(number_format($openingBalance, 2, '.', ',')) ?></td>
            <td class='py-2 px-4 border-b'><?= esc(number_format($assetCostClosingBalance, 2, '.', ',')) ?></td>
            <td class='py-2 px-4 border-b'><?= esc(number_format($depreciationCost, 2, '.', ',')) ?></td>
            <td class='py-2 px-4 border-b'><?= esc(number_format($totalAccumulatedDepreciation, 2, '.', ',')) ?></td>
            <td class='py-2 px-4 border-b'><?= esc(number_format($accountDepreciationClosingBalance, 2, '.', ',')) ?></td>
            <td class='py-2 px-4 border-b'><?= esc(number_format($closingCarryingValue, 2, '.', ',')) ?></td>
            <td class='py-2 px-4 border-b'><?= (int)$currentLifetime ?></td>
            <td class='py-2 px-4 border-b'><?= (int)$unexpiredLifetime ?></td>
        </tr>
    </tbody>
</table>
</div>

</body>
</html>
