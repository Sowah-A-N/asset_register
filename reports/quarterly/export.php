<?php
/**
 * FILE: schedule_officer/reports/quarterly/export.php
 * PURPOSE: Branded .xlsx export of the Quarterly Depreciation report.
 *          Two worksheets — USD (headline) and GHS — each per class with
 *          Q1–Q4 + year total. Re-runs the same live engine as the page.
 */

require_once '../../auth.php';
requirePermission('report.view', '../../login/');

include '../datacon.php';
require_once '../lib/depreciation.php';
require __DIR__ . '/compute.php';
require_once '../lib/report_export.php';

if (!$rows) { header('Location: index.php?year=' . $selectedYear); exit; }

$headers    = ['S/N','Asset Class','Q1 (Jan–Mar)','Q2 (Apr–Jun)','Q3 (Jul–Sep)','Q4 (Oct–Dec)','Year Total'];
$lastColIdx = count($headers);   // 7

/** Write the class rows + total for one currency onto a branded sheet. */
$fill = function ($sheet, int $hr, string $cur) use ($rows, $grand, $headers, $lastColIdx) {
    $yk = $cur . 'Year';
    $sheet->fromArray([$headers], null, 'A' . $hr);
    rmu_style_header_row($sheet, $hr, $lastColIdx);
    $row = $hr + 1; $sn = 1;
    foreach ($rows as $r) {
        $sheet->fromArray([[
            $sn++, $r['name'], $r[$cur][0], $r[$cur][1], $r[$cur][2], $r[$cur][3], $r[$yk],
        ]], null, 'A' . $row);
        $row++;
    }
    $sheet->fromArray([[
        '', 'TOTAL', $grand[$cur][0], $grand[$cur][1], $grand[$cur][2], $grand[$cur][3], array_sum($grand[$cur]),
    ]], null, 'A' . $row);
    rmu_style_total_row($sheet, $row, $lastColIdx);
    rmu_format_numbers($sheet, 3, $lastColIdx, $hr + 1, $row);   // Q1..Year Total
    rmu_autosize($sheet);
};

// Sheet 1 — USD (headline currency)
[$ss, $usd, $hrU] = rmu_report_spreadsheet(
    'Quarterly USD ' . $selectedYear,
    ['REGIONAL MARITIME UNIVERSITY',
     $selectedYear . ' QUARTERLY DEPRECIATION',
     'PER CLASS — Q1–Q4 (USD, IAS 21)'],
    $lastColIdx
);
$fill($usd, $hrU, 'usd');

// Sheet 2 — GHS
[$ghs, $hrG] = rmu_report_add_sheet(
    $ss, 'Quarterly GHS ' . $selectedYear,
    ['REGIONAL MARITIME UNIVERSITY',
     $selectedYear . ' QUARTERLY DEPRECIATION',
     'PER CLASS — Q1–Q4 (GHS)'],
    $lastColIdx
);
$fill($ghs, $hrG, 'ghs');

$ss->setActiveSheetIndex(0);
rmu_report_send($ss, 'Quarterly Depreciation - ' . $selectedYear . '.xlsx');
