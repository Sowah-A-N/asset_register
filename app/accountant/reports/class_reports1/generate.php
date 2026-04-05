<?php
require_once '../../init.php';

ob_start();

require_once '../vendor/autoload.php';
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

// Load data set by class_reports1/index.php
$assetClassData = $_SESSION['assetClassData'] ?? null;
$assetData      = $_SESSION['assetData']      ?? null;
$yearOfReport   = $assetClassData['year']     ?? date('Y');
$assetClassName = $assetClassData['asset_class'] ?? 'Unknown';

if (!$assetClassData || !$assetData) {
    ob_end_clean();
    header('Location: index.php');
    exit;
}

// Pull class-level figures
$openingBalance   = (float)($assetClassData['opening_balance']                          ?? 0);
$accumDeprStart   = (float)($assetClassData['asset_class_info']['total_accum_depr_start'] ?? 0);
$deprYearCharge   = (float)($assetClassData['asset_class_info']['total_depr_year_charge'] ?? 0);
$disposalsDepr    = (float)($assetClassData['asset_class_info']['disposals_depr']         ?? 0);
$accumDeprEnd     = $accumDeprStart + $deprYearCharge - $disposalsDepr;
$totalDisposals   = (float)($assetClassData['total_disposals'] ?? 0);
$netBookValueEnd  = (float)($assetClassData['asset_class_info']['net_book_value']          ?? 0);
if ($netBookValueEnd < 0) $netBookValueEnd = 0;

// ── TCPDF setup ────────────────────────────────────────────────────────────
$pdf = new TCPDF('L', 'mm', 'A3', true, 'UTF-8', false);

$pdf->SetCreator('Asset Register');
$pdf->SetAuthor('Regional Maritime University');
$pdf->SetTitle("$assetClassName — Depreciation Report $yearOfReport");
$pdf->SetSubject('Fixed Asset Depreciation Schedule');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);

// ── Report header ──────────────────────────────────────────────────────────
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(0, 7, 'REGIONAL MARITIME UNIVERSITY', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 6, strtoupper($yearOfReport . ' FIXED ASSET REGISTER — ' . $assetClassName . ' (CEDI REPORT)'), 0, 1, 'C');
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'Depreciation Schedule — Financial Year ' . $yearOfReport, 0, 1, 'C');
$pdf->Ln(4);

// ── Class-level summary box ────────────────────────────────────────────────
$pdf->SetFont('helvetica', 'B', 9);
$colW = [60, 40, 40, 40, 40, 40, 40];
$pdf->Cell($colW[0], 7, 'Asset Class',             1, 0, 'C');
$pdf->Cell($colW[1], 7, 'Opening Balance (Cost)',  1, 0, 'C');
$pdf->Cell($colW[2], 7, 'Disposals',               1, 0, 'C');
$pdf->Cell($colW[3], 7, 'Accum. Depr (Opening)',   1, 0, 'C');
$pdf->Cell($colW[4], 7, 'Depr. Charge (Year)',     1, 0, 'C');
$pdf->Cell($colW[5], 7, 'Accum. Depr (Closing)',   1, 0, 'C');
$pdf->Cell($colW[6], 7, 'Net Book Value (End)',    1, 1, 'C');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell($colW[0], 6, $assetClassName,                       1, 0, 'L');
$pdf->Cell($colW[1], 6, number_format($openingBalance, 2),     1, 0, 'R');
$pdf->Cell($colW[2], 6, number_format($totalDisposals, 2),     1, 0, 'R');
$pdf->Cell($colW[3], 6, number_format($accumDeprStart, 2),     1, 0, 'R');
$pdf->Cell($colW[4], 6, number_format($deprYearCharge, 2),     1, 0, 'R');
$pdf->Cell($colW[5], 6, number_format($accumDeprEnd, 2),       1, 0, 'R');
$pdf->Cell($colW[6], 6, number_format($netBookValueEnd, 2),    1, 1, 'R');
$pdf->Ln(5);

// ── Individual asset depreciation schedule ─────────────────────────────────
$pdf->SetFont('helvetica', 'B', 8);
$hdr = [
    ['w' => 4,  'txt' => '#'],
    ['w' => 42, 'txt' => 'Asset Name'],
    ['w' => 28, 'txt' => 'Location'],
    ['w' => 22, 'txt' => 'Tag/Serial'],
    ['w' => 18, 'txt' => 'Acq. Date'],
    ['w' => 20, 'txt' => 'Cost (GH₵)'],
    ['w' => 22, 'txt' => 'Accum. Depr\n(Opening)'],
    ['w' => 18, 'txt' => 'Depr Rate'],
    ['w' => 22, 'txt' => 'Depr Expense\n(Year)'],
    ['w' => 22, 'txt' => 'Accum. Depr\n(Closing)'],
    ['w' => 22, 'txt' => 'NBV End\n(GH₵)'],
];

foreach ($hdr as $h) {
    $pdf->MultiCell($h['w'], 8, $h['txt'], 1, 'C', false, 0);
}
$pdf->Ln();

$pdf->SetFont('helvetica', '', 8);
$assetsData = $assetData['assetsData'] ?? [];
$counter    = 1;

foreach ($assetsData as $asset) {
    $accumDeprStartAsset = (float)($asset['newAccumulatedDepreciationStart'] ?? 0);
    $deprExpense         = (float)($asset['newDepreciationExpense']          ?? 0);
    $accumDeprEndAsset   = (float)($asset['newAccumulatedDepreciation']      ?? 0);
    $nbvEnd              = (float)($asset['newClosingCarryingValue']         ?? 0);

    $pdf->Cell(4,  6, $counter++,                                            1, 0, 'C');
    $pdf->Cell(42, 6, mb_strimwidth($asset['newAssetName'] ?? '', 0, 35, '…'), 1, 0, 'L');
    $pdf->Cell(28, 6, mb_strimwidth($asset['newAssetLocation'] ?? '', 0, 22, '…'), 1, 0, 'L');
    $pdf->Cell(22, 6, mb_strimwidth($asset['newAssetSerial'] ?? '', 0, 18, '…'), 1, 0, 'L');
    $pdf->Cell(18, 6, $asset['newAssetDate'] ?? '',                          1, 0, 'C');
    $pdf->Cell(20, 6, number_format((float)($asset['newAssetAdditions'] ?? 0), 2), 1, 0, 'R');
    $pdf->Cell(22, 6, $accumDeprStartAsset > 0 ? number_format($accumDeprStartAsset, 2) : '-', 1, 0, 'R');
    $pdf->Cell(18, 6, ($asset['newAssetDepreciationRate'] ?? 0) . '%',       1, 0, 'C');
    $pdf->Cell(22, 6, number_format($deprExpense, 2),                        1, 0, 'R');
    $pdf->Cell(22, 6, number_format($accumDeprEndAsset, 2),                  1, 0, 'R');
    $pdf->Cell(22, 6, number_format(max($nbvEnd, 0), 2),                     1, 1, 'R');
}

// Totals row
$totals = $assetData['assetsTotals'] ?? [];
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(4+42+28+22+18, 6, 'TOTAL', 1, 0, 'R');
$pdf->Cell(20, 6, number_format((float)($totals['totalAdditions']                  ?? 0), 2), 1, 0, 'R');
$pdf->Cell(22, 6, number_format((float)($totals['totalAccumulatedDepreciationStart'] ?? 0), 2), 1, 0, 'R');
$pdf->Cell(18, 6, '', 1, 0);
$pdf->Cell(22, 6, number_format((float)($totals['totalDepreciationExpense']        ?? 0), 2), 1, 0, 'R');
$pdf->Cell(22, 6, number_format((float)($totals['totalAccumulatedDepreciation']    ?? 0), 2), 1, 0, 'R');
$pdf->Cell(22, 6, number_format((float)($totals['totalBookValueEnd']               ?? 0), 2), 1, 1, 'R');

// Footer timestamp
$pdf->Ln(3);
$pdf->SetFont('helvetica', 'I', 7);
$pdf->Cell(0, 5, 'Generated: ' . date('d M Y, H:i') . '  |  Financial Year: ' . $yearOfReport, 0, 1, 'R');

ob_end_clean();
$pdf->Output(strtolower(str_replace(' ', '_', $assetClassName)) . '_depreciation_' . $yearOfReport . '.pdf', 'I');
