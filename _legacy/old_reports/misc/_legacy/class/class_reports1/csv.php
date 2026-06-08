<?php

require '../venvdor/autoload.php'; // Required for PHPSpreadsheet (Excel Export)
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['export_csv']) || isset($_POST['export_excel'])) {
    include "../datacon.php";
    }

    $yearOfReport = $_POST['year']; // Get the selected year

    // Fetch Data from Database
    $query = "SELECT asset_name, location, serial, date, additions, book_value_start, 
                     depreciation_rate, depreciation_expense, accumulated_depreciation, 
                     book_value_end, rate
              FROM depreciation_schedule 
              WHERE year_of_report = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $yearOfReport);
    $stmt->execute();
    $result = $stmt->get_result();

    // Data Array
    $data = [];
    $headers = ["S/N", "Asset Name", "Location", "Tag/Chassis No.", "Date of Purchase",
                "Cost of Purchase (GHS)", "Year of Report", "Net Book Value", "Depreciation Rate",
                "Depreciation Expense", "Accumulated Depreciation", "Closing Carrying Value (GHS)",
                "Closing Carrying Value (USD)", "Dollar Rate Used"];

    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $counter,
            $row['asset_name'],
            $row['location'],
            $row['serial'],
            $row['date'],
            number_format($row['additions'], 2),
            $yearOfReport,
            number_format($row['book_value_start'], 2),
            ($row['depreciation_rate'] * 100) . "%",
            number_format($row['depreciation_expense'], 2),
            number_format($row['accumulated_depreciation'], 2),
            number_format($row['book_value_end'], 2),
            number_format(($row['book_value_end'] / $row['rate']), 2),
            $row['rate']
        ];
        $counter++;
    }

    if (isset($_POST['export_csv'])) {
        // CSV Export
        $filename = "Depreciation_Schedule_$yearOfReport.csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen("php://output", "w");
        fputcsv($output, $headers);

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    if (isset($_POST['export_excel'])) {
        // Excel Export
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add Headers
        $sheet->fromArray([$headers], NULL, 'A1');

        // Add Data
        $sheet->fromArray($data, NULL, 'A2');

        // Auto-size Columns
        foreach (range('A', 'N') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Download File
        $filename = "Depreciation_Schedule_$yearOfReport.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

?>
