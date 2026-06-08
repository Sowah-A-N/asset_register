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

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>


</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Asset Depreciation Schedule</h2>

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
       
       <!-- Assets Per Class
        <?php if (!empty($assetsPerClass['assets'])) { ?>
            
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3>Assets Per Class</h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Asset Name</th>
                                <th>Acquisition Date</th>
                                <th>Historical Cost</th>
                                <th>Monthly Depreciation Amount</th>
                                <th>Accumulated Depreciation</th>
                                <th>Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php foreach ($assetsPerClass['assets'] as $asset) { ?>
                                <tr>                                
                                    <input type="hidden" name="asset_id" id="asset_id" value="<?php echo $asset['asset_id']; ?>">
                                    <td><?php echo $asset['asset_name']; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($asset['acquisition_date'])); ?></td> 
                                    <td><?php echo "GH¢" . number_format(floatval($asset['additions']), 2) ?></td>
                                    <td><?php echo "GH¢" . number_format(floatval($asset['monthly_depreciation']), 2) ?></td>
                                    <td><?php echo "GH¢" . number_format(floatval($asset['accumulated_depreciation']), 2); ?></td><td><?php echo $selectedYear; ?></td>
                                    <td><button id="" class="btn btn-outline-info view-breakdown"
                                                data-asset-id="<?php echo $asset['asset_id']?>" data-year="<?php echo $selectedYear?>">
                                        View Monthly Breakdown
                                    </button></td>

                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Totals:</th>
                                <th><?php echo "GH¢" . number_format($assetsPerClass['totals']['total_historical_cost'], 2) ?></th>
                                <th><?php echo "GH¢" . number_format($assetsPerClass['totals']['total_monthly_depreciation'], 2) ?></th>
                                <th><?php echo "GH¢" . number_format($assetsPerClass['totals']['total_accumulated_depreciation'], 2) ?></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning mt-3">No assets found for the selected class.</div>
        <?php } ?> -->

       <!-- Assets Per Class -->
<?php if (!empty($assetsPerClass['assets'])) { 
    //   print_r($assetsPerClass);
    //   die();
    ?>
    
  
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Asset ID</th>
                <th>Asset Name</th>
                <th>Acquisition Date</th>
                <th>Historical Cost</th>
                <th>Monthly Depreciation</th>
                <th>Accumulated Depreciation</th>
                <th>Net Book Value</th>
                <?php
            for ($month = 1; $month <= 12; $month++) {
                echo "<th>" . date("F", mktime(0, 0, 0, $month, 1, $selectedYear)) . "</th>";
            }
            ?>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($assetsPerClass['assets'] as $asset) { ?>
        <tr>
            <td><?= $asset['asset_id'] ?></td>
            <td><?= $asset['asset_name'] ?></td>
            <td><?= $asset['acquisition_date'] ?></td>
            <td><?= "GH¢" . number_format(floatval($asset['additions']), 2) ?></td>
            <td><?= "GH¢" . number_format(floatval($asset['monthly_depreciation']), 2) ?></td>
            <td><?= "GH¢" . number_format(floatval($asset['accumulated_depreciation']), 2) ?></td>
            <td><?= "GH¢" . number_format(floatval($asset['additions']) - floatval($asset['accumulated_depreciation']), 2) ?></td>

            <?php
            // Loop through 12 months to ensure columns match headers
            for ($month = 1; $month <= 12; $month++) {
                $depreciationValue = isset($asset['monthly_depreciation'][$month]) 
                    ? floatval($asset['monthly_depreciation'][$month]) 
                    : 0; // Default to 0 if no data

                echo "<td>GH¢" . number_format($depreciationValue, 2) . "</td>";
            }
            ?>
        </tr>
    <?php } ?>
</tbody>
            <tr>
                <th colspan="3">Totals</th>
                <td><?= "GH¢" .number_format(floatval($assetsPerClass['totals']['total_historical_cost']), 2) ?></td>
                <td><?= "GH¢" .number_format(floatval($assetsPerClass['totals']['total_monthly_depreciation']), 2) ?></td>
                <td><?= "GH¢" .number_format(floatval($assetsPerClass['totals']['total_accumulated_depreciation']), 2) ?></td>
                <td><?= "GH¢" .number_format(floatval($assetsPerClass['totals']['total_net_book_value']), 2) ?></td>
                <td colspan="12"></td>
            </tr>
        </tfoot>
    </table> 


    <!-- <?php print_r($assetsPerClass)?>
    <?php } else { ?>
    <p>No assets found.</p>
<?php } ?> -->

        <?php if (1 < 0) { ?>
        <?php } else { ?>
        <?php } ?>        

  <!-- Depreciation Schedule Modal -->
<div class="modal fade" id="depreciationScheduleModal" tabindex="-1" role="dialog" aria-labelledby="depreciationScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="depreciationScheduleModalLabel">Depreciation Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Depreciation schedule data will be displayed here -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Depreciation</th>
                            <th>Accumulated Depreciation</th>
                            <th>Net Book Value</th>
                        </tr>
                    </thead>
                    <tbody id="depreciation-schedule-data">
                        <!-- Depreciation schedule data will be displayed here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Select all buttons with the "view-breakdown" class
    const viewBreakdownButtons = document.querySelectorAll(".view-breakdown");

    // Attach an event listener to each button
    viewBreakdownButtons.forEach(button => {
        button.addEventListener("click", function () {
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
           .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if(data){
                    // Display data in modal
                   const depreciationScheduleData = document.getElementById('depreciation-schedule-data');

                    depreciationScheduleData.innerHTML = '';
                    
                    Object.entries(data).forEach(([month, values]) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${values.month}</td>
                            <td>GH¢${values.depreciation}</td>
                            <td>GH¢${values.accumulatedDepreciation}</td>
                            <td>GH¢${values.netBookValue}</td>
                        `;
                        depreciationScheduleData.appendChild(row);
                    });

                    // Show modal
                    $('#depreciationScheduleModal').modal('show');

                } else {
                    console.error("No data received from server");
                }
            })
            .catch(error => console.error("Error fetching data:", error));
                });
            });
        }); 

    // Close the modal when the "X" button is clicked
    // document.querySelector(".close").addEventListener("click", function () {
    //     document.getElementById("depreciationModal").style.display = "none";
    // });

    // // Close the modal when clicking outside of it
    // window.addEventListener("click", function (event) {
    //     if (event.target.id === "depreciationModal") {
    //         document.getElementById("depreciationModal").style.display = "none";
    //     }
    // });

</script>


</body>
</html>
