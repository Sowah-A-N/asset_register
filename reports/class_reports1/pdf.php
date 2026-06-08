<?php
/**
 * FILE: reports/class_reports1/pdf.php — Branded PDF of the GHS Class
 * Depreciation Schedule. Same live engine as the page/Excel (compute.php);
 * the monthly columns are omitted for PDF legibility (the .xlsx has them).
 */
require_once '../../auth.php';
requirePermission('report.view', '../../login/');
include '../datacon.php';
require_once '../lib/depreciation.php';
require __DIR__ . '/compute.php';
require_once '../lib/report_pdf.php';

if (!$hasData) {
    header('Location: index.php' . ($selectedClass !== '' ? '?asset_class=' . urlencode($selectedClass) . '&year=' . $selectedYear : ''));
    exit;
}
function n($v) { return number_format((float)$v, 2, '.', ','); }

$cols = ['S/N','Asset Name','Location','Tag/Serial','Acquired','Cost','Residual','Depr. Base','Accum. 1 Jan','Charge ' . $selectedYear,'Accum. 31 Dec','NBV'];
$h  = '<table border="0.4" cellpadding="3" style="font-size:7.5px;"><thead><tr style="' . RMU_PDF_THEAD . '">';
foreach ($cols as $c) $h .= '<th>' . $c . '</th>';
$h .= '</tr></thead><tbody>';

if ($pool) {
    $h .= '<tr style="background-color:#EFF6FF;"><td>--</td><td>Opening Balance (brought forward)</td><td>--</td><td>--</td><td>1 Jan ' . RMU_POOL_BASE_YEAR . '</td>'
        . '<td align="right">' . n($pool['depreciable_base']) . '</td><td align="right">0.00</td><td align="right">' . n($pool['depreciable_base']) . '</td>'
        . '<td align="right">' . n($pool['accumulated_start']) . '</td><td align="right">' . n($pool['depreciation_expense']) . '</td>'
        . '<td align="right">' . n($pool['accumulated_end']) . '</td><td align="right">' . n($pool['nbv_end']) . '</td></tr>';
}
$sn = 1;
foreach (($report['lines'] ?? []) as $ln) {
    $a = $ln['asset'];
    $h .= '<tr><td>' . ($sn++) . '</td><td>' . htmlspecialchars($a['asset_name']) . ($ln['disposed_in_year'] ? ' (disposed)' : '') . '</td>'
        . '<td>' . htmlspecialchars($a['location']) . '</td><td>' . htmlspecialchars($a['serial_number'] ?: '--') . '</td>'
        . '<td>' . date('d M Y', strtotime($a['acquisition_date'])) . '</td>'
        . '<td align="right">' . n($a['additions']) . '</td><td align="right">' . n($a['active_res_value'] ?? 0) . '</td>'
        . '<td align="right">' . n($ln['depreciable_base']) . '</td><td align="right">' . n($ln['accumulated_start']) . '</td>'
        . '<td align="right">' . n($ln['depreciation_expense']) . '</td><td align="right">' . n($ln['accumulated_end']) . '</td>'
        . '<td align="right">' . n($ln['nbv_end']) . '</td></tr>';
}
$h .= '<tr style="' . RMU_PDF_TFOOT . '"><td colspan="5">TOTAL (incl. brought-forward)</td>'
    . '<td align="right">' . n($combined['cost']) . '</td><td align="right">' . n($combined['cost'] - $combined['base']) . '</td>'
    . '<td align="right">' . n($combined['base']) . '</td><td align="right">' . n($combined['accum_start']) . '</td>'
    . '<td align="right">' . n($combined['expense']) . '</td><td align="right">' . n($combined['accum_end']) . '</td>'
    . '<td align="right">' . n($combined['nbv']) . '</td></tr>';
$h .= '</tbody></table>';

rmu_report_pdf(
    ['REGIONAL MARITIME UNIVERSITY', $selectedYear . ' FIXED ASSET REGISTER',
     strtoupper(trim($selectedClass)) . ' - DEPRECIATION SCHEDULE (GHS)'],
    $h, trim($selectedClass) . ' - ' . $selectedYear . ' Depreciation (GHS).pdf', 'L'
);
