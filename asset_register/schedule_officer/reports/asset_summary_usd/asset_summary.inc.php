<?php

session_start();
include "./datacon.php";
include "functions.php";

if (isset($_POST['summaryYear'])) {
    $summaryYear = ($_POST['summaryYear']);
    $assetClassSummary = assetClassSummary($summaryYear);
    ?>
    <?php
// Initialize totals
$totals = [
    'open_bal_usd' => 0,
    'total_additions_dollar' => 0,
    'total_disposals_dollar' => 0,
    'total_accum_start_usd' => 0,
    'total_depr_year_charge_usd' => 0,
    'disposals_depr_usd' => 0,
    'total_accum_end_usd' => 0,
    'net_book_value_usd' => 0,
    'closing_carrying_value'=>0,
];
?>

<table class="table table-bordered table-striped">
    <thead class="table-primary">
        <tr>
            <th rowspan="2">Asset Class</th>
            <th rowspan="2">Year</th>

            <th colspan="5" class="text-center">Cost</th>
            <th colspan="3" class="text-center">Depreciation</th>
            <th rowspan="2" class="text-center">Net Book Value</th>

            <!-- <th rowspan="2">Expected Life (Months)</th>
            <th rowspan="2">Rate (%)</th>
            <th rowspan="2">Depreciated</th> -->
        </tr>
        <tr>
            <th>Opening Balance</th>
            <th>Reevaluation Adjustment</th>
            <th>Additions (USD)</th>
            <th>Disposals (USD)</th>
            <th>Closing Balance</th>

            <th>Accum. Depr (Start)</th>
            <th>Depr. Charge (Year)</th>
            <!-- <th>Depr. on Disposals</th> -->
            <th>Accum. Depr (End)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($assetClassSummary as $assetClass) { 

$rate =$assetClass['rate'];
            // Update totals
            $totals['open_bal_usd'] += $assetClass['open_bal_usd'];
            $totals['total_additions_dollar'] += $assetClass['total_additions_dollar'];
            $totals['total_disposals_dollar'] += $assetClass['total_disposals_dollar'];
            $totals['closing_carrying_value'] += ($assetClass['open_bal_usd']+$assetClass['total_additions_dollar']-$assetClass['total_disposals_dollar']);
            $totals['total_accum_start_usd'] += $assetClass['total_accum_start_usd'];
            $totals['total_depr_year_charge_usd'] += $assetClass['total_depr_year_charge_usd'];
            // $totals['disposals_depr_usd'] += $assetClass['disposals_depr_usd'];
            $totals['total_accum_end_usd'] += $assetClass['total_accum_end_usd'];
            $totals['net_book_value_usd'] += $assetClass['net_book_value_usd'];
        ?>

        
            <tr>
                <td><?php echo $assetClass['asset_class']; ?></td>
                <td><?php echo $assetClass['year']; ?></td>

                <td><?php echo number_format(($assetClass['open_bal_usd']), 2); ?></td>
                <td>-</td>
                <td><?php echo number_format($assetClass['total_additions_dollar'], 2); ?></td>
                <td><?php echo number_format($assetClass['total_disposals_dollar'], 2); ?></td>
                

                <td><?php echo number_format(($assetClass['open_bal_usd']+$assetClass['total_additions_dollar']-$assetClass['total_disposals_dollar']), 2); ?></td>

                <td><?php echo number_format(($assetClass['total_accum_start_usd']), 2); ?></td>
                <td><?php echo number_format(($assetClass['total_depr_year_charge_usd'] ), 2); ?></td>
                <!-- <td><?php echo number_format(($assetClass['disposals_depr_usd']), 2); ?></td> -->
                <td><?php echo number_format(($assetClass['total_accum_end_usd']), 2); ?></td>

                <td><?php echo number_format(($assetClass['net_book_value_usd']), 2); ?></td>
<!-- 
                <td><?php echo $assetClass['expected_life_months']; ?></td>
                <td><?php echo $assetClass['rate']; ?></td>
                <td><?php echo $assetClass['depreciated'] ? 'Yes' : 'No'; ?></td> -->
            </tr>
        <?php } ?>
    </tbody>
    <tfoot class="table-secondary fw-bold">
        <tr>
            <td colspan="2" class="text-end">Total</td>

            <td><?php echo number_format($totals['open_bal_usd'], 2); ?></td>
            <td>-</td>
            <td><?php echo number_format($totals['total_additions_dollar'], 2); ?></td>
            <td><?php echo number_format($totals['total_disposals_dollar'], 2); ?></td>
            <td><?php echo number_format($totals['closing_carrying_value'], 2); ?></td>

            <td><?php echo number_format($totals['total_accum_start_usd'], 2); ?></td>
            <td><?php echo number_format($totals['total_depr_year_charge_usd'], 2); ?></td>
            <!-- <td><?php echo number_format($totals['disposals_depr_usd'], 2); ?></td> -->
            <td><?php echo number_format($totals['total_accum_end_usd'], 2); ?></td>

            <td><?php echo number_format($totals['net_book_value_usd'], 2); ?></td>

            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

    <?php
}
?>