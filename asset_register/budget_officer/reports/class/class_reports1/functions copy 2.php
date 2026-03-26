<?php 

    include_once "./datacon.php";
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
    
    function calculateAssetCalculationsForMultipleAssets($assetClass, $year) {
        global $conn;
        // Fetch assets from the database
        $sqlAssets = "SELECT * FROM assets WHERE asset_class = '$assetClass'";
        $resultAssets = mysqli_query($conn, $sqlAssets);
    
        if (!$resultAssets) {
            die("Error in SQL query (Assets): " . mysqli_error($conn));
        }
    
        $results = [];
    
        while ($rowAsset = mysqli_fetch_assoc($resultAssets)) {
            $assetId = $rowAsset['asset_id'];
            $results[] = singleAssetCalculationsAlternate($assetId, $year);
        }
    
        return $results;
    }
    
    function singleAssetCalculationsAlternate($assetId, $year) {
        global $conn;
    
        // Fetch asset details
        $sqlAsset = "SELECT * FROM assets WHERE asset_id = $assetId";
        $resultAsset = mysqli_query($conn, $sqlAsset);
    
        if (!$resultAsset) {
            die("Error in SQL query (Asset): " . mysqli_error($conn));
        }
    
        $rowAsset = mysqli_fetch_assoc($resultAsset);
    
        // Fetch asset class details
        $assetClass = $rowAsset['asset_class'];
        $sqlAssetClasses = "SELECT opening_bal, opbal_plus_additions, estimated_life, dep_rate FROM asset_classes WHERE asset_class = '$assetClass'";
        $resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);
    
        if (!$resultAssetClasses) {
            die("Error in SQL query (Asset Classes): " . mysqli_error($conn));
        }
    
        $rowAssetClasses = mysqli_fetch_assoc($resultAssetClasses);
        var_dump($rowAssetClasses);
       //  /**
        // Calculate acquisition year
        $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
    
        // Calculate current year
        $currentYear = date('Y');
    
        // Calculate number of years
        $numYears = $rowAssetClasses['estimated_life'] ?? "N/A";
    
        // Asset details
        $assetName = $rowAsset['asset_name'];
        $cost = $rowAssetClasses['opening_bal'] ?? 0;
        $additions = $rowAsset['additions'];
        $life = $numYears; // Estimated life in years
        $depreciationRate = $rowAssetClasses['dep_rate'] ?? 0; // Depreciation rate from database
    
        // Capture selected start and end years from the form submission
        $startYear = isset($_GET['start_year']) ? (int)$_GET['start_year'] : $acquisitionYear;
        $endYear = isset($_GET['end_year']) ? (int)$_GET['end_year'] : $currentYear;
    
        // Ensure the start year is not earlier than the acquisition year
        $startYear = max($startYear, $acquisitionYear);
    
        // Ensure the end year is not later than the current year
        $endYear = min($endYear, $currentYear);
    
        // Acquisition details
        $acquisitionDate = strtotime($rowAsset['acquisition_date']); // Assuming format is YYYY-MM-DD
        $acquisitionYear = (int)date('Y', $acquisitionDate);
        $acquisitionMonth = (int)date('m', $acquisitionDate);
    
        // Adjust for months remaining in the first year
        $monthsInYear = 12;
        $monthsRemainingFirstYear = $monthsInYear - $acquisitionMonth + 1;
    
        // First-year depreciation
        $firstYearDepreciation = $additions * $depreciationRate * ($monthsRemainingFirstYear / $monthsInYear);
    
        // Depreciation calculations
        $depreciationResults = array();
        $bookValueStart = $additions; // Initial cost
        $accumulatedDepreciation = 0;
    
        for ($i = 0; $i < $life; $i++) {
            $year = $acquisitionYear + $i;
    
            // Skip years outside the selected range
            if ($year < $startYear || $year > $endYear) {
                continue;
            }
    
            if ($i === 0) {
                // First-year depreciation
                $depreciationExpense = $firstYearDepreciation;
            } else {
                // Full depreciation for subsequent years
                $depreciationExpense = $additions * $depreciationRate;
            }
    
            // Prevent depreciation from exceeding book value
            if ($bookValueStart - $depreciationExpense < 0) {
                $depreciationExpense = $bookValueStart; // Cap at remaining book value
            }
    
            $accumulatedDepreciation += $depreciationExpense;
            $bookValueEnd = $bookValueStart - $depreciationExpense;
    
            // Store results
            $depreciationResults[$year] = array(
                'book_value_start' => $bookValueStart,
                'depreciation_rate' => $depreciationRate,
                'depreciation_expense' => $depreciationExpense,
                'accumulated_depreciation' => $accumulatedDepreciation,
                'book_value_end' => $bookValueEnd,
            );
    
            // Update book value for the next year
            $bookValueStart = $bookValueEnd;
    
            // Stop if book value reaches zero or salvage value
            if ($bookValueStart <= 0) {
                break;
            }
        }
    
        // Filter results based on the selected year
        $filteredResults = array_filter($depreciationResults, function($yearKey) use ($year) {
            return $yearKey == $year;
        }, ARRAY_FILTER_USE_KEY);

        // Calculate total accumulated depreciation
        $totalAccumulatedDepreciation = array_sum(array_column($filteredResults, 'accumulated_depreciation'));

        // Calculate total depreciation cost
        $totalDepreciationCost = array_sum(array_column($filteredResults, 'depreciation_expense'));

        // Calculate closing carrying value
        $closingCarryingValue = $additions - $totalAccumulatedDepreciation;

        // Calculate current lifetime
        $currentLifetime = $year - $acquisitionYear + 1;

        // Calculate unexpired lifetime
        $unexpiredLifetime = $life - $currentLifetime;

        // Return results
        return [
            'asset_name' => $assetName,
            'asset_cost_opening_balance' => $cost,
            'asset_cost_closing_balance' => $closingCarryingValue,
            'depreciation_cost' => $totalDepreciationCost,
            'total_accumulated_depreciation' => $totalAccumulatedDepreciation,
            'account_depreciation_closing_balance' => $totalAccumulatedDepreciation,
            'closing_carrying_value' => $closingCarryingValue,
            'current_lifetime' => $currentLifetime,
            'unexpired_lifetime' => $unexpiredLifetime,
            'depreciation_results' => $filteredResults,
        ];
    }

    function calculations($assetId, $yearOfReport){
        global $conn;

        // Fetch asset details
        $sqlAsset = "SELECT * FROM assets WHERE asset_id = $assetId";
        $resultAsset = mysqli_query($conn, $sqlAsset);

        if (!$resultAsset) {
            die("Error in SQL query (Asset): " . mysqli_error($conn));
        }

        $rowAsset = mysqli_fetch_assoc($resultAsset);

        // Fetch asset class details
        $assetClass = $rowAsset['asset_class'];
        $sqlAssetClasses = "SELECT opening_bal, opbal_plus_additions, estimated_life, dep_rate FROM asset_classes WHERE asset_class = '$assetClass'";
        $resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);

        if (!$resultAssetClasses) {
            die("Error in SQL query (Asset Classes): " . mysqli_error($conn));
        }

        $rowAssetClasses = mysqli_fetch_assoc($resultAssetClasses);

        // Calculate acquisition year
        $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));

        // Calculate current year
        $currentYear = date('Y');

        // Calculate number of years
        $numYears = $rowAssetClasses['estimated_life'];
        $serial=["serial_number"];

        // Asset details
        $assetName = $rowAsset['asset_name'];
        $cost = $rowAssetClasses['opening_bal'];
        $additions = $rowAsset['additions'];
        $life = $numYears; // Estimated life in years
        $depreciation_rate = $rowAssetClasses['dep_rate']; // Depreciation rate from database

        //<!-- Bootstrap JS -->
        echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>';

        echo "<div class='container my-4'>";
        echo "<h2 class='text-primary mb-4'>Asset Details for $assetName</h2>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered table-striped'>";
        // Asset details table
        echo "<thead class='table-primary'>
            <tr>
                <th>Asset Name</th>
                <th>Chassis/Tag Number</th>
                <th>Asset Class</th>
                <th>Acquisition Date</th>
                <th>Active Res Value</th>
                <th>Additions</th>
                <th>Disposals</th>
                <th>Depreciation Rate</th>
            </tr>
        </thead>";
        echo "<tbody>
            <tr>
                <td>{$rowAsset['asset_name']}</td>
                <td>{$rowAsset['serial_number']}</td>
                <td>{$rowAsset['asset_class']}</td>
                <td>" . date('d/m/Y', strtotime($rowAsset['acquisition_date'])) . "</td>
                <td>" . number_format($rowAsset['active_res_value'], 2, ".", ",") . "</td>
                <td>" . number_format($rowAsset['additions'], 2, ".", ",") . "</td>
                <td>" . number_format($rowAsset['disposals'], 2, ".", ",") . "</td>
                <td>{$depreciation_rate}</td>
            </tr>
        </tbody>";
        echo "</table></div>";

        // Depreciation Schedule Table
        echo "<h2 class='text-primary mt-5 mb-4'>Depreciation Schedule for $assetName</h2>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead class='table-primary'>";
        echo "<tr>
            <th>Year of Report</th>
            <th>Net Book Value</th>
            <th>Depreciation Rate</th>
            <th>Depreciation Expense</th>
            <th>Accumulated Depreciation</th>
            <th>Closing Carrying Value</th>
        
        </tr>";
        echo "</thead>";
        echo "<tbody>";

        // Extract acquisition month and year
        // Acquisition details
        $acquisitionDate = strtotime($rowAsset['acquisition_date']); // Assuming format is YYYY-MM-DD
        $acquisitionYear = (int)date('Y', $acquisitionDate);
        $acquisitionMonth = (int)date('m', $acquisitionDate);

        // Adjust for months remaining in the first year
        $monthsInYear = 12;
        $monthsRemainingFirstYear = $monthsInYear - $acquisitionMonth + 1;

        // Debugging Outputs
        echo "Acquisition Month: $acquisitionMonth\n"; // Should be 4
        echo "Months Remaining First Year: $monthsRemainingFirstYear\n"; // Should be 9
        echo "Depreciation Rate: $depreciation_rate\n"; // Should be 0.2

        // First-year depreciation
        $depreciation_expense = $additions * $depreciation_rate * ($monthsRemainingFirstYear / $monthsInYear);
        echo "First-Year Depreciation Expense: $depreciation_expense\n";

        // Depreciation calculations
        $depreciationResults = array();
        $book_value_start = $additions; // Initial cost
        $accumulated_depreciation = 0;

        for ($i = 0; $i < $life; $i++) {
            $year = $acquisitionYear + $i;

            // Skip years outside the selected range
            if($year < $yearOfReport || $year > $yearOfReport){
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
                $depreciation_expense = $book_value_start; // Cap at remaining book value
            }

            $accumulated_depreciation += $depreciation_expense;
            $book_value_end = $book_value_start - $depreciation_expense;

            // Store results
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

        // Output results
        foreach ($depreciationResults as $year => $result) {
            echo "<tr>";
            echo "<td>$year</td>";
            echo "<td>GH₵" . number_format($result['book_value_start'], 2) . "</td>";
            echo "<td>" . ($result['depreciation_rate'] * 100) . "%</td>";
            echo "<td>GH₵" . number_format($result['depreciation_expense'], 2) . "</td>";
            echo "<td>GH₵" . number_format($result['accumulated_depreciation'], 2) . "</td>";
            echo "<td>GH₵" . number_format($result['book_value_end'], 2) . "</td>";
            echo "<td><button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modal$year'>View Monthly Breakdown</button></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        //    return "Calculating...";
    }

    function calculateAccumulatedDepreciation($purchaseYear, $purchaseMonth, $currentYear, $monthlyDepreciation) {
        if ($currentYear < $purchaseYear) {
            return 0; // No depreciation before purchase year
        }
    
        $yearsElapsed = $currentYear - $purchaseYear; // Full years passed
        $monthsInFirstYear = 12 - $purchaseMonth + 1; // Months of use in the first year
    
        // Accumulated depreciation formula
        $accumulatedDepreciation = ($yearsElapsed * (12 * $monthlyDepreciation)) + ($monthsInFirstYear * $monthlyDepreciation);
    
        return $accumulatedDepreciation;
    }

    function calculateNetBookValue($purchaseYear, $purchaseMonth, $currentYear, $purchaseCost, $monthlyDepreciation) {
        if ($currentYear == $purchaseYear) {
            return $purchaseCost; // In acquisition year, NBV = Purchase Cost
        }
    
        // Get the Accumulated Depreciation for the previous year
        $previousYear = $currentYear - 1;
        $accumulatedDepreciationPreviousYear = calculateAccumulatedDepreciation($purchaseYear, $purchaseMonth, $previousYear, $monthlyDepreciation);
        
        // Closing Carrying Value of the Previous Year is the NBV for the Current Year
        $closingCarryingValuePreviousYear = $purchaseCost - $accumulatedDepreciationPreviousYear;
    
        return max($closingCarryingValuePreviousYear, 0); // Ensure NBV doesn't go negative
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

        echo "<div class='container my-4 '>";
        echo "<h2 class='text-primary mb-4'>Depreciation Schedule for $assetClass in $yearOfReport (Cedi Report) </h2>";
        echo "<div class='table w-90'>";
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
            <th>Closing Carrying Value(GH₵)</th>
            <th>Dollar Rate Used</th>";
        
           
        for ($month = 1; $month <= 12; $month++) {
            echo "<th>" . date('F', mktime(0, 0, 0, $month, 1)) . "</th>";
        }
        
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        $counter = 1;
        $foundRecords = false;  // Flag to check if there are any records

        // Initialize an array to store monthly totals
        $monthlyTotals = array_fill(1, 12, 0); // Initialize all months with zero

        // Arrays
        $firstYearDataArray = array();
        $lastYearDataArray = array();
        $yearsBetweenDataArray = array();
        $assetTotalsArray = array();

        // Accumulate totals
        $totalBookValueStart = 0;
        $totalDepreciationExpense = 0;
        $totalAccumulatedDepreciation = 0;
        $totalBookValueEnd = 0;
        $totalAdditions = 0;
        $foundRecords = false;
        $totalDisposals = 0;

        while ($rowAsset = mysqli_fetch_assoc($resultAssets)) {
            //var_dump($rowAsset);
            // // Calculate acquisition year
            // $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));

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
            $estimatedLifeinMonths = $numYears * 12;
            $monthsRemainingAfterFirstYear = $estimatedLifeinMonths - $monthsRemainingFirstYear;
            $monthsRemainingLastYear = 0;

            $newMonthlyDepreciation = $additions / $estimatedLifeinMonths;

            if ($acquisitionMonth != 1) {
                $monthsRemainingLastYear = $monthsRemainingAfterFirstYear % 12;
                $lastYear = $acquisitionYear + $numYears;
            }

            $monthsBetweenFirstAndLastYear = $monthsRemainingAfterFirstYear - $monthsRemainingLastYear;
            $exactLastYear = $acquisitionYear + $life;
            $estimatedLifeInYears = $exactLastYear + ($monthsRemainingLastYear ? 1 : 0);

            if ($rowAsset["disposals"]) {
                // Refactored code
                $disposedAssetsQuery = "SELECT date_of_disposal FROM disposals WHERE serial_number = '{$rowAsset['serial_number']}' AND date_of_disposal IS NOT NULL";
                $disposedAssetsResult = mysqli_query($conn, $disposedAssetsQuery);

                $disposedAssetsRow = mysqli_fetch_assoc($disposedAssetsResult);
                $monthOfDisposal = date('m', strtotime($disposedAssetsRow['date_of_disposal'])); 
                $yearOfDisposal = date('Y', strtotime($disposedAssetsRow['date_of_disposal']));

                if($yearOfDisposal < $yearOfReport){
                    continue;
                } else if($yearOfDisposal == $yearOfReport) {
                    $lastYear = $yearOfReport;
                    $monthsRemainingLastYear = $monthOfDisposal;
                    $totalDisposals += $rowAsset['additions'];
                }
            }

            $firstYearData = array(
                "first" => "first",
                "newAssetName" => $assetName,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear, 
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear, 
                "monthsRemainingLastYear" => $monthsRemainingLastYear, 
                "newAcquisitionMonth" => $acquisitionMonth, 
                "newAcquisitionYear" => $acquisitionYear, 
                "newLastYear" => $lastYear, 
                "months" => $monthsRemainingFirstYear,
                "newMonthlyDepreciation" => $newMonthlyDepreciation,
                "newDepreciationExpense" => $newMonthlyDepreciation * $monthsRemainingFirstYear,
            );
            $firstYearData["newAccumulatedDepreciation"] = $firstYearData["newDepreciationExpense"];
            $firstYearData["newClosingCarryingValue"] = $additions - $firstYearData["newAccumulatedDepreciation"];
            $firstYearData["newNetBookValue"] = calculateNetBookValue($acquisitionYear, $acquisitionMonth, $yearOfReport, $additions, $newMonthlyDepreciation);

            $yearsBetweenData = array(
                "between" => "between",
                "newAssetName" => $assetName,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear, 
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear, 
                "monthsRemainingLastYear" => $monthsRemainingLastYear, 
                "newAcquisitionMonth" => $acquisitionMonth, 
                "newAcquisitionYear" => $acquisitionYear, 
                "newLastYear" => $lastYear, 
                "months"=> $monthsBetweenFirstAndLastYear > 12 ? 12 : $monthsBetweenFirstAndLastYear,
                "newMonthlyDepreciation" => $newMonthlyDepreciation,
                "newDepreciationExpense"=> $newMonthlyDepreciation * $monthsBetweenFirstAndLastYear / (intdiv($monthsBetweenFirstAndLastYear, $monthsInYear))
            );
            $yearsBetweenData["newAccumulatedDepreciation"] = calculateAccumulatedDepreciation($acquisitionYear, $acquisitionMonth, $yearOfReport, $newMonthlyDepreciation);
            $yearsBetweenData["newClosingCarryingValue"] = $additions - $yearsBetweenData["newAccumulatedDepreciation"];
            $yearsBetweenData["newNetBookValue"] = calculateNetBookValue($acquisitionYear, $acquisitionMonth, $yearOfReport, $additions, $newMonthlyDepreciation);

            $lastYearData = array(
                "last" => "last",
                "newAssetName" => $assetName,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear, 
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear, 
                "monthsRemainingLastYear" => $monthsRemainingLastYear, 
                "newAcquisitionMonth" => $acquisitionMonth, 
                "newAcquisitionYear" => $acquisitionYear, 
                "newLastYear" => $lastYear, 
                "months" => $monthsRemainingLastYear,
                "newMonthlyDepreciation" => $newMonthlyDepreciation,
                "newDepreciationExpense"=> $newMonthlyDepreciation * $monthsRemainingLastYear
            );
            $lastYearData["newAccumulatedDepreciation"] = $firstYearData["newAccumulatedDepreciation"] + ($newMonthlyDepreciation * $monthsBetweenFirstAndLastYear) + ($newMonthlyDepreciation * $monthsRemainingLastYear);
            $lastYearData["newClosingCarryingValue"] = $additions - $lastYearData["newAccumulatedDepreciation"];
            $lastYearData["newNetBookValue"] = calculateNetBookValue($acquisitionYear, $acquisitionMonth, $yearOfReport, $additions, $newMonthlyDepreciation);
            $lastYearData["monthsDisplay"] = array();

            if ($yearOfReport == $firstYearData["newAcquisitionYear"]) {
                //var_dump($firstYearData);
                $foundRecords = true;
                $totalBookValueStart += $firstYearData["newNetBookValue"];
                $totalDepreciationExpense += $firstYearData["newDepreciationExpense"];
                $totalAccumulatedDepreciation += $firstYearData["newAccumulatedDepreciation"];
                $totalBookValueEnd += $firstYearData["newClosingCarryingValue"];
                $totalAdditions += $additions;

                array_push($firstYearDataArray, $firstYearData);

                echo "<tr>";
                echo "<td>$counter</td>";
                echo "<td>$assetName</td>";
                echo "<td>$location</td>";
                echo "<td>$serial</td>";
                echo "<td>$date</td>";
                echo "<td>GH₵" . number_format($additions, 2) . "</td>";
                echo "<td>$yearOfReport</td>";
                echo "<td>GH₵" . number_format($firstYearData['newNetBookValue'], 2) . "</td>";
                echo "<td>" . ($depreciation_rate * 100) . "%</td>";
                echo "<td>GH₵" . number_format($firstYearData['newDepreciationExpense'], 2) . "</td>";
                echo "<td>GH₵" . number_format($firstYearData['newAccumulatedDepreciation'], 2) . "</td>";
                echo "<td>GH₵" . number_format($firstYearData['newClosingCarryingValue'], 2) . "</td>";
                echo "<td>$rate</td>";
                $emptyMonth = $monthsInYear - $firstYearData["months"];
                for ($month=1; $month <= $monthsInYear ; $month++) { 
                    if ($emptyMonth >= 1) {
                        echo "<td>-</td>";
                    } else {
                        $formatedMonthlyDeprciation = number_format($firstYearData["newMonthlyDepreciation"], 2);
                        echo "<td>GH₵" . $formatedMonthlyDeprciation . "</td>";
                        $monthlyTotals[$month] += $firstYearData["newMonthlyDepreciation"];
                    }
                    $emptyMonth--;
                }
            }
            else if ($yearOfReport == $lastYearData["newLastYear"]) {
                //var_dump($lastYearData);
                $foundRecords = true;
                $totalBookValueStart += $lastYearData["newNetBookValue"];
                $totalDepreciationExpense += $lastYearData["newDepreciationExpense"];
                $totalAccumulatedDepreciation += $lastYearData["newAccumulatedDepreciation"];
                $totalBookValueEnd += $lastYearData["newClosingCarryingValue"];
                $totalAdditions += $additions;

                array_push($lastYearDataArray, $lastYearData);

                echo "<tr>";
                echo "<td>$counter</td>";
                echo "<td>$assetName</td>";
                echo "<td>$location</td>";
                echo "<td>$serial</td>";
                echo "<td>$date</td>";
                echo "<td>GH₵" . number_format($additions, 2) . "</td>";
                echo "<td>$yearOfReport</td>";
                echo "<td>GH₵" . number_format($lastYearData['newNetBookValue'], 2) . "</td>";
                echo "<td>" . ($depreciation_rate * 100) . "%</td>";
                echo "<td>GH₵" . number_format($lastYearData['newDepreciationExpense'], 2) . "</td>";
                echo "<td>GH₵" . number_format($lastYearData['newAccumulatedDepreciation'], 2) . "</td>";
                echo "<td>GH₵" . number_format($lastYearData['newClosingCarryingValue'], 2) . "</td>";
                echo "<td>$rate</td>";
                $months = $lastYearData["months"];
                for ($month=1; $month <= $monthsInYear ; $month++) { 
                    if ($months > 0) {
                        $formatedMonthlyDeprciation = number_format($lastYearData["newMonthlyDepreciation"], 2);
                        echo "<td>GH₵" . $formatedMonthlyDeprciation . "</td>";
                        $monthlyTotals[$month] += $lastYearData["newMonthlyDepreciation"];
                    } else {
                        echo "<td>-</td>";
                    }
                    $months--;
                }
            }
            else if ($yearOfReport > $yearsBetweenData["newAcquisitionYear"] && $yearOfReport < $estimatedLifeInYears) {
                //var_dump($yearsBetweenData);
                $foundRecords = true;
                $totalBookValueStart += $yearsBetweenData["newNetBookValue"];
                $totalDepreciationExpense += $yearsBetweenData["newDepreciationExpense"];
                $totalAccumulatedDepreciation += $yearsBetweenData["newAccumulatedDepreciation"];
                $totalBookValueEnd += $yearsBetweenData["newClosingCarryingValue"];
                $totalAdditions += $additions;

                array_push($yearsBetweenDataArray, $yearsBetweenData);

                echo "<tr>";
                echo "<td>$counter</td>";
                echo "<td>$assetName</td>";
                echo "<td>$location</td>";
                echo "<td>$serial</td>";
                echo "<td>$date</td>";
                echo "<td>GH₵" . number_format($additions, 2) . "</td>";
                echo "<td>$yearOfReport</td>";
                echo "<td>GH₵" . number_format($yearsBetweenData['newNetBookValue'], 2) . "</td>";
                echo "<td>" . ($depreciation_rate * 100) . "%</td>";
                echo "<td>GH₵" . number_format($yearsBetweenData['newDepreciationExpense'], 2) . "</td>";
                echo "<td>GH₵" . number_format($yearsBetweenData['newAccumulatedDepreciation'], 2) . "</td>";
                echo "<td>GH₵" . number_format($yearsBetweenData['newClosingCarryingValue'], 2) . "</td>";
                echo "<td>$rate</td>";
                $emptyMonth = $monthsInYear - $yearsBetweenData["months"];
                for ($month=1; $month <= $monthsInYear ; $month++) { 
                    if ($emptyMonth >= 1) {
                        echo "<td>-</td>";
                    } else {
                        $formatedMonthlyDeprciation = number_format($yearsBetweenData["newMonthlyDepreciation"], 2);
                        echo "<td>GH₵" . $formatedMonthlyDeprciation . "</td>";
                        $monthlyTotals[$month] += $yearsBetweenData["newMonthlyDepreciation"];
                    }
                    $emptyMonth--;
                }
            }

            $counter++;
        }
            
        array_push($assetTotalsArray, array(
                "totalBookValueStart" => $totalBookValueStart,
                "totalDepreciationExpense" => $totalDepreciationExpense,
                "totalAccumulatedDepreciation" => $totalAccumulatedDepreciation, 
                "totalBookValueEnd" => $totalBookValueEnd,
                "totalAdditions" => $totalAdditions,
                "totalDisposals" => $totalDisposals,
        ));

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

            echo "<tr><td colspan='13'><strong>Total Monthly Depreciation</strong></td>";

            for ($month = 1; $month <= 12; $month++) {
                $total = isset($monthlyTotals[$month]) ? $monthlyTotals[$month] : 0;
                echo "<td><strong>GH₵" . number_format($total, 2) . "</strong></td>";
            }
            
            echo "</tr>";
            
            echo "</tfoot>";
        }
        

        echo "</tbody>";
        echo "</table>";
        // echo "<pre>";
        // print_r($monthlyTotals);
        // echo "</pre>";
        echo "</div>";  
        echo "</div>";

        // Optionally, you can return the assetData array if needed elsewhere in your script
        //return $assetData;
        $payload = array();
        $payload['firstYearDataArray'] = $firstYearDataArray;
        $payload['lastYearDataArray'] = $lastYearDataArray;
        $payload['yearsBetweenDataArray'] = $yearsBetweenDataArray;
        $payload['assetTotalsArray'] = $assetTotalsArray;

        return $payload;
    }

    function displayData (string $assetClass, $yearOfReport, array $assetData) {

        echo "<div class='container my-4 '>";
        echo "<h2 class='text-primary mb-4'>Depreciation Schedule for $assetClass in $yearOfReport (Cedi Report) </h2>";
        echo "<div class='table w-90'>";
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
            <th>Closing Carrying Value(GH₵)</th>
            <th>Dollar Rate Used</th>";
        
           
        for ($month = 1; $month <= 12; $month++) {
            echo "<th>" . date('F', mktime(0, 0, 0, $month, 1)) . "</th>";
        }
        
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $counter = 1;

        if (!$assetData) {
            echo "<tr><td colspan='14' class='text-center text-danger'>No records found for the selected asset class and year.</td></tr>";
        } else {
            foreach($assetData as $asset) {
                if ($yearOfReport == $asset["firstYearData"]["newAcquisitionYear"]) {
                    //var_dump($asset);
                    $foundRecords = true;
                    $totalBookValueStart += $asset["firstYearData"]["newNetBookValue"];
                    $totalDepreciationExpense += $asset["firstYearData"]["newDepreciationExpense"];
                    $totalAccumulatedDepreciation += $asset["firstYearData"]["newAccumulatedDepreciation"];
                    $totalBookValueEnd += $asset["firstYearData"]["newClosingCarryingValue"];
                    $totalAdditions += $additions;
    
                    echo "<tr>";
                    echo "<td>$counter</td>";
                    echo "<td>$assetName</td>";
                    echo "<td>$location</td>";
                    echo "<td>$serial</td>";
                    echo "<td>$date</td>";
                    echo "<td>GH₵" . number_format($additions, 2) . "</td>";
                    echo "<td>$yearOfReport</td>";
                    echo "<td>GH₵" . number_format($asset["firstYearData"]['newNetBookValue'], 2) . "</td>";
                    echo "<td>" . ($depreciation_rate * 100) . "%</td>";
                    echo "<td>GH₵" . number_format($asset["firstYearData"]['newDepreciationExpense'], 2) . "</td>";
                    echo "<td>GH₵" . number_format($asset["firstYearData"]['newAccumulatedDepreciation'], 2) . "</td>";
                    echo "<td>GH₵" . number_format($asset["firstYearData"]['newClosingCarryingValue'], 2) . "</td>";
                    echo "<td>$rate</td>";
                    $emptyMonth = $monthsInYear - $asset["firstYearData"]["months"];
                    for ($month=1; $month <= $monthsInYear ; $month++) { 
                        if ($emptyMonth >= 1) {
                            echo "<td>-</td>";
                        } else {
                            $formatedMonthlyDeprciation = number_format($asset["firstYearData"]["newMonthlyDepreciation"], 2);
                            echo "<td>GH₵" . $formatedMonthlyDeprciation . "</td>";
                            $monthlyTotals[$month] += $asset["firstYearData"]["newMonthlyDepreciation"];
                        }
                        $emptyMonth--;
                    }
                }
                else if ($yearOfReport == $asset["lastYearData"]["newLastYear"]) {
                    //var_dump($asset);
                    $foundRecords = true;
                    $totalBookValueStart += $asset["lastYearData"]["newNetBookValue"];
                    $totalDepreciationExpense += $asset["lastYearData"]["newDepreciationExpense"];
                    $totalAccumulatedDepreciation += $asset["lastYearData"]["newAccumulatedDepreciation"];
                    $totalBookValueEnd += $asset["lastYearData"]["newClosingCarryingValue"];
                    $totalAdditions += $additions;
                    echo "<tr>";
                    echo "<td>$counter</td>";
                    echo "<td>$assetName</td>";
                    echo "<td>$location</td>";
                    echo "<td>$serial</td>";
                    echo "<td>$date</td>";
                    echo "<td>GH₵" . number_format($additions, 2) . "</td>";
                    echo "<td>$yearOfReport</td>";
                    echo "<td>GH₵" . number_format($asset["lastYearData"]['newNetBookValue'], 2) . "</td>";
                    echo "<td>" . ($depreciation_rate * 100) . "%</td>";
                    echo "<td>GH₵" . number_format($asset["lastYearData"]['newDepreciationExpense'], 2) . "</td>";
                    echo "<td>GH₵" . number_format($asset["lastYearData"]['newAccumulatedDepreciation'], 2) . "</td>";
                    echo "<td>GH₵" . number_format($asset["lastYearData"]['newClosingCarryingValue'], 2) . "</td>";
                    echo "<td>$rate</td>";
                    $months = $asset["lastYearData"]["months"];
                    for ($month=1; $month <= $monthsInYear ; $month++) { 
                        if ($months > 0) {
                            $formatedMonthlyDeprciation = number_format($asset["lastYearData"]["newMonthlyDepreciation"], 2);
                            echo "<td>GH₵" . $formatedMonthlyDeprciation . "</td>";
                            $monthlyTotals[$month] += $asset["lastYearData"]["newMonthlyDepreciation"];
                        } else {
                            echo "<td>-</td>";
                        }
                        $months--;
                    }
                }
                else if ($yearOfReport > $asset["yearsBetweenData"]["newAcquisitionYear"] && $yearOfReport < $estimatedLifeInYears) {
                    //var_dump($asset);
                    $foundRecords = true;
                    $totalBookValueStart += $asset["yearsBetweenData"]["newNetBookValue"];
                    $totalDepreciationExpense += $asset["yearsBetweenData"]["newDepreciationExpense"];
                    $totalAccumulatedDepreciation += $asset["yearsBetweenData"]["newAccumulatedDepreciation"];
                    $totalBookValueEnd += $asset["yearsBetweenData"]["newClosingCarryingValue"];
                    $totalAdditions += $additions;
                    echo "<tr>";
                    echo "<td>$counter</td>";
                    echo "<td>$assetName</td>";
                    echo "<td>$location</td>";
                    echo "<td>$serial</td>";
                    echo "<td>$date</td>";
                    echo "<td>GH₵" . number_format($additions, 2) . "</td>";
                    echo "<td>$yearOfReport</td>";
                    echo "<td>GH₵" . number_format($asset["yearsBetweenData"]['newNetBookValue'], 2) . "</td>";
                    echo "<td>" . ($depreciation_rate * 100) . "%</td>";
                    echo "<td>GH₵" . number_format($asset["yearsBetweenData"]['newDepreciationExpense'], 2) . "</td>";
                    echo "<td>GH₵" . number_format($asset["yearsBetweenData"]['newAccumulatedDepreciation'], 2) . "</td>";
                    echo "<td>GH₵" . number_format($asset["yearsBetweenData"]['newClosingCarryingValue'], 2) . "</td>";
                    echo "<td>$rate</td>";
                    $emptyMonth = $monthsInYear - $asset["yearsBetweenData"]["months"];
                    for ($month=1; $month <= $monthsInYear ; $month++) { 
                        if ($emptyMonth >= 1) {
                            echo "<td>-</td>";
                        } else {
                            $formatedMonthlyDeprciation = number_format($asset["yearsBetweenData"]["newMonthlyDepreciation"], 2);
                            echo "<td>GH₵" . $formatedMonthlyDeprciation . "</td>";
                            $monthlyTotals[$month] += $asset["yearsBetweenData"]["newMonthlyDepreciation"];
                        }
                        $emptyMonth--;
                    }
                }    
            }
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

            echo "<tr><td colspan='13'><strong>Total Monthly Depreciation</strong></td>";

            for ($month = 1; $month <= 12; $month++) {
                $total = isset($monthlyTotals[$month]) ? $monthlyTotals[$month] : 0;
                echo "<td><strong>GH₵" . number_format($total, 2) . "</strong></td>";
            }
            
            echo "</tr>";
            
            echo "</tfoot>";
        }
        

        echo "</tbody>";
        echo "</table>";
        // echo "<pre>";
        // print_r($monthlyTotals);
        // echo "</pre>";
        echo "</div>";  
        echo "</div>";
    }


    function assetMonthlyBreakdown()
    {
        
    }