<?php
    include "../datacon.php";
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> 
</head>
<body>
    <form action="" method="post" class="flex items-center space-x-4">
        <label for="asset_class_select" class="text-lg font-semibold">Select Asset Class:</label>
            <?php 
            echo "<select name='asset_class_select' id='asset_class_select' class='p-2 border border-gray-300 rounded'>";

            if(isset($_POST['asset_class_select'])){
                echo "<option value={$_POST['asset_class_select']} selected> {$_POST['asset_class_select']} </option>";            
            }

            ?>
            
            <?php
            $assetClassQuery = "SELECT * FROM asset_classes;";
            $assetClassResult = $conn->query($assetClassQuery);
            
            // Check if there are results
            if ($assetClassResult->num_rows > 0) {
                

                // Output data of each row
                while ($row = $assetClassResult->fetch_assoc()) {
                    echo "<option value=\"" . $row["asset_class"] . "\">" . $row["asset_class"] . "</option>";
                }
            } else {
                echo "<option value=\"\">No asset classes available</option>";
            }

            echo "</select>";
            ?>
            
        
        <label for="asset_class_select" class="text-lg font-semibold">Select Start Date:</label>
        <input type='date' name='startDate'>    

        <label for="asset_class_select" class="text-lg font-semibold">Select End Date:</label>
        <input type='date' name='endDate'>    


        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Filter</button>
    </form>

<?php 

            $startDate = (isset($_POST['startDate'])) ? date("Y-m-d",strtotime($_POST['startDate'])) : "";
            $endDate = (isset($_POST['endDate'])) ? date("Y-m-d",strtotime($_POST['endDate'])) : "";


       // Run the selected query based on user input
        if (!isset($_POST['asset_class_select']) || $_POST['asset_class_select'] == "" ) {
            // Query 1: All Assets
            $sqlQuery = "SELECT * FROM assets WHERE disposals = 0";
        } elseif (isset($_POST['asset_class_select'])) {
            // Query 2: All Assets in a selected asset class
            $selectedAssetClass = $_POST["asset_class_select"]; // Replace with actual user input
            $sqlQuery = "SELECT * FROM assets WHERE asset_class = '{$selectedAssetClass}' AND disposals = 0";
        } elseif (isset($startDate) && isset($endDate) && (!isset($_POST['asset_class_select']) || $_POST['asset_class_select'] == "" )) {
            // Query 3: All assets in a specified date range
            $sqlQuery = "SELECT * FROM assets WHERE acquisition_date BETWEEN '$startDate' AND '$endDate' AND disposals = 0";
        } elseif (isset($_POST['asset_class_select']) && (isset($startDate)) && (isset($endDate))) {
            // Query 4: All assets in a selected class and selected date range
            $selectedAssetClass = $_POST["asset_class_select"]; // Replace with actual user input
            $sqlQuery = "SELECT * FROM assets WHERE asset_class = '$selectedAssetClass' AND acquisition_date BETWEEN '{$startDate}' AND '{$endDate}' AND disposals = 0";
        } else {
            echo "Invalid user selection";
            // You may choose to handle invalid selections differently (redirect, display an error message, etc.)
            exit;
        }

    $sqlResult = $conn -> query($sqlQuery);

    if ($sqlResult -> num_rows > 0){       
        $totalAdditions = $totalAssetCostOpBal = $totalAssetCostCloseBal = 0;
        $completeTotalAccumDepr = $totalAccDeprCloseBal = $totalCloseCarryValue = 0;
        $totalDollarAdditions = 0;

        echo "<div class='h-screen overflow-auto mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";
        if(isset($selectedAssetClass)){
            echo "<h2 class='font-san-serif font-bold text-lg'>Calculations for {$selectedAssetClass}</h2>";
           } else {
            echo "<h2 class='font-san-serif font-bold text-lg'>Calculations For All Assets</h2>";
           }
        echo "<div class='relative'>";   
        echo "<table class='table-auto mb-8'>";
        echo "<thead><tr class='bg-gray-200 z-10'>
                <th class='border px-4 py-2'>Asset Name</th>
                <th class='border px-4 py-2'>Asset Class</th>
                <th class='border px-4 py-2'>Asset Type</th>
                <th class='border px-4 py-2'>Location</th>
                <th class='border px-4 py-2'>Acquisition Date</th>
                <!--th class='border px-4 py-2'>Additions (USD)</th-->
                <th class='border px-4 py-2'>Active Res Value</th>
                <th class='border px-4 py-2'>Dollar Rate Used</th>
                <th class='border px-4 py-2'>Date Added</th>
                <th class='py-2 px-4 border'>Asset Cost Opening Balance(USD)</th>
                <th class='py-2 px-4 border'>Asset Cost Closing Balance(USD)</th>
                <th class='py-2 px-4 border'>Depreciation Cost(USD)</th>
                <th class='py-2 px-4 border'>Total Accumulated Depreciation(USD)</th>
                <th class='py-2 px-4 border'>Account Depreciation Closing Balance(USD)</th>
                <th class='py-2 px-4 border'>Closing Carrying Value(USD)</th>
                <th class='py-2 px-4 border'>Current Lifetime</th>
                <th class='py-2 px-4 border'>Unexpired Lifetime</th></tr></thead>";

        echo "<tbody>";        
        echo date("d-m-Y",strtotime($startDate)) . " : " . date("d-m-Y",strtotime($endDate)) ;
       
       // <td class='border px-4 py-2'>" . number_format($row['additions']/$row['dollar_rate_used'], 2, ".", ",") . "</td>


            while($row = $sqlResult->fetch_assoc()){  
                // echo "<tr><td class='border px-4 py-2'>". $row['asset_name']."</td>
                //     <td class='border px-4 py-2'>". $row['asset_class']."</td>
                //     <td class='border px-4 py-2'>". $row['asset_type']."</td>
                //     <td class='border px-4 py-2'>". $row['location']."</td>
                //     <td class='border px-4 py-2'>". $row['acquisition_date']."</td>
                //     <td class='border px-4 py-2'>". $row['additions']."</td>
                //     <td class='border px-4 py-2'>". $row['active_res_value']."</td>
                //     <td class='border px-4 py-2'>". $row['dollar_rate_used']."</td>
                //     <td class='border px-4 py-2'>". $row['date_added']."</td>";
                    //echo $row['asset_id'];

                    if (isset($row['asset_id'])) {
                        $assetId = $row['asset_id'];

                        
                    
                        // Fetch asset details
                        $sqlAsset = "SELECT * FROM assets WHERE asset_id = $assetId AND disposals = 0";
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

                        //$totalAssetCostOpBal = $totalAssetCostOpBal + $assetCostOpeningBalance; //Calc for total asset cost opening balance
                    
                        // Calculate assetCostClosingBalance
                        $assetCostClosingBalance = $assetCostOpeningBalance + $rowAsset['additions'] - $rowAsset['disposals'];

                        //$totalAdditions = $totalAdditions + $rowAsset['additions']; //Calc for total additions
                        //$totalAssetCostCloseBal = $totalAssetCostCloseBal + $assetCostClosingBalance; //Calc for total asset cost closing balance
                    
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
                        //$completeTotalAccumDepr = $completeTotalAccumDepr + $totalAccumulatedDepreciation;
                    
                        // Calculate account_depreciation_closing_balance
                        $accountDepreciationClosingBalance = $totalAccumulatedDepreciation - $rowAsset['active_res_value'];
                        //$totalAccDeprCloseBal = $totalAccDeprCloseBal + $accountDepreciationClosingBalance; //Calc for total account depreciation closing balance
                    
                        // Calculate closing_carrying_value
                        $closingCarryingValue = $assetCostClosingBalance - $accountDepreciationClosingBalance;
                        //$totalCloseCarryValue = $totalCloseCarryValue + $closingCarryingValue; // Calc for total closing carrying value 

                        // Calculate current_lifetime
                        $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
                        $currentLifetime = $rowAsset['current_year'] - $acquisitionYear + 1;
                    
                        // Calculate unexpired_lifetime
                        $unexpiredLifetime = $estimatedLife - $currentLifetime;
                    
                       
                        // echo "<h2 class='text-2xl font-bold mb-4'>Calculations for $a_name</h2>";

                        // echo "<div class='overflow-x-auto'>";
                        // echo "<table class='table-auto min-w-full bg-white border border-gray-300'>";
                        // echo "<thead class='bg-gray-200'>";
                        // echo "<tr>
                        //         <!--th class='py-2 px-4 border-b'>Asset Name</th-->
                        //         <th class='py-2 px-4 border-b'>Asset Cost Opening Balance(GHC)</th>
                        //         <th class='py-2 px-4 border-b'>Asset Cost Closing Balance(GHC)</th>
                        //         <th class='py-2 px-4 border-b'>Depreciation Cost(GHC)</th>
                        //         <th class='py-2 px-4 border-b'>Total Accumulated Depreciation(GHC)</th>
                        //         <th class='py-2 px-4 border-b'>Account Depreciation Closing Balance(GHC)</th>
                        //         <th class='py-2 px-4 border-b'>Closing Carrying Value(GHC)</th>
                        //         <th class='py-2 px-4 border-b'>Current Lifetime</th>
                        //         <th class='py-2 px-4 border-b'>Unexpired Lifetime</th>
                        //     </tr>";
                        // echo "</thead>";
                    
                        // echo "<tbody>";
                        // echo "<tr>";
                    //    // echo "<td class='py-2 px-4 border-b'>" . $a_name . "</td>";
                    //    echo "<tr><td class='border px-4 py-2'>". $row['asset_name']."</td>
                    //    <td class='border px-4 py-2'>". $row['asset_class']."</td>
                    //    <td class='border px-4 py-2'>". $row['asset_type']."</td>
                    //    <td class='border px-4 py-2'>". $row['location']."</td>
                    //    <td class='border px-4 py-2'>".  date("d-m-Y",strtotime($row['acquisition_date']))."</td>
                    //    <td class='border px-4 py-2'>". number_format($row['additions'], 2, ".", ",")."</td>
                    //    <td class='border px-4 py-2'>" . number_format($row['additions']/$row['dollar_rate_used'], 2, ".", ",") . "</td>
                    //    <td class='border px-4 py-2'>". $row['active_res_value']."</td>
                    //    <td class='border px-4 py-2'>". $row['dollar_rate_used']."</td>
                    //    <td class='border px-4 py-2'>".  date("d-m-Y",strtotime($row['date_added']))."</td>
                    //    <td class='py-2 px-4 border-b'>" . number_format($assetCostOpeningBalance, 2, ".", ",") . "</td>
                    //    <td class='py-2 px-4 border-b'>" . number_format($assetCostClosingBalance, 2, ".", ",") . "</td>
                    //    <td class='py-2 px-4 border-b'>" . number_format($depreciationCost, 2, ".", ",") . "</td>
                    //    <td class='py-2 px-4 border-b'>" . number_format($totalAccumulatedDepreciation, 2, ".", ",") . "</td>
                    //    <td class='py-2 px-4 border-b'>" . number_format($accountDepreciationClosingBalance, 2, ".", ",") . "</td>
                    //    <td class='py-2 px-4 border-b'>" . number_format($closingCarryingValue, 2, ".", ",") . "</td>
                    //    <td class='py-2 px-4 border-b'>" . $currentLifetime . "</td>
                    //    <td class='py-2 px-4 border-b'>" . $unexpiredLifetime . "</td>";
                    //     // ... add more columns for additional calculations as needed ...
                    //     echo "</tr>";

                    echo "<tr>";
                    echo "<td class='border px-4 py-2'>" . $row['asset_name'] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row['asset_class'] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row['asset_type'] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row['location'] . "</td>";
                    echo "<td class='border px-4 py-2'>" . date("d-m-Y", strtotime($row['acquisition_date'])) . "</td>";
                
                    // Convert values to dollars using the dollar_rate_used for each row
                    $additionsInDollars = $row['additions'] / $row['dollar_rate_used'];
                    $activeResValueInDollars = $row['active_res_value'] / $row['dollar_rate_used'];
                    $assetCostOpeningBalanceInDollars = $assetCostOpeningBalance / $row['dollar_rate_used'];
                    $assetCostClosingBalanceInDollars = $assetCostClosingBalance / $row['dollar_rate_used'];
                    $depreciationCostInDollars = $depreciationCost / $row['dollar_rate_used'];
                    $totalAccumulatedDepreciationInDollars = $totalAccumulatedDepreciation / $row['dollar_rate_used'];
                    $accountDepreciationClosingBalanceInDollars = $accountDepreciationClosingBalance / $row['dollar_rate_used'];
                    $closingCarryingValueInDollars = $closingCarryingValue / $row['dollar_rate_used'];
                
                    //echo "<td class='border px-4 py-2'>" . number_format($additionsInDollars, 2, ".", ",") . "</td>";
                    echo "<td class='border px-4 py-2'>" . number_format($row['active_res_value'], 2, ".", ",") . "</td>";
                    echo "<td class='border px-4 py-2'>" . number_format($row['dollar_rate_used'], 2, ".", ",") . "</td>";
                    echo "<td class='border px-4 py-2'>" . date("d-m-Y", strtotime($row['date_added'])) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . number_format($assetCostOpeningBalanceInDollars, 2, ".", ",") . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . number_format($assetCostClosingBalanceInDollars, 2, ".", ",") . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . number_format($depreciationCostInDollars, 2, ".", ",") . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . number_format($totalAccumulatedDepreciationInDollars, 2, ".", ",") . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . number_format($accountDepreciationClosingBalanceInDollars, 2, ".", ",") . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . number_format($closingCarryingValueInDollars, 2, ".", ",") . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . $currentLifetime . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . $unexpiredLifetime . "</td>";
                    echo "</tr>";

                    $totalAdditions += $additionsInDollars;
                    $totalAssetCostOpBal += $assetCostOpeningBalanceInDollars;
                    $totalAssetCostCloseBal += $assetCostClosingBalanceInDollars;
                    $completeTotalAccumDepr += $totalAccumulatedDepreciationInDollars;
                    $totalAccDeprCloseBal += $accountDepreciationClosingBalanceInDollars;
                    $totalCloseCarryValue += $closingCarryingValueInDollars;

                    } else {
                        echo "<p>No asset ID provided.</p>";
                    }           
            }

    

    echo "<tr><td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td></tr>";

            echo "<tr><td class='border px-4 py-2 font-bold'>" . "Totals" . "</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2 font-bold'>" . number_format($totalAdditions, 2, ".", ",") . "</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2 font-bold'>" . number_format($totalAssetCostOpBal, 2, ".", ",") . "</td>
            <td class='border px-4 py-2 font-bold'>" . number_format($totalAssetCostCloseBal, 2, ".", ",") . "</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2 font-bold'>" . number_format($completeTotalAccumDepr, 2, ".", ",") . "</td>
            <td class='border px-4 py-2 font-bold'>" . number_format($totalAccDeprCloseBal, 2, ".", ",") . "</td>
            <td class='border px-4 py-2 font-bold'>" . number_format($totalCloseCarryValue, 2, ".", ",") . "</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2'>" . "" . "</td></tr>";
        
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

} else {
    echo "<div class='h-full mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'><p>No records exist for this category</p></div>";
} 
    mysqli_free_result($sqlResult);
?>


</body>

</html>

