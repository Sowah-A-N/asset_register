<?php

require_once '../../init.php';
include "functions.php";

if (isset($_POST['summaryYear'])) {
    $summaryYear = ($_POST['summaryYear']);
    $assetClassSummary = assetClassSummary($summaryYear);
    ?>
    <?php
// Initialize totals
$totals = [
    'opening_balance' => 0,
    'total_additions_cedi' => 0,
    'total_disposals_cedi' => 0,
    'total_accum_depr_start' => 0,
    'total_depr_year_charge' => 0,
    'disposals_depr' => 0,
    'total_accum_depr_end' => 0,
    'net_book_value' => 0,
];
?>

<table class="table table-bordered table-striped">
    <thead class="table-primary">
        <tr>
            <th rowspan="2">Asset Class</th>
            <th rowspan="2">Year</th>

            <th colspan="4" class="text-center">Cost</th>
            <th colspan="4" class="text-center">Depreciation</th>
            <th rowspan="2" class="text-center">Net Book Value</th>

            <th rowspan="2">Expected Life (Months)</th>
            <th rowspan="2">Rate (%)</th>
            <th rowspan="2">Depreciated</th>
        </tr>
        <tr>
            <th>Opening Balance</th>
            <th>Reevaluation Adjustment</th>
            <th>Additions (GHS)</th>
            <th>Disposals (GHS)</th>

            <th>Accum. Depr (Start)</th>
            <th>Depr. Charge (Year)</th>
            <th>Depr. on Disposals</th>
            <th>Accum. Depr (End)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($assetClassSummary as $assetClass) { 
            // Update totals
            $totals['opening_balance'] += $assetClass['opening_balance'];
            $totals['total_additions_cedi'] += $assetClass['total_additions_cedi'];
            $totals['total_disposals_cedi'] += $assetClass['total_disposals_cedi'];
            $totals['total_accum_depr_start'] += $assetClass['total_accum_depr_start'];
            $totals['total_depr_year_charge'] += $assetClass['total_depr_year_charge'];
            $totals['disposals_depr'] += $assetClass['disposals_depr'];
            $totals['total_accum_depr_end'] += $assetClass['total_accum_depr_end'];
            $totals['net_book_value'] += $assetClass['net_book_value'];
        ?>
            <tr>
                <td><?php echo $assetClass['asset_class']; ?></td>
                <td><?php echo $assetClass['year']; ?></td>

                <td><?php echo number_format($assetClass['opening_balance'], 2); ?></td>
                <td>-</td>
                <td><?php echo number_format($assetClass['total_additions_cedi'], 2); ?></td>
                <td><?php echo number_format($assetClass['total_disposals_cedi'], 2); ?></td>

                <td><?php echo number_format($assetClass['total_accum_depr_start'], 2); ?></td>
                <td><?php echo number_format($assetClass['total_depr_year_charge'], 2); ?></td>
                <td><?php echo number_format($assetClass['disposals_depr'], 2); ?></td>
                <td><?php echo number_format($assetClass['total_accum_depr_end'], 2); ?></td>

                <td><?php echo number_format($assetClass['net_book_value'], 2); ?></td>

                <td><?php echo $assetClass['expected_life_months']; ?></td>
                <td><?php echo $assetClass['rate']; ?></td>
                <td><?php echo $assetClass['depreciated'] ? 'Yes' : 'No'; ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot class="table-secondary fw-bold">
        <tr>
            <td colspan="2" class="text-end">Total</td>

            <td><?php echo number_format($totals['opening_balance'], 2); ?></td>
            <td>-</td>
            <td><?php echo number_format($totals['total_additions_cedi'], 2); ?></td>
            <td><?php echo number_format($totals['total_disposals_cedi'], 2); ?></td>

            <td><?php echo number_format($totals['total_accum_depr_start'], 2); ?></td>
            <td><?php echo number_format($totals['total_depr_year_charge'], 2); ?></td>
            <td><?php echo number_format($totals['disposals_depr'], 2); ?></td>
            <td><?php echo number_format($totals['total_accum_depr_end'], 2); ?></td>

            <td><?php echo number_format($totals['net_book_value'], 2); ?></td>

            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>

    <?php
}
?>