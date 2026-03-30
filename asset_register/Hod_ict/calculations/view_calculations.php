<?php
include "../datacon.php";

if (isset($_GET['asset_id'])) {
    $assetId = $_GET['asset_id'];

    

    // Fetch asset details
    $sqlAsset = "SELECT * FROM assets WHERE asset_id = $assetId";
    $resultAsset = mysqli_query($conn, $sqlAsset);

    if (!$resultAsset) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    $rowAsset = mysqli_fetch_assoc($resultAsset);
    $a_name=$rowAsset['asset_name'];

    // Fetch opbal_plus_additions and estimated_life from asset_classes table
    $assetClass = $rowAsset['asset_class'];
    $sqlAssetClasses = "SELECT opening_bal, opbal_plus_additions, estimated_life FROM asset_classes WHERE asset_class = '$assetClass'";
    $resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);

    if (!$resultAssetClasses) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    $rowAssetClasses = mysqli_fetch_assoc($resultAssetClasses);
    $opbalPlusAdditions = is_numeric($rowAssetClasses['opbal_plus_additions']) ? $rowAssetClasses['opbal_plus_additions'] : 0;
    $openingBalance = is_numeric($rowAssetClasses['opening_bal']) ? $rowAssetClasses['opening_bal'] : 0;
    $estimatedLife = $rowAssetClasses['estimated_life'];

    // Calculate assetCostOpeningBalance using opbal_plus_additions
    $assetCostOpeningBalance = $rowAsset['active_res_value'];

    // Calculate assetCostClosingBalance
    $assetCostClosingBalance = $assetCostOpeningBalance + $rowAsset['additions'] - $rowAsset['disposals'];

    // Fetch depreciation rate from asset_classes table
    $assetClass = $rowAsset['asset_class'];
    $sqlDepRate = "SELECT dep_rate FROM asset_classes WHERE asset_class = '$assetClass'";
    $resultDepRate = mysqli_query($conn, $sqlDepRate);

    if (!$resultDepRate) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    $rowDepRate = mysqli_fetch_assoc($resultDepRate);
    $depRate = $rowDepRate['dep_rate'];

    // Calculate depreciation cost
    $depreciationCost = $assetCostClosingBalance * $depRate;

    // Fetch acc_depr_opening_bal from other_values table
    $sqlAccDeprOpeningBal = "SELECT acc_depr_opening_bal FROM other_values";
    $resultAccDeprOpeningBal = mysqli_query($conn, $sqlAccDeprOpeningBal);

    if (!$resultAccDeprOpeningBal) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    $rowAccDeprOpeningBal = mysqli_fetch_assoc($resultAccDeprOpeningBal);
    $accDeprOpeningBal = 0; // Default value if $rowAccDeprOpeningBal is null or the key is not set

if (
    is_array($rowAccDeprOpeningBal) &&
    isset($rowAccDeprOpeningBal['acc_depr_opening_bal']) &&
    is_numeric($rowAccDeprOpeningBal['acc_depr_opening_bal'])
) {
    $accDeprOpeningBal = $rowAccDeprOpeningBal['acc_depr_opening_bal'];
}

// Now $accDeprOpeningBal contains the appropriate value


    // Calculate total_accumulated_depreciation
    $totalAccumulatedDepreciation = $accDeprOpeningBal + $depreciationCost;

    // Calculate account_depreciation_closing_balance
    $accountDepreciationClosingBalance = $totalAccumulatedDepreciation - $rowAsset['active_res_value'];

    // Calculate closing_carrying_value
    $closingCarryingValue = $assetCostClosingBalance - $accountDepreciationClosingBalance;

    // Calculate current_lifetime
    $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
    $currentLifetime = $rowAsset['current_year'] - $acquisitionYear + 1;

    // Calculate unexpired_lifetime
    $unexpiredLifetime = $estimatedLife - $currentLifetime;

    // Insert calculated values into the calculations table
    $sqlInsertCalculations = "INSERT INTO calculations (asset_cost_opening_balance, asset_cost_closing_balance, depreciation_cost, 
                                                            total_accumulated_depreciation, account_depreciation_closing_balance, closing_carrying_value,
                                                            current_lifetime, unexpired_lifetime)
                              VALUES ( $assetCostOpeningBalance, $assetCostClosingBalance, $depreciationCost, 
                                      $totalAccumulatedDepreciation, $accountDepreciationClosingBalance, $closingCarryingValue,
                                      $currentLifetime, $unexpiredLifetime)";

    $resultInsertCalculations = mysqli_query($conn, $sqlInsertCalculations);

    if (!$resultInsertCalculations) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    echo "<h2>Calculations for  $a_name</h2>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Asset Name</th>
            <th>Asset Cost Opening Balance(GHC)</th>
            <th>Asset Cost Closing Balance(GHC)</th>
            <th>Depreciation Cost(GHC)</th>
            <th>Total Accumulated Depreciation(GHC)</th>
            <th>Account Depreciation Closing Balance(GHC)</th>
            <th>Closing Carrying Value(GHC)</th>
            <th>Current Lifetime</th>
            <th>Unexpired Lifetime</th>
            <!-- Add more columns for additional calculations as needed -->
          </tr>";

    echo "<tr>";
    echo "<td>" . $a_name . "</td>";
    echo "<td>" . number_format($assetCostOpeningBalance, 2, ".", ",") . "</td>";
    echo "<td>" . number_format($assetCostClosingBalance, 2, ".", ","). "</td>";
    echo "<td>" . number_format($depreciationCost ,2, ".", ",") . "</td>";
    echo "<td>" . number_format($totalAccumulatedDepreciation, 2, ".", ",") . "</td>";
    echo "<td>" . number_format($accountDepreciationClosingBalance,2, ".", ",") . "</td>";
    echo "<td>" . number_format($closingCarryingValue, 2, ".", ",") . "</td>";
    echo "<td>" . $currentLifetime . "</td>";
    echo "<td>" . $unexpiredLifetime . "</td>";
    // ... add more columns for additional calculations as needed ...
    echo "</tr>";

    echo "</table>";
} else {
    echo "<p>No asset ID provided.</p>";
}

mysqli_close($conn);
?>
