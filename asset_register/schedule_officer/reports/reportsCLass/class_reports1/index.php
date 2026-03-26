<?php
session_start();
include "./datacon.php";
include "functions.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Reports - New</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <a href="../" class="btn btn-outline-primary btn-sm mb-3">Back To Reports</a>

    <form class="needs-validation" method="POST" novalidate>
        <div class="card shadow-sm p-3 ">
            <div class="card-body">
                <div class="row justify-content-center align-items-center">
                    <!-- Asset Class Field -->
                    <div class="col-md-3 mb-3">
                        <label for="asset_class" class="form-label">Asset Class</label>
                        <select name="asset_class" id="asset_class" class="form-select form-select-sm" required>
                            <option value="">--Select Asset Class--</option>
                            <?php
                            $query = "SELECT * FROM asset_classes ";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_array($result)) {
                                if ($row['asset_class'] == $assetClass) {
                                    echo "<option value='" . $row['asset_class'] . "' selected>" . $row['asset_class'] . "</option>";
                                } else {
                                    echo "<option value='" . $row['asset_class'] . "'>" . $row['asset_class'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Please select an asset class.</div>
                    </div>

                    <!-- Year Field -->
                    <div class="col-md-3 mb-3">
                        <label for="year" class="form-label">Year</label>
                        <select name="year" id="year" class="form-select form-select-sm" required>
                            <option value="">--Select Year--</option>
                            <?php
                            $current_year = date("Y");
                            for ($year = 2024; $year <= $current_year; $year++) {
                                echo "<option value='" . $year . "'>" . $year . "</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Please select a year.</div>



                    </div>
                </div>



                <!-- Submit Button -->
                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        (function() {
            'use strict'

            var forms = document.querySelectorAll('.needs-validation')

            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>

    <?php

    $reportYear = isset($_POST['year']) ? $_POST['year'] : '';
    $assetClass = isset($_POST['asset_class']) ? $_POST['asset_class'] : '';

    if (!empty($reportYear) && !empty($assetClass)) {
        $assetClassValue = getUntrackedAssetsByYear($reportYear, $assetClass);
        $_SESSION["assetClassData"] = $assetClassValue;
        displayClassInfo($reportYear, $assetClass, $assetClassValue);

        $assetData = calculationsByClass($assetClass, $reportYear);
        $_SESSION["assetData"] = $assetData;
        displayData($reportYear, $assetData, $assetClassValue);

        //dd($_SESSION);
    }

    // Create the form
    echo '<form action="csv.php" method="POST" class="d-flex justify-content-center">';
    echo '    <button type="submit" class="btn btn-outline-primary btn-rounded ">' . "Export to csv" . '</button>';
    echo '</form>';

    ?>

</body>