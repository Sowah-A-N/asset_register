<?php
/**
 * FILE: schedule_officer/reports/asset_summary/compute.php
 * PURPOSE: Shared, side-effect-free computation for the GHS Asset Summary
 *          (one row per class, pool + individual). Included by index.php
 *          (render) and export.php (.xlsx) for identical live figures.
 * REQUIRES: $conn, ../lib/depreciation.php
 * EXPORTS:  $selectedYear, $currentYear, $rows, $grand
 */

$currentYear  = (int)date('Y');
$selectedYear = (int)($_GET['year'] ?? $currentYear);

// Class metadata (individual-asset life/dep), keyed by trimmed name
$classMeta = [];
$cr = mysqli_query($conn, "SELECT asset_class, estimated_life, depreciated FROM asset_classes ORDER BY asset_class ASC");
while ($row = mysqli_fetch_assoc($cr)) {
    $classMeta[rtrim($row['asset_class'])] = [
        'name' => trim($row['asset_class']),
        'life' => (int)$row['estimated_life'],
        'dep'  => (bool)(int)$row['depreciated'],
    ];
}

// Legacy pools (brought-forward opening balances), base year only
$pools = [];
$pr = mysqli_query($conn,
    "SELECT asset_class, opening_balance, total_accum_depr_start, expected_life_months, depreciated
     FROM asset_class_opbal_year WHERE year = " . RMU_POOL_BASE_YEAR);
while ($row = mysqli_fetch_assoc($pr)) {
    $k = rtrim($row['asset_class']);
    if ((float)$row['opening_balance'] <= 0) continue;
    $pools[$k] = [
        'name'    => trim($row['asset_class']),
        'opening' => (float)$row['opening_balance'],
        'accum'   => (float)$row['total_accum_depr_start'],
        'elm'     => (int)$row['expected_life_months'],
        'dep'     => (bool)(int)$row['depreciated'],
    ];
}

// Individually-tracked assets grouped by class
$assetsByClass = [];
$ar = mysqli_query($conn,
    "SELECT a.asset_class, a.additions, a.active_res_value, COALESCE(a.in_service_date, a.acquisition_date) AS acquisition_date,
            d.date_of_disposal AS disposal_date
     FROM assets a
     LEFT JOIN disposals d ON d.serial_number = a.serial_number
     WHERE a.acquisition_date IS NOT NULL AND a.acquisition_date <> '0000-00-00'");
while ($row = mysqli_fetch_assoc($ar)) {
    $assetsByClass[rtrim($row['asset_class'])][] = $row;
}

// One row per class = legacy pool + individual additions (combined)
$classKeys = array_unique(array_merge(array_keys($pools), array_keys($assetsByClass)));
$rows = [];
$grand = ['count'=>0,'cost'=>0.0,'additions'=>0.0,'disposals'=>0.0,
          'accum_start'=>0.0,'charge'=>0.0,'accum_end'=>0.0,'nbv'=>0.0];

$untracked = rmu_load_untracked_disposals($conn);   // untracked pool disposals by class

foreach ($classKeys as $key) {
    $name = $pools[$key]['name'] ?? ($classMeta[$key]['name'] ?? trim($key));

    $cost=$additions=$disposals=$accumStart=$charge=$accumEnd=$nbv=0.0;
    $count=0; $wipPool=false; $hasPool=false;

    if (isset($pools[$key])) {
        $p = $pools[$key];
        $hasPool = true;
        $wipPool = !$p['dep'];
        $pos = rmu_depreciate_pool($p['opening'], $p['accum'], $p['elm'], $p['dep'], $selectedYear);
        // Derecognise untracked-asset disposals from this class's pool.
        $poolCost = $p['opening'];
        if (!empty($untracked[$key])) {
            $delta = rmu_untracked_disposal_delta($untracked[$key], max(1,(int)round($p['elm']/12)), $selectedYear, $p['dep']);
            $pos = rmu_apply_pool_delta($pos, $delta);
            $poolCost  += $delta['depreciable_base'];   // cost removed
            $disposals += $delta['disposed_value'];     // surface in the Disposals column
        }
        $cost       += $poolCost;
        $accumStart += $pos['accumulated_start'];
        $charge     += $pos['depreciation_expense'];
        $accumEnd   += $pos['accumulated_end'];
        $nbv        += $pos['nbv_end'];
    }

    if (isset($assetsByClass[$key])) {
        $meta = $classMeta[$key] ?? ['life'=>0,'dep'=>true];
        $out  = rmu_depreciate_class($assetsByClass[$key], $meta['life'], $meta['dep'], $selectedYear);
        $t    = $out['totals'];
        $cost       += $t['cost'];
        $accumStart += $t['accumulated_start'];
        $charge     += $t['expense'];
        $accumEnd   += $t['accumulated_end'];
        $nbv        += $t['nbv_end'];
        $count      += count($out['lines']);
        foreach ($out['lines'] as $ln) {
            $acqYear = (int)date('Y', strtotime($ln['asset']['acquisition_date']));
            if ($acqYear === $selectedYear) $additions += (float)$ln['asset']['additions'];
            if ($ln['disposed_in_year'])    $disposals += (float)$ln['asset']['additions'];
        }
    }

    if ($cost <= 0 && $count === 0) continue;

    $rows[] = [
        'name'=>$name, 'wip'=>$wipPool, 'has_pool'=>$hasPool, 'count'=>$count,
        'cost'=>$cost, 'additions'=>$additions, 'disposals'=>$disposals,
        'accum_start'=>$accumStart, 'charge'=>$charge, 'accum_end'=>$accumEnd, 'nbv'=>$nbv,
    ];
    $grand['count']+=$count; $grand['cost']+=$cost; $grand['additions']+=$additions;
    $grand['disposals']+=$disposals; $grand['accum_start']+=$accumStart;
    $grand['charge']+=$charge; $grand['accum_end']+=$accumEnd; $grand['nbv']+=$nbv;
}

usort($rows, fn($a,$b) => strcmp($a['name'], $b['name']));
