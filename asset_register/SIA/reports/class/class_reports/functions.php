<?php 

    include_once "../datacon.php";
    global $conn;

    function singleAssetCalculations($assetId, $year){
        global $conn;
        $sqlAsset = "SELECT * FROM assets WHERE asset_id = $assetId";
        $resultAsset = mysqli_query($conn, $sqlAsset);
    
        if (!$resultAsset) {
            die("Error in SQL query (Asset): " . mysqli_error($conn));
        }
    
        $rowAsset = mysqli_fetch_assoc($resultAsset);
        $a_name = $rowAsset['asset_name'];
    
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
    
        // Calculate total_accumulated_depreciation
        $totalAccumulatedDepreciation = $accDeprOpeningBal + $depreciationCost;
    
        // Calculate account_depreciation_closing_balance
        $accountDepreciationClosingBalance = $totalAccumulatedDepreciation - $rowAsset['active_res_value'];
    
        // Calculate closing_carrying_value
        $closingCarryingValue = $assetCostClosingBalance - $accountDepreciationClosingBalance;
    
        // Calculate current_lifetime
        $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
        $currentLifetime = $year - $acquisitionYear + 1;
    
        // Calculate unexpired_lifetime
        $unexpiredLifetime = $estimatedLife - $currentLifetime;
    
        return [
            'asset_name' => $a_name,
            'asset_cost_opening_balance' => $openingBalance,
            'asset_cost_closing_balance' => $assetCostClosingBalance,
            'depreciation_cost' => $depreciationCost,
            'total_accumulated_depreciation' => $totalAccumulatedDepreciation,
            'account_depreciation_closing_balance' => $accountDepreciationClosingBalance,
            'closing_carrying_value' => $closingCarryingValue,
            'current_lifetime' => $currentLifetime,
            'unexpired_lifetime' => $unexpiredLifetime,
        ];
    }

    function calculationsByClass($assetClass, $yearOfReport) {
        global $conn;

        // Fetch assets from the database
        $sqlAssets = "SELECT * FROM assets WHERE asset_class = '$assetClass' ORDER BY acquisition_date ASC";
        $resultAssets = mysqli_query($conn, $sqlAssets);

        if (!$resultAssets) {
            die("Error in SQL query (Assets): " . mysqli_error($conn));
        }

        // Fetch asset class details
        $sqlAssetClasses = "SELECT opening_bal, opbal_plus_additions, estimated_life, dep_rate FROM asset_classes WHERE asset_class = '$assetClass'";
        $resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);

        if (!$resultAssetClasses) {
            die("Error in SQL query (Asset Classes): " . mysqli_error($conn));
        }

        $rowAssetClasses = mysqli_fetch_assoc($resultAssetClasses);

        // Initialize summation variables
        $totalBookValueStart = 0;
        $totalDepreciationExpense = 0;
        $totalAccumulatedDepreciation = 0;
        $totalBookValueEnd = 0;
        $totalAdditions = 0;

        // Initialize asset data array
        $assetData = array();

        echo "<div class='container mx-4 my-4 '>";
        echo "<h2 class='text-primary mb-4'>Depreciation Schedule for $assetClass in $yearOfReport (Cedi Report) </h2>";
        echo "<div class='table w-100'>";
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead class='table-primary'>";
        echo "<tr>
            <th>S/N</th>
            <th>Asset Name</th>
            <th>Location</th>
            <th>Tag/Chassis No.</th>
            <th>Date of Purchase</th>
            <th>Cost of Purchase(GHS)</th>
            <th>Year of Report</th>
            <th>Net Book Value</th>
            <th>Depreciation Rate</th>
            <th>Depreciation Expense</th>
            <th>Accumulated Depreciation</th>
            <th>Closing Carrying Value(USD)</th>
            <th>Dollar Rate Used</th>";

        for($month = 1; $month <= 12; $month++){
            //echo "<th>". date('F', strtotime(mktime(0, 0, 0, $month, 0, 0))) ."</th>";
        }    

        $month = 0;

        while($month <= 12){
            //echo "<th>". date('F', strtotime(mktime(0, 0, 0, $month, 0, 0))) ."</th>";
            $month++;
            print_r(date('F', strtotime(mktime(0, 0, 0, $month, 0, 0))) );
        }

        die();
        
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        $counter = 1;
        $foundRecords = false;  // Flag to check if there are any records

        while ($rowAsset = mysqli_fetch_assoc($resultAssets)) {
            // Calculate acquisition year
            $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));

            // Calculate number of years
            $numYears = $rowAssetClasses['estimated_life'];

            // Asset details
            $assetName = $rowAsset['asset_name'];
            $rate = $rowAsset['dollar_rate_used'];
            $serial = $rowAsset['serial_number'];
            $date = date('d-m-Y', strtotime($rowAsset['acquisition_date']));
            $location = $rowAsset['location'];
            $cost = $rowAssetClasses['opening_bal'];
            $additions = $rowAsset['additions'];
            $life = $numYears; // Estimated life in years
            $depreciation_rate = $rowAssetClasses['dep_rate']; // Depreciation rate from database

            // Acquisition date details
            $acquisitionDate = strtotime($rowAsset['acquisition_date']);
            $acquisitionYear = (int)date('Y', $acquisitionDate);
            $acquisitionMonth = (int)date('m', $acquisitionDate);

            // Adjust for months remaining in the first year
            $monthsInYear = 12;
            $monthsRemainingFirstYear = $monthsInYear - $acquisitionMonth + 1;

            // First-year depreciation calculation
            $depreciation_expense = $additions * $depreciation_rate * ($monthsRemainingFirstYear / $monthsInYear);

            // Depreciation calculations
            $depreciationResults = array();
            $book_value_start = $additions; // Initial cost
            $accumulated_depreciation = 0;

            for ($i = 0; $i < $life; $i++) {
                $year = $acquisitionYear + $i;

                // Skip years outside the selected range
                if ($year < $acquisitionYear || $year > $yearOfReport) {
                    continue;
                }

                if ($i === 0) {
                    // First-year depreciation
                    $depreciation_expense = $additions * $depreciation_rate * ($monthsRemainingFirstYear / $monthsInYear);
                } else {
                    // Full depreciation for subsequent years
                    $depreciation_expense = $additions * $depreciation_rate;
                }

                // Prevent depreciation from exceeding book value
                if ($book_value_start - $depreciation_expense < 0) {
                    $depreciation_expense = $book_value_start;
                }

                $accumulated_depreciation += $depreciation_expense;
                $book_value_end = $book_value_start - $depreciation_expense;

                // Store results in the array
                $depreciationResults[$year] = array(
                    'book_value_start' => $book_value_start,
                    'depreciation_rate' => $depreciation_rate,
                    'depreciation_expense' => $depreciation_expense,
                    'accumulated_depreciation' => $accumulated_depreciation,
                    'book_value_end' => $book_value_end,
                );

                // Update book value for the next year
                $book_value_start = $book_value_end;

                // Stop if book value reaches zero or salvage value
                if ($book_value_start <= 0) {
                    break;
                }
            }

            // Output results and store in assetData array
            foreach ($depreciationResults as $year => $result) {
                if ($year == $yearOfReport) {
                    $foundRecords = true;

                    echo "<tr>";
                    echo "<td>$counter</td>";
                    echo "<td>$assetName</td>";
                    echo "<td>$location</td>";
                    echo "<td>$serial</td>";
                    echo "<td>$date</td>";
                    echo "<td>GH₵" . number_format($additions, 2) . "</td>";
                    echo "<td>$year</td>";
                    echo "<td>GH₵" . number_format($result['book_value_start'], 2) . "</td>";
                    echo "<td>" . ($result['depreciation_rate'] * 100) . "%</td>";
                    echo "<td>GH₵" . number_format($result['depreciation_expense'], 2) . "</td>";
                    echo "<td>GH₵" . number_format($result['accumulated_depreciation'], 2) . "</td>";
                    echo "<td>GH₵" . number_format($result['book_value_end'], 2) . "</td>";
                    echo "<td>$rate</td>";
                    echo "</tr>";

                    $counter++;

                    // Accumulate totals
                    $totalBookValueStart += $result['book_value_start'];
                    $totalDepreciationExpense += $result['depreciation_expense'];
                    $totalAccumulatedDepreciation += $result['accumulated_depreciation'];
                    $totalBookValueEnd += $result['book_value_end'];
                    $totalAdditions += $additions;

                    // Store the calculated data in assetData
                    $assetData[] = array(
                        'asset_name' => $assetName,
                        'location' => $location,
                        'serial_number' => $serial,
                        'date_of_purchase' => $date,
                        'cost_of_purchase' => $additions,
                        'year_of_report' => $year,
                        'net_book_value' => $result['book_value_start'],
                        'depreciation_rate' => $result['depreciation_rate'],
                        'depreciation_expense' => $result['depreciation_expense'],
                        'accumulated_depreciation' => $result['accumulated_depreciation'],
                        'closing_carrying_value' => $result['book_value_end'],
                        'dollar_rate_used' => $rate
                    );
                }
            }
        }

        // Check if any records were found, if not display a message
        if (!$foundRecords) {
            echo "<tr><td colspan='14' class='text-center text-danger'>No records found for the selected asset class and year.</td></tr>";
        } else {
            // Output the summation row if records exist
            echo "<tfoot>";
            echo "<tr>";
            echo "<td colspan='5'><strong>Total</strong></td>";
            echo "<td><strong>GH₵" . number_format($totalAdditions, 2) . "</strong></td>";
            echo "<td></td>"; 
            echo "<td><strong>GH₵" . number_format($totalBookValueStart, 2) . "</strong></td>";
            echo "<td></td>"; // Empty cell for depreciation rate (no summation)
            echo "<td><strong>GH₵" . number_format($totalDepreciationExpense, 2) . "</strong></td>";
            echo "<td><strong>GH₵" . number_format($totalAccumulatedDepreciation, 2) . "</strong></td>";
            echo "<td><strong>GH₵" . number_format($totalBookValueEnd, 2) . "</strong></td>";
            echo "<td></td>"; // Empty cell for the button column
            echo "</tr>";
            echo "</tfoot>";
        }

        echo "</tbody>";
        echo "</table>";

        echo "</div>";  
        echo "</div>";

        // Optionally, you can return the assetData array if needed elsewhere in your script
        return $assetData;
    }
