<?php
/**
 * FILE: schedule_officer/reports/class_reports_usd/export.php
 * PURPOSE: Branded .xlsx export of the USD Class Depreciation Schedule
 *          (IAS 21 historical rate per item). Re-runs the same live engine
 *          as the on-screen report (compute.php) for matching figures.
 */

require_once '../../auth.php';
requirePermission('report.view', '../../login/');

include '../datacon.php';
require_once '../lib/depreciation.php';
require __DIR__ . '/compute.php';
require_once '../lib/report_export.php';

if (!$hasData) {
    header('Location: index.php' . ($selectedClass !== '' ? '?asset_class=' . urlencode($selectedClass) . '&year=' . $selectedYear : ''));
    exit;
}

$months  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$headers = ['S/N','Asset Name','Location','Tag / Serial','Acquired','Rate','Cost ($)',
            'Depr. Base','Accum. Depr. 1 Jan','Depr. Charge ' . $selectedYear,
            'Accum. Depr. 31 Dec','Net Book Value', ...$months];
$lastColIdx  = count($headers);   // 24
$numFirstCol = 6;                 // Rate onward

[$ss, $sheet, $hr] = rmu_report_spreadsheet(
    trim($selectedClass) . ' (USD)',
    ['REGIONAL MARITIME UNIVERSITY',
     $selectedYear . ' FIXED ASSET REGISTER',
     strtoupper(trim($selectedClass)) . ' — DEPRECIATION SCHEDULE (USD, IAS 21)'],
    $lastColIdx
);

$sheet->fromArray([$headers], null, 'A' . $hr);
rmu_style_header_row($sheet, $hr, $lastColIdx);
$row = $hr + 1;

if ($pool) {
    $line = ['—', 'Opening Balance (brought forward)', '—', '—', '1 Jan ' . RMU_POOL_BASE_YEAR,
        $poolRate, $pool['depreciable_base'], $pool['depreciable_base'],
        $pool['accumulated_start'], $pool['depreciation_expense'], $pool['accumulated_end'], $pool['nbv_end']];
    for ($m = 1; $m <= 12; $m++) $line[] = $pool['monthly_breakdown'][$m];
    $sheet->fromArray([$line], null, 'A' . $row);
    $sheet->getStyle("A$row:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIdx) . "$row")->getFont()->setItalic(true);
    $row++;
}

$sn = 1;
foreach (($report['lines'] ?? []) as $ln) {
    $a = $ln['asset'];
    $name = $a['asset_name'] . ($ln['disposed_in_year'] ? ' (disposed)' : '') . ($ln['rate_substituted'] ? ' *' : '');
    $line = [$sn++, $name, $a['location'], ($a['serial_number'] ?: '—'),
        date('d M Y', strtotime($a['acquisition_date'])),
        $ln['rate_used'], $ln['cost_usd'], $ln['depreciable_base'],
        $ln['accumulated_start'], $ln['depreciation_expense'], $ln['accumulated_end'], $ln['nbv_end']];
    for ($m = 1; $m <= 12; $m++) $line[] = $ln['monthly_breakdown'][$m];
    $sheet->fromArray([$line], null, 'A' . $row);
    $row++;
}

$tot = ['', 'TOTAL (incl. brought-forward)', '', '', '', '',
    $combined['cost'], $combined['base'], $combined['accum_start'],
    $combined['expense'], $combined['accum_end'], $combined['nbv']];
for ($m = 1; $m <= 12; $m++) $tot[] = $combined['monthly'][$m];
$sheet->fromArray([$tot], null, 'A' . $row);
rmu_style_total_row($sheet, $row, $lastColIdx);

rmu_format_numbers($sheet, $numFirstCol, $lastColIdx, $hr + 1, $row);
rmu_autosize($sheet);

if ($poolRateSub || array_filter($report['lines'] ?? [], fn($l) => $l['rate_substituted'])) {
    $sheet->setCellValue('A' . ($row + 2), '* rate substituted with active rate ($' . number_format($activeRate, 2) . ') — stored historical rate missing/invalid.');
}

rmu_report_send($ss, trim($selectedClass) . ' - ' . $selectedYear . ' Depreciation Report (USD).xlsx');
