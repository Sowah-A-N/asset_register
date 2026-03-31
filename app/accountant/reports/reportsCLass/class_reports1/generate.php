<?php

ob_start(); // Start output buffering

include '../vendor/autoload.php';

// Include TCPDF library
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

// Create a new TCPDF object
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Asset Depreciation Report');
$pdf->SetSubject('Asset Depreciation Report');
$pdf->SetKeywords('Asset Depreciation Report');

// Set header and footer fonts
$pdf->setHeaderFont(Array('helvetica', '', 15));
$pdf->setFooterFont(Array('helvetica', '', 15));

// Add a page
$pdf->AddPage();

// Set font for content
$pdf->SetFont('helvetica', '', 12);

// Title: Asset Details
$pdf->Cell(0, 10, 'Asset Details', 1, 1, 'C');
$pdf->Ln(10);

// Add Asset Details table header with bold text
$pdf->SetFont('helvetica', 'B', 12); // Bold header font
$pdf->Cell(30, 10, 'Asset Name', 1, 0, 'L');
$pdf->Cell(30, 10, 'Asset Class', 1, 0, 'L');
$pdf->Cell(30, 10, 'Acquisition Date', 1, 0, 'L');
$pdf->Cell(30, 10, 'Active Res Value', 1, 0, 'L');
$pdf->Cell(30, 10, 'Additions', 1, 0, 'L');
$pdf->Cell(30, 10, 'Disposals', 1, 0, 'L');
$pdf->Cell(30, 10, 'Depreciation Rate', 1, 0, 'L');
$pdf->Ln(10);

// Set font back to normal for table content
$pdf->SetFont('helvetica', '', 12);

// Ensure $assetData exists and is not empty
if (!empty($assetData) && isset($assetData[0])) {
    $pdf->Cell(30, 10, $assetData[0]['Asset Name'] ?? '', 1, 0, 'L');
    $pdf->Cell(30, 10, $assetData[0]['Asset Class'] ?? '', 1, 0, 'L');
    $pdf->Cell(30, 10, $assetData[0]['Acquisition Date'] ?? '', 1, 0, 'L');
    $pdf->Cell(30, 10, $assetData[0]['Active Res Value'] ?? '', 1, 0, 'L');
    $pdf->Cell(30, 10, $assetData[0]['Additions'] ?? '', 1, 0, 'L');
    $pdf->Cell(30, 10, $assetData[0]['Disposals'] ?? '', 1, 0, 'L');
    $pdf->Cell(30, 10, $assetData[0]['Depreciation Rate'] ?? '', 1, 0, 'L');
    $pdf->Ln(20);
}

// Title: Depreciation Schedule
$pdf->Cell(0, 10, 'Depreciation Schedule', 1, 1, 'C');
$pdf->Ln(10);

// Add Depreciation Schedule table header with bold text
$pdf->SetFont('helvetica', 'B', 12); // Bold header font
$pdf->Cell(20, 10, 'Year', 1, 0, 'L');
$pdf->Cell(30, 10, 'Net Book Value', 1, 0, 'L');
$pdf->Cell(30, 10, 'Depreciation Rate', 1, 0, 'L');
$pdf->Cell(30, 10, 'Depreciation Expense', 1, 0, 'L');
$pdf->Cell(30, 10, 'Accumulated Depreciation', 1, 0, 'L');
$pdf->Cell(30, 10, 'Closing Carrying Value', 1, 0, 'L');
$pdf->Ln(10);

// Set font back to normal for table content
$pdf->SetFont('helvetica', '', 12);

// Ensure $depreciationScheduleData exists and is not empty
if (!empty($depreciationScheduleData)) {
    foreach ($depreciationScheduleData as $rowData) {
        $pdf->Cell(20, 10, $rowData['Year'] ?? '', 1, 0, 'L');
        $pdf->Cell(30, 10, $rowData['Net Book Value'] ?? '', 1, 0, 'L');
        $pdf->Cell(30, 10, $rowData['Depreciation Rate'] ?? '', 1, 0, 'L');
        $pdf->Cell(30, 10, $rowData['Depreciation Expense'] ?? '', 1, 0, 'L');
        $pdf->Cell(30, 10, $rowData['Accumulated Depreciation'] ?? '', 1, 0, 'L');
        $pdf->Cell(30, 10, $rowData['Closing Carrying Value'] ?? '', 1, 0, 'L');
        $pdf->Ln(10);
    }
}

// Clean any previous output before sending the PDF
ob_end_clean();

// Output the PDF directly to the browser
$pdf->Output('asset_depreciation_report.pdf', 'I');
