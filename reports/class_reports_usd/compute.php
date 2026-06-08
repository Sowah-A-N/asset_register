<?php
/**
 * FILE: schedule_officer/reports/class_reports_usd/compute.php
 * PURPOSE: Shared, side-effect-free computation for the USD Class Depreciation
 *          Schedule (IAS 21 historical rate per item). Included by index.php
 *          (render) and export.php (.xlsx) so both show identical live figures.
 * REQUIRES: $conn, ../lib/depreciation.php
 */

$selectedClass = trim($_GET['asset_class'] ?? '');
$selectedYear  = (int)($_GET['year'] ?? 0);
$currentYear   = (int)date('Y');

// Active dollar rate (fallback for missing/invalid historical rates)
$rateRow    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
$activeRate = $rateRow ? (float)$rateRow['dollar_rate'] : 0;

$classes = [];
$cr = mysqli_query($conn,
    "SELECT TRIM(asset_class) AS c FROM asset_classes
     UNION
     SELECT TRIM(asset_class) FROM asset_class_opbal_year
       WHERE year = " . RMU_POOL_BASE_YEAR . " AND opening_balance > 0
     ORDER BY c ASC");
while ($row = mysqli_fetch_assoc($cr)) $classes[] = $row['c'];

$report = null; $pool = null; $classMeta = null; $combined = null; $poolRateSub = false; $poolRate = 0;

if ($selectedClass !== '' && $selectedYear > 0) {
    $escClass = mysqli_real_escape_string($conn, $selectedClass);

    $classMeta = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT asset_class, estimated_life, depreciated FROM asset_classes WHERE TRIM(asset_class)='$escClass' LIMIT 1"));

    $assets = [];
    $ar = mysqli_query($conn,
        "SELECT a.asset_name, a.serial_number, a.location, a.dollar_rate_used,
                a.additions, a.active_res_value, COALESCE(a.in_service_date, a.acquisition_date) AS acquisition_date,
                d.date_of_disposal AS disposal_date
         FROM assets a LEFT JOIN disposals d ON d.serial_number = a.serial_number
         WHERE TRIM(a.asset_class)='$escClass' AND a.acquisition_date IS NOT NULL
           AND a.acquisition_date <> '0000-00-00'
         ORDER BY a.acquisition_date ASC");
    while ($row = mysqli_fetch_assoc($ar)) $assets[] = $row;

    $report = rmu_depreciate_class_usd(
        $assets, (int)($classMeta['estimated_life'] ?? 0),
        (bool)(int)($classMeta['depreciated'] ?? 1), $selectedYear, $activeRate);

    $prow = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT opening_balance, total_accum_depr_start, expected_life_months, depreciated, rate
         FROM asset_class_opbal_year WHERE TRIM(asset_class)='$escClass' AND year=" . RMU_POOL_BASE_YEAR . " LIMIT 1"));
    if ($prow && (float)$prow['opening_balance'] > 0) {
        $poolGhs  = rmu_depreciate_pool((float)$prow['opening_balance'], (float)$prow['total_accum_depr_start'],
                                        (int)$prow['expected_life_months'], (bool)(int)$prow['depreciated'], $selectedYear);

        // Remove untracked-asset disposals (GHS) before USD translation.
        $ud = [];
        $udq = mysqli_query($conn, "SELECT value, acquisition_date, date_of_disposal FROM untracked_asset_disposals WHERE TRIM(class) = '$escClass'");
        if ($udq) while ($u = mysqli_fetch_assoc($udq)) $ud[] = $u;
        if ($ud) {
            $lifeY   = max(1, (int)round((int)$prow['expected_life_months'] / 12));
            $poolGhs = rmu_apply_pool_delta($poolGhs, rmu_untracked_disposal_delta($ud, $lifeY, $selectedYear, (bool)(int)$prow['depreciated']));
        }

        $poolRate = (float)$prow['rate'];
        if ($poolRate < 1) { $poolRate = $activeRate; $poolRateSub = true; }
        $pool = rmu_convert_position($poolGhs, $poolRate);
    }

    $it = $report['totals'];
    $combined = [
        'cost'=>$it['cost']+($pool['depreciable_base']??0), 'base'=>$it['depreciable_base']+($pool['depreciable_base']??0),
        'accum_start'=>$it['accumulated_start']+($pool['accumulated_start']??0),
        'expense'=>$it['expense']+($pool['depreciation_expense']??0),
        'accum_end'=>$it['accumulated_end']+($pool['accumulated_end']??0),
        'nbv'=>$it['nbv_end']+($pool['nbv_end']??0), 'monthly'=>[],
    ];
    for ($m=1;$m<=12;$m++) $combined['monthly'][$m] = $it['monthly'][$m] + ($pool['monthly_breakdown'][$m] ?? 0);
}

$hasData = ($report && !empty($report['lines'])) || $pool;
