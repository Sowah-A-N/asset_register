<?php
/** reports/class_reports_usd/pdf.php — Branded PDF of the USD Class Schedule. */
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

$cols = ['S/N','Asset Name','Location','Tag/Serial','Acquired','Rate','Cost ($)','Depr. Base','Accum. 1 Jan','Charge ' . $selectedYear,'Accum. 31 Dec','NBV'];
$h  = '<table border="0.4" cellpadding="3" style="font-size:7.5px;"><thead><tr style="' . RMU_PDF_THEAD . '">';
foreach ($cols as $c) $h .= '<th>' . $c . '</th>';
$h .= '</tr></thead><tbody>';

if ($pool) {
    $h .= '<tr style="background-color:#EFF6FF;"><td>--</td><td>Opening Balance (brought forward)</td><td>--</td><td>--</td><td>1 Jan ' . RMU_POOL_BASE_YEAR . '</td>'
        . '<td align="right">' . n($poolRate) . '</td><td align="right">' . n($pool['depreciable_base']) . '</td><td align="right">' . n($pool['depreciable_base']) . '</td>'
        . '<td align="right">' . n($pool['accumulated_start']) . '</td><td align="right">' . n($pool['depreciation_expense']) . '</td>'
        . '<td align="right">' . n($pool['accumulated_end']) . '</td><td align="right">' . n($pool['nbv_end']) . '</td></tr>';
}
$sn = 1;
foreach (($report['lines'] ?? []) as $ln) {
    $a = $ln['asset'];
    $h .= '<tr><td>' . ($sn++) . '</td><td>' . htmlspecialchars($a['asset_name']) . ($ln['disposed_in_year'] ? ' (disposed)' : '') . ($ln['rate_substituted'] ? ' *' : '') . '</td>'
        . '<td>' . htmlspecialchars($a['location']) . '</td><td>' . htmlspecialchars($a['serial_number'] ?: '--') . '</td>'
        . '<td>' . date('d M Y', strtotime($a['acquisition_date'])) . '</td>'
        . '<td align="right">' . n($ln['rate_used']) . '</td><td align="right">' . n($ln['cost_usd']) . '</td>'
        . '<td align="right">' . n($ln['depreciable_base']) . '</td><td align="right">' . n($ln['accumulated_start']) . '</td>'
        . '<td align="right">' . n($ln['depreciation_expense']) . '</td><td align="right">' . n($ln['accumulated_end']) . '</td>'
        . '<td align="right">' . n($ln['nbv_end']) . '</td></tr>';
}
$h .= '<tr style="' . RMU_PDF_TFOOT . '"><td colspan="6">TOTAL (incl. brought-forward)</td>'
    . '<td align="right">' . n($combined['cost']) . '</td><td align="right">' . n($combined['base']) . '</td>'
    . '<td align="right">' . n($combined['accum_start']) . '</td><td align="right">' . n($combined['expense']) . '</td>'
    . '<td align="right">' . n($combined['accum_end']) . '</td><td align="right">' . n($combined['nbv']) . '</td></tr>';
$h .= '</tbody></table>';
if ($poolRateSub || array_filter($report['lines'] ?? [], fn($l) => $l['rate_substituted'])) {
    $h .= '<p style="font-size:7px;color:#666;">* rate substituted with active rate ($' . n($activeRate) . ') — stored historical rate missing/invalid.</p>';
}

rmu_report_pdf(
    ['REGIONAL MARITIME UNIVERSITY', $selectedYear . ' FIXED ASSET REGISTER',
     strtoupper(trim($selectedClass)) . ' - DEPRECIATION SCHEDULE (USD, IAS 21)'],
    $h, trim($selectedClass) . ' - ' . $selectedYear . ' Depreciation (USD).pdf', 'L'
);
