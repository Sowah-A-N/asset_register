<?php
/** reports/asset_summary/pdf.php — Branded PDF of the GHS Asset Summary. */
require_once '../../auth.php';
requirePermission('report.view', '../../login/');
include '../datacon.php';
require_once '../lib/depreciation.php';
require __DIR__ . '/compute.php';
require_once '../lib/report_pdf.php';

if (!$rows) { header('Location: index.php?year=' . $selectedYear); exit; }
function n($v) { return number_format((float)$v, 2, '.', ','); }

$cols = ['S/N','Asset Class','Assets','Cost (GHS)','Additions ' . $selectedYear,'Disposals ' . $selectedYear,'Accum. b/f','Charge ' . $selectedYear,'Accum. c/f','Net Book Value'];
$h  = '<table border="0.4" cellpadding="4" style="font-size:8.5px;"><thead><tr style="' . RMU_PDF_THEAD . '">';
foreach ($cols as $c) $h .= '<th>' . $c . '</th>';
$h .= '</tr></thead><tbody>';
$sn = 1;
foreach ($rows as $r) {
    $h .= '<tr><td>' . ($sn++) . '</td><td>' . htmlspecialchars($r['name']) . ($r['wip'] ? ' (WIP)' : '') . '</td>'
        . '<td align="center">' . (int)$r['count'] . '</td>'
        . '<td align="right">' . n($r['cost']) . '</td><td align="right">' . ($r['additions'] > 0 ? n($r['additions']) : '-') . '</td>'
        . '<td align="right">' . ($r['disposals'] > 0 ? n($r['disposals']) : '-') . '</td>'
        . '<td align="right">' . n($r['accum_start']) . '</td><td align="right">' . n($r['charge']) . '</td>'
        . '<td align="right">' . n($r['accum_end']) . '</td><td align="right">' . n($r['nbv']) . '</td></tr>';
}
$h .= '<tr style="' . RMU_PDF_TFOOT . '"><td colspan="2">GRAND TOTAL</td><td align="center">' . (int)$grand['count'] . '</td>'
    . '<td align="right">' . n($grand['cost']) . '</td><td align="right">' . n($grand['additions']) . '</td>'
    . '<td align="right">' . n($grand['disposals']) . '</td><td align="right">' . n($grand['accum_start']) . '</td>'
    . '<td align="right">' . n($grand['charge']) . '</td><td align="right">' . n($grand['accum_end']) . '</td>'
    . '<td align="right">' . n($grand['nbv']) . '</td></tr></tbody></table>';

rmu_report_pdf(
    ['REGIONAL MARITIME UNIVERSITY', $selectedYear . ' FIXED ASSET REGISTER', 'ASSET SUMMARY - MOVEMENT (GHS)'],
    $h, 'Asset Summary - ' . $selectedYear . ' (GHS).pdf', 'L'
);
