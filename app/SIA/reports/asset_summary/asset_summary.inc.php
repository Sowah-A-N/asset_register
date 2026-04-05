<?php

require_once '../../init.php';
include "functions.php";

if (isset($_POST['summaryYear'])) {
    $summaryYear = (int)$_POST['summaryYear'];
    $assetClassSummary = assetClassSummary($summaryYear);
    ?>

    <div class="mb-4 text-center">
        <h4 class="fw-bold text-uppercase mb-0">REGIONAL MARITIME UNIVERSITY</h4>
        <h5 class="fw-bold">FIXED ASSET REGISTER — FINANCIAL YEAR <?php echo htmlspecialchars($summaryYear); ?></h5>
        <p class="text-muted mb-0">Schedule of Property, Plant &amp; Equipment (GH₵)</p>
        <hr>
    </div>

    <?php
    // Initialize totals
    $totals = [
        'nbv_opening'            => 0,
        'total_additions_cedi'   => 0,
        'total_disposals_cedi'   => 0,
        'total_cost'             => 0,
        'total_accum_depr_start' => 0,
        'total_depr_year_charge' => 0,
        'disposals_depr'         => 0,
        'accum_depr_end'         => 0,
        'nbv_end'                => 0,
    ];
    ?>

    <div class="table-responsive">
    <table class="table table-bordered table-striped table-sm" style="font-size:0.82rem">
        <thead class="table-primary">
            <tr>
                <th rowspan="2">Asset Class</th>

                <th colspan="5" class="text-center">Cost (GH₵)</th>
                <th colspan="4" class="text-center">Accumulated Depreciation (GH₵)</th>
                <th rowspan="2" class="text-center">Net Book Value<br>End (GH₵)</th>
                <th rowspan="2">Life<br>(Months)</th>
                <th rowspan="2">Rate (%)</th>
            </tr>
            <tr>
                <th>Opening Balance<br><small class="fw-normal">(NBV at 1 Jan)</small></th>
                <th>Additions</th>
                <th>Revaluation</th>
                <th>Disposals</th>
                <th>Total Cost</th>

                <th>Accum. Depr<br>(Opening)</th>
                <th>Depr. Charge<br>(Year)</th>
                <th>Depr. on<br>Disposals</th>
                <th>Accum. Depr<br>(Closing)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assetClassSummary as $row):
                // ── Derived figures ──────────────────────────────────────────
                $openingCost      = (float)($row['opening_balance']       ?? 0);
                $accumDeprStart   = (float)($row['total_accum_depr_start'] ?? 0);
                $deprCharge       = (float)($row['total_depr_year_charge'] ?? 0);
                $disposalsDepr    = (float)($row['disposals_depr']         ?? 0);
                $additionsCedi    = (float)($row['total_additions_cedi']   ?? 0);
                $disposalsCedi    = (float)($row['total_disposals_cedi']   ?? 0);
                $revaluation      = 0; // No revaluation column yet in DB

                // Issue 6: Opening Balance shown as NBV, not gross cost
                $nbvOpening  = $openingCost - $accumDeprStart;

                // Issue 12: Total Cost = Opening Cost + Additions + Revaluation − Disposals
                $totalCost   = $openingCost + $additionsCedi + $revaluation - $disposalsCedi;

                // Issue 11: Accum Depr End = Opening + Charge − Depr on Disposals
                $accumDeprEnd = $accumDeprStart + $deprCharge - $disposalsDepr;

                // Issue 13: NBV End = Total Cost − Accum Depr End
                $nbvEnd = $totalCost - $accumDeprEnd;
                if ($nbvEnd < 0) $nbvEnd = 0;

                // Update totals
                $totals['nbv_opening']            += $nbvOpening;
                $totals['total_additions_cedi']   += $additionsCedi;
                $totals['total_disposals_cedi']   += $disposalsCedi;
                $totals['total_cost']             += $totalCost;
                $totals['total_accum_depr_start'] += $accumDeprStart;
                $totals['total_depr_year_charge'] += $deprCharge;
                $totals['disposals_depr']         += $disposalsDepr;
                $totals['accum_depr_end']         += $accumDeprEnd;
                $totals['nbv_end']                += $nbvEnd;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['asset_class']); ?></td>

                    <td class="text-end"><?php echo number_format($nbvOpening, 2); ?></td>
                    <td class="text-end"><?php echo number_format($additionsCedi, 2); ?></td>
                    <td class="text-end">-</td>
                    <td class="text-end"><?php echo number_format($disposalsCedi, 2); ?></td>
                    <td class="text-end fw-semibold"><?php echo number_format($totalCost, 2); ?></td>

                    <td class="text-end"><?php echo number_format($accumDeprStart, 2); ?></td>
                    <td class="text-end"><?php echo number_format($deprCharge, 2); ?></td>
                    <td class="text-end"><?php echo number_format($disposalsDepr, 2); ?></td>
                    <td class="text-end"><?php echo number_format($accumDeprEnd, 2); ?></td>

                    <td class="text-end fw-bold"><?php echo number_format($nbvEnd, 2); ?></td>

                    <td class="text-center"><?php echo htmlspecialchars($row['expected_life_months']); ?></td>
                    <td class="text-center"><?php echo number_format((float)$row['rate'] * 100, 0); ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td>TOTAL</td>

                <td class="text-end"><?php echo number_format($totals['nbv_opening'], 2); ?></td>
                <td class="text-end"><?php echo number_format($totals['total_additions_cedi'], 2); ?></td>
                <td class="text-end">-</td>
                <td class="text-end"><?php echo number_format($totals['total_disposals_cedi'], 2); ?></td>
                <td class="text-end"><?php echo number_format($totals['total_cost'], 2); ?></td>

                <td class="text-end"><?php echo number_format($totals['total_accum_depr_start'], 2); ?></td>
                <td class="text-end"><?php echo number_format($totals['total_depr_year_charge'], 2); ?></td>
                <td class="text-end"><?php echo number_format($totals['disposals_depr'], 2); ?></td>
                <td class="text-end"><?php echo number_format($totals['accum_depr_end'], 2); ?></td>

                <td class="text-end"><?php echo number_format($totals['nbv_end'], 2); ?></td>

                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    </div>

    <p class="text-muted small mt-2">
        Report generated on <?php echo date('d M Y, H:i'); ?> &nbsp;&bull;&nbsp;
        Financial Year: <?php echo htmlspecialchars($summaryYear); ?>
    </p>

    <?php
}
?>
