<?php
/**
 * FILE: schedule_officer/reports/asset_summary_usd/export.php
 * PURPOSE: Branded .xlsx export of the USD Asset Summary (one row per class,
 *          IAS 21 historical rate). Re-runs the same engine as the page.
 */

require_once '../../auth.php';
requirePermission('report.view', '../../login/');

include '../datacon.php';
require_once '../lib/depreciation.php';
require __DIR__ . '/compute.php';
require_once '../lib/report_export.php';

if (!$rows) { header('Location: index.php?year=' . $selectedYear); exit; }

$headers = ['S/N','Asset Class','Assets','Cost ($)','Accum. Depr. b/f',
            'Charge ' . $selectedYear, 'Accum. Depr. c/f', 'Net Book Value'];
$lastColIdx = count($headers);   // 8

[$ss, $sheet, $hr] = rmu_report_spreadsheet(
    'Asset Summary USD ' . $selectedYear,
    ['REGIONAL MARITIME UNIVERSITY',
     $selectedYear . ' FIXED ASSET REGISTER',
     'ASSET SUMMARY — MOVEMENT (USD, IAS 21)'],
    $lastColIdx
);

$sheet->fromArray([$headers], null, 'A' . $hr);
rmu_style_header_row($sheet, $hr, $lastColIdx);
$row = $hr + 1;

$sn = 1;
foreach ($rows as $r) {
    $name = $r['name'] . ($r['wip'] ? ' (WIP)' : '') . ($r['sub'] ? ' *' : '');
    $sheet->fromArray([[
        $sn++, $name, $r['count'], $r['cost'], $r['accum_start'], $r['charge'], $r['accum_end'], $r['nbv'],
    ]], null, 'A' . $row);
    $row++;
}

$sheet->fromArray([[
    '', 'GRAND TOTAL', $grand['count'], $grand['cost'], $grand['accum_start'], $grand['charge'], $grand['accum_end'], $grand['nbv'],
]], null, 'A' . $row);
rmu_style_total_row($sheet, $row, $lastColIdx);

rmu_format_numbers($sheet, 4, $lastColIdx, $hr + 1, $row);   // Cost..NBV
rmu_autosize($sheet);

if ($anySub) {
    $sheet->setCellValue('A' . ($row + 2), '* class includes a substituted rate (stored historical rate missing/invalid → active rate $' . number_format($activeRate, 2) . ').');
}

rmu_report_send($ss, 'Asset Summary - ' . $selectedYear . ' (USD).xlsx');
