<?php
// Include database connection
include "../datacon.php";

// Get asset ID from URL parameter
$assetId = $_GET['asset_id'];

// Fetch asset details
$sqlAsset = "SELECT * FROM assets WHERE asset_id = $assetId";
$resultAsset = mysqli_query($conn, $sqlAsset);

if (!$resultAsset) {
    error_log(mysqli_error($conn)); die('A database error occurred.');
}

$rowAsset = mysqli_fetch_assoc($resultAsset);

// Fetch asset class details
$assetClass = $rowAsset['asset_class'];
$sqlAssetClasses = "SELECT opening_bal, opbal_plus_additions, estimated_life, dep_rate FROM asset_classes WHERE asset_class = '$assetClass'";
$resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);

if (!$resultAssetClasses) {
    error_log(mysqli_error($conn)); die('A database error occurred.');
}

$rowAssetClasses = mysqli_fetch_assoc($resultAssetClasses);

// Calculate acquisition year
$acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));

// Calculate current year
$currentYear = date('Y');

// Calculate number of years
$numYears = $rowAssetClasses['estimated_life'];

// Asset details
$assetName = $rowAsset['asset_name'];
$cost = $rowAssetClasses['opening_bal'];
$additions = $rowAsset['additions'];
$life = $numYears; // Estimated life in years
$depreciation_rate = $rowAssetClasses['dep_rate']; // Depreciation rate from database

/****************************************   Filter for months and years **********/

// Capture selected start and end years from the form submission
$startYear = isset($_GET['start_year']) ? (int)$_GET['start_year'] : $acquisitionYear;
$endYear = isset($_GET['end_year']) ? (int)$_GET['end_year'] : $currentYear;

// Ensure the start year is not earlier than the acquisition year
$startYear = max($startYear, $acquisitionYear);

// Ensure the end year is not later than the current year
$endYear = min($endYear, $currentYear);


// Generate months dropdown
$months = [
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December'
];

// Generate years dropdown (e.g., from 2000 to current year)
$years = range(2000, $currentYear);

// Output HTML
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
echo "<div class='container my-4'>";
echo "<h2 class='text-primary mb-4'>Filter Reports</h2>";

// Filter form
echo "<form method='get' action=''>";
echo "<input type='hidden' name='asset_id' value='$assetId'>"; // Include asset_id
echo "<div class='row mb-3'>";

// First set of dropdowns (Month and Year)
echo "<div class='col-md-6'>";
echo "<h4>Start Date</h4>";
echo "<div class='form-group'>";
echo "<label for='start_month'>Month:</label>";
echo "<select name='start_month' id='start_month' class='form-control'>";
foreach ($months as $key => $month) {
    echo "<option value='$key'>$month</option>";
}
echo "</select>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label for='start_year'>Year:</label>";
echo "<select name='start_year' id='start_year' class='form-control'>";
foreach ($years as $year) {
    echo "<option value='$year'>$year</option>";
}
echo "</select>";
echo "</div>";
echo "</div>";

// Second set of dropdowns (Month and Year)
echo "<div class='col-md-6'>";
echo "<h4>End Date</h4>";
echo "<div class='form-group'>";
echo "<label for='end_month'>Month:</label>";
echo "<select name='end_month' id='end_month' class='form-control'>";
foreach ($months as $key => $month) {
    echo "<option value='$key'>$month</option>";
}
echo "</select>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label for='end_year'>Year:</label>";
echo "<select name='end_year' id='end_year' class='form-control'>";
foreach ($years as $year) {
    echo "<option value='$year'>$year</option>";
}
echo "</select>";
echo "</div>";
echo "</div>";

echo "</div>"; // Close row
echo "<button type='submit' name='filter' class='btn btn-primary'>Filter</button>";
echo "</form>";



// Output HTML
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';



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
        <th>Year</th>
        <th>Net Book Value</th>
        <th>Depreciation Rate</th>
        <th>Depreciation Expense</th>
        <th>Accumulated Depreciation</th>
        <th>Closing Carrying Value</th> 
        <th>Actions</th>
    </tr>";
echo "</thead>";
echo "<tbody>";

// Initialize depreciation calculation
$book_value_start = $additions;
$initialValue= $additions;
$accumulated_depreciation = 0;

// Loop through the years within the selected range
// Calculate depreciation for each year upfront

$depreciationResults = array();
$book_value_start = $initialValue;
for ($i = 0; $i < $life; $i++) {
    $year = $acquisitionYear + $i;
    $depreciation_expense = $initialValue * ($depreciation_rate);
    $accumulated_depreciation = ($depreciation_expense * ($i + 1));
    $book_value_end = $book_value_start - $depreciation_expense;

    // Store results in an array
    $depreciationResults[$year] = array(
        'book_value_start' => $book_value_start,
        'depreciation_rate' => $depreciation_rate,
        'depreciation_expense' => $depreciation_expense,
        'accumulated_depreciation' => $accumulated_depreciation,
        'book_value_end' => $book_value_end,
    );

    // Update book value start for next year
    $book_value_start = $book_value_end;
}

// Filter results based on selected year range
$filteredResults = array_filter($depreciationResults, function($year) use ($startYear, $endYear) {
    return ($year >= $startYear && $year <= $endYear);
}, ARRAY_FILTER_USE_KEY);

// Output filtered results
foreach ($filteredResults as $year => $result) {
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
    // // Update book value for the next iteration
    // $book_value_start = $book_value_end;

    // // Stop if book value reaches salvage value
    // if ($book_value_start <= 0) {
    //     break;
    // }
//}

echo "</tbody>";
echo "</table>";
echo "</div>";

// Monthly Breakdown Modals
$book_value_start = $additions;
$accumulated_depreciation = 0;

for ($i = 0; $i < $life; $i++) {
    $year = $acquisitionYear + $i;

    // Skip years outside the selected range
    if ($year < $startYear || $year > $endYear) {
        continue;
    }

    // Calculate depreciation for the year
    $depreciation_expense = $additions * ($depreciation_rate);
    $accumulated_depreciation += $depreciation_expense;

    echo "<div class='modal fade' id='modal$year' tabindex='-1' aria-labelledby='modalLabel$year' aria-hidden='true'>";
echo "<div class='modal-dialog modal-lg'>";
echo "<div class='modal-content'>";
echo "<div class='modal-header'>";
echo "<h5 class='modal-title' id='modalLabel$year'>Monthly Breakdown for $year</h5>";
echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
echo "</div>";
echo "<div class='modal-body'>";
echo "<table class='table table-bordered'>";
echo "<thead><tr><th>Month</th><th>Depreciation Expense</th><th>Accumulated Depreciation</th><th>Book Value End</th></tr></thead>";
echo "<tbody>";

$monthly_depreciation = $depreciation_expense / 12;
$monthly_accumulated = $accumulated_depreciation - $depreciation_expense;
$monthly_book_value = $book_value_start;

// Start capturing the monthly data
$monthly_data = []; 

for ($month = 1; $month <= 12; $month++) {
    $monthly_accumulated += $monthly_depreciation;
    $monthly_book_value -= $monthly_depreciation;

    // Add to the array
    $monthly_data[] = [
        'month' => $months[str_pad($month, 2, '0', STR_PAD_LEFT)],
        'depreciation' => $monthly_depreciation,
        'accumulated' => $monthly_accumulated,
        'book_value' => $monthly_book_value,
    ];

    echo "<tr>";
    echo "<td>" . $months[str_pad($month, 2, '0', STR_PAD_LEFT)] . "</td>";
    echo "<td>GH₵" . number_format($monthly_depreciation, 2) . "</td>";
    echo "<td>GH₵" . number_format($monthly_accumulated, 2) . "</td>";
    echo "<td>GH₵" . number_format($monthly_book_value, 2) . "</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";
echo "<div class='modal-footer'>";
echo "<form method='get' action='csv.php'>"; // Change method to POST for security
echo "<input type='hidden' name='asset_id' value='{$assetId}'>";
echo "<input type='hidden' name='year' value='$year'>"; // Pass the selected year

// Pass monthly data as JSON
echo "<input type='hidden' name='monthly_data' value='$assetId'" . htmlspecialchars(json_encode($monthly_data), ENT_QUOTES, 'UTF-8') . "'>";
echo "<button type='submit' class='btn btn-primary'>Export to CSV</button>";
echo "</form>";
echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

$book_value_start -= $depreciation_expense;

if ($book_value_start <= 0) {
    break;
}
}

// Add a button to trigger the external CSV export functionality
echo "<form method='get' action='csv.php'>";
echo "<input type='hidden' name='asset_id' value='{$assetId}'>";
echo "<button type='submit' name='export_csv' class='btn btn-primary'>Export to CSV</button>";
echo "</form>";

echo "</div>";

mysqli_close($conn);
?>
