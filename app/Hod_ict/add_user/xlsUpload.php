<!-- <?php
    // require 'vendor/autoload.php'; // Include PhpSpreadsheet

    // if ($_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
    //     $tmpFilePath = $_FILES['excel_file']['tmp_name'];
    //     $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmpFilePath);
    //     $worksheet = $spreadsheet->getActiveSheet();

    //     foreach ($worksheet->getRowIterator() as $row) {
    //         $rowData = [];
    //         foreach ($row->getCellIterator() as $cell) {
    //             $rowData[] = $cell->getValue();
    //         }
    //         // Insert $rowData into your database (e.g., MySQL)
    //         // Adjust this part according to your database setup
    //         // Example: $db->insert($rowData);
    //     }

    //     echo 'Data imported successfully!';
    // } else {
    //     echo 'Error uploading the file.';
    // }
?> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel File</title>
</head>
<body>
    <form  method="post" enctype="multipart/form-data">
        <input type="file" name="excelFile" accept=".xls, .xlsx">
        <input type="submit" value="Upload">
    </form>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $targetDir = "../uploads/";
            $targetFile = $targetDir . basename($_FILES["excelFile"]["name"]);
            $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

            // Validate file type (optional)
            if ($fileType != "xls" && $fileType != "xlsx") {
                echo "Invalid file format. Please upload an Excel file.";
                exit;
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["excelFile"]["tmp_name"], $targetFile)) {
                echo "File uploaded successfully!";
            } else {
                echo "Error uploading file.";
            }
        }
    ?>

    <?php
        // require '../../vendor/autoload.php'; // Include PhpSpreadsheet library

        // $inputFileName = 'uploads/my_excel_file.xlsx'; // Path to the uploaded Excel file

        // $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        // $worksheet = $spreadsheet->getActiveSheet();

        // // Assuming data starts from row 2 (skip header row)
        // foreach ($worksheet->getRowIterator(2) as $row) {
        //    // $rowData = $row->toArray();
        //     // Insert $rowData into your database (e.g., MySQL)
        //     // Example: INSERT INTO my_table (col1, col2, col3) VALUES (?, ?, ?)
        // }
    ?>



</body>
</html>