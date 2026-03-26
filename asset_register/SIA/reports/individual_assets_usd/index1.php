<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Individual Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

</head>
<body>
    

</body>
<?php

    include "../datacon.php";

    $assetId = $_GET['asset_id'];
    $query="SELECT asset_class FROM assets WHERE asset_id = {$assetId}";
    $result = $conn->query($query);
    $row=mysqli_fetch_array($result);
    $assetClass=$row['asset_class'];


    $query="SELECT opening_bal FROM asset_classes WHERE asset_class = '$assetClass' ";
    $result = $conn->query($query);
    $row=mysqli_fetch_array($result);
    $opening_bal=$row['opening_bal'];



    $query = "SELECT * FROM assets WHERE asset_id = {$assetId}";

    $result = $conn->query($query);

    if($result->num_rows > 0){

        echo "<div class='h-full overflow-y-scroll mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";
        echo "<table class='table-auto mb-8'>";
        echo "<thead><tr class='bg-gray-200'><th class='border px-4 py-2'>Asset Name</th>
                <th class='border px-4 py-2'>Asset Class</th>
                <th class='border px-4 py-2'>Asset Type</th>
                <th class='border px-4 py-2'>Location</th>
                <th class='border px-4 py-2'>Acquisition Date</th><br />
                <!--th class='border px-4 py-2'>Additions (USD)</th>
                <th class='border px-4 py-2'>Disposals</th-->
                <th class='border px-4 py-2'>Active Res Value</th>
                <th class='border px-4 py-2'>Dollar Rate Used</th>
                <th class='border px-4 py-2'>Date Added</th></tr></thead>";
        echo "<tbody>";

        while($row = $result->fetch_assoc()){            
            echo "<tr><td class='border px-4 py-2'>". $row['asset_name']."</td>
                    <td class='border px-4 py-2'>". $row['asset_class']."</td>
                    <td class='border px-4 py-2'>". $row['asset_type']."</td>
                    <td class='border px-4 py-2'>". $row['location']."</td>
                    <td class='border px-4 py-2'>". date("d-m-Y",strtotime($row['acquisition_date']))."</td>
                    <!--td class='border px-4 py-2'>". number_format(($row['additions']/$row['dollar_rate_used']), 2, ".", ",") ."</td>
                    <td class='border px-4 py-2'>". number_format($row['disposals'], 2, ".", ",") ."</td-->
                    <td class='border px-4 py-2'>". $row['active_res_value']."</td>
                    <td class='border px-4 py-2'>". $row['dollar_rate_used']."</td>
                    <td class='border px-4 py-2'>". date("d-m-Y",strtotime($row['date_added']))."</td></tr>";
            echo "</tbody>";
            echo "</div>";
        
        };
        

    };
?>

<?php
include "../datacon.php";

if (isset($_GET['asset_id'])) {
    $assetId = $_GET['asset_id'];

    // Fetch asset details
    $sqlAsset = "SELECT * FROM assets WHERE asset_id = $assetId";
    $resultAsset = mysqli_query($conn, $sqlAsset);

    if (!$resultAsset) {
        die("Error in SQL query (Asset): " . mysqli_error($conn));
    }

    $rowAsset = mysqli_fetch_assoc($resultAsset);
    $a_name=$rowAsset['asset_name'];

    // Fetch opbal_plus_additions and estimated_life from asset_classes table
    $assetClass = $rowAsset['asset_class'];
    $sqlAssetClasses = "SELECT opening_bal, opbal_plus_additions, estimated_life FROM asset_classes WHERE asset_class = '$assetClass'";
    $resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);

    if (!$resultAssetClasses) {
        die("Error in SQL query (Asset Classes): " . mysqli_error($conn));
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
        die("Error in SQL query (Depreciation Rate): " . mysqli_error($conn));
    }

    $rowDepRate = mysqli_fetch_assoc($resultDepRate);
    $depRate = $rowDepRate['dep_rate'];

    // Calculate depreciation cost
    $depreciationCost = $assetCostClosingBalance * $depRate;

    // Fetch acc_depr_opening_bal from other_values table
    $sqlAccDeprOpeningBal = "SELECT acc_depr_opening_bal FROM other_values";
    $resultAccDeprOpeningBal = mysqli_query($conn, $sqlAccDeprOpeningBal);

    if (!$resultAccDeprOpeningBal) {
        die("Error in SQL query (Accumulated Depreciation Opening Balance): " . mysqli_error($conn));
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

   
    echo "<h2 class='text-2xl font-bold mb-4'>Calculations for $a_name</h2>";
    echo "<div class='overflow-x-auto'>";
    echo "<table class='table-auto min-w-full bg-white border border-gray-300'>";
    echo "<thead class='bg-gray-200'>";
    echo "<tr>
            <!--th class='py-2 px-4 border-b'>Asset Name</th-->
            <th class='py-2 px-4 border-b'>Asset Cost Opening Balance(USD)</th>
            <th class='py-2 px-4 border-b'>Asset Cost Closing Balance(USD)</th>
            <th class='py-2 px-4 border-b'>Depreciation Cost(USD)</th>
            <th class='py-2 px-4 border-b'>Total Accumulated Depreciation(USD)</th>
            <th class='py-2 px-4 border-b'>Account Depreciation Closing Balance(USD)</th>
            <th class='py-2 px-4 border-b'>Closing Carrying Value(USD)</th>
            <th class='py-2 px-4 border-b'>Current Lifetime</th>
            <th class='py-2 px-4 border-b'>Unexpired Lifetime</th>
        </tr>";
    echo "</thead>";

    echo "<tbody>";
    echo "<tr>";
   // echo "<td class='py-2 px-4 border-b'>" . $a_name . "</td>";
    echo "<td class='py-2 px-4 border-b'>" . number_format(($opening_bal/$rowAsset['dollar_rate_used']), 2, ".", ",") . "</td>";
    echo "<td class='py-2 px-4 border-b'>" . number_format(($assetCostClosingBalance/$rowAsset['dollar_rate_used']), 2, ".", ",") . "</td>";
    echo "<td class='py-2 px-4 border-b'>" . number_format(($depreciationCost/$rowAsset['dollar_rate_used']), 2, ".", ",") . "</td>";
    echo "<td class='py-2 px-4 border-b'>" . number_format(($totalAccumulatedDepreciation/$rowAsset['dollar_rate_used']), 2, ".", ",") . "</td>";
    echo "<td class='py-2 px-4 border-b'>" . number_format(($accountDepreciationClosingBalance/$rowAsset['dollar_rate_used']), 2, ".", ",") . "</td>";
    echo "<td class='py-2 px-4 border-b'>" . number_format(($closingCarryingValue/$rowAsset['dollar_rate_used']), 2, ".", ",") . "</td>";
    echo "<td class='py-2 px-4 border-b'>" . $currentLifetime . "</td>";
    echo "<td class='py-2 px-4 border-b'>" . $unexpiredLifetime . "</td>";
    // ... add more columns for additional calculations as needed ...
    echo "</tr>";
    echo "</tbody>";

echo "</table>";
echo "</div>";
} else {
    echo "<p>No asset ID provided.</p>";
}

mysqli_close($conn);
?>


</html>