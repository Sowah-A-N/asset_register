<?php
require_once '../init.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Welcome- Accountant</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css" />
  <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- End layout styles -->
  <link rel="shortcut icon" href="assets/images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" id="profileDropdown" data-toggle="dropdown" aria-expanded="false">
              <div class="nav-profile-img">
                <img src="assets/images/favicon.png" alt="image">
              </div>
              <div class="nav-profile-text">
                <p class="mb-1 text-black"><?php echo ($username) ?></p>
              </div>
            </a>
            <div class="dropdown-menu navbar-dropdown dropdown-menu-right p-0 border-0 font-size-sm" aria-labelledby="profileDropdown" data-x-placement="bottom-end">
              <div class="p-3 text-center bg-light">
                <img class="img-avatar img-avatar48 img-avatar-thumb" src="assets/images/favicon.png" alt="">
              </div>
              <div class="p-2">
                <h5 class="dropdown-header text-uppercase pl-2 text-dark">User Options</h5>
                <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="/admin_dashboard/change_password/">
                  <span>Change Password</span>
                  <i class="mdi mdi-settings"></i></a>
                </a>
                <div role="separator" class="dropdown-divider"></div>
                <h5 class="dropdown-header text-uppercase  pl-2 text-dark mt-2">Actions</h5>
                <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="../logout/">
                  <span>Log Out</span>
                  <i class="mdi mdi-logout ml-1"></i>
                </a>
              </div>
            </div>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>
    <?php include "sidebar.html" ?>
    <!-- partial -->
    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row" id="proBanner">
        </div>
        <div class="d-xl-flex justify-content-between align-items-start">
          <h2 class="text-dark font-weight-bold mb-2"> Reports Overview (GH₵)</h2>
          <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
            <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
              <!-- <a href='../generator/' target="_blank"><button class="btn bg-primary" type="button" id="dropdownMenuButton1" aria-haspopup="true" aria-expanded="false"> Generate Timetable</button></a> -->
            </div>
          </div>
        </div>

        <div>
        <!-- Canvas element for the chart -->
        <canvas id='yearAdditionChart'></canvas>
    </div>

    <!-- Include Chart.js library -->
    <script src='../static/chart.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php
    // PHP code to fetch data from the database
    include '../datacon.php';

    $sqlClassQuery = "SELECT asset_class, opening_bal, opbal_plus_additions FROM asset_classes;";
    $resultClassQuery = $conn->query($sqlClassQuery);

    if ($resultClassQuery->num_rows > 0) {
        $assetClassData = array();
        $openingBalanceData = array();
        $opBalPlusAdditionsData = array();

        while ($row = $resultClassQuery->fetch_assoc()) {
            // Storing data in arrays
            $assetClassData[] = $row['asset_class'];
            $openingBalanceData[] = $row['opening_bal'];
            $opBalPlusAdditionsData[] = $row['opbal_plus_additions'];
        }
    }
    ?>

   <script>
    // JavaScript block for Chart setup

        // Retrieve data from PHP and assign to JavaScript variables
        const assetClasses = <?php echo json_encode($assetClassData) ?>;
        const openingBalances = <?php echo json_encode($openingBalanceData) ?>;
        const opBalPlusAdditions = <?php echo json_encode($opBalPlusAdditionsData) ?>;

        // Chart data setup
        const setupData = {
            // Labels for the X-axis (asset classes)
            labels: assetClasses,
            // Datasets containing data for each bar in the chart
            datasets: [{
                    // Dataset for Opening Balances
                    label: "Opening Balances",
                    data: openingBalances,
                    // Background color for bars representing opening balances
                    backgroundColor: [
                        'rgba(90, 223, 68, 0.8)'
                    ],
                    // Border color for bars representing opening balances
                    borderColor: [
                        'rgba(102, 223, 100, 0.5)'
                    ],
                    // Border width for bars representing opening balances
                    borderWidth: 1
                },
                {
                    // Dataset for Plus Additions
                    label: "Plus Additions",
                    data: opBalPlusAdditions,
                    // Background color for bars representing plus additions
                    backgroundColor: [
                        'rgba(199, 67, 222, 0.8)'
                    ],
                    // Border color for bars representing plus additions
                    borderColor: [
                        'rgba(199, 67, 222, 0.5)'
                    ],
                    // Border width for bars representing plus additions
                    borderWidth: 1
                }
            ]
        };

        // Chart configuration
        const configData = {
            // Type of chart (bar chart in this case)
            type: 'bar',
            // Data to be displayed in the chart
            data: setupData,
            // Chart options, including scale configurations
            options: {
                scales: {
                    x: {
                        // Start the X-axis at zero and allow stacking
                        beginAtZero: true,
                        stacked: true,
                    },
                    y: {
                        // Start the Y-axis at zero and do not stack
                        beginAtZero: true,
                        stacked: false,
                    }
                }
            }
        };

        // Chart rendering using Chart.js library
        const yearAdditionChart = new Chart(
            document.getElementById('yearAdditionChart'), configData
        );
   </script>


        <div class="row">
          <div class="col-md-12">
            <div class="d-sm-flex justify-content-between align-items-center transaparent-tab-border {">
            </div>
            <div class="tab-content tab-transparent-content">
              <div class="tab-pane fade show active" id="business-1" role="tabpanel" aria-labelledby="business-tab">
                <div class="row">
                  
                  <?php

                  $sql = "SELECT asset_classes.ast_id, asset_classes.asset_class, COUNT(assets.asset_id) AS asset_count
                  FROM asset_classes
                  LEFT JOIN assets ON asset_classes.asset_class = assets.asset_class
                  GROUP BY asset_classes.asset_class;";
                  $result = mysqli_query($conn, $sql);

                  if(mysqli_num_rows( $result ) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                    //var_dump($row);
                     echo "<div class='col-xl-4 col-lg-6 col-sm-6 grid-margin stretch-card'>"; 
                     echo "<div class='card' id='". $row['ast_id'] ."'>"; 
                     echo "<div class='card-body text-center'>";
                     echo "<i class='mdi mdi-account icon-md text-dark'></i>";
                     echo "<a href=index2.php/?asset_class=" . urlencode($row['asset_class']).">";
                     //print_r($row['asset_class']);
                     echo "<h3 class='mb-2 text-dark font-weight-normal'>" . $row['asset_class'] . "</h3><br />";
                     echo "<h2 class='mb-4 text-dark font-weight-bold'>" . $row['asset_count'] . "</h2>";       
                     echo "</a>";             
                     echo "<div class='dashboard-progress dashboard-progress-1 d-flex align-items-center justify-content-center item-parent'></div>";
                     echo "</div>";
                     echo "</div>";
                     echo "</div>";
                    }
                  }

                  ?>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
      <!-- partial:partials/_footer.html -->
      <footer class="footer">
        <div class="footer-inner-wraper">
          <div class="d-sm-flex justify-content-center justify-content-sm-between"> </div>
        </div>
      </footer>
      <!-- partial -->
    </div>
    <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="assets/vendors/chart.js/Chart.min.js"></script>
  <script src="assets/vendors/jquery-circle-progress/js/circle-progress.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/hoverable-collapse.js"></script>
  <script src="assets/js/misc.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <script src="assets/js/dashboard.js"></script>
  <!-- End custom js for this page -->
</body>

</html>