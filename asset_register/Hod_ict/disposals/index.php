<?php

session_start();
if (!isset($_SESSION['username'])) {
  header("Location:../login/");
  die();
}
$username = $_SESSION['username'];
include "../datacon.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['view'])) {
}

if (isset($_POST['archive'])) {
  $assetId = $_POST['id'];

  // Fetch the record to be archived
  $sqlFetchAsset = "SELECT * FROM disposals WHERE asset_id = '$assetId'";
  $resultFetchAsset = mysqli_query($conn, $sqlFetchAsset);

  if (!$resultFetchAsset) {
      die("Error in SQL query (Fetch Asset): " . mysqli_error($conn));
  }

  if (mysqli_num_rows($resultFetchAsset) > 0) {
      $rowAsset = mysqli_fetch_assoc($resultFetchAsset);

      // Insert the record into the asset_archive table
      $sqlArchive = "INSERT INTO assets_archive (asset_id, asset_name, asset_class, grv_number, id_number, pv_number, asset_type, location, acquisition_date, additions, dollar_rate_used)
                     VALUES ('" . $rowAsset['asset_id'] . "', '" . $rowAsset['asset_name'] . "', '" . $rowAsset['asset_class'] . "', '" . $rowAsset['grv_number'] . "', '" . $rowAsset['id_number'] . "', '" . $rowAsset['pv_number'] . "', '" . $rowAsset['asset_type'] . "', '" . $rowAsset['location'] . "', '" . $rowAsset['acquisition_date'] . "', '" . $rowAsset['additions'] . "', '" . $rowAsset['dollar_rate_used'] . "')";

      $resultArchive = mysqli_query($conn, $sqlArchive);
      

      if (!$resultArchive) {
          die("Error in SQL query (Archive): " . mysqli_error($conn));
      }

      // Delete the record from the assets table
      $sqlDeleteAsset = "DELETE FROM assets WHERE asset_id = '$assetId'";
      $resultDeleteAsset = mysqli_query($conn, $sqlDeleteAsset);
      

      if (!$resultDeleteAsset) {
          die("Error in SQL query (Delete Asset): " . mysqli_error($conn));
      }
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>View Disposed Assets</title>
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
            <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
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
                <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="/staff_allowance/change_password/">
                  <span>User Settings</span>
                  <i class="mdi mdi-settings"></i>
                </a>
                <div role="separator" class="dropdown-divider"></div>
                <h5 class="dropdown-header text-uppercase  pl-2 text-dark mt-2">Actions</h5>
                <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="logout.php">
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
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">

      <?php include "../sidebar.html" ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row" id="proBanner">
          </div>
          <div class="d-xl-flex justify-content-between align-items-start">
            <h2 class="text-dark font-weight-bold mb-2"> Filter Disposed Assets by Classes </h2>
            <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
              <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="d-sm-flex justify-content-between align-items-center transaparent-tab-border {">
              </div>
              <div class="tab-content tab-transparent-content overflow-auto">
                <div class="tab-pane fade show active" id="business-1" role="tabpanel" aria-labelledby="business-tab">
                  <div class="row">
                    <!--Table goes here -->
                    <?php
                    // Fetch all asset classes from the database
$sqlAssetClasses = "SELECT * FROM asset_classes";
$resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);

// Check if the query was successful
if (!$resultAssetClasses) {
    die("Error in SQL query (Asset Classes): " . mysqli_error($conn));
}

// Check if there are any asset classes
if (mysqli_num_rows($resultAssetClasses) > 0) {
  // echo "<h2>Filter by Asset Class</h2>";
  echo "<form method='get'>";
  echo "<select name='asset_class_filter'>";
  echo "<option value=''>All Asset Classes</option>";

  while ($rowAssets = mysqli_fetch_assoc($resultAssetClasses)) {
      $assetClass = $rowAssets['asset_class'];
      $selected = ($_GET['asset_class_filter'] ?? '') == $assetClass ? 'selected' : '';
      echo "<option value='$assetClass' $selected>$assetClass</option>";
  }

  echo "</select>";
  echo "<button type='submit'>Filter</button>";
  echo "</form>";

  if (isset($_GET['asset_class_filter'])) {
      $assetClassFilter = $_GET['asset_class_filter'];
      if (!empty($assetClassFilter)) {
        $sql = "SELECT * FROM disposals WHERE asset_class = '$assetClassFilter'";

          $result = mysqli_query($conn, $sql);

          if (!$result) {
              printf("Error: %s\n", mysqli_error($conn));
              exit();
          }
      } else {
          // If no specific asset class is selected, fetch all assets
          $sql = "SELECT * FROM disposals";
          $result = mysqli_query($conn, $sql);

          if (!$result) {
              printf("Error: %s\n", mysqli_error($conn));
              exit();
          }
      }
  } else {
      // If no filter is provided, fetch all assets
      $sql = "SELECT * FROM disposals";
      $result = mysqli_query($conn, $sql);

      if (!$result) {
          printf("Error: %s\n", mysqli_error($conn));
          exit();
      }
  }
}
                    ?>
                    <br />

                    
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">S/n</th>
                          <th scope="col">Asset Name</th>
                          <th scope="col">Asset Class</th>
                          <th scope="col">GRV Number</th>
                          <th scope="col">ID Number</th>
                          <th scope="col">PV Number</th>
                          <th scope="col">Asset Type</th>
                          <th scope="col">Location</th>
                          <th scope="col">Acquistion Date</th>
                          <th scope="col">Historical Cost (GHS)</th>
                          <th scope="col">Disposal Cost (GHS)</th>
                          <th scope="col">Dollar Rate</th>
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $serial_number = 1;
                        while ($row = mysqli_fetch_array($result)) {
                          echo "<td>$serial_number</td>";
                          echo "<td>" . $row['asset_name'] . "</td>";
                          echo "<td>" . $row['asset_class'] . "</td>";
                          echo "<td>" . $row['grv_number'] . "</td>"; 
                          echo "<td>" . $row['id_number'] . "</td>"; 
                          echo "<td>" . $row['pv_number'] . "</td>";
                          echo "<td>" . $row['asset_type'] . "</td>"; 
                          echo "<td>" . $row['location'] . "</td>";
                          echo "<td>" . date("d F, Y", strtotime($row['acquisition_date'])) . "</td>";

                          echo "<td>" . number_format($row['additions'], 2, '.', ',') . "</td>"; 
                          echo "<td>" . number_format($row['disposal_value'], 2, '.', ',') . "</td>"; 
                          echo "<td>" . $row['dollar_rate_used'] . "</td>"; ?>
                          <form action="index.php" method="POST">
                            <?php echo "<input type=hidden name=id value='" . $row['asset_id'] . "' ?>"; ?>
                            
                          </form>

                          
                        <?php
                          echo "</form></tr>";
                          $serial_number++;
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