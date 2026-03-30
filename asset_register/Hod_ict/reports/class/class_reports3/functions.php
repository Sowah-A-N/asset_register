<?php

//Include database connection
include_once "datacon.php";

global $conn;

/**************** Initial Setup *******************/
function getAssetClassInitialData($assetClass) {
    //Use global DB connection
    global $conn;

    /**
     * Retrieves the initial data for a given asset class.
     *
     * @param string $assetClass The asset class to retrieve data for.
     *
     * @return array The initial data for the asset class.
     *
     * @throws Exception If the database connection is not established or the query execution fails.
     */

    // Check if the database connection is established
    if (!$conn) {
        throw new Exception("Database connection is not established.");
    }

    // Prepare the SQL query
    $assetClassQuery = "SELECT asset_class, opening_bal, dep_rate, estimated_life
                        FROM asset_classes
                        WHERE asset_class = ?;";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $assetClassQuery);

    // Check if the statement preparation was successful
    if (!$stmt) {
        throw new Exception("Failed to prepare SQL statement: " . mysqli_error($conn));
    }

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "s", $assetClass);

    // Execute the query
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to execute SQL query: " . mysqli_stmt_error($stmt));
    }

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if the result is not empty
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the data
        $assetClassDataArray = mysqli_fetch_array($result);
        return $assetClassDataArray;
    } else {
        // Return an empty array if no data is found
        return [];
    }
}

// function getAssetsPerClass($assetClass){
//     //Use global DB connection
//     global $conn;

//     //Array to store the assets per class
//     $assetsPerClassArray = [];

//     //Get all assets from the selected class
//     $assetsPerClassQuery = "SELECT * 
//                             FROM assets 
//                             WHERE asset_class = ?;";

//     $stmt = mysqli_prepare($conn, $assetsPerClassQuery);
//     if (!$stmt) {
//         die("Error in SQL Query preparation for getting assets in class: " . mysqli_error($conn));
//     }

//     mysqli_stmt_bind_param($stmt, "s", $assetClass);
//     mysqli_stmt_execute($stmt);
//     $result = mysqli_stmt_get_result($stmt);

//     if (!$result) {
//         die("Error in SQL Query execution for getting assets in class: " . mysqli_error($conn));
//     }

//     $usefulLife = getAssetClassInitialData($assetClass)['estimated_life'];
   
//     // Initialize total variables
//     $totalHistoricalCost = 0;
//     $totalMonthlyDepreciation = 0;
//     $totalAccumulatedDepreciation = 0;
//     $totalNetBookValue = 0;

//     while ($row = mysqli_fetch_array($result)) {
//         $historicalCost = $row['additions'];
//         $acquisitionDate = $row['acquisition_date'];
//         $monthlyDepreciation = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];
//         $accumulatedDepreciation = getAssetDepToDate($historicalCost, $usefulLife, $acquisitionDate);
        
//         // Update total variables
//         $totalHistoricalCost += $historicalCost;
//         $totalMonthlyDepreciation += $monthlyDepreciation;
//         $totalAccumulatedDepreciation += $accumulatedDepreciation;
//         $totalNetBookValue += $historicalCost - $accumulatedDepreciation;

//         $row['monthly_depreciation'] = $monthlyDepreciation;
//         $row['accumulated_depreciation'] = $accumulatedDepreciation;


//         $assetsPerClassArray[] = $row; // Store the row in the array
//         // var_dump ($row);
//     }

//     // Calculate totals
//     $totals = [
//         'total_historical_cost' => floatval($totalHistoricalCost),
//         'total_monthly_depreciation' => floatval($totalMonthlyDepreciation),
//         'total_accumulated_depreciation' => floatval($totalAccumulatedDepreciation),
//         'total_net_book_value' => floatval($totalNetBookValue)
//     ];

//     //return $assetsPerClassArray;

//     return [
//         'assets' => $assetsPerClassArray,
//         'totals' => $totals
//     ];

// }


function getAssetsPerClass($assetClass){
    //Use global DB connection
    global $conn;

    //Array to store the assets per class
    $assetsPerClassArray = [];

    //Get all assets from the selected class
    $assetsPerClassQuery = "SELECT * 
                            FROM assets 
                            WHERE asset_class = ?;";

    $stmt = mysqli_prepare($conn, $assetsPerClassQuery);
    if (!$stmt) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    mysqli_stmt_bind_param($stmt, "s", $assetClass);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    $usefulLife = getAssetClassInitialData($assetClass)['estimated_life'];
   
    // Initialize total variables
    $totalHistoricalCost = 0;
    $totalMonthlyDepreciation = 0;
    $totalAccumulatedDepreciation = 0;
    $totalNetBookValue = 0;

    while ($row = mysqli_fetch_array($result)) {
        $historicalCost = $row['additions'];
        $acquisitionDate = $row['acquisition_date'];
        $monthlyDepreciation = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];
        $accumulatedDepreciation = getAssetDepToDate($historicalCost, $usefulLife, $acquisitionDate);
        
        // Update total variables
        $totalHistoricalCost += $historicalCost;
        $totalMonthlyDepreciation += $monthlyDepreciation;
        $totalAccumulatedDepreciation += $accumulatedDepreciation;
        $totalNetBookValue += $historicalCost - $accumulatedDepreciation;

        $row['monthly_depreciation'] = $monthlyDepreciation;
        $row['accumulated_depreciation'] = $accumulatedDepreciation;

        // Calculate monthly breakdown for the current year
        $currentYear = date("Y");
        $monthlyBreakdown = calculateMonthlyDepreciationForYear($historicalCost, $usefulLife, $acquisitionDate, $currentYear);
        $row['monthly_breakdown'] = $monthlyBreakdown;

        $assetsPerClassArray[] = $row; // Store the row in the array
    }

    // Calculate totals
    $totals = [
        'total_historical_cost' => floatval($totalHistoricalCost),
        'total_monthly_depreciation' => floatval($totalMonthlyDepreciation),
        'total_accumulated_depreciation' => floatval($totalAccumulatedDepreciation),
        'total_net_book_value' => floatval($totalNetBookValue)
    ];

    return [
        'assets' => $assetsPerClassArray,
        'totals' => $totals
    ];
}
function getAssetInfo($assetId, $year = null){
    //Use global DB connection
    global $conn;
    //Array to store the asset info
    $assetInfoArray = [];
    //Get asset info from the selected asset
    $assetInfoQuery = "SELECT * FROM `assets` WHERE asset_id = ?;";

    $stmt = mysqli_prepare($conn, $assetInfoQuery);
    if (!$stmt) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    mysqli_stmt_bind_param($stmt, "i", $assetId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        error_log(mysqli_error($conn)); die('A database error occurred.');
    }

    if ($row = mysqli_fetch_array($result)) {
        $assetInfoArray = $row;
        $assetInfoArray['monthly_depreciation'] = getMonthlyDeprAmount($row['additions'], getAssetClassInitialData($row['asset_class'])['estimated_life'])['monthlyDepAmt'];
        $assetInfoArray['accumulated_depreciation'] = getAssetDepToDate($row['additions'], getAssetClassInitialData($row['asset_class'])['estimated_life'], $row['acquisition_date']);
        // if ($year) {
        //     $assetInfoArray['depreciation_for_year'] = getAssetDepForSelectedYear($row['additions'], getAssetClassInitialData($row['asset_class'])['estimated_life'], $row['acquisition_date'], $year);
        //     $assetInfoArray['monthly_depreciation_for_year'] = calculateMonthlyDepreciationForYear($row['additions'], getAssetClassInitialData($row['asset_class'])['estimated_life'], $row['acquisition_date'], $year);
        // }
    }

    return $assetInfoArray;
}

/************* Asset Depreciation Calculations ***************/

//Function to calculate the monthly depreciation amount of asset

function getMonthlyDeprAmount($historicalCost, $usefulLife){
    //Use global DB connection
    global $conn;

    //Get useful life in months
    $usefulLifeInMonths = $usefulLife * 12;
    
    //Formula for monthly depreciation amount
    $monthlyDepAmt = $historicalCost / $usefulLifeInMonths;

    return array(
            'monthlyDepAmt' => round($monthlyDepAmt, 2),
            'usefulLifeInMonths' => $usefulLifeInMonths
    );
}

//Function to calculate the total depreciation amount of asset to date
function getAssetDepToDate($historicalCost, $usefulLife, $acquisitionDate){
    $acquisitionDate = new DateTime($acquisitionDate);
    $currentDate = new DateTime();

    // Calculate total months the asset has been in use
    $monthsUsed = ($acquisitionDate->diff($currentDate)->y * 12) + $acquisitionDate->diff($currentDate)->m;

    // Get monthly depreciation from external function
    $monthlyDepreciation = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];

    // Accumulated depreciation up to today
    // $accumulatedDepreciation = min($monthsUsed * $monthlyDepreciation, $historicalCost - $salvageValue);
    $accumulatedDepreciation = min($monthsUsed * $monthlyDepreciation, $historicalCost);


    //Array to store the asset calculations
    // $individualAssetCalculationsArray = [];
    // return $assetCalculationsArray;

    return $accumulatedDepreciation;
}

//Function to calculate depreciation for selected year
function getAssetDepForSelectedYear($historicalCost, $usefulLife, $acquisitionDate, $selectedYear){
    $acquisitionDate = new DateTime($acquisitionDate);
    $yearOfAcquisition = date("Y", $acquisitionDate->getTimestamp());
    $selectedYear = date("Y", strtotime($selectedYear));

    $monthlyDeprAmount = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];
    $annualDepreciation = $monthlyDeprAmount * 12;
    $yearsUsed = $selectedYear - $yearOfAcquisition + 1;

    $accumulatedDepreciation = min($annualDepreciation * $yearsUsed , $historicalCost);

    return [
        'accumulatedDepreciation' => $accumulatedDepreciation,
        'annualDepreciation' => $annualDepreciation,
        'yearsUsed' => $yearsUsed
    ];

}

// function calculateMonthlyDepreciationForYear($historicalCost, $usefulLife, $acquisitionDate, $selectedYear){
//     //Get useful life in months
//     $usefulLifeInMonths = getMonthlyDeprAmount($historicalCost, $usefulLife)['usefulLifeInMonths'];
//     $monthlyDepreciation = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];

//     $acquisitionDate = new DateTime($acquisitionDate);
//     $monthOfAcquisition = $acquisitionDate->format("m");
//     $yearOfAcquisition = $acquisitionDate->format("Y");
//     $selectedYear = date("Y", strtotime($selectedYear));
//     $selectedMonth = date("m", strtotime($selectedYear));

//     $monthsUsed = 0;
//     $accumulatedDepreciation = 0;
//     $netBookValue = $historicalCost;
//     $monthlyData = [];

//     for ($month = 1; $month <= 12; $month++) {
//         if(($selectedYear == $yearOfAcquisition)  && ($selectedMonth < $monthOfAcquisition)){
//             continue;
//         }

//         $accumulatedDepreciation += $monthlyDepreciation;

//         if ($accumulatedDepreciation > $historicalCost){
//             $accumulatedDepreciation = $historicalCost;
//         }

//         $netBookValue = $historicalCost - $accumulatedDepreciation;

//         // $monthlyData[date("F", mktime(0, 0, 0, $selectedMonth, 1, $selectedYear))] = [
//         //         'monthlyDepreciation' => number_format($monthlyDepreciation, 2),
//         //         'accumulatedDepreciation' => number_format($accumulatedDepreciation, 2),
//         //         'netBookValue' => number_format($netBookValue, 2)
//         // ];

//         $monthlyData = [
//             'monthlyDepreciation' => 1, 
//             'accumulatedDepreciation' => 1,
//             'netBookValue' => 1
//         ];

//         if($accumulatedDepreciation >= $historicalCost){
//             break;
//         }
//     }   

//     return $monthlyData;
// }

// function calculateMonthlyDepreciationForYear($historicalCost, $usefulLife, $acquisitionDate, $selectedYear) {
//     // Get useful life in months
//     $usefulLifeInMonths = getMonthlyDeprAmount($historicalCost, $usefulLife)['usefulLifeInMonths'];
//     $monthlyDepreciation = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];

//     $acquisitionDate = new DateTime($acquisitionDate);
//     $monthOfAcquisition = $acquisitionDate->format("m");
//     $yearOfAcquisition = $acquisitionDate->format("Y");

//     $selectedYear = (int) $selectedYear; // Ensure it's an integer
//     $accumulatedDepreciation = 0;
//     $netBookValue = $historicalCost;
//     $monthlyData = []; // Initialize as an empty array

//     for ($month = 1; $month <= 12; $month++) {
//         if (($selectedYear == $yearOfAcquisition) && ($month < $monthOfAcquisition)) {
//             continue;
//         }

//         $accumulatedDepreciation += $monthlyDepreciation;
//         if ($accumulatedDepreciation > $historicalCost) {
//             $accumulatedDepreciation = $historicalCost;
//         }

//         $netBookValue = $historicalCost - $accumulatedDepreciation;

//         // Store data correctly for each month
//         $monthName = date("F", mktime(0, 0, 0, $month, 1, $selectedYear));
//         $monthlyData[$monthName] = [
//             'monthlyDepreciation' => number_format($monthlyDepreciation, 2),
//             'accumulatedDepreciation' => number_format($accumulatedDepreciation, 2),
//             'netBookValue' => number_format($netBookValue, 2)
//         ];

//         if ($accumulatedDepreciation >= $historicalCost) {
//             break;
//         }
//     }

//     return $monthlyData; // Returns an array with month names as keys
// }

function calculateMonthlyDepreciationForYear($historicalCost, $usefulLife, $acquisitionDate, $selectedYear) {
    $monthlyDepreciation = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];

    $acquisitionDate = new DateTime($acquisitionDate);
    $monthOfAcquisition = (int) $acquisitionDate->format("m");
    $yearOfAcquisition = (int) $acquisitionDate->format("Y");

    $selectedYear = (int) $selectedYear;
    $accumulatedDepreciation = 0;
    $netBookValue = $historicalCost;
    $monthlyData = [];

    //Test Variables
    $monthsInFirstYear = '';

    // Check if selected year is valid
    if ($selectedYear < $yearOfAcquisition) {
        // Handle error or set default value
        $selectedYear = $yearOfAcquisition;
    }

    // Calculate months in the first year
    if ($monthOfAcquisition < 1 || $monthOfAcquisition > 12) {
        // Handle error or set default value
        $monthOfAcquisition = 1;
    }

    // Calculate total months
    $yearDifference = $selectedYear - $yearOfAcquisition;
    $yearDifferenceInMonths = $yearDifference * 12;

    // Calculate months in the first year
    if ($monthOfAcquisition != 1) {
        $monthsInFirstYear = (12 - $monthOfAcquisition) + 1;
        if ($monthsInFirstYear < 0) {
            $monthsInFirstYear = 0;
        }
    } else {
        $monthsInFirstYear = 12;
    }

    // Calculate total months
    $totalMonths = $yearDifferenceInMonths + $monthsInFirstYear;

    // Check if total months is negative
    if ($totalMonths < 0) {
        // Handle error or set default value
        $totalMonths = 0;
    }    

    // Initialize an array to store the depreciation results
    $depreciationResults = [];

    //Working up to this point  

    // Loop through each month and calculate the depreciation
    for ($i = 1; $i <= $totalMonths; $i++) {
        // Calculate the depreciation for the current month
        $accumulatedDepreciation += $monthlyDepreciation;
        $netBookValue -= $monthlyDepreciation;

        // Convert month number to month name
        $monthName = date("F", mktime(0, 0, 0, $monthOfAcquisition, 1, $selectedYear));
    
         // Store the depreciation result in the array
        $depreciationResults[] = [
            //'month' => $i,
            'month' => $monthName ,
            'depreciation' => number_format($monthlyDepreciation, 2),
            'accumulatedDepreciation' => number_format($accumulatedDepreciation, 2),
            'netBookValue' => number_format($netBookValue, 2)
        ];

        $monthOfAcquisition++;
    }
       
    // Filter the depreciation results to only show the results for the selected year
    $selectedYearResults = array_filter($depreciationResults, function ($result) use ($yearDifferenceInMonths) {
        return $result['month'] > $yearDifferenceInMonths;
    });

    // Display the results for the selected year
    //echo "Depreciation Schedule for $selectedYear:\n";
    foreach ($selectedYearResults as $result) {
        // echo "Month: " . $result['month'] .
        //     ", Depreciation: " . $result['depreciation'] .
        //     ", Accumulated Depreciation: " . $result['accumulatedDepreciation'] .
        //     ", Net Book Value: " . $result['netBookValue'] . "\n";

        $monthlyData[] = $result;
    }

    //While format from Francis
    // $yearDifference = $selectedYear - $yearOfAcquisition;
    // while ($yearDifference != 0) {
    //     if (($selectedYear == $yearOfAcquisition) && ($month < $monthOfAcquisition)) {
    //         $month++;
    //         continue;
    //     }

    //     $accumulatedDepreciation += $monthlyDepreciation;
    //     if ($accumulatedDepreciation > $historicalCost) {
    //         $accumulatedDepreciation = $historicalCost;
    //     }

    //     $netBookValue = $historicalCost - $accumulatedDepreciation;

    //     // Store data correctly for each month
    //     $monthName = date("F", mktime(0, 0, 0, $month, 1, $selectedYear));
    //     $monthlyData[$monthName] = [
    //         'monthly_depreciation' => number_format($monthlyDepreciation, 2),
    //         'accumulated_depreciation' => number_format($accumulatedDepreciation, 2),
    //         'net_book_value' => number_format($netBookValue, 2)
    //     ];

    //     if ($accumulatedDepreciation >= $historicalCost) {
    //         break;
    //     }
    //     $month++;
    // }

    return $monthlyData; // ✅ Now it returns an associative array
}

function getRemainingUsefulLife($acquisitionDate, $usefulLife){
    $acquisitionDate = new DateTime($acquisitionDate);
    $currentDate = new DateTime();

    //Get useful life in months
    $usefulLifeInMonths = $usefulLife * 12;    

    // Calculate total months the asset has been in use
    $monthsUsed = ($acquisitionDate->diff($currentDate)->y * 12) + $acquisitionDate->diff($currentDate)->m;

    $remainingUsefulLife = $usefulLifeInMonths - $monthsUsed;

    return $remainingUsefulLife;
}

function assetClassCalculations($assetClass){
    //Array to store the asset class calculations
    $assetClassCalculationsArray = [];

    while ($row = mysqli_fetch_array($result)) {
        $historicalCost = $row['additions'];
        $acquisitionDate = $row['acquisition_date'];
        $row['monthly_depreciation'] = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];
        $row['accumulated_depreciation'] = getAssetDepToDate($historicalCost, $usefulLife, $acquisitionDate);
        $row['asset_calculations'] = individualAssetCalculations($row);
        $assetsPerClassArray[] = $row; // Store the row in the array
    }

}
