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
            echo "<option hidden value=''>--All Assets--</option>";

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

<!-- <label for="asset_sub_class_select" class="text-lg font-semibold">Select Asset Sub-Class:</label>
<select name="asset_sub_class_select" id="asset_sub_class_select" class="p-2 border border-gray-300 rounded">
    <option value="">--Select Asset Class First--</option>
</select>

<script>
    // JavaScript to handle change event of asset class dropdown
    document.getElementById('asset_class_select').addEventListener('change', function() {
        var assetClass = this.value; // Get the selected asset class

        // Make AJAX request to fetch sub-classes based on selected asset class
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the asset sub-class dropdown with fetched sub-classes
                document.getElementById('asset_sub_class_select').innerHTML = xhr.responseText;
            }
        };
        xhr.send('asset_class=' + assetClass); // Send selected asset class as POST parameter
    });
</script> -->

<!-- <?php
// Check if asset class is set and not empty
if (isset($_POST['asset_class']) && !empty($_POST['asset_class'])) {
    include "../datacon.php"; // Include database connection

    // Escape the asset class value to prevent SQL injection
    $assetClass = mysqli_real_escape_string($conn, $_POST['asset_class']);

    // Query to fetch sub-classes based on selected asset class
    $subClassQuery = "SELECT * FROM asset_class_sub_classes WHERE asset_class = '$assetClass'";
    $subClassResult = $conn->query($subClassQuery);

    if ($subClassResult->num_rows > 0) {
        // Output sub-class options
        while ($row = $subClassResult->fetch_assoc()) {
            echo "<option value=\"" . $row["sub_class"] . "\">" . $row["sub_class"] . "</option>";
        }
    } else {
        // No sub-classes available for the selected asset class
        echo "<option value=\"\">No sub-classes available</option>";
    }
} else {
    // Asset class not provided or empty
    echo "<option value=\"\">--Select Asset Class First--</option>";
}
?> -->

        
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
            $sqlQuery = "SELECT * FROM assets";
        } elseif (isset($_POST['asset_class_select'])) {
            // Query 2: All Assets in a selected asset class
            $selectedAssetClass = $_POST["asset_class_select"]; // Replace with actual user input
            $sqlQuery = "SELECT * FROM assets WHERE asset_class = '{$selectedAssetClass}'";
        } elseif (isset($startDate) && isset($endDate) && (!isset($_POST['asset_class_select']) || $_POST['asset_class_select'] == "" )) {
            // Query 3: All assets in a specified date range
            $sqlQuery = "SELECT * FROM assets WHERE acquisition_date BETWEEN '$startDate' AND '$endDate'";
        } elseif (isset($_POST['asset_class_select']) && (isset($startDate)) && (isset($endDate))) {
            // Query 4: All assets in a selected class and selected date range
            $selectedAssetClass = $_POST["asset_class_select"]; // Replace with actual user input
            $sqlQuery = "SELECT * FROM assets WHERE asset_class = '$selectedAssetClass' AND acquisition_date BETWEEN '{$startDate}' AND '{$endDate}'";
        } else {
            echo "Invalid user selection";
            // You may choose to handle invalid selections differently (redirect, display an error message, etc.)
            exit;
        }

        $sqlResult = $conn -> query($sqlQuery);
        if (isset($selectedAssetClass)){
            $sqlClassQuery = "SELECT * FROM asset_classes WHERE asset_class = '{$selectedAssetClass}';";
            $sqlClassResult = $conn -> query($sqlClassQuery);

            if ($sqlClassResult -> num_rows > 0){
                while($row = $sqlClassResult -> fetch_assoc()){
                    $a_class = $row["asset_class"];
                    $a_opening_bal = $row['opening_bal'];
                    $a_lifetime = $row['estimated_life'];
                    $a_class_active_res_value = 0;
                    $a_class_depr_charge = $row['dep_rate'];
                    $a_asset_cost_op_bal = $a_opening_bal - $a_class_active_res_value;
                    $a_depr_cost_per_charge = $a_opening_bal * $a_class_depr_charge;
                    $a_accum_depr_op_bal = $row['account_depr_open_bal'];
                    $a_total_accum_depr = $a_accum_depr_op_bal + $a_depr_cost_per_charge;
                    $a_closing_carry_value = $a_opening_bal - $a_total_accum_depr;
                }
            }
        }
        


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
        echo "<table class='table-auto mb-8'>";
        echo "<thead><tr class='bg-gray-200 '>
                <th class='border  px-4 py-2'>Asset Name</th>
                <th class='border  px-4 py-2'>Asset Class</th>
                <th class='border  px-4 py-2'>Asset Type</th>
                <th class='border  px-4 py-2'>Location</th>
                <th class='border  px-4 py-2'>Acquisition Date</th><br />
                <th class='border  px-4 py-2'>Active Res Value</th>
                <th class='border  px-4 py-2'>Dollar Rate Used</th>
                <th class='border  px-4 py-2'>Date Added</th>
                <th class='py-2 px-4 border '>Asset Cost Opening Balance(GHC)</th>
                <th class='py-2 px-4 border '>Asset Cost Closing Balance(GHC)</th>
                <th class='py-2 px-4 border '>Accumulated Depr. Opening Balance(GHC)</th>
                <th class='py-2 px-4 border '>Depreciation Cost(GHC)</th>
                <th class='py-2 px-4 border '>Total Accumulated Depreciation(GHC)</th>
                <th class='py-2 px-4 border '>Accumulated Depreciation Closing Balance(GHC)</th>
                <th class='py-2 px-4 border '>Closing Carrying Value(GHC)</th>
                <th class='py-2 px-4 border '>Current Lifetime</th>
                <th class='py-2 px-4 border '>Unexpired Lifetime</th></tr></thead>";

        echo "<tbody>";        



            while($row = $sqlResult->fetch_assoc()){  
                

                    if (isset($row['asset_id'])) {
                        $assetId = $row['asset_id'];                        
                    
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
                        $opbalPlusAdditions = isset($rowAssetClasses['opbal_plus_additions']) ? (is_numeric($rowAssetClasses['opbal_plus_additions']) ? $rowAssetClasses['opbal_plus_additions'] : 0) : 0;
                        $openingBalance = isset($rowAssetClasses['opening_bal']) ? (is_numeric($rowAssetClasses['opening_bal']) ? $rowAssetClasses['opening_bal'] : 0) : 0;
                        $estimatedLife = isset($rowAssetClasses['estimated_life']) ? $rowAssetClasses['estimated_life'] : 0 ;
                    
                        // Calculate assetCostOpeningBalance using opbal_plus_additions
                        $assetCostOpeningBalance = $rowAsset['active_res_value'];

                        $totalAssetCostOpBal = $totalAssetCostOpBal + $assetCostOpeningBalance; //Calc for total asset cost opening balance
                    
                        // Calculate assetCostClosingBalance
                        $assetCostClosingBalance = $assetCostOpeningBalance + $rowAsset['additions'] - $rowAsset['disposals'];

                        $totalAdditions = $totalAdditions + $rowAsset['additions']; //Calc for total additions
                        $totalAssetCostCloseBal = $totalAssetCostCloseBal + $assetCostClosingBalance; //Calc for total asset cost closing balance
                    
                        // Fetch depreciation rate from asset_classes table
                        $assetClass = $rowAsset['asset_class'];
                        $sqlDepRate = "SELECT dep_rate FROM asset_classes WHERE asset_class = '$assetClass'";
                        $resultDepRate = mysqli_query($conn, $sqlDepRate);
                    
                        if (!$resultDepRate) {
                            die("Error in SQL query (Depreciation Rate): " . mysqli_error($conn));
                        }
                    
                        $rowDepRate = mysqli_fetch_assoc($resultDepRate);
                        $depRate = isset($rowDepRate['dep_rate']) ? $rowDepRate['dep_rate'] : 0;
                    
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
                        $completeTotalAccumDepr = $completeTotalAccumDepr + $totalAccumulatedDepreciation;
                    
                        // Calculate account_depreciation_closing_balance
                        $accountDepreciationClosingBalance = $totalAccumulatedDepreciation - $rowAsset['active_res_value'];
                        $totalAccDeprCloseBal = $totalAccDeprCloseBal + $accountDepreciationClosingBalance; //Calc for total account depreciation closing balance
                    
                        // Calculate closing_carrying_value
                        $closingCarryingValue = $assetCostClosingBalance - $accountDepreciationClosingBalance- $openingBalance; //hj made a change on 14-01-25
                        $totalCloseCarryValue = $totalCloseCarryValue + $closingCarryingValue; // Calc for total closing carrying value 

                        // Calculate current_lifetime
                        $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
                        $currentLifetime = $rowAsset['current_year'] - $acquisitionYear + 1;
                    
                        // Calculate unexpired_lifetime
                        $unexpiredLifetime = $estimatedLife - $currentLifetime;
                    
                                            
                      
                       echo "<tr><td class='border px-4 py-2'>". $row['asset_name']."</td>
                       <td class='border px-4 py-2'>". $row['asset_class']."</td>
                       <td class='border px-4 py-2'>". $row['asset_type']."</td>
                       <td class='border px-4 py-2'>". $row['location']."</td>
                       <td class='border px-4 py-2'>".  date("d-m-Y",strtotime($row['acquisition_date']))."</td>
                       <td class='border px-4 py-2'>". $row['active_res_value']."</td>
                       <td class='border px-4 py-2'>". $row['dollar_rate_used']."</td>
                       <td class='border px-4 py-2'>".  date("d-m-Y",strtotime($row['date_added']))."</td>
                       <td class='py-2 px-4 border-b'>" . number_format($assetCostOpeningBalance, 2, ".", ",") . "</td>
                       <td class='py-2 px-4 border-b'>" . number_format($assetCostClosingBalance, 2, ".", ",") . "</td>
                       <td class='py-2 px-4 border-b'>" ."". "</td>
                       <td class='py-2 px-4 border-b'>" . number_format($depreciationCost, 2, ".", ",") . "</td>
                       <td class='py-2 px-4 border-b'>" . number_format($totalAccumulatedDepreciation, 2, ".", ",") . "</td>
                       <td class='py-2 px-4 border-b'>" . number_format($accountDepreciationClosingBalance, 2, ".", ",") . "</td>
                       <td class='py-2 px-4 border-b'>" . number_format($closingCarryingValue, 2, ".", ",") . "</td>
                       <td class='py-2 px-4 border-b'>" . $currentLifetime . "</td>
                       <td class='py-2 px-4 border-b'>" . $unexpiredLifetime . "</td>";
                        // ... add more columns for additional calculations as needed ...
                        echo "</tr>";

                        $totalDollarAdditions += ($row['additions']/$row['dollar_rate_used']);

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

            $t_class_dep_cost = isset($a_opening_bal) * isset($a_class_depr_charge);
            $a_asset_cost_op_bal = isset($a_asset_cost_op_bal) ? $a_asset_cost_op_bal : 0;
            $a_opening_bal = isset($a_opening_bal) ? $a_opening_bal : 0;
            $a_total_accum_depr = isset($a_total_accum_depr) ? $a_total_accum_depr : 0;
            $a_closing_carry_value = isset($a_closing_carry_value) ? $a_closing_carry_value : 0;
            $a_depr_cost_per_charge = isset($a_depr_cost_per_charge) ? $a_depr_cost_per_charge : 0;





    echo "<tr><td class='border px-4 py-2 font-bold'>"."Totals"."</td>
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2 font-bold'>".(isset($totalAssetCostOpBal) ? number_format($totalAssetCostOpBal+$a_asset_cost_op_bal, 2, ".", ",") :"")."</td> <!--Asset Cost Opening Balance-->
        <td class='border px-4 py-2 font-bold'>".(isset($totalAssetCostCloseBal) ? number_format($a_opening_bal+$totalAssetCostCloseBal, 2, ".", ",") :"")."</td> <!--Asset Cost Closing Balance--> 
        <td class='py-2 px-4 border font-bold'>" . (isset($a_accum_depr_op_bal) ? number_format($a_accum_depr_op_bal, 2, ".", ",") : "") . "</td>
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2 font-bold'>".(isset($completeTotalAccumDepr) ? number_format($completeTotalAccumDepr + $a_total_accum_depr, 2, ".", ",") :"")."</td> <!--Total Accumulated Depreciation-->
        <td class='border px-4 py-2 font-bold'>".(isset($totalAccDeprCloseBal) ? number_format($totalAccDeprCloseBal + $a_depr_cost_per_charge, 2, ".", ",") :"")."</td> <!--Acct Depr Closing Balance-->
        <td class='border px-4 py-2 font-bold'>".(isset($totalCloseCarryValue) ? number_format($totalCloseCarryValue + $a_closing_carry_value, 2, ".", ",") :"")."</td> <!--Closing Carying Value-->
        <td class='border px-4 py-2'>".""."</td>
        <td class='border px-4 py-2'>".""."</td></tr>";
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

