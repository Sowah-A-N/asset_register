<?php
/** reports/quarterly/pdf.php — Branded PDF of the Quarterly report (USD + GHS). */
require_once '../../auth.php';
requirePermission('report.view', '../../login/');
include '../datacon.php';
require_once '../lib/depreciation.php';
require __DIR__ . '/compute.php';
require_once '../lib/report_pdf.php';

if (!$rows) { header('Location: index.php?year=' . $selectedYear); exit; }
function n($v) { return number_format((float)$v, 2, '.', ','); }

/** Build one currency table. $cur = 'usd' | 'ghs'. */
function rmu_q_table(string $cur, string $heading, array $rows, array $grand): string {
    $yk = $cur . 'Year';
    $h  = '<h4 style="font-size:9px;color:#1E3A5F;">' . $heading . '</h4>';
    $h .= '<table border="0.4" cellpadding="4" style="font-size:8.5px;"><thead><tr style="' . RMU_PDF_THEAD . '">'
        . '<th>S/N</th><th>Asset Class</th><th>Q1 (Jan-Mar)</th><th>Q2 (Apr-Jun)</th><th>Q3 (Jul-Sep)</th><th>Q4 (Oct-Dec)</th><th>Year Total</th></tr></thead><tbody>';
    $sn = 1;
    foreach ($rows as $r) {
        $h .= '<tr><td>' . ($sn++) . '</td><td>' . htmlspecialchars($r['name']) . '</td>'
            . '<td align="right">' . n($r[$cur][0]) . '</td><td align="right">' . n($r[$cur][1]) . '</td>'
            . '<td align="right">' . n($r[$cur][2]) . '</td><td align="right">' . n($r[$cur][3]) . '</td>'
            . '<td align="right">' . n($r[$yk]) . '</td></tr>';
    }
    $h .= '<tr style="' . RMU_PDF_TFOOT . '"><td colspan="2">TOTAL</td>'
        . '<td align="right">' . n($grand[$cur][0]) . '</td><td align="right">' . n($grand[$cur][1]) . '</td>'
        . '<td align="right">' . n($grand[$cur][2]) . '</td><td align="right">' . n($grand[$cur][3]) . '</td>'
        . '<td align="right">' . n(array_sum($grand[$cur])) . '</td></tr></tbody></table>';
    return $h;
}

$h  = rmu_q_table('usd', $selectedYear . ' Quarterly Depreciation — USD (IAS 21)', $rows, $grand);
$h .= '<br>' . rmu_q_table('ghs', $selectedYear . ' Quarterly Depreciation — GHS', $rows, $grand);

rmu_report_pdf(
    ['REGIONAL MARITIME UNIVERSITY', $selectedYear . ' QUARTERLY DEPRECIATION', 'PER CLASS - Q1 to Q4 (USD + GHS)'],
    $h, 'Quarterly Depreciation - ' . $selectedYear . '.pdf', 'L'
);
