<?php

//Include database connection
include_once "datacon.php";
//global $conn = 1;
global $conn;

/**************** Initial Setup *******************/
function getAssetClassInitialData($assetClass) {
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
    while ($row = mysqli_fetch_array($result)) {
    //     $someValue = $row['some_column'];
    //     $assetsPerClassArray[] = applyUserDefinedFunction($someValue);
    // }
    
    // function applyUserDefinedFunction($row) {
    //     // Apply your custom logic here
    //     // For example, let's assume you want to uppercase the asset name
    //     $row['asset_name'] = strtoupper($row['asset_name']);
        return $row;
    }

    return $assetsPerClassArray;
}

/************* Asset Depreciation Calculations ***************/

//Function to calculate the monthly depreciation amount of asset
function getMonthlyDeprAmount($historicalCost, $usefulLife){
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
    $selectedYear = new DateTime($selectedYear);

    $monthlyDeprAmount = getMonthlyDeprAmount($historicalCost, $usefulLife)['monthlyDepAmt'];

    $accumulatedDepreciation = min();

}

function getRemainingUsefulLife($usefulLife){
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