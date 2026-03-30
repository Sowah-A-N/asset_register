<?php
require_once '../../init.php';
include "functions.php";

// Check if the year is set
if (isset($_POST['summaryYear'])) {
    // Call the assetClassSummary function
    $summaryYear = ($_POST['summaryYear']);
} else {
    $summaryYear = date('Y');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Summary</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

</head>

<body>
    <a href="../" class="btn btn-outline-primary btn-sm mb-3">Back To Reports</a>
    <?php
        
        $year = date("Y");
        $years = range(2024, $year);

    ?>
    <select name="year" id="year">
        <option value="">--Select Year--</option>
        <?php foreach ($years as $year) { ?>
            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
        <?php } ?>
    </select>

    <button id="export-btn" class="btn btn-danger mb-3">Export to PDF</button>


    <div id="content">
        
    </div>

    <style>
    #content {
        transform: scale(0.85); /* Scale down to fit */
        transform-origin: top left;
    }
</style>
</body>

<script>
  // Get the select element
const yearSelect = document.getElementById('year');

// Add an event listener to the select element
yearSelect.addEventListener('change', function() {
    // Get the selected year
    const selectedYear = this.value;

    // Make an AJAX request to the update_content.php file
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'asset_summary.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('summaryYear=' + selectedYear);

    // Handle the response
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Update the content
            const content = document.getElementById('content');
            content.innerHTML = xhr.responseText;
        } else {
            console.error('Error: ' + xhr.statusText);
        }
    };
});

window.onload = function() {
    // Define the exportToPDF function
    function exportToPDF() {
        console.log('Export button clicked!');
        const element = document.getElementById('content');
        const opt = {
            margin:       0.5,
            filename:     'asset-summary.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 1, logging: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape', compress: 'true' }
        };
        html2pdf().set(opt).from(element).save();
    }

    // Attach the function to the button after the page has fully loaded
    document.getElementById('export-btn').addEventListener('click', exportToPDF);
};

</script>

<?php  
  
?>
