<?php

session_start();

require_once '../vendor/autoload.php';
require_once 'functions.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

try {
    $exportedAssetClassTitle = $_SESSION['assetClassData']['asset_class'];
    $assetClassOpeningBal = $_SESSION['assetClassData']['opening_balance'];
    $assetClass_total_accum_depr_start = $_SESSION['assetClassData']['asset_class_info']['total_accum_depr_start'];
    $assetClass_total_depr_year_charge = $_SESSION['assetClassData']['asset_class_info']['total_depr_year_charge'];
    $assetClass_total_accum_depr_end = $_SESSION['assetClassData']['asset_class_info']['total_accum_depr_end'];
    $assetClass_disposals_depreciation = $_SESSION['assetClassData']['asset_class_info']['disposals_depr'];
    $assetClass_total_net_book_value_end = $_SESSION['assetClassData']['asset_class_info']['net_book_value'];
    $assetClass_year = $_SESSION['assetClassData']['asset_class_info']['year'];
    $assetClass_rate = $_SESSION['assetClassData']['asset_class_info']['rate'];
    $assetClass_disposal_value = $_SESSION['assetClassData']['total_disposals'];
    
    $assetData = $_SESSION["assetData"] ?? [];
    $assetClassData = $_SESSION["assetClassData"] ?? [];
    $yearOfReport = $_SESSION['assetClassData']["year"] ?? date("Y");
    $spreadsheet = new Spreadsheet();
    $color = new Color();
    $fill = new Fill();
    
    // =====================================
    // SHEET 1: ASSET DETAILS
    // =====================================
    
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle($exportedAssetClassTitle);
    
    $sheet->setCellValue('D1', "REGIONAL MARITIME UNIVERSITY");
    $sheet->mergeCells('D1:J1');
    
    $sheet->setCellValue('D2', "$yearOfReport FIXED ASSET REGISTER");
    $sheet->mergeCells('D2:J2');
    
    $sheet->setCellValue('D3', strtoupper($exportedAssetClassTitle . " (CEDI REPORT)"));
    $sheet->mergeCells('D3:J3');
    
    $sheet->getStyle("A1:AA3")->getFont()->getColor()->setARGB($color::COLOR_BLACK);
    $sheet->getStyle("A1:AA3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4472C4');
    
    $sheet->getStyle("A4:AA4")->getFont()->getColor()->setARGB($color::COLOR_BLACK);
    $sheet->getStyle("A4:AA4")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD700');
    
    $sheet->getStyle("A1:AA4")->getFont()->setBold(true);
    $sheet->getRowDimension(4)->setRowHeight(72);
    
    $sheet->getStyle('F:AA')->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('G:G')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    
    $headerSummary = [
        "S/N",
        "Asset Name",
        "Location",
        "Tag/Chassis No.",
        "Date of Purchase",
        "Cost of Purchase (GHS)",
        "Year of Report",
        "Total Accumulated Depreciation As At Jan 1st $yearOfReport",
        "Net Book Value As at Jan 1st",
        "Depreciation Rate",
        "Total Depreciation/Charge for the year",
        "Total Accumulated Depreciation End",
        "Disposals Depreciation",
        "Net Book Value End (GHS)",
        "Dollar Rate Used",
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
    ];
    
    for ($month = 1; $month <= 12; $month++) {
        $headers[] = date('F', mktime(0, 0, 0, $month, 1));
    }
    
    $sheet->fromArray([$headerSummary], NULL, 'A4');
    
    $sheet->setCellValue('B5', "OPENING BALANCE");
    $sheet->getStyle('B5:D5')->getFont()->setBold(true);
    $sheet->mergeCells('B5:D5');
    
    $sheet->setCellValue('B6', "DISPOSAL");
    $sheet->getStyle('B6:D6')->getFont()->setBold(true);
    $sheet->mergeCells('B6:D6');
    
    $sheet->setCellValue('F5', number_format($assetClassOpeningBal, 2));
    $sheet->setCellValue('F6', number_format($assetClass_disposal_value, 2));
    $sheet->setCellValue('G5', $assetClass_year);
    $sheet->setCellValue('H5', number_format($assetClass_total_accum_depr_start, 2));
    $sheet->setCellValue('K5', number_format($assetClass_total_depr_year_charge, 2));
    $sheet->setCellValue('L5', number_format($assetClass_total_accum_depr_end, 2));
    $sheet->setCellValue('M5', number_format($assetClass_disposals_depreciation, 2));
    $sheet->setCellValue('N5', number_format($assetClass_total_net_book_value_end, 2));
    $sheet->setCellValue('O5', number_format($assetClass_rate, 2));
    
    $rowNum = 5;
    $counter = 4;
    
    $summaryRow = [];
    
    foreach ($assetClassData as $class) {
        if (!is_array($class)) continue;
    
        if (isset($class['asset_class_info']) && !empty($class['asset_class_info'])) {
            $summaryRow = [
                $counter++,
                $class['asset_class_info']['description'] ?? 'Opening Balance',
                $class['asset_class_info']['opening_balance'],
                $class['asset_class_info']['rate'],
                $class['asset_class_info']['expected_life_months'],
                $class['asset_class_info']['total_accum_depr_start'],
                $class['asset_class_info']['total_depr_year_charge'],
                $class['asset_class_info']['total_accum_depr_end'],
                $class['asset_class_info']['total_disposal_depr'],
                $class['asset_class_info']['net_book_value'],
            ];
        }
    
        $sheet->fromArray([$summaryRow], NULL, 'A' . $rowNum);
        $rowNum++;
    }
    
    if (isset($assetClassData['monthly_depreciations']) && !empty($assetClassData['monthly_depreciations']) && is_array($assetClassData['monthly_depreciations'])) {
        $cols = ['P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
        $colIndex = 0;
        foreach ($assetClassData['monthly_depreciations'] as $total) {
            $cell = $cols[$colIndex] . "5";
            $sheet->setCellValue($cell, number_format($total["value"], 2));
            $colIndex++;
        }
    } else {
        $cols = ['P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
        $colIndex = 0;
        foreach ([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] as $total) {
            $cell = $cols[$colIndex] . "5";
            $sheet->setCellValue($cell, number_format($total, 2));
            $colIndex++;
        }
    }
    
    foreach ($assetData["assetsData"] as $asset) {
        $row = [
            $counter++,
            $asset["newAssetName"],
            $asset["newAssetLocation"],
            $asset["newAssetSerial"],
            $asset["newAssetDate"],
            $asset["newAssetAdditions"],
            $yearOfReport,
            (trim($asset["newAccumulatedDepreciationStart"]) !== '') ? $asset["newAccumulatedDepreciationStart"] : "-",
            $asset["newNetBookValue"],
            $asset["newAssetDepreciationRate"] . "%",
            $asset["newDepreciationExpense"],
            $asset["newAccumulatedDepreciation"],
            '',
            $asset["newClosingCarryingValue"],
            $asset["newAssetDollarRate"]
        ];
    
        foreach ($asset["monthsDisplay"] as $monthValue) {
            $row[] = $monthValue;
        }
    
        $sheet->fromArray([$row], NULL, 'A' . $rowNum);
        $rowNum++;
    }
    
    // Totals
    $totalsRow = $counter + 6;
    
    $sheet->mergeCells("B" . $totalsRow . ":" . "E" . $totalsRow);
    $sheet->setCellValue('B' . $totalsRow, "TOTAL");
    $sheet->setCellValue('I' . $totalsRow, number_format($assetData["assetsTotals"]["totalBookValueStart"], 2));
    $sheet->setCellValue('K' . $totalsRow, number_format($assetData["assetsTotals"]["totalDepreciationExpense"], 2));
    $sheet->setCellValue('H' . $totalsRow, number_format($assetData["assetsTotals"]["totalAccumulatedDepreciationStart"], 2));
    $sheet->setCellValue('L' . $totalsRow, number_format($assetData["assetsTotals"]["totalAccumulatedDepreciation"], 2));
    $sheet->setCellValue('N' . $totalsRow, number_format($assetData["assetsTotals"]["totalBookValueEnd"], 2));
    $sheet->setCellValue('F' . $totalsRow, number_format($assetData["assetsTotals"]["totalAdditions"], 2));
    
    $cols = ['P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
    $colIndex = 0;
    foreach ($assetData["assetsTotals"]["monthlyTotals"] as $total) {
        $cell = $cols[$colIndex] . $totalsRow;
        $sheet->setCellValue($cell, number_format($total, 2));
        $colIndex++;
    }
    
    $sheet->getStyle('F' . $totalsRow . ':AA' . $totalsRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);
    $sheet->getStyle('F' . $totalsRow . ':AA' . $totalsRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);
    $sheet->getStyle("A" . $totalsRow . ":AA" . $totalsRow)->getFont()->setBold(true);
    
    // Grand totals
    $gTotalsRow = $totalsRow + 2;
    
    $sheet->mergeCells("B" . $gTotalsRow . ":" . "E" . $gTotalsRow);
    $sheet->setCellValue('B' . $gTotalsRow, "GRAND TOTAL");
    $sheet->setCellValue('K' . $gTotalsRow, number_format($_SESSION["grandTotals"]["g_total_DepreciationExpense"], 2));
    $sheet->setCellValue('H' . $gTotalsRow, number_format($_SESSION["grandTotals"]["g_total_AccumulatedDepreciationStart"], 2));
    $sheet->setCellValue('L' . $gTotalsRow, number_format($_SESSION["grandTotals"]["g_total_AccumulatedDepreciationEnd"], 2));
    $sheet->setCellValue('N' . $gTotalsRow, number_format($_SESSION["grandTotals"]["g_total_BookValueEnd"], 2));
    $sheet->setCellValue('F' . $gTotalsRow, number_format($_SESSION["grandTotals"]["g_totalAdditions"], 2));
    
    $sheet->getStyle('F' . $gTotalsRow . ':AA' . $gTotalsRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);
    $sheet->getStyle('F' . $gTotalsRow . ':AA' . $gTotalsRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);
    $sheet->getStyle("A" . $gTotalsRow . ":AA" . $gTotalsRow)->getFont()->setBold(true);
    
    
    
    if (isset($assetClassData['monthly_depreciations']) && !empty($assetClassData['monthly_depreciations']) && is_array($assetClassData['monthly_depreciations'])) {
        $cols = ['P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
        $colIndex = 0;
        foreach ($assetClassData['monthly_depreciations'] as $assetMonthlyTotal) {
            $monthIndex = $colIndex + 1;
            $monthlyGrandTotal = $assetData["assetsTotals"]["monthlyTotals"][$monthIndex] + $assetMonthlyTotal["value"];
            $cell = $cols[$colIndex] . $gTotalsRow;
            $sheet->setCellValue($cell, number_format($monthlyGrandTotal, 2));
            $colIndex++;
        }
    }
    
    // }
    
    // Auto-size columns for asset sheet
    $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
    for ($col = 1; $col <= $lastCol; $col++) {
        $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
    }
    
    
    // Return to first sheet
    $spreadsheet->setActiveSheetIndex(0);
    
    // =====================================
    //  DOWNLOAD FILE
    // =====================================
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $exportedAssetClassTitle . ' - ' . $yearOfReport . ' Depreciation Report.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
} catch (Exception $e) {
    var_dump($e->getMessage());
    die();
}

exit;
