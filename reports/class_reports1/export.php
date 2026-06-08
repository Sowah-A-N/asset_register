<?php
/**
 * FILE: schedule_officer/reports/class_reports1/export.php
 * PURPOSE: Branded .xlsx export of the GHS Class Depreciation Schedule.
 *          Re-runs the SAME live engine as the on-screen report (compute.php)
 *          and lays it out with the RMU logo + institutional header, so the
 *          download matches both the figures and the design of the report.
 */

require_once '../../auth.php';
requirePermission('report.view', '../../login/');

include '../datacon.php';                 // $conn
require_once '../lib/depreciation.php';
require __DIR__ . '/compute.php';          // $selectedClass, $selectedYear, $report, $pool, $combined, $hasData
require_once '../lib/report_export.php';

if (!$hasData) {
    header('Location: index.php' . ($selectedClass !== '' ? '?asset_class=' . urlencode($selectedClass) . '&year=' . $selectedYear : ''));
    exit;
}

/* ── Column layout ─────────────────────────────────────────────────────
   1 #            7 Residual        13..24  Jan..Dec
   2 Asset Name   8 Depr. Base
   3 Location     9 Accum. Start
   4 Tag/Serial  10 Expense {yr}
   5 Acquired    11 Accum. End
   6 Cost        12 NBV End                                              */
$months  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$headers = ['S/N','Asset Name','Location','Tag / Serial','Acquired','Cost (GHS)','Residual',
            'Depr. Base','Accum. Depr. 1 Jan','Depr. Charge ' . $selectedYear,
            'Accum. Depr. 31 Dec','Net Book Value', ...$months];
$lastColIdx  = count($headers);                 // 24
$numFirstCol = 6;                               // first numeric column (Cost)

[$ss, $sheet, $hr] = rmu_report_spreadsheet(
    trim($selectedClass) . ' (GHS)',
    ['REGIONAL MARITIME UNIVERSITY',
     $selectedYear . ' FIXED ASSET REGISTER',
     strtoupper(trim($selectedClass)) . ' — DEPRECIATION SCHEDULE (GHS)'],
    $lastColIdx
);

// Column headers
$sheet->fromArray([$headers], null, 'A' . $hr);
rmu_style_header_row($sheet, $hr, $lastColIdx);

$row = $hr + 1;

// Brought-forward pool row (if any)
if ($pool) {
    $line = [
        '—', 'Opening Balance (brought forward)', '—', '—', '1 Jan ' . RMU_POOL_BASE_YEAR,
        $pool['depreciable_base'], 0, $pool['depreciable_base'],
        $pool['accumulated_start'], $pool['depreciation_expense'],
        $pool['accumulated_end'], $pool['nbv_end'],
    ];
    for ($m = 1; $m <= 12; $m++) $line[] = $pool['monthly_breakdown'][$m];
    $sheet->fromArray([$line], null, 'A' . $row);
    $sheet->getStyle("A$row:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIdx) . "$row")
          ->getFont()->setItalic(true);
    $row++;
}

// Itemised asset rows
$sn = 1;
foreach (($report['lines'] ?? []) as $ln) {
    $a = $ln['asset'];
    $name = $a['asset_name'] . ($ln['disposed_in_year'] ? ' (disposed)' : '');
    $line = [
        $sn++, $name, $a['location'], ($a['serial_number'] ?: '—'),
        date('d M Y', strtotime($a['acquisition_date'])),
        (float)$a['additions'], (float)($a['active_res_value'] ?? 0), $ln['depreciable_base'],
        $ln['accumulated_start'], $ln['depreciation_expense'],
        $ln['accumulated_end'], $ln['nbv_end'],
    ];
    for ($m = 1; $m <= 12; $m++) $line[] = $ln['monthly_breakdown'][$m];
    $sheet->fromArray([$line], null, 'A' . $row);
    $row++;
}

// Totals row
$tot = [
    '', 'TOTAL (incl. brought-forward)', '', '', '',
    $combined['cost'], $combined['cost'] - $combined['base'], $combined['base'],
    $combined['accum_start'], $combined['expense'], $combined['accum_end'], $combined['nbv'],
];
for ($m = 1; $m <= 12; $m++) $tot[] = $combined['monthly'][$m];
$sheet->fromArray([$tot], null, 'A' . $row);
rmu_style_total_row($sheet, $row, $lastColIdx);

// Number formatting for all value columns (Cost..Dec), header+1 down to totals
rmu_format_numbers($sheet, $numFirstCol, $lastColIdx, $hr + 1, $row);
rmu_autosize($sheet);

$fname = trim($selectedClass) . ' - ' . $selectedYear . ' Depreciation Report (GHS).xlsx';
rmu_report_send($ss, $fname);
