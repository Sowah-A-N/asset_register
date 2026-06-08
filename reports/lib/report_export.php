<?php
/**
 * FILE: schedule_officer/reports/lib/report_export.php
 * PURPOSE: Reusable, branded .xlsx export helpers for the RMU reports.
 *          Centralises the RMU logo, the institutional title block, the
 *          column-header styling and the download headers so every report
 *          export shares one consistent design (navy banner + gold header row,
 *          thousands-formatted numbers, bordered totals).
 *
 * Uses the consolidated Composer libraries at the project root (./vendor).
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

const RMU_BRAND_NAVY = '1E3A5F';   // deep navy — matches the app shell
const RMU_BRAND_GOLD = 'FFD700';   // gold accent (legacy header colour)
const RMU_LOGO_FILE  = __DIR__ . '/../../assets/img/rmulog.png';

/**
 * Create a branded spreadsheet with the RMU logo + 3-line title block.
 * Returns [$spreadsheet, $sheet, $headerRow] where $headerRow is the row the
 * caller should write its column headers into (style it via rmu_style_header_row).
 *
 * @param string   $sheetTitle  worksheet tab name
 * @param string[] $titleLines  up to 3 lines (institution / register / report)
 * @param int      $lastColIdx  number of columns the report spans (1-based)
 */
function rmu_report_spreadsheet(string $sheetTitle, array $titleLines, int $lastColIdx): array
{
    $ss    = new Spreadsheet();
    $sheet = $ss->getActiveSheet();
    $sheet->setTitle(rmu_sheet_name($sheetTitle));
    $headerRow = rmu_brand_sheet($sheet, $titleLines, $lastColIdx);
    return [$ss, $sheet, $headerRow];
}

/** Add a second (or later) branded worksheet to an existing workbook. */
function rmu_report_add_sheet(Spreadsheet $ss, string $sheetTitle, array $titleLines, int $lastColIdx): array
{
    $sheet = $ss->createSheet();
    $sheet->setTitle(rmu_sheet_name($sheetTitle));
    $headerRow = rmu_brand_sheet($sheet, $titleLines, $lastColIdx);
    return [$sheet, $headerRow];
}

/** Sanitise a worksheet tab name (≤31 chars, none of : \ / ? * [ ]). */
function rmu_sheet_name(string $name): string
{
    return substr(preg_replace('/[:\\\\\/?*\[\]]/', ' ', $name), 0, 31) ?: 'Report';
}

/**
 * Paint the RMU logo + 3-line navy title banner onto a worksheet.
 * Returns the row the caller should write column headers into.
 */
function rmu_brand_sheet($sheet, array $titleLines, int $lastColIdx): int
{
    $lastCol = Coordinate::stringFromColumnIndex($lastColIdx);

    // RMU logo (top-left). A Drawing belongs to a single worksheet.
    if (is_file(RMU_LOGO_FILE)) {
        $drawing = new Drawing();
        $drawing->setName('RMU');
        $drawing->setPath(RMU_LOGO_FILE);
        $drawing->setHeight(58);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(8);
        $drawing->setOffsetY(6);
        $drawing->setWorksheet($sheet);
    }
    $sheet->getColumnDimension('A')->setWidth(11);

    // Title block (rows 1-3), text starts at column B
    $lines = array_values(array_filter($titleLines, fn($l) => $l !== null && $l !== ''));
    $r = 1;
    foreach (array_slice($lines, 0, 3) as $line) {
        $sheet->setCellValue("B$r", $line);
        $sheet->mergeCells("B$r:$lastCol$r");
        $r++;
    }
    $titleEnd = max(3, $r - 1);
    $banner = $sheet->getStyle("A1:$lastCol$titleEnd");
    $banner->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(RMU_BRAND_NAVY);
    $banner->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
    $banner->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('B1')->getFont()->setSize(15);
    $sheet->getStyle('B2')->getFont()->setSize(12);
    $sheet->getStyle('B3')->getFont()->setSize(11);
    for ($i = 1; $i <= $titleEnd; $i++) $sheet->getRowDimension($i)->setRowHeight(22);

    return $titleEnd + 2;   // one blank spacer row, then headers
}

/** Style a column-header row: gold fill, bold, centred, wrapped, bordered. */
function rmu_style_header_row($sheet, int $row, int $lastColIdx): void
{
    $lastCol = Coordinate::stringFromColumnIndex($lastColIdx);
    $st = $sheet->getStyle("A$row:$lastCol$row");
    $st->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(RMU_BRAND_GOLD);
    $st->getFont()->setBold(true);
    $st->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER)
       ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $st->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getRowDimension($row)->setRowHeight(40);
}

/** Apply #,##0.00 number format to a rectangular range of value columns. */
function rmu_format_numbers($sheet, int $fromColIdx, int $toColIdx, int $fromRow, int $toRow): void
{
    if ($toRow < $fromRow) return;
    $from = Coordinate::stringFromColumnIndex($fromColIdx);
    $to   = Coordinate::stringFromColumnIndex($toColIdx);
    $sheet->getStyle("$from$fromRow:$to$toRow")->getNumberFormat()->setFormatCode('#,##0.00');
}

/** Bold + thick top/bottom border on a totals row. */
function rmu_style_total_row($sheet, int $row, int $lastColIdx): void
{
    $lastCol = Coordinate::stringFromColumnIndex($lastColIdx);
    $st = $sheet->getStyle("A$row:$lastCol$row");
    $st->getFont()->setBold(true);
    $st->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);
    $st->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THICK);
}

/** Auto-size every used column. */
function rmu_autosize($sheet): void
{
    $last = Coordinate::columnIndexFromString($sheet->getHighestColumn());
    for ($c = 1; $c <= $last; $c++) $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
}

/** Stream the workbook to the browser as a download, then exit. */
function rmu_report_send(Spreadsheet $ss, string $filename): never
{
    while (ob_get_level() > 0) ob_end_clean();   // avoid corrupting the binary
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '"');
    header('Cache-Control: max-age=0');
    (new Xlsx($ss))->save('php://output');
    exit;
}
