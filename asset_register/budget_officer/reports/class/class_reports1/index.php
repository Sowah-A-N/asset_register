<?php
include_once "datacon.php";
include_once "functions.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    try {
        $selectedAssetClass = $_POST['asset_class'];
        $selectedYear = isset($_POST['year']) ? $_POST['year'] : null;
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}

if (!empty($selectedAssetClass)) {
    try {
        $assetClassInitialData = getAssetClassInitialData($selectedAssetClass);
        $assetsPerClass = getAssetsPerClass($selectedAssetClass);
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Asset Management</h2>

        <!-- Form Section -->
        <form action="" method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label for="year" class="form-label">Select Year:</label>
                    <select name="year" id="year" class="form-select">
                        <?php 
                        $currentYear = date("Y");
                        $years = range(2020, $currentYear);
                        foreach ($years as $year) { ?>
                            <option data-year="<?php echo $year; ?>" value="<?php echo $year; ?>"><?php echo $year; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="asset_class" class="form-label">Asset Class:</label>
                    <select name="asset_class" data-asset-id="asset_class" id="asset_class" class="form-select">
                        <?php 
                        $query = "SELECT asset_class FROM asset_classes";
                        $result = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_array($result)) { ?>
                            <option value="<?php echo $row['asset_class']; ?>"><?php echo $row['asset_class']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="submit" id="submit" class="btn btn-primary mt-3">Submit</button>
        </form>

        <!-- Asset Class Details -->
        <?php if (!empty($assetClassInitialData)) { ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3>Asset Class Details</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Asset Class:</th>
                            <td><?php echo $assetClassInitialData['asset_class']; ?></td>
                        </tr>
                        <tr>
                            <th>Depreciation Rate:</th>
                            <td><?php echo $assetClassInitialData['dep_rate']; ?>%</td>
                        </tr>
                        <tr>
                            <th>Estimated Life:</th>
                            <td><?php echo $assetClassInitialData['estimated_life']; ?> years</td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning">No data found for the selected options.</div>
        <?php } ?>

        <!-- Assets Per Class -->
        <?php if (!empty($assetsPerClass)) { ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3>Assets Per Class</h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Asset Name</th>
                                <th>Historical Cost</th>
                                <th>Monthly Depreciation Amount</th>
                                <th>Accumulated Depreciation</th>
                                <th>Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assetsPerClass as $asset) { ?>
                                <
                                    <input type="hidden" name="asset_id" id="asset_id" value="<?php echo $asset['asset_id']; ?>">
                                    <td><?php echo $asset['asset_name']; ?></td>
                                    <td><?php echo $asset['additions']; ?></td>
                                    <td><?php echo $asset['monthly_depreciation']['monthlyDepAmt']; ?></td>
                                    <td><?php echo $asset['accumulated_depreciation']; ?></td>
                                    <td><?php echo $selectedYear; ?></td>
                                    <td><button id="view-breakdown" class="btn btn-outline-info"
                                                 data-asset-id="<?php echo $asset['asset_id']?>" data-year='{$selectedYear}'>
                                        View Monthly Breakdown
                                    </button></d>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning mt-3">No assets found for the selected class.</div>
        <?php } ?>

        <?php if (1 < 0) { ?>
        <?php } else { ?>
        <?php } ?>        

   <!-- Modal -->
   <div id="depreciationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Monthly Depreciation Breakdown</h2>
            <div id="modal-content-area"></div>
        </div>
    </div>

<style>/* The Modal (background) */
    .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content/Box */
    .modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
    }

    .modal.show {
        display: block;
    }

    /* The Close Button */
    .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    }

    .close:hover,
    .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Select all buttons with the "view-breakdown" class
    document.getElementById("view-breakdown").addEventListener("click", function () {
            let assetId = this.getAttribute("data-asset-id");
            let year = this.getAttribute("data-year");

            // Create a FormData object for the POST request
            let formData = new FormData();
            formData.append("asset_id", assetId);
            formData.append("year", year);

            // Send an AJAX request using Fetch API
            fetch("assetMonthlyBreakdown.inc.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if(data){
                    document.getElementById("depreciationModal").classList.add("show");
                    document.getElementById("modal-content-area").innerHTML = data;
                    document.getElementById("depreciationModal").style.display = "block";
                } else {
                    console.error("No data received from server");
                }
            })
            .catch(error => console.error("Error fetching data:", error));
        });

    // Close the modal when the "X" button is clicked
    document.querySelector(".close").addEventListener("click", function () {
        document.getElementById("depreciationModal").style.display = "none";
    });

    // Close the modal when clicking outside of it
    window.addEventListener("click", function (event) {
        if (event.target.id === "depreciationModal") {
            document.getElementById("depreciationModal").style.display = "none";
        }
    });
});
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
