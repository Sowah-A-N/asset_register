<?php
// Include database connection
include "../datacon.php";

// Get asset ID and year from URL parameters
$assetId = $_GET['asset_id'];
$year = isset($_GET['year']) ? $_GET['year'] : null;

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

// Asset details and calculations
$acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
$additions = $rowAsset['additions'];
$life = $rowAssetClasses['estimated_life'];
$depreciation_rate = $rowAssetClasses['dep_rate'];

$book_value_start = $additions;
$accumulated_depreciation = 0;
$salvage = 0;

// Check if exporting monthly breakdown or yearly schedule
if ($year) {
    // Monthly breakdown calculations for the selected year
    $monthly_depreciation = ($book_value_start * $depreciation_rate) / 12;
    $monthlyScheduleData = [];
    $accumulated_depreciation_year = $accumulated_depreciation;

    for ($i = 0; $i < $life; $i++) {
        $currentYear = $acquisitionYear + $i;

        if ($currentYear == $year) {
            for ($month = 1; $month <= 12; $month++) {
                $accumulated_depreciation_year += $monthly_depreciation;
                $book_value_start -= $monthly_depreciation;

                $monthlyScheduleData[] = [
                    'Month' => date('F', mktime(0, 0, 0, $month, 1)),
                    'Depreciation Expense' => number_format($monthly_depreciation, 2),
                    'Accumulated Depreciation' => number_format($accumulated_depreciation_year, 2),
                    'Book Value End' => number_format($book_value_start, 2)
                ];

                // Stop if book value reaches salvage value
                if ($book_value_start <= $salvage) {
                    break 2;
                }
            }
        }
    }

    // Generate Monthly Breakdown CSV
    $filename = "asset_monthly_breakdown_$year_" . date('Y-m-d_H-i-s') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Monthly Breakdown Header
    fputcsv($output, ['Month', 'Depreciation Expense', 'Accumulated Depreciation', 'Book Value End']);
    foreach ($monthlyScheduleData as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

// Yearly schedule calculations
$depreciationScheduleData = [];
$book_value_start = $additions;

for ($i = 0; $i < $life; $i++) {
    $year = $acquisitionYear + $i;
    $depreciation_expense = $book_value_start * $depreciation_rate;
    $accumulated_depreciation += $depreciation_expense;
    $book_value_end = $book_value_start - $depreciation_expense;

    $depreciationScheduleData[] = [
        'Year' => $year,
        'Net Book Value' => number_format($book_value_start, 2),
        'Depreciation Rate' => $depreciation_rate,
        'Depreciation Expense' => number_format($depreciation_expense, 2),
        'Accumulated Depreciation' => number_format($accumulated_depreciation, 2),
        'Closing Carrying Value' => number_format($book_value_end, 2)
    ];

    $book_value_start = $book_value_end;
    if ($book_value_start <= $salvage) {
        break;
    }
}

// Generate Yearly Schedule CSV
$filename = "asset_depreciation_" . date('Y-m-d_H-i-s') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Yearly Schedule Header
fputcsv($output, ['Year', 'Net Book Value', 'Depreciation Rate', 'Depreciation Expense', 'Accumulated Depreciation', 'Closing Carrying Value']);
foreach ($depreciationScheduleData as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit;
