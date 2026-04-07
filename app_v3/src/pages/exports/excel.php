<?php
// CSV export handler — streams a CSV download.
// Route: exports/assets, exports/assets-pdf, exports/disposals
// GET params: report, year, class, format

require_auth();
require_once SRC . '/queries/reports.php';
require_once SRC . '/queries/assets.php';
require_once SRC . '/queries/asset_classes.php';

$report = get('report', 'all');
$year   = get_int('year', (int)date('Y'));
$class  = get('class', '');

// Build data and headers based on report type
switch ($report) {
    case 'summary':
        $rows = get_asset_class_summary($conn, $year);
        $headers = ['Asset Class','Opening Balance','Additions (GHS)','Additions (USD)',
                    'Disposals (GHS)','Depr Charge','Accum Depr End','Net Book Value'];
        $mapper = function($r) {
            return [
                $r['asset_class'],
                number_format((float)$r['opening_balance'], 2, '.', ''),
                number_format((float)$r['total_additions_cedi'], 2, '.', ''),
                number_format((float)$r['total_additions_dollar'], 2, '.', ''),
                number_format((float)$r['total_disposals_cedi'], 2, '.', ''),
                number_format((float)$r['total_depr_year_charge'], 2, '.', ''),
                number_format((float)$r['total_accum_depr_end'], 2, '.', ''),
                number_format((float)$r['net_book_value'], 2, '.', ''),
            ];
        };
        $filename = "asset_summary_{$year}.csv";
        break;

    case 'additions':
        $rows = get_additions_by_year($conn, $year);
        $headers = ['Asset Name','ID Number','Class','Sub-Class','Supplier','Location',
                    'Acq Date','Year','Additions (GHS)','Additions (USD)'];
        $mapper = function($r) {
            $usd = $r['dollar_rate_used'] > 0
                ? number_format((float)$r['additions'] / (float)$r['dollar_rate_used'], 2, '.', '')
                : '0.00';
            return [$r['asset_name'],$r['id_number'],$r['asset_class'],$r['sub_class'],
                    $r['supplier_name'],$r['location'],$r['acquisition_date'],$r['current_year'],
                    number_format((float)$r['additions'],2,'.',''),(string)$usd];
        };
        $filename = "additions_{$year}.csv";
        break;

    case 'by-location':
        $rows = get_assets_by_location($conn);
        $headers = ['Location','Asset Count','Total Value (GHS)'];
        $mapper  = function($r) {
            return [$r['location'], $r['asset_count'],
                    number_format((float)$r['total_value'],2,'.','' )];
        };
        $filename = 'assets_by_location.csv';
        break;

    case 'disposals':
        $result = mysqli_query($conn,
            'SELECT * FROM disposals ORDER BY date_of_disposal DESC');
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
        $headers = ['Asset Name','Class','Location','Additions (GHS)','Disposal Value','Date'];
        $mapper  = function($r) {
            return [$r['asset_name'],$r['asset_class'],$r['location'],
                    number_format((float)$r['additions'],2,'.','' ),
                    number_format((float)$r['disposal_value'],2,'.','' ),
                    $r['date_of_disposal']];
        };
        $filename = 'disposals.csv';
        break;

    default: // 'all'
        $rows = get_all_active_assets($conn, $class);
        $headers = ['Asset Name','ID Number','Class','Sub-Class','Asset Type','Location',
                    'User','Acq Date','Year','Additions (GHS)','Additions (USD)'];
        $mapper = function($r) {
            $usd = $r['dollar_rate_used'] > 0
                ? number_format((float)$r['additions'] / (float)$r['dollar_rate_used'], 2, '.', '')
                : '0.00';
            return [$r['asset_name'],$r['id_number'],$r['asset_class'],$r['sub_class'],
                    $r['asset_type'],$r['location'],$r['user'] ?? '',$r['acquisition_date'],
                    $r['current_year'],number_format((float)$r['additions'],2,'.','' ),(string)$usd];
        };
        $filename = 'active_assets.csv';
        break;
}

// Stream CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache');

$out = fopen('php://output', 'w');
// BOM for Excel UTF-8 compatibility
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, $headers);
foreach ($rows as $row) {
    fputcsv($out, $mapper($row));
}
fclose($out);
exit;
