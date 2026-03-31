<?php
require_once '../../init.php';

$assetId = isset($_GET['asset_id']) ? (int)$_GET['asset_id'] : 0;
$year    = isset($_GET['year'])     ? (int)$_GET['year']     : null;

if ($assetId <= 0) {
    http_response_code(400);
    die('Invalid asset ID.');
}

// Fetch asset details
$stmt = mysqli_prepare($conn, "SELECT * FROM assets WHERE asset_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $assetId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$rowAsset = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$rowAsset) {
    http_response_code(404);
    die('Asset not found.');
}

// Fetch asset class details
$assetClass = $rowAsset['asset_class'];
$stmt2 = mysqli_prepare($conn, "SELECT opening_bal, opbal_plus_additions, estimated_life, dep_rate FROM asset_classes WHERE asset_class = ? LIMIT 1");
mysqli_stmt_bind_param($stmt2, 's', $assetClass);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);
$rowAssetClasses = mysqli_fetch_assoc($res2);
mysqli_stmt_close($stmt2);

// Asset calculations
$acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
$additions       = (float)$rowAsset['additions'];
$life            = (int)($rowAssetClasses['estimated_life'] ?? 0);
$depRate         = (float)($rowAssetClasses['dep_rate'] ?? 0);

$book_value_start        = $additions;
$accumulated_depreciation = 0;
$salvage                 = 0;

if ($year) {
    $monthly_depreciation = ($book_value_start * $depRate) / 12;
    $monthlyScheduleData  = [];
    $accumulated_depreciation_year = 0;

    for ($i = 0; $i < $life; $i++) {
        $currentYear = (int)$acquisitionYear + $i;
        if ($currentYear == $year) {
            for ($month = 1; $month <= 12; $month++) {
                $accumulated_depreciation_year += $monthly_depreciation;
                $book_value_start -= $monthly_depreciation;
                $monthlyScheduleData[] = [
                    'Month'                    => date('F', mktime(0, 0, 0, $month, 1)),
                    'Depreciation Expense'     => number_format($monthly_depreciation, 2),
                    'Accumulated Depreciation' => number_format($accumulated_depreciation_year, 2),
                    'Book Value End'           => number_format($book_value_start, 2),
                ];
                if ($book_value_start <= $salvage) break 2;
            }
        }
    }

    $filename = "asset_monthly_breakdown_{$year}_" . date('Y-m-d_H-i-s') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Month', 'Depreciation Expense', 'Accumulated Depreciation', 'Book Value End']);
    foreach ($monthlyScheduleData as $dataRow) { fputcsv($output, $dataRow); }
    fclose($output);
    exit;
}

// Yearly schedule
$depreciationScheduleData = [];
$book_value_start = $additions;

for ($i = 0; $i < $life; $i++) {
    $yr                   = (int)$acquisitionYear + $i;
    $depreciation_expense = $book_value_start * $depRate;
    $accumulated_depreciation += $depreciation_expense;
    $book_value_end       = $book_value_start - $depreciation_expense;

    $depreciationScheduleData[] = [
        'Year'                    => $yr,
        'Net Book Value'          => number_format($book_value_start, 2),
        'Depreciation Rate'       => $depRate,
        'Depreciation Expense'    => number_format($depreciation_expense, 2),
        'Accumulated Depreciation'=> number_format($accumulated_depreciation, 2),
        'Closing Carrying Value'  => number_format($book_value_end, 2),
    ];

    $book_value_start = $book_value_end;
    if ($book_value_start <= $salvage) break;
}

$filename = "asset_depreciation_" . date('Y-m-d_H-i-s') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$output = fopen('php://output', 'w');
fputcsv($output, ['Year', 'Net Book Value', 'Depreciation Rate', 'Depreciation Expense', 'Accumulated Depreciation', 'Closing Carrying Value']);
foreach ($depreciationScheduleData as $dataRow) { fputcsv($output, $dataRow); }
fclose($output);
exit;
