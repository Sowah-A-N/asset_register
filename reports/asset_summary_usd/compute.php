<?php
/**
 * FILE: schedule_officer/reports/asset_summary_usd/compute.php
 * PURPOSE: Shared, side-effect-free computation for the USD Asset Summary
 *          (one row per class, pool + individual, IAS 21 historical rate).
 *          Included by index.php (render) and export.php (.xlsx).
 * REQUIRES: $conn, ../lib/depreciation.php
 * EXPORTS:  $selectedYear, $currentYear, $activeRate, $rows, $grand, $anySub
 */

$currentYear  = (int)date('Y');
$selectedYear = (int)($_GET['year'] ?? $currentYear);

$rateRow    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE' LIMIT 1"));
$activeRate = $rateRow ? (float)$rateRow['dollar_rate'] : 0;

// Class meta (individual-asset life/dep)
$classMeta = [];
$cr = mysqli_query($conn, "SELECT asset_class, estimated_life, depreciated FROM asset_classes");
while ($row = mysqli_fetch_assoc($cr)) {
    $classMeta[rtrim($row['asset_class'])] = ['name'=>trim($row['asset_class']),'life'=>(int)$row['estimated_life'],'dep'=>(bool)(int)$row['depreciated']];
}

// Legacy pools (base year) with their stored historical rate
$pools = [];
$pr = mysqli_query($conn,
    "SELECT asset_class, opening_balance, total_accum_depr_start, expected_life_months, depreciated, rate
     FROM asset_class_opbal_year WHERE year = " . RMU_POOL_BASE_YEAR);
while ($row = mysqli_fetch_assoc($pr)) {
    if ((float)$row['opening_balance'] <= 0) continue;
    $pools[rtrim($row['asset_class'])] = [
        'name'=>trim($row['asset_class']),'opening'=>(float)$row['opening_balance'],
        'accum'=>(float)$row['total_accum_depr_start'],'elm'=>(int)$row['expected_life_months'],
        'dep'=>(bool)(int)$row['depreciated'],'rate'=>(float)$row['rate'],
    ];
}

// Individual assets grouped by class
$assetsByClass = [];
$ar = mysqli_query($conn,
    "SELECT a.asset_class, a.additions, a.active_res_value, COALESCE(a.in_service_date, a.acquisition_date) AS acquisition_date, a.dollar_rate_used,
            d.date_of_disposal AS disposal_date
     FROM assets a LEFT JOIN disposals d ON d.serial_number = a.serial_number
     WHERE a.acquisition_date IS NOT NULL AND a.acquisition_date <> '0000-00-00'");
while ($row = mysqli_fetch_assoc($ar)) $assetsByClass[rtrim($row['asset_class'])][] = $row;

// One USD row per class = pool(USD) + individual(USD)
$classKeys = array_unique(array_merge(array_keys($pools), array_keys($assetsByClass)));
$rows = [];
$grand = ['count'=>0,'cost'=>0.0,'accum_start'=>0.0,'charge'=>0.0,'accum_end'=>0.0,'nbv'=>0.0];
$anySub = false;
$untracked = rmu_load_untracked_disposals($conn);   // untracked pool disposals by class

foreach ($classKeys as $key) {
    $name = $pools[$key]['name'] ?? ($classMeta[$key]['name'] ?? trim($key));
    $cost=$accumStart=$charge=$accumEnd=$nbv=0.0; $count=0; $wip=false; $sub=false;

    if (isset($pools[$key])) {
        $p = $pools[$key]; $wip = !$p['dep'];
        $ghs = rmu_depreciate_pool($p['opening'],$p['accum'],$p['elm'],$p['dep'],$selectedYear);
        if (!empty($untracked[$key]))   // derecognise untracked disposals (GHS) before translation
            $ghs = rmu_apply_pool_delta($ghs, rmu_untracked_disposal_delta($untracked[$key], max(1,(int)round($p['elm']/12)), $selectedYear, $p['dep']));
        $rate = $p['rate']; if ($rate < 1) { $rate = $activeRate; $sub = true; $anySub = true; }
        $u = rmu_convert_position($ghs, $rate);
        $cost+=$u['depreciable_base']; $accumStart+=$u['accumulated_start']; $charge+=$u['depreciation_expense'];
        $accumEnd+=$u['accumulated_end']; $nbv+=$u['nbv_end'];
    }
    if (isset($assetsByClass[$key])) {
        $meta = $classMeta[$key] ?? ['life'=>0,'dep'=>true];
        $o = rmu_depreciate_class_usd($assetsByClass[$key], $meta['life'], $meta['dep'], $selectedYear, $activeRate);
        $t = $o['totals'];
        $cost+=$t['cost']; $accumStart+=$t['accumulated_start']; $charge+=$t['expense'];
        $accumEnd+=$t['accumulated_end']; $nbv+=$t['nbv_end']; $count+=count($o['lines']);
        foreach ($o['lines'] as $ln) if ($ln['rate_substituted']) { $sub=true; $anySub=true; }
    }
    if ($cost <= 0 && $count === 0) continue;

    $rows[] = ['name'=>$name,'wip'=>$wip,'sub'=>$sub,'count'=>$count,'cost'=>$cost,
               'accum_start'=>$accumStart,'charge'=>$charge,'accum_end'=>$accumEnd,'nbv'=>$nbv];
    $grand['count']+=$count; $grand['cost']+=$cost; $grand['accum_start']+=$accumStart;
    $grand['charge']+=$charge; $grand['accum_end']+=$accumEnd; $grand['nbv']+=$nbv;
}
usort($rows, fn($a,$b)=>strcmp($a['name'],$b['name']));
