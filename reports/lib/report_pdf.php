<?php
/**
 * FILE: reports/lib/report_pdf.php
 * PURPOSE: Reusable branded PDF export for the RMU reports (TCPDF). Renders an
 *          HTML table the caller builds from the SAME compute.php data as the
 *          on-screen report + the .xlsx export, with an RMU logo + institutional
 *          header repeated on every page.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

/** TCPDF subclass that paints the RMU logo + 3-line title on every page. */
class RMU_PDF extends \TCPDF
{
    public array $rmuTitle = ['', '', ''];

    public function Header()
    {
        // NB: assets/img/rmu.png is actually a JPEG; use the .jpg copy and let
        // TCPDF auto-detect the type (passing 'PNG' makes its PNG decoder fail).
        $logo = __DIR__ . '/../../assets/img/rmu.jpg';
        if (is_file($logo)) { $this->Image($logo, 12, 7, 16, 0, ''); }
        $this->SetTextColor(30, 58, 95);          // RMU navy
        $this->SetXY(30, 7);  $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 5, $this->rmuTitle[0], 0, 1, 'L');
        $this->SetX(30);      $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 4, $this->rmuTitle[1], 0, 1, 'L');
        $this->SetX(30);      $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 4, $this->rmuTitle[2], 0, 1, 'L');
    }

    public function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('helvetica', '', 7);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 6, 'RMU Asset Register · Regional Maritime University · ' . date('d M Y'),
                    0, 0, 'L');
        $this->Cell(0, 6, 'Page ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(),
                    0, 0, 'R');
    }
}

/**
 * Stream a branded PDF download.
 * @param string[] $titleLines  [institution, register line, report line]
 * @param string   $htmlTable   the report table as HTML (use inline styles)
 * @param string   $filename
 * @param string   $orientation 'L' (landscape, default) or 'P'
 */
function rmu_report_pdf(array $titleLines, string $htmlTable, string $filename, string $orientation = 'L'): never
{
    // A binary download must not be polluted by stray warnings/notices (TCPDF
    // on PHP 8 can emit deprecations). Log them instead of printing them.
    @ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    while (ob_get_level() > 0) ob_end_clean();

    $pdf = new RMU_PDF($orientation, 'mm', 'A4', true, 'UTF-8', false);
    $pdf->rmuTitle = [$titleLines[0] ?? '', $titleLines[1] ?? '', $titleLines[2] ?? ''];
    $pdf->SetCreator('RMU Asset Register');
    $pdf->SetAuthor('Regional Maritime University');
    $pdf->SetTitle($titleLines[2] ?? 'RMU Report');
    $pdf->SetMargins(12, 26, 12);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 14);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 7);
    $pdf->writeHTML($htmlTable, true, false, true, false, '');
    $pdf->Output(str_replace('"', '', $filename), 'D');
    exit;
}

/** Shared cell styles for the report tables (navy header, gold totals). */
const RMU_PDF_THEAD = 'background-color:#1E3A5F;color:#FFFFFF;font-weight:bold;';
const RMU_PDF_TFOOT = 'background-color:#FFD700;font-weight:bold;';
