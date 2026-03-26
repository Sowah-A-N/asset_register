<?php

//Include database connection
include_once "datacon.php";
//global $conn = 1;
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
        die("Error in SQL Query preparation for getting assets in class: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $assetClass);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Error in SQL Query execution for getting assets in class: " . mysqli_error($conn));
    }

    $usefulLife = getAssetClassInitialData($assetClass)['estimated_life'];

    

    while ($row = mysqli_fetch_array($result)) {
        $historicalCost = $row['additions'];
        $acquisitionDate = $row['acquisition_date'];
        $row['monthly_depreciation'] = getMonthlyDeprAmount($historicalCost, $usefulLife);
        $row['accumulated_depreciation'] = getAssetDepToDate($historicalCost, $usefulLife, $acquisitionDate);
        
        $assetsPerClassArray[] = $row; // Store the row in the array
        // var_dump ($row);
    }

    return $assetsPerClassArray;
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
        die("Error in SQL Query preparation for getting asset info: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $assetId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Error in SQL Query execution for getting asset info: " . mysqli_error($conn));
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

    // for ($month = 1; $month <= 12; $month++) {
    //     if (($selectedYear == $yearOfAcquisition) && ($month < $monthOfAcquisition)) {
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
    // }

    
    $yearDifference = $selectedYear - $yearOfAcquisition;
    while ($yearDifference != 0) {
        if (($selectedYear == $yearOfAcquisition) && ($month < $monthOfAcquisition)) {
            $month++;
            continue;
        }

        $accumulatedDepreciation += $monthlyDepreciation;
        if ($accumulatedDepreciation > $historicalCost) {
            $accumulatedDepreciation = $historicalCost;
        }

        $netBookValue = $historicalCost - $accumulatedDepreciation;

        // Store data correctly for each month
        $monthName = date("F", mktime(0, 0, 0, $month, 1, $selectedYear));
        $monthlyData[$monthName] = [
            'monthly_depreciation' => number_format($monthlyDepreciation, 2),
            'accumulated_depreciation' => number_format($accumulatedDepreciation, 2),
            'net_book_value' => number_format($netBookValue, 2)
        ];

        if ($accumulatedDepreciation >= $historicalCost) {
            break;
        }
        $month++;
    }

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

    /* For each asset in the selected class,
    run the individualAssetCalculations fxn to
    get the asset calculations and store them
    */


}