<?php
require_once '../../init.php';
include "functions.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Asset Register — Summary</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 11px; }
        }
    </style>
</head>
<body class="p-3">

    <div class="no-print d-flex align-items-center gap-2 mb-3">
        <a href="../" class="btn btn-outline-secondary btn-sm">&larr; Back To Reports</a>
        <h5 class="mb-0 ms-2">Fixed Asset Register — Summary (GH₵)</h5>
    </div>

    <?php
    // Dynamic year range: earliest year in asset_class_opbal_year → current year
    $minYearResult = mysqli_query($conn, "SELECT MIN(year) AS min_year FROM asset_class_opbal_year");
    $minYearRow    = $minYearResult ? mysqli_fetch_assoc($minYearResult) : null;
    $minYear       = ($minYearRow && $minYearRow['min_year']) ? (int)$minYearRow['min_year'] : 2020;
    $currentYear   = (int)date('Y');
    ?>

    <div class="no-print row g-2 align-items-center mb-3">
        <div class="col-auto">
            <label for="year" class="form-label mb-0">Financial Year:</label>
        </div>
        <div class="col-auto">
            <select name="year" id="year" class="form-select form-select-sm">
                <option value="">-- Select Year --</option>
                <?php for ($y = $currentYear; $y >= $minYear; $y--): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <button id="export-btn" class="btn btn-danger btn-sm">Export to PDF</button>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">Print</button>
        </div>
    </div>

    <div id="content"></div>

</body>

<script>
const yearSelect = document.getElementById('year');

yearSelect.addEventListener('change', function () {
    const selectedYear = this.value;
    if (!selectedYear) return;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'asset_summary.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('summaryYear=' + encodeURIComponent(selectedYear));

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById('content').innerHTML = xhr.responseText;
        } else {
            document.getElementById('content').innerHTML =
                '<div class="alert alert-danger">Failed to load report. Please try again.</div>';
        }
    };
});

window.onload = function () {
    document.getElementById('export-btn').addEventListener('click', function () {
        const element = document.getElementById('content');
        if (!element.innerHTML.trim()) {
            alert('Please select a year first.');
            return;
        }
        const year = document.getElementById('year').value;
        html2pdf().set({
            margin:      0.4,
            filename:    'fixed-asset-summary-' + year + '.pdf',
            image:       { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 1.5, logging: false },
            jsPDF:       { unit: 'in', format: 'a3', orientation: 'landscape' }
        }).from(element).save();
    });
};
</script>
</html>
