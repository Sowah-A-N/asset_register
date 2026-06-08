<?php
/**
 * FILE: schedule_officer/reports/quarterly/compute.php
 * PURPOSE: Shared, side-effect-free computation for the Quarterly Depreciation
 *          report (per class, Q1–Q4 + year total, USD headline + GHS).
 *          Included by index.php (render) and export.php (.xlsx).
 * REQUIRES: $conn, ../lib/depreciation.php
 * EXPORTS:  $selectedYear, $currentYear, $activeRate, $rows, $grand,
 *           $grandGhsYear, $grandUsdYear  (+ helper quarters())
 */

$currentYear  = (int)date('Y');
$selectedYear = (int)($_GET['year'] ?? $currentYear);

$rateRow    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
$activeRate = $rateRow ? (float)$rateRow['dollar_rate'] : 0;

/* class meta */
$classMeta = [];
$cr = mysqli_query($conn, "SELECT asset_class, estimated_life, depreciated FROM asset_classes");
while ($row = mysqli_fetch_assoc($cr)) $classMeta[rtrim($row['asset_class'])] = ['name'=>trim($row['asset_class']),'life'=>(int)$row['estimated_life'],'dep'=>(bool)(int)$row['depreciated']];

/* pools (base year) */
$pools = [];
$pr = mysqli_query($conn, "SELECT asset_class, opening_balance, total_accum_depr_start, expected_life_months, depreciated, rate FROM asset_class_opbal_year WHERE year=".RMU_POOL_BASE_YEAR);
while ($row = mysqli_fetch_assoc($pr)) { if ((float)$row['opening_balance'] <= 0) continue; $pools[rtrim($row['asset_class'])] = $row; }

/* individual assets (in-service date drives depreciation) */
$assetsByClass = [];
$ar = mysqli_query($conn,
    "SELECT a.asset_class, a.additions, a.active_res_value, a.dollar_rate_used,
            COALESCE(a.in_service_date, a.acquisition_date) AS acquisition_date,
            d.date_of_disposal AS disposal_date
     FROM assets a LEFT JOIN disposals d ON d.serial_number = a.serial_number
     WHERE COALESCE(a.in_service_date, a.acquisition_date) NOT IN ('0000-00-00') AND a.acquisition_date IS NOT NULL");
while ($row = mysqli_fetch_assoc($ar)) $assetsByClass[rtrim($row['asset_class'])][] = $row;

/* helper: monthly[1..12] -> [Q1,Q2,Q3,Q4] */
function quarters(array $m): array {
    return [
        ($m[1]??0)+($m[2]??0)+($m[3]??0),
        ($m[4]??0)+($m[5]??0)+($m[6]??0),
        ($m[7]??0)+($m[8]??0)+($m[9]??0),
        ($m[10]??0)+($m[11]??0)+($m[12]??0),
    ];
}

$classKeys = array_unique(array_merge(array_keys($pools), array_keys($assetsByClass)));
$rows = [];
$grand = ['ghs'=>[0,0,0,0], 'usd'=>[0,0,0,0]];
$untracked = rmu_load_untracked_disposals($conn);   // untracked pool disposals by class

foreach ($classKeys as $key) {
    $name = $pools[$key]['name'] ?? ($classMeta[$key]['name'] ?? trim($key));
    $ghsMonthly = array_fill(1,12,0.0);
    $usdMonthly = array_fill(1,12,0.0);

    if (isset($assetsByClass[$key])) {
        $meta = $classMeta[$key] ?? ['life'=>0,'dep'=>true];
        $g = rmu_depreciate_class($assetsByClass[$key], $meta['life'], $meta['dep'], $selectedYear)['totals']['monthly'];
        $u = rmu_depreciate_class_usd($assetsByClass[$key], $meta['life'], $meta['dep'], $selectedYear, $activeRate)['totals']['monthly'];
        for ($m=1;$m<=12;$m++){ $ghsMonthly[$m]+=$g[$m]; $usdMonthly[$m]+=$u[$m]; }
    }
    if (isset($pools[$key])) {
        $p = $pools[$key];
        $pg = rmu_depreciate_pool((float)$p['opening_balance'],(float)$p['total_accum_depr_start'],(int)$p['expected_life_months'],(bool)(int)$p['depreciated'],$selectedYear);
        if (!empty($untracked[$key]))   // derecognise untracked disposals before quarter split
            $pg = rmu_apply_pool_delta($pg, rmu_untracked_disposal_delta($untracked[$key], max(1,(int)round((int)$p['expected_life_months']/12)), $selectedYear, (bool)(int)$p['depreciated']));
        $rate = (float)$p['rate']; if ($rate < 1) $rate = $activeRate;
        $pu = rmu_convert_position($pg, $rate);
        for ($m=1;$m<=12;$m++){ $ghsMonthly[$m]+=$pg['monthly_breakdown'][$m]; $usdMonthly[$m]+=$pu['monthly_breakdown'][$m]; }
    }

    $gq = quarters($ghsMonthly); $uq = quarters($usdMonthly);
    if (array_sum($gq) <= 0 && array_sum($uq) <= 0) continue;
    $rows[] = ['name'=>$name,'ghs'=>$gq,'usd'=>$uq,'ghsYear'=>array_sum($gq),'usdYear'=>array_sum($uq)];
    for ($q=0;$q<4;$q++){ $grand['ghs'][$q]+=$gq[$q]; $grand['usd'][$q]+=$uq[$q]; }
}
usort($rows, fn($a,$b)=>strcmp($a['name'],$b['name']));
$grandGhsYear = array_sum($grand['ghs']); $grandUsdYear = array_sum($grand['usd']);
