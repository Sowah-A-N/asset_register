<?php
/**
 * FILE: schedule_officer/reports/asset_summary/export.php
 * PURPOSE: Branded .xlsx export of the GHS Asset Summary (one row per class).
 *          Re-runs the same live engine as the on-screen report (compute.php).
 */

require_once '../../auth.php';
requirePermission('report.view', '../../login/');

include '../datacon.php';
require_once '../lib/depreciation.php';
require __DIR__ . '/compute.php';
require_once '../lib/report_export.php';

if (!$rows) { header('Location: index.php?year=' . $selectedYear); exit; }

$headers = ['S/N','Asset Class','Assets','Cost (GHS)','Additions ' . $selectedYear,
            'Disposals ' . $selectedYear, 'Accum. Depr. b/f', 'Charge ' . $selectedYear,
            'Accum. Depr. c/f', 'Net Book Value'];
$lastColIdx = count($headers);   // 10

[$ss, $sheet, $hr] = rmu_report_spreadsheet(
    'Asset Summary ' . $selectedYear,
    ['REGIONAL MARITIME UNIVERSITY',
     $selectedYear . ' FIXED ASSET REGISTER',
     'ASSET SUMMARY — MOVEMENT (GHS)'],
    $lastColIdx
);

$sheet->fromArray([$headers], null, 'A' . $hr);
rmu_style_header_row($sheet, $hr, $lastColIdx);
$row = $hr + 1;

$sn = 1;
foreach ($rows as $r) {
    $name = $r['name'] . ($r['wip'] ? ' (WIP)' : '');
    $sheet->fromArray([[
        $sn++, $name, $r['count'], $r['cost'], $r['additions'], $r['disposals'],
        $r['accum_start'], $r['charge'], $r['accum_end'], $r['nbv'],
    ]], null, 'A' . $row);
    $row++;
}

$sheet->fromArray([[
    '', 'GRAND TOTAL', $grand['count'], $grand['cost'], $grand['additions'], $grand['disposals'],
    $grand['accum_start'], $grand['charge'], $grand['accum_end'], $grand['nbv'],
]], null, 'A' . $row);
rmu_style_total_row($sheet, $row, $lastColIdx);

rmu_format_numbers($sheet, 4, $lastColIdx, $hr + 1, $row);   // Cost..NBV
rmu_autosize($sheet);

rmu_report_send($ss, 'Asset Summary - ' . $selectedYear . ' (GHS).xlsx');
