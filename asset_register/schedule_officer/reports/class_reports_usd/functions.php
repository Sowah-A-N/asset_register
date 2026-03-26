<?php

include_once "./datacon.php";
global $conn;

include_once "src/DatabaseMethods.php";

use Src\System\DatabaseMethods;

function dd($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die();
}

function getUntrackedAssetsByYear($yearOfReport, $assetClass)
{
    $openingBalance = 0;
    $totalDisposals = 0;
    $disposalDepreciation = 0;
    $monthlyDepreciations = [];

    $dm = new DatabaseMethods("asset_register_new", "assets");

    // Get asset class information
    $sql1 = "SELECT * FROM `asset_classes` WHERE `asset_class` = :a";
    $params1 = [":a" => $assetClass];
    $assetClassData = $dm->getData($sql1, $params1)[0];

    // Get asset opening balance if set for year of report
    $sql2 = "SELECT * FROM `asset_class_opbal_year` WHERE `asset_class` = :a AND `year` = :y";
    $params2 = [":a" => $assetClass, ":y" => $yearOfReport];
    $currentYearData = $dm->getData($sql2, $params2);
    $currentYearData = $currentYearData ? $currentYearData[0] : null;
    
    // $currentDateTime = (int) (new DateTime())->format('Y');
    // $currentYear = (int) $yearOfReport;

    if ($currentYearData) {
        // Use the existing current year data
        $openingBalance = $currentYearData['open_bal_usd'];
        $expectedLifeMonths = $currentYearData['expected_life_months'];
        $baseMonthlyDepreciation = $openingBalance / $expectedLifeMonths;

        // Initialize monthly depreciation array with base value
        $monthlyDepreciationByMonth = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyDepreciationByMonth[$month] = $baseMonthlyDepreciation;
        }

        // Get Total disposals of year of report
        $sql3 = "SELECT * FROM `untracked_asset_disposals` WHERE `class` = :a AND YEAR(`date_of_disposal`) = :y ORDER BY `date_of_disposal` ASC;";
        $params3 = [":a" => $assetClass, ":y" => $yearOfReport];
        $untrackedDisposedAssets = $dm->getData($sql3, $params3);

        $totalDeprThisYear = 0;
        $disposalDetails = [];

        if (!empty($untrackedDisposedAssets)) {
            foreach ($untrackedDisposedAssets as $asset) {
                $assetValue = $asset['value'];
                $totalDisposals += $assetValue;

                $disposalDate = new DateTime($asset['date_of_disposal']);
                $disposalMonth = (int)$disposalDate->format('m');
                $disposalYear = (int)$disposalDate->format('Y');

                // Calculate how many months this asset has been depreciated
                $acquisitionDate = new DateTime($asset['acquisition_date']);
                $monthsSinceAcquisition = 0;

                // Calculate months from acquisition to disposal
                // Add 1 because we count the disposal month
                $monthsDiff = (($disposalYear - $acquisitionDate->format('Y')) * 12) +
                    ($disposalMonth - $acquisitionDate->format('m')) + 1;

                // Calculate this asset's monthly depreciation
                $assetMonthlyDepr = $assetValue / $expectedLifeMonths;

                // Calculate disposal depreciation for this asset
                $assetDisposalDepr = $assetMonthlyDepr * $monthsDiff;

                // After disposal month, remove this asset's depreciation from remaining months
                for ($month = $disposalMonth + 1; $month <= 12; $month++) {
                    $monthlyDepreciationByMonth[$month] -= $assetMonthlyDepr;
                }

                $disposalDetails[] = [
                    'asset' => $asset['name'],
                    'value' => $assetValue,
                    'disposal_month' => $disposalMonth,
                    'months_since_acquisition' => $monthsDiff,
                    'monthly_depreciation' => $assetMonthlyDepr,
                    'disposal_depreciation' => $assetDisposalDepr
                ];

                $disposalDepreciation += $assetDisposalDepr;
            }
        }
        $totalDeprYearCharge = 0;

        // Convert the monthly depreciation array to the expected format
        foreach ($monthlyDepreciationByMonth as $month => $value) {
            $monthlyDepreciations[] = [
                'month' => $month,
                'value' => round($value, 2)
            ];
            $totalDeprYearCharge += $value;
        }

        // Calculate total depreciation for the year
        //$totalDeprYearCharge += array_sum(array_column($monthlyDepreciationByMonth, 'value'));

        // Update the total depreciation year charge
        $sql4 = "UPDATE `asset_class_opbal_year` SET 
                `total_depr_year_charge_usd` = :tdyc, 
                `disposals_depr_usd` = :dd 
                WHERE `asset_class` = :a AND `year` = :y";

        $params4 = [
            ":tdyc" => $totalDeprYearCharge,
            ":dd" => $disposalDepreciation,
            ":a" => $assetClass,
            ":y" => $yearOfReport
        ];

        $dm->inputData($sql4, $params4);
    } else {
        // Create new current year data based on previous year
        $previousYear = (int)$yearOfReport - 1;
        $sql2 = "SELECT * FROM `asset_class_opbal_year` WHERE `asset_class` = :a AND `year` = :y";
        $params2 = [":a" => $assetClass, ":y" => $previousYear];
        $previousYearData = $dm->getData($sql2, $params2);
        $previousYearData = $previousYearData ? $previousYearData[0] : null;

        if ($previousYearData) {
            // Get disposals for previous year
            $totalPrevDisposals = 0;
            $sql3 = "SELECT * FROM `untracked_asset_disposals` WHERE `class` = :a AND YEAR(`date_of_disposal`) = :y;";
            $params3 = [":a" => $assetClass, ":y" => $previousYear];
            $prevDisposedAssets = $dm->getData($sql3, $params3);

            if ($prevDisposedAssets) {
                foreach ($prevDisposedAssets as $prevDisposedAsset) {
                    $totalPrevDisposals += $prevDisposedAsset['value'];
                }
            }

            $openingBalance = $previousYearData['open_bal_usd'] - $totalPrevDisposals;
            $totalAccumulatedDepreciationStart = $previousYearData['total_accum_end_usd'];
            $expectedLifeMonths = $assetClassData['estimated_life_months'];
            $baseMonthlyDepreciation = $openingBalance / $expectedLifeMonths;
            $totalDeprYearCharge = $baseMonthlyDepreciation * 12; // Initial estimate

            // Initialize with base value
            $monthlyDepreciationByMonth = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthlyDepreciationByMonth[$month] = $baseMonthlyDepreciation;
            }

            // Insert new year record
            $sql4 = "INSERT INTO `asset_class_opbal_year` 
                    (`asset_class`, `open_bal_usd`, `total_accum_start_usd`, `total_depr_year_charge_usd`, `disposals_depr_usd`, `year`, `expected_life_months`, `rate`) 
                    VALUES(:ac, :ob, :tads, :tdyc, :dd, :y, :el, :r)";

            $params4 = [
                ":ac" => $assetClass,
                ":ob" => $openingBalance,
                ":tads" => $totalAccumulatedDepreciationStart,
                ":tdyc" => $totalDeprYearCharge,
                ":dd" => 0, // No disposals calculated yet
                ":y" => $yearOfReport,
                ":el" => $expectedLifeMonths,
                ":r" => $previousYearData['rate']
            ];

            $result = $dm->inputData($sql4, $params4);

            if ($result) {
                // Now process disposals for the current year
                $sql3 = "SELECT * FROM `untracked_asset_disposals` WHERE `class` = :a AND YEAR(`date_of_disposal`) = :y ORDER BY `date_of_disposal` ASC;";
                $params3 = [":a" => $assetClass, ":y" => $yearOfReport];
                $untrackedDisposedAssets = $dm->getData($sql3, $params3);

                $disposalDetails = [];

                if (!empty($untrackedDisposedAssets)) {
                    foreach ($untrackedDisposedAssets as $asset) {
                        $assetValue = $asset['value'];
                        $totalDisposals += $assetValue;

                        $disposalDate = new DateTime($asset['date_of_disposal']);
                        $disposalMonth = (int)$disposalDate->format('m');
                        $disposalYear = (int)$disposalDate->format('Y');

                        // Calculate how many months this asset has been depreciated
                        $acquisitionDate = new DateTime($asset['acquisition_date']);

                        // Calculate months from acquisition to disposal
                        // Add 1 because we count the disposal month
                        $monthsDiff = (($disposalYear - $acquisitionDate->format('Y')) * 12) +
                            ($disposalMonth - $acquisitionDate->format('m')) + 1;

                        // Calculate this asset's monthly depreciation
                        $assetMonthlyDepr = $assetValue / $expectedLifeMonths;

                        // Calculate disposal depreciation for this asset
                        $assetDisposalDepr = $assetMonthlyDepr * $monthsDiff;

                        // After disposal month, remove this asset's depreciation from remaining months
                        for ($month = $disposalMonth + 1; $month <= 12; $month++) {
                            $monthlyDepreciationByMonth[$month] -= $assetMonthlyDepr;
                        }

                        $disposalDetails[] = [
                            'asset' => $asset['name'],
                            'value' => $assetValue,
                            'disposal_month' => $disposalMonth,
                            'months_since_acquisition' => $monthsDiff,
                            'monthly_depreciation' => $assetMonthlyDepr,
                            'disposal_depreciation' => $assetDisposalDepr
                        ];

                        $disposalDepreciation += $assetDisposalDepr;
                    }

                    $totalDeprYearCharge = 0;

                    // Convert monthly depreciation array to the expected format
                    foreach ($monthlyDepreciationByMonth as $month => $value) {
                        $monthlyDepreciations[] = [
                            'month' => $month,
                            'value' => round($value, 2)
                        ];

                        // Calculate new total depreciation for the year
                        $totalDeprYearCharge += $value;
                    }

                    // Update the record with disposal info
                    $sql5 = "UPDATE `asset_class_opbal_year` SET 
                            `total_depr_year_charge_usd` = :tdyc, 
                            `disposals_depr_usd` = :dd 
                            WHERE `asset_class` = :a AND `year` = :y";

                    $params5 = [
                        ":tdyc" => $totalDeprYearCharge,
                        ":dd" => $disposalDepreciation,
                        ":a" => $assetClass,
                        ":y" => $yearOfReport
                    ];

                    $dm->inputData($sql5, $params5);
                }
            }
        }
    }

    // Re-query to get the latest data
    $sql6 = "SELECT * FROM `asset_class_opbal_year` WHERE `asset_class` = :a AND `year` = :y";
    $params6 = [":a" => $assetClass, ":y" => $yearOfReport];
    $updatedCurrentYearData = $dm->getData($sql6, $params6);
    $updatedCurrentYearData = $updatedCurrentYearData ? $updatedCurrentYearData[0] : [];

    return [
        "asset_class" => $assetClass,
        "year" => $yearOfReport,
        "opening_balance" => $openingBalance,
        "total_disposals" => $totalDisposals,
        "disposal_depreciation" => $disposalDepreciation,
        "asset_class_info" => $updatedCurrentYearData,
        "monthly_depreciations" => $monthlyDepreciations,
        "disposal_details" => $disposalDetails ?? []
    ];
}



function displayClassInfo($yearOfReport, $assetClass, array $assetClassInfo)
{
    echo "<div class='container my-4 '>";
    echo "<h2 class='text-primary mb-4'>Depreciation Schedule for $assetClass in $yearOfReport (Dollar Report) </h2>";
    echo "<div class='table w-90'>";
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='table-primary'>";
    echo "<tr>
            <th></th>
            <th style='width:250px'></th>
            <th style='width:200px'>DATE</th>
            <th>COST (USD)</th>
            <th>FXRATE</th>
            <th>ESTIMATED LIFE (MONTH)</th>
            <th>ACCUMULATED DEPRECIATION AS AT JAN 1ST $yearOfReport</th>
            ";
    for ($month = 1; $month <= 12; $month++) {
        echo "<th>" . strtoupper(date('F', mktime(0, 0, 0, $month, 1))) . " DEPRECIATION</th>";
    }
    echo "
            <th>TOTAL DEPRECIATION/ CHARGE FOR THE YEAR</th>
            <th>TOTAL ACCUMULATED DEPRECIATION $yearOfReport</th>
            <th>DISPOSALS DEPRECIATION</th>
            <th>NET BOOK VALUE</th>
            </tr>";
    echo "</thead>";
    echo "<tbody>";

    echo "<tr>";
    echo "<td></td>";
    echo "<td>OPENING BALANCE</td>";
    echo "<td>" . "1st Jan " . $yearOfReport . "</td>";
    echo "<td>" . number_format($assetClassInfo['asset_class_info']['open_bal_usd'], 2) . "</td>";
    echo "<td>" . number_format($assetClassInfo['asset_class_info']['rate'], 2) . "</td>";
    echo "<td>" . ($assetClassInfo['asset_class_info']['expected_life_months']) . "</td>";
    if ($assetClassInfo['asset_class_info']['depreciated']) {
        echo "<td>" . number_format($assetClassInfo['asset_class_info']['total_accum_start_usd'], 2) . "</td>";
        foreach ($assetClassInfo['monthly_depreciations'] as $monthData) {
            $value = $monthData['value'];
            echo '<td>' . number_format($value, 2) . '</td>';
        }
        echo "<td>" . number_format($assetClassInfo['asset_class_info']['total_depr_year_charge_usd'], 2) . "</td>";
        echo "<td>" . number_format($assetClassInfo['asset_class_info']['total_accum_end_usd'], 2) . "</td>";
        echo "<td>" . number_format($assetClassInfo['asset_class_info']['disposals_depr_usd'], 2) . "</td>";
        $netbookValue = $assetClassInfo['asset_class_info']['net_book_value_usd'] < 0 ? 0 : $assetClassInfo['asset_class_info']['net_book_value_usd'];
        $_SESSION["assetClassData"]["asset_class_info"]["net_book_value"] = $netbookValue;
        $_SESSION["assetClassData"]["asset_class_info"]["net_book_value_usd"] = $netbookValue;
        echo "<td>" . number_format($netbookValue, 2) . "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td></td>";
        echo "<td>DISPOSALS</td>";
        echo "<td>" . "31st Dec " . $yearOfReport . "</td>";
        echo "<td>" . number_format($assetClassInfo['total_disposals'], 2) . "</td>";
        echo "<td colspan='19'></td>";
        echo "</tr>";
    } else {
        $_SESSION["assetClassData"]["asset_class_info"]["asset_class"] = $_SESSION["assetClassData"]["asset_class"];
        $_SESSION["assetClassData"]["asset_class_info"]["opening_balance"] = $_SESSION["assetClassData"]["opening_balance"];
        $_SESSION["assetClassData"]["asset_class_info"]["total_accum_depr_start"] = 0;
        $_SESSION["assetClassData"]["asset_class_info"]["total_depr_year_charge"] = 0;
        $_SESSION["assetClassData"]["asset_class_info"]["total_accum_depr_end"] = 0;
        $_SESSION["assetClassData"]["asset_class_info"]["disposals_depr"] = 0;
        $_SESSION["assetClassData"]["asset_class_info"]["net_book_value"] = 0 ;
        $_SESSION["assetClassData"]["asset_class_info"]["year"] = $_SESSION["assetClassData"]["year"];
        $_SESSION["assetClassData"]["asset_class_info"]["expected_life_months"] = 0;
        $_SESSION["assetClassData"]["asset_class_info"]["rate"] = 0;

    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</div>";
}



function calculateAccumulatedDepreciation($purchaseYear, $purchaseMonth, $currentYear, $monthlyDepreciation)
{
    if ($currentYear < $purchaseYear) {
        return 0; // No depreciation before purchase year
    }

    $yearsElapsed = $currentYear - $purchaseYear; // Full years passed
    $monthsInFirstYear = 12 - $purchaseMonth + 1; // Months of use in the first year

    // Accumulated depreciation formula
    $accumulatedDepreciation = ($yearsElapsed * (12 * $monthlyDepreciation)) + ($monthsInFirstYear * $monthlyDepreciation);

    return $accumulatedDepreciation;
}

function calculateNetBookValue($purchaseYear, $purchaseMonth, $currentYear, $purchaseCost, $monthlyDepreciation)
{
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

function calculationsByClass($assetClass, $yearOfReport)
{
    global $conn;

    // Fetch assets from the database
    $sqlAssets = "SELECT * FROM assets WHERE asset_class = '$assetClass' ORDER BY acquisition_date ASC";
    $resultAssets = mysqli_query($conn, $sqlAssets);

    if (!$resultAssets) {
        die("Error in SQL query (Assets): " . mysqli_error($conn));
    }

    // Fetch asset class details
    $sqlAssetClasses = "SELECT opening_bal, opbal_plus_additions, estimated_life, dep_rate, depreciated FROM asset_classes WHERE asset_class = '$assetClass'";
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

    $assetClassOpeningBalance = $rowAssetClasses["opening_bal"];
    $previousYearsDisposals = 0;
    $previousYearDisposals = 0;
    $previousDisposals = 0;
    $previousOpeningBalances = 0;

    $counter = 1;
    $foundRecords = false;  // Flag to check if there are any records

    // Initialize an array to store monthly totals
    $monthlyTotals = array_fill(1, 12, 0); // Initialize all months with zero

    // Arrays
    $payload["assetsData"] = array();
    $payload['assetsTotals'] = array();

    // Accumulate totals
    $totalBookValueStart = 0;
    $totalDepreciationExpense = 0;
    $totalAccumulatedDepreciationStart = 0;
    $totalAccumulatedDepreciation = 0;
    $totalBookValueEnd = 0;
    $totalAdditions = 0;
    $totalDisposals = 0;

    $deprciated_class = (int) $rowAssetClasses["depreciated"];

    while ($rowAsset = mysqli_fetch_assoc($resultAssets)) {
        //var_dump($rowAsset);
        // // Calculate acquisition year
        // $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
        $disposed = false;
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

        if (!$deprciated_class) {

            // Adjust for months remaining in the first year
            $monthsInYear = 12;
            $monthsRemainingFirstYear = $monthsInYear - $acquisitionMonth + 1;
            $estimatedLifeinMonths = $numYears * 12;
            $monthsRemainingAfterFirstYear = $estimatedLifeinMonths - $monthsRemainingFirstYear;
            $monthsRemainingLastYear = 0;

            $newMonthlyDepreciation = $additions / $estimatedLifeinMonths;
            $lastYear = $acquisitionYear + $numYears;

            if ($acquisitionMonth != 1) {
                $monthsRemainingLastYear = $monthsRemainingAfterFirstYear % 12;
                $lastYear = $acquisitionYear + $numYears;
            }

            $monthsBetweenFirstAndLastYear = $monthsRemainingAfterFirstYear - $monthsRemainingLastYear;
            $exactLastYear = $acquisitionYear + $life;
            $estimatedLifeInYears = $exactLastYear + ($monthsRemainingLastYear ? 1 : 0);
            
            $firstYearData = array(
                "type" => "non",
                "newAssetName" => $assetName,
                "newAssetLocation" => $location,
                "newAssetSerial" => $serial,
                "newAssetDate" => $date,
                "newAssetAdditions" => $additions,
                "newAssetDepreciationRate" => $depreciation_rate * 100,
                "newAssetDollarRate" => $rate,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear,
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear,
                "monthsRemainingLastYear" => $monthsRemainingLastYear,
                "newEstimatedLifeInYears" => $estimatedLifeInYears,
                "newAcquisitionMonth" => $acquisitionMonth,
                "newAcquisitionYear" => $acquisitionYear,
                "newLastYear" => $lastYear,
                "months" => $monthsRemainingFirstYear,
            );
            
            $yearsBetweenData = array(
                "type" => "non",
                "newAssetName" => $assetName,
                "newAssetLocation" => $location,
                "newAssetSerial" => $serial,
                "newAssetDate" => $date,
                "newAssetAdditions" => $additions,
                "newAssetDepreciationRate" => $depreciation_rate * 100,
                "newAssetDollarRate" => $rate,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear,
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear,
                "monthsRemainingLastYear" => $monthsRemainingLastYear,
                "newEstimatedLifeInYears" => $estimatedLifeInYears,
                "newAcquisitionMonth" => $acquisitionMonth,
                "newAcquisitionYear" => $acquisitionYear,
                "newLastYear" => $lastYear,
                "months" => $monthsRemainingFirstYear,
            );
            
            $lastYearData = array(
                "type" => "non",
                "newAssetName" => $assetName,
                "newAssetLocation" => $location,
                "newAssetSerial" => $serial,
                "newAssetDate" => $date,
                "newAssetAdditions" => $additions,
                "newAssetDepreciationRate" => $depreciation_rate * 100,
                "newAssetDollarRate" => $rate,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear,
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear,
                "monthsRemainingLastYear" => $monthsRemainingLastYear,
                "newEstimatedLifeInYears" => $estimatedLifeInYears,
                "newAcquisitionMonth" => $acquisitionMonth,
                "newAcquisitionYear" => $acquisitionYear,
                "newLastYear" => $lastYear,
                "months" => $monthsRemainingFirstYear,
            );

            if ($yearOfReport == $firstYearData["newAcquisitionYear"]) {
                if ($disposed) $firstYearData["disposed"] = true;
                else $firstYearData["disposed"] = false;
                //var_dump($firstYearData);
                $foundRecords = true;
                $totalBookValueStart += 0;
                $totalDepreciationExpense += 0;
                $totalAccumulatedDepreciationStart += 0;
                $totalAccumulatedDepreciation += 0;
                $totalBookValueEnd += 0;
                $totalAdditions += $additions;

                $emptyMonth = $monthsInYear - $firstYearData["months"];
               

                array_push($payload["assetsData"], $firstYearData);
            } else if ($yearOfReport == $lastYearData["newLastYear"]) {
                if ($disposed) $lastYearData["disposed"] = true;
                else $lastYearData["disposed"] = false;
                //var_dump($lastYearData);
                $foundRecords = true;
                $totalBookValueStart += 0;
                $totalDepreciationExpense += 0;
                $totalAccumulatedDepreciationStart += 0;
                $totalAccumulatedDepreciation += 0;
                $totalBookValueEnd += 0;
                $totalAdditions += $additions;

                $months = $lastYearData["months"];
                
                array_push($payload["assetsData"], $lastYearData);
            } else if ($yearOfReport > $yearsBetweenData["newAcquisitionYear"] && $yearOfReport < $estimatedLifeInYears) {
                if ($disposed) $yearsBetweenData["disposed"] = true;
                else $yearsBetweenData["disposed"] = false;
                //var_dump($yearsBetweenData);
                $foundRecords = true;
                $totalBookValueStart += 0;
                $totalDepreciationExpense += 0;
                $totalAccumulatedDepreciationStart += 0;
                $totalAccumulatedDepreciation += 0;
                $totalBookValueEnd += 0;
                $totalAdditions += $additions;

                $emptyMonth = $monthsInYear - $yearsBetweenData["months"];
                
                array_push($payload["assetsData"], $yearsBetweenData);
            }
        } else {

            // Adjust for months remaining in the first year
            $monthsInYear = 12;
            $monthsRemainingFirstYear = $monthsInYear - $acquisitionMonth + 1;
            $estimatedLifeinMonths = $numYears * 12;
            $monthsRemainingAfterFirstYear = $estimatedLifeinMonths - $monthsRemainingFirstYear;
            $monthsRemainingLastYear = 0;

            $newMonthlyDepreciation = $additions / $estimatedLifeinMonths;
            $lastYear = $acquisitionYear + $numYears;

            if ($acquisitionMonth != 1) {
                $monthsRemainingLastYear = $monthsRemainingAfterFirstYear % 12;
                $lastYear = $acquisitionYear + $numYears;
            }

            $monthsBetweenFirstAndLastYear = $monthsRemainingAfterFirstYear - $monthsRemainingLastYear;
            $exactLastYear = $acquisitionYear + $life;
            $estimatedLifeInYears = $exactLastYear + ($monthsRemainingLastYear ? 1 : 0);

            if ($rowAsset["disposals"]) {
                $disposedAssetsQuery = "SELECT date_of_disposal FROM disposals WHERE serial_number = '{$rowAsset['serial_number']}' AND date_of_disposal IS NOT NULL";
                $disposedAssetsResult = mysqli_query($conn, $disposedAssetsQuery);

                $disposedAssetsRow = mysqli_fetch_assoc($disposedAssetsResult);
                $monthOfDisposal = date('m', strtotime($disposedAssetsRow['date_of_disposal']));
                $yearOfDisposal = date('Y', strtotime($disposedAssetsRow['date_of_disposal']));

                if ($yearOfDisposal < $yearOfReport) {
                    $previousDisposals += $rowAsset['additions'];
                    continue;
                } else if ($yearOfDisposal == $yearOfReport) {
                    $disposed = true;
                    $lastYear = $yearOfReport;
                    $monthsRemainingLastYear = $monthOfDisposal;
                    $totalDisposals += $rowAsset['additions'];
                }
            }

            $firstYearData = array(
                "type" => "first",
                "newAssetName" => $assetName,
                "newAssetLocation" => $location,
                "newAssetSerial" => $serial,
                "newAssetDate" => $date,
                "newAssetAdditions" => $additions /$rate,
                "newAssetDepreciationRate" => $depreciation_rate * 100,
                "newAssetDollarRate" => $rate,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear,
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear,
                "monthsRemainingLastYear" => $monthsRemainingLastYear,
                "newEstimatedLifeInYears" => $estimatedLifeInYears,
                "newAcquisitionMonth" => $acquisitionMonth,
                "newAcquisitionYear" => $acquisitionYear,
                "newLastYear" => $lastYear,
                "months" => $monthsRemainingFirstYear,
                "newMonthlyDepreciation" => $newMonthlyDepreciation/$rate,
                "newDepreciationExpense" => ($newMonthlyDepreciation * $monthsRemainingFirstYear)/$rate,
            );
            $firstYearData["newAccumulatedDepreciationStart"] = 0;
            $firstYearData["newAccumulatedDepreciation"] = ($firstYearData["newDepreciationExpense"]);
            $firstYearData["newClosingCarryingValue"] = ($additions/$rate - $firstYearData["newAccumulatedDepreciation"]);
            $firstYearData["newNetBookValue"] = calculateNetBookValue($acquisitionYear, $acquisitionMonth, $yearOfReport, $additions, $newMonthlyDepreciation)/$rate;

            $yearsBetweenData = array(
                "type" => "between",
                "newAssetName" => $assetName,
                "newAssetLocation" => $location,
                "newAssetSerial" => $serial,
                "newAssetDate" => $date,
                "newAssetAdditions" => $additions/$rate,
                "newAssetDepreciationRate" => $depreciation_rate * 100,
                "newAssetDollarRate" => $rate,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear,
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear,
                "monthsRemainingLastYear" => $monthsRemainingLastYear,
                "newEstimatedLifeInYears" => $estimatedLifeInYears,
                "newAcquisitionMonth" => $acquisitionMonth,
                "newAcquisitionYear" => $acquisitionYear,
                "newLastYear" => $lastYear,
                "months" => $monthsBetweenFirstAndLastYear > 12 ? 12 : $monthsBetweenFirstAndLastYear,
                "newMonthlyDepreciation" => $newMonthlyDepreciation/$rate,
                "newDepreciationExpense" => ($newMonthlyDepreciation * $monthsBetweenFirstAndLastYear / (intdiv($monthsBetweenFirstAndLastYear, $monthsInYear)))/$rate
            );
            $yearsBetweenData["newAccumulatedDepreciationStart"] = calculateAccumulatedDepreciation($acquisitionYear, $acquisitionMonth, ($yearOfReport - 1), $newMonthlyDepreciation)/$rate;
            $yearsBetweenData["newAccumulatedDepreciation"] = calculateAccumulatedDepreciation($acquisitionYear, $acquisitionMonth, $yearOfReport, $newMonthlyDepreciation)/$rate;
            $yearsBetweenData["newClosingCarryingValue"] = ($additions/$rate - $yearsBetweenData["newAccumulatedDepreciation"]) ;
            $yearsBetweenData["newNetBookValue"] = calculateNetBookValue($acquisitionYear, $acquisitionMonth, $yearOfReport, $additions, $newMonthlyDepreciation) /$rate;

            $lastYearData = array(
                "type" => "last",
                "newAssetName" => $assetName,
                "newAssetLocation" => $location,
                "newAssetSerial" => $serial,
                "newAssetDate" => $date,
                "newAssetAdditions" => $additions /$rate,
                "newAssetDepreciationRate" => $depreciation_rate * 100,
                "newAssetDollarRate" => $rate,
                "monthsRemainingFirstYear" => $monthsRemainingFirstYear,
                "monthsRemainingAfterFirstYear" => $monthsRemainingAfterFirstYear,
                "monthsRemainingLastYear" => $monthsRemainingLastYear,
                "newEstimatedLifeInYears" => $estimatedLifeInYears,
                "newAcquisitionMonth" => $acquisitionMonth,
                "newAcquisitionYear" => $acquisitionYear,
                "newLastYear" => $lastYear,
                "months" => $monthsRemainingLastYear,
                "newMonthlyDepreciation" => $newMonthlyDepreciation /$rate,
                "newDepreciationExpense" => ($newMonthlyDepreciation * $monthsRemainingLastYear)/$rate
            );
            $lastYearData["newAccumulatedDepreciationStart"] = calculateAccumulatedDepreciation($acquisitionYear, $acquisitionMonth, ($yearOfReport - 1), $newMonthlyDepreciation) /$rate;
            $lastYearData["newAccumulatedDepreciation"] = $firstYearData["newAccumulatedDepreciation"] + ($newMonthlyDepreciation * $monthsBetweenFirstAndLastYear) + ($newMonthlyDepreciation * $monthsRemainingLastYear) /$rate;
            $lastYearData["newClosingCarryingValue"] = ($additions/$rate - $lastYearData["newAccumulatedDepreciation"]) ;
            $lastYearData["newNetBookValue"] = calculateNetBookValue($acquisitionYear, $acquisitionMonth, $yearOfReport, $additions, $newMonthlyDepreciation) /$rate;
            $lastYearData["monthsDisplay"] = array();

            if ($yearOfReport == $firstYearData["newAcquisitionYear"]) {
                if ($disposed) $firstYearData["disposed"] = true;
                else $firstYearData["disposed"] = false;
                //var_dump($firstYearData);
                $foundRecords = true;
                $totalBookValueStart += ($firstYearData["newNetBookValue"]);
                $totalDepreciationExpense += ($firstYearData["newDepreciationExpense"]);
                $totalAccumulatedDepreciationStart += ($firstYearData["newAccumulatedDepreciationStart"]);
                $totalAccumulatedDepreciation += ($firstYearData["newAccumulatedDepreciation"]);
                $totalBookValueEnd += ($firstYearData["newClosingCarryingValue"]);
                $totalAdditions += ($additions/$rate);

                $emptyMonth = $monthsInYear - $firstYearData["months"];
                for ($month = 1; $month <= $monthsInYear; $month++) {
                    if ($emptyMonth >= 1) {
                        $firstYearData["monthsDisplay"][$month] = "-";
                    } else {
                        $monthlyTotals[$month] += $firstYearData["newMonthlyDepreciation"];
                        $firstYearData["monthsDisplay"][$month] = "$" . number_format($firstYearData["newMonthlyDepreciation"], 2);
                    }
                    $emptyMonth--;
                }

                array_push($payload["assetsData"], $firstYearData);
            } else if ($yearOfReport == $lastYearData["newLastYear"]) {
                if ($disposed) $lastYearData["disposed"] = true;
                else $lastYearData["disposed"] = false;
                //var_dump($lastYearData);
                $foundRecords = true;
                $totalBookValueStart += ($lastYearData["newNetBookValue"]);
                $totalDepreciationExpense += ($lastYearData["newDepreciationExpense"]);
                $totalAccumulatedDepreciationStart += ($lastYearData["newAccumulatedDepreciationStart"]);
                $totalAccumulatedDepreciation += ($lastYearData["newAccumulatedDepreciation"]);
                $totalBookValueEnd += ($lastYearData["newClosingCarryingValue"]);
                $totalAdditions += ($additions/$rate);

                $months = $lastYearData["months"];
                for ($month = 1; $month <= $monthsInYear; $month++) {
                    if ($months > 0) {
                        $monthlyTotals[$month] += $lastYearData["newMonthlyDepreciation"];
                        $lastYearData["monthsDisplay"][$month] = "$" . number_format($lastYearData["newMonthlyDepreciation"], 2);
                    } else {
                        $lastYearData["monthsDisplay"][$month] = "-";
                    }
                    $months--;
                }

                array_push($payload["assetsData"], $lastYearData);
            } else if ($yearOfReport > $yearsBetweenData["newAcquisitionYear"] && $yearOfReport < $estimatedLifeInYears) {
                if ($disposed) $yearsBetweenData["disposed"] = true;
                else $yearsBetweenData["disposed"] = false;
                //var_dump($yearsBetweenData);
                $foundRecords = true;
                $totalBookValueStart += ($yearsBetweenData["newNetBookValue"]);
                $totalDepreciationExpense += ($yearsBetweenData["newDepreciationExpense"]);
                $totalAccumulatedDepreciationStart += ($yearsBetweenData["newAccumulatedDepreciationStart"]);
                $totalAccumulatedDepreciation += ($yearsBetweenData["newAccumulatedDepreciation"]);
                $totalBookValueEnd += ($yearsBetweenData["newClosingCarryingValue"]);
                $totalAdditions += ($additions/$rate);

                $emptyMonth = $monthsInYear - $yearsBetweenData["months"];
                for ($month = 1; $month <= $monthsInYear; $month++) {
                    if ($emptyMonth >= 1) {
                        $lastYearData["monthsDisplay"][$month] = "-";
                    } else {
                        $monthlyTotals[$month] += $yearsBetweenData["newMonthlyDepreciation"];
                        $yearsBetweenData["monthsDisplay"][$month] = "$" . number_format($yearsBetweenData["newMonthlyDepreciation"], 2);
                    }
                    $emptyMonth--;
                }

                array_push($payload["assetsData"], $yearsBetweenData);
            }

        }

        $counter++;
    }

    $payload['assetsTotals']["totalBookValueStart"] = $totalBookValueStart;
    $payload['assetsTotals']["totalDepreciationExpense"] = $totalDepreciationExpense;
    $payload['assetsTotals']["totalAccumulatedDepreciationStart"] = $totalAccumulatedDepreciationStart;
    $payload['assetsTotals']["totalAccumulatedDepreciation"] = $totalAccumulatedDepreciation;
    $payload['assetsTotals']["totalBookValueEnd"] = $totalBookValueEnd;
    $payload['assetsTotals']["totalAdditions"] = $totalAdditions;
    $payload['assetsTotals']["totalDisposalsPrevious"] = $totalDisposals;
    $payload['assetsTotals']["totalDisposals"] = $totalDisposals;
    $payload['assetsTotals']["monthlyTotals"] = $monthlyTotals;

    $payload['assetsTotals']["previousYearsDisposals"] = $previousYearsDisposals;
    $payload['assetsTotals']["previousYearDisposals"] = $previousYearDisposals;
    $payload['assetsTotals']["previousOpeningBalances"] = $previousOpeningBalances;
    $payload['assetsTotals']["assetClassOpeningBalance"] = $assetClassOpeningBalance;

    return $payload;
}


function displayData($yearOfReport, array $assetData, array $assetClassData)
{
    echo "<div class='container my-4 '>";
    echo "<div class='table w-90'>";
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='table-primary'>";
    echo "<tr>
            <th>S/N</th>
            <th>Asset Name</th>
            <th>Location</th>
            <th>Tag/Chassis No.</th>
            <th>Date of Purchase</th>
            <th>Cost of Purchase(USD)</th>
            <th>Year of Report</th>
            <th>Accumulated Deprciation Start</th>
            <th>Net Book Value</th>
            <th>Depreciation Rate</th>
            <th>Depreciation Expense</th>
            <th>Accumulated Depreciation End</th>
            <th>Closing Carrying Value(GH₵)</th>
            <th>Dollar Rate Used</th>";


    for ($month = 1; $month <= 12; $month++) {
        echo "<th>" . date('F', mktime(0, 0, 0, $month, 1)) . "</th>";
    }

    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $counter = 1;

    if (!$assetData["assetsData"]) {
        if ($assetClassData['asset_class_info']['depreciated']) {
            
            $g_totalAdditions = ($assetClassData['asset_class_info']['open_bal_usd'] - $assetClassData['total_disposals'] + $assetData["assetsTotals"]["totalAdditions"]);
            $g_total_Accumulated_DepreciationStart = $assetClassData['asset_class_info']['total_accum_start_usd'] + ($assetData["assetsTotals"]["totalAccumulatedDepreciationStart"]);
            $g_total_DepreciationExpense = $assetClassData['asset_class_info']['total_depr_year_charge_usd'] + ($assetData["assetsTotals"]["totalDepreciationExpense"]);
            $g_total_AccumulatedDepreciationEnd = $assetClassData['asset_class_info']['total_accum_end_usd'] + ($assetData["assetsTotals"]["totalAccumulatedDepreciation"]);
            $netbookValue = $assetClassData['asset_class_info']['net_book_value_usd'] < 0 ? 0 : $assetClassData['asset_class_info']['net_book_value_usd'];
            $g_total_BookValueEnd = $netbookValue + ($assetData["assetsTotals"]["totalBookValueEnd"]);

            $_SESSION["grandTotals"]["g_totalAdditions"] = $g_totalAdditions ?? 0;
            $_SESSION["grandTotals"]["g_total_AccumulatedDepreciationStart"] = $g_total_Accumulated_DepreciationStart ?? 0;
            $_SESSION["grandTotals"]["g_total_DepreciationExpense"] = $g_total_DepreciationExpense ?? 0;
            $_SESSION["grandTotals"]["g_total_AccumulatedDepreciationEnd"] = $g_total_AccumulatedDepreciationEnd ?? 0;
            $_SESSION["grandTotals"]["g_total_BookValueEnd"] = $g_total_BookValueEnd ?? 0;
        } else {
            $g_totalAdditions = $assetData["assetsTotals"]["totalAdditions"];
            $g_total_Accumulated_DepreciationStart = 0;
            $g_total_DepreciationExpense = 0;
            $g_total_AccumulatedDepreciationEnd = 0;
            $g_total_BookValueEnd = 0;

            $_SESSION["grandTotals"]["g_totalAdditions"] = $g_totalAdditions ?? 0;
            $_SESSION["grandTotals"]["g_total_AccumulatedDepreciationStart"] =  0;
            $_SESSION["grandTotals"]["g_total_DepreciationExpense"] = 0;
            $_SESSION["grandTotals"]["g_total_AccumulatedDepreciationEnd"] = 0;
            $_SESSION["grandTotals"]["g_total_BookValueEnd"] = 0;
        }
        echo "<tr><td colspan='14' class='text-center text-danger'>No records found for the selected asset class and year.</td></tr>";
    } else {
        foreach ($assetData["assetsData"] as $asset) {
            // var_dump($assetClassData);
            // $deprciated_class = (int) $assetClassData['asset_class_info']["depreciated"];
            // if (!$deprciated_class) {

            // }
            if ($asset["type"] == "first") {
                if ($yearOfReport == $asset["newAcquisitionYear"]) {
                    echo "<tr>";
                    echo "<td>$counter</td>";
                    echo "<td>{$asset["newAssetName"]}</td>";
                    echo "<td>{$asset["newAssetLocation"]}</td>";
                    echo "<td>{$asset["newAssetSerial"]}</td>";
                    echo "<td>{$asset["newAssetDate"]}</td>";
                    echo "<td>$" . number_format($asset["newAssetAdditions"], 2) . "</td>";
                    echo "<td>$yearOfReport</td>";
                    echo "<td>$" . number_format($asset['newAccumulatedDepreciationStart'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newNetBookValue'], 2) . "</td>";
                    echo "<td>" . $asset['newAssetDepreciationRate'] . "%</td>";
                    echo "<td>$" . number_format($asset['newDepreciationExpense'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newAccumulatedDepreciation'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newClosingCarryingValue'], 2) . "</td>";
                    echo "<td>{$asset["newAssetDollarRate"]}</td>";

                    foreach ($asset["monthsDisplay"] as $month) {
                        echo "<td>" . $month . "</td>";
                    }
                }
            } else if ($asset["type"] == "last") {
                if ($yearOfReport == $asset["newLastYear"]) {
                    echo "<tr>";
                    echo "<td>$counter</td>";
                    echo "<td>{$asset["newAssetName"]}</td>";
                    echo "<td>{$asset["newAssetLocation"]}</td>";
                    echo "<td>{$asset["newAssetSerial"]}</td>";
                    echo "<td>{$asset["newAssetDate"]}</td>";
                    echo "<td>$" . number_format($asset["newAssetAdditions"], 2) . "</td>";
                    echo "<td>$yearOfReport</td>";
                    echo "<td>$" . number_format($asset['newAccumulatedDepreciationStart'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newNetBookValue'], 2) . "</td>";
                    echo "<td>" . $asset['newAssetDepreciationRate'] . "%</td>";
                    echo "<td>$" . number_format($asset['newDepreciationExpense'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newAccumulatedDepreciation'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newClosingCarryingValue'], 2) . "</td>";
                    echo "<td>{$asset["newAssetDollarRate"]}</td>";

                    foreach ($asset["monthsDisplay"] as $month) {
                        echo "<td>" . $month . "</td>";
                    }
                }
            } else if ($asset["type"] == "between") {
                if ($yearOfReport > $asset["newAcquisitionYear"] && $yearOfReport < $asset["newEstimatedLifeInYears"]) {
                    echo "<tr>";
                    echo "<td>$counter</td>";
                    echo "<td>{$asset["newAssetName"]}</td>";
                    echo "<td>{$asset["newAssetLocation"]}</td>";
                    echo "<td>{$asset["newAssetSerial"]}</td>";
                    echo "<td>{$asset["newAssetDate"]}</td>";
                    echo "<td>$" . number_format($asset["newAssetAdditions"], 2) . "</td>";
                    echo "<td>$yearOfReport</td>";
                    echo "<td>$" . number_format($asset['newAccumulatedDepreciationStart'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newNetBookValue'], 2) . "</td>";
                    echo "<td>" . $asset['newAssetDepreciationRate'] . "%</td>";
                    echo "<td>$" . number_format($asset['newDepreciationExpense'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newAccumulatedDepreciation'], 2) . "</td>";
                    echo "<td>$" . number_format($asset['newClosingCarryingValue'], 2) . "</td>";
                    echo "<td>{$asset["newAssetDollarRate"]}</td>";

                    foreach ($asset["monthsDisplay"] as $month) {
                        echo "<td>" . $month . "</td>";
                    }
                }
            } else if ($asset["type"] == "non") {

                $rate = $asset["newAssetDollarRate"];
                echo "<tr>";
                echo "<td>$counter</td>";
                echo "<td>{$asset["newAssetName"]}</td>";
                echo "<td>{$asset["newAssetLocation"]}</td>";
                echo "<td>{$asset["newAssetSerial"]}</td>";
                echo "<td>{$asset["newAssetDate"]}</td>";
                echo "<td>$" . number_format($asset["newAssetAdditions"]/ $rate, 2) . "</td>";
                echo "<td>$yearOfReport</td>";
                echo "<td>{$asset["newAssetDollarRate"]}</td>";
            }

            $counter++;
        }
        


        // Output the summation row if records exist
        echo "<tfoot>";

        if ($assetClassData['asset_class_info']['depreciated']){
            echo "<tr>";
        echo "<td colspan='5'><strong>Total</strong></td>";
        echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalAdditions"], 2) . "</strong></td>";
        echo "<td></td>";
        echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalAccumulatedDepreciationStart"], 2) . "</strong></td>";
        echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalBookValueStart"], 2) . "</strong></td>";
        echo "<td></td>"; // Empty cell for depreciation rate (no summation)
        echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalDepreciationExpense"], 2) . "</strong></td>";
        echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalAccumulatedDepreciation"], 2) . "</strong></td>";
        echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalBookValueEnd"], 2) . "</strong></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td colspan='14'><strong>Total Monthly Depreciation</strong></td>";
        for ($month = 1; $month <= 12; $month++) {
            $total = isset($assetData['assetsTotals']["monthlyTotals"][$month]) ? $assetData['assetsTotals']["monthlyTotals"][$month] : 0;
            echo "<td><strong>$" . number_format($total, 2) . "</strong></td>";
        }
        echo "</tr>";
        
        }

        else{
            echo "<tr>";
            echo "<td colspan='5'><strong>Total</strong></td>";
            echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalAdditions"]/$asset["newAssetDollarRate"], 2) . "</strong></td>";
            echo "<td></td>";
            echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalAccumulatedDepreciationStart"], 2) . "</strong></td>";
            echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalBookValueStart"], 2) . "</strong></td>";
            echo "<td></td>"; // Empty cell for depreciation rate (no summation)
            echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalDepreciationExpense"], 2) . "</strong></td>";
            echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalAccumulatedDepreciation"], 2) . "</strong></td>";
            echo "<td><strong>$" . number_format($assetData["assetsTotals"]["totalBookValueEnd"], 2) . "</strong></td>";
            echo "</tr>";
    
            echo "<tr>";
            echo "<td colspan='14'><strong>Total Monthly Depreciation</strong></td>";
            for ($month = 1; $month <= 12; $month++) {
                $total = isset($assetData['assetsTotals']["monthlyTotals"][$month]) ? $assetData['assetsTotals']["monthlyTotals"][$month] : 0;
                echo "<td><strong>$" . number_format($total, 2) . "</strong></td>";
            }
            echo "</tr>";
            
        }
  
        if ($assetClassData['asset_class_info']['depreciated']) {
            $g_totalAdditions = ($assetClassData['asset_class_info']['open_bal_usd'] - $assetClassData['total_disposals'] + $assetData["assetsTotals"]["totalAdditions"]);
            $g_total_Accumulated_DepreciationStart = $assetClassData['asset_class_info']['total_accum_start_usd'] + ($assetData["assetsTotals"]["totalAccumulatedDepreciationStart"]);
            $g_total_DepreciationExpense = $assetClassData['asset_class_info']['total_depr_year_charge_usd'] + ($assetData["assetsTotals"]["totalDepreciationExpense"]);
            $g_total_AccumulatedDepreciationEnd = $assetClassData['asset_class_info']['total_accum_end_usd'] + ($assetData["assetsTotals"]["totalAccumulatedDepreciation"]);
            $netbookValue = $assetClassData['asset_class_info']['net_book_value_usd'] < 0 ? 0 : $assetClassData['asset_class_info']['net_book_value_usd'];
            $g_total_BookValueEnd = $netbookValue + ($assetData["assetsTotals"]["totalBookValueEnd"]);

            $_SESSION["grandTotals"]["g_totalAdditions"] = $g_totalAdditions ?? 0;
            $_SESSION["grandTotals"]["g_total_AccumulatedDepreciationStart"] = $g_total_Accumulated_DepreciationStart ?? 0;
            $_SESSION["grandTotals"]["g_total_DepreciationExpense"] = $g_total_DepreciationExpense ?? 0;
            $_SESSION["grandTotals"]["g_total_AccumulatedDepreciationEnd"] = $g_total_AccumulatedDepreciationEnd ?? 0;
            $_SESSION["grandTotals"]["g_total_BookValueEnd"] = $g_total_BookValueEnd ?? 0;
        } else {
            $g_totalAdditions = ($assetClassData['asset_class_info']['open_bal_usd'] + ($assetData["assetsTotals"]["totalAdditions"]/$asset["newAssetDollarRate"]));
            $g_total_Accumulated_DepreciationStart = 0;
            $g_total_DepreciationExpense = 0;
            $g_total_AccumulatedDepreciationEnd = 0;
            $g_total_BookValueEnd = 0;

            $_SESSION["grandTotals"]["g_totalAdditions"] = $g_totalAdditions ?? 0;
            $_SESSION["grandTotals"]["g_total_AccumulatedDepreciationStart"] = 0;
            $_SESSION["grandTotals"]["g_total_DepreciationExpense"] = 0;
            $_SESSION["grandTotals"]["g_total_AccumulatedDepreciationEnd"] = 0;
            $_SESSION["grandTotals"]["g_total_BookValueEnd"] = 0;
        }

            echo "<tr>";
            echo "<td colspan='5'><strong>Grand Total</strong></td>";
            echo "<td><strong>$" . number_format($g_totalAdditions, 2) . "</strong></td>";
            echo "<td></td>";
            echo "<td><strong>$" . number_format($g_total_Accumulated_DepreciationStart, 2) . "</strong></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td> <strong>$" . number_format($g_total_DepreciationExpense, 2) . "</strong></td>";

            echo "<td><strong>$" . number_format($g_total_AccumulatedDepreciationEnd, 2) . "</strong></td>";

            echo "<td><strong>$" . number_format($g_total_BookValueEnd, 2) . "</strong></td>";
            echo "</tr>";

        echo "</tfoot>";
    }


    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</div>";
}
