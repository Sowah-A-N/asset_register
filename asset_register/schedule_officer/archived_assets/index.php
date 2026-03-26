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
  $sqlFetchAsset = "SELECT * FROM assets WHERE asset_id = '$assetId'";
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
  <title>View Archived Assets</title>
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
            <h2 class="text-dark font-weight-bold mb-2"> Filter Archived Assets by Classes </h2>
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
                  <div class="px-4 row">
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
  echo "<form method='get' id='filterForm'>";
  echo "<select name='asset_class_filter'>";
  echo "<option value=''>All Asset Classes</option>";

  while ($rowAssets = mysqli_fetch_assoc($resultAssetClasses)) {
      $assetClass = $rowAssets['asset_class'];
      $selected = ($_GET['asset_class_filter'] ?? '') == $assetClass ? 'selected' : '';
      echo "<option value='$assetClass' $selected>$assetClass</option>";
  }

  echo "</select>";
  echo "</form>";

  if (isset($_GET['asset_class_filter'])) {
      $assetClassFilter = $_GET['asset_class_filter'];
      if (!empty($assetClassFilter)) {
          $sql = "SELECT * FROM assets_archive WHERE asset_class = '$assetClassFilter'";
          $result = mysqli_query($conn, $sql);

          if (!$result) {
              printf("Error: %s\n", mysqli_error($conn));
              exit();
          }
      } else {
          // If no specific asset class is selected, fetch all assets
          $sql = "SELECT * FROM assets_archive";
          $result = mysqli_query($conn, $sql);

          if (!$result) {
              printf("Error: %s\n", mysqli_error($conn));
              exit();
          }
      }
  } else {
      // If no filter is provided, fetch all assets
      $sql = "SELECT * FROM assets_archive";
      $result = mysqli_query($conn, $sql);

      if (!$result) {
          printf("Error: %s\n", mysqli_error($conn));
          exit();
      }
  }
}
                    ?>
                    <br />

                  <div>
                    <table class="table">
                      <thead>
                        <tr >
                          <th scope="col" style="position:sticky; top:0;">S/n</th>
                          <th scope="col">Asset Name</th>
                          <th scope="col">Asset Class</th>
                          <th scope="col">GRV Number</th>
                          <th scope="col">ID Number</th>
                          <th scope="col">PV Number</th>
                         <th scope="col">Disposals</th> 
                          <th scope="col">Asset Type</th>
                          <th scope="col">Location</th>
                          <th scope="col">Acquistion Date</th>
                          <th scope="col">Historical Cost (GHS)</th>
                          <th scope="col">Dollar Rate</th>
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $serial_number = 1;
                        while ($row = mysqli_fetch_array($result)) {
                          echo "<tr>";
                          echo "<td>$serial_number</td>";
                          echo "<td>" . $row['asset_name'] . "</td>";
                          echo "<td>" . $row['asset_class'] . "</td>";
                          echo "<td>" . $row['grv_number'] . "</td>"; 
                          echo "<td>" . $row['id_number'] . "</td>"; 
                          echo "<td>" . $row['pv_number'] . "</td>";
                          echo "<td>" . $row['disposals'] . "</td>"; 
                          echo "<td>" . $row['asset_type'] . "</td>"; 
                          echo "<td>" . $row['location'] . "</td>";
                          echo "<td>" . $row['acquisition_date'] . "</td>";
                          echo "<td>" . number_format($row['additions'], 2, '.', ',') . "</td>"; 
                          echo "<td>" . $row['dollar_rate_used'] . "</td>"; ?>
        

        <td>
    <form action="index.php" method="POST">
        <input type="hidden" name="submit_archive" value="1">
        <input type="hidden" name="asset_id" value="<?php echo $row['asset_id']; ?>">
        <button type="submit" class="btn btn-outline-success"><?php echo $row['disposals'] ? 'Unarchive' : 'Archive'; ?></button>
    </form>
</td>
<?php
      echo "</tr>";

      $serial_number++;
    }
                        

    
    
                        
// Server-side code for handling archive action
if (isset($_POST['submit_archive'])) {
  $archiveAssetId = $_POST['asset_id'];

  // Get information about the asset to be archived
  $assetInfoQuery = "SELECT grv_number, id_number, pv_number FROM asset_register.assets_archive WHERE asset_id = '$archiveAssetId'";
  $assetInfoResult = mysqli_query($conn, $assetInfoQuery);

  if ($assetInfoResult) {
      $assetInfo = mysqli_fetch_assoc($assetInfoResult);
      $grvNumber = mysqli_real_escape_string($conn, $assetInfo['grv_number']);
      $idNumber = mysqli_real_escape_string($conn, $assetInfo['id_number']);
      $pvNumber = mysqli_real_escape_string($conn, $assetInfo['pv_number']);

      // Check if the grv_number, id_number, or pv_number already exists in the assets table
      $checkExistingQuery = "SELECT COUNT(*) as count FROM asset_register.assets 
                            WHERE grv_number = '$grvNumber' OR id_number = '$idNumber' OR pv_number = '$pvNumber'";
      $checkExistingResult = mysqli_query($conn, $checkExistingQuery);

      if ($checkExistingResult) {
          $existingRecordCount = mysqli_fetch_assoc($checkExistingResult)['count'];

          if ($existingRecordCount > 0) {
              // Display an alert that the record already exists in the assets table
              echo '<script>alert("Error unarchiving Asset: Record already exists in the assets table."); window.location.href = "index.php";</script>';
              exit();
          }
      }
  }

  // Use a transaction to ensure atomicity
  mysqli_autocommit($conn, false);

  // Move the record from the "assets" table to the "assets_archive" table
  $moveToArchiveQuery = "INSERT INTO asset_register.assets SELECT * FROM asset_register.assets_archive WHERE asset_id = '$archiveAssetId'";
  $deleteFromAssetsQuery = "DELETE FROM asset_register.assets_archive WHERE asset_id = '$archiveAssetId'";

  // Check for errors during the archive process
  $moveToArchiveResult = mysqli_query($conn, $moveToArchiveQuery);
  $deleteFromAssetsResult = mysqli_query($conn, $deleteFromAssetsQuery);

  if ($moveToArchiveResult && $deleteFromAssetsResult) {
      // Commit the transaction
      mysqli_commit($conn);
      echo '<script>alert("Asset successfully unarchived!"); window.location.href = "index.php";</script>';
  } else {
      // Rollback the transaction on failure
      mysqli_rollback($conn);

      // Display an error message with details
      $errorDetails = mysqli_error($conn);
      echo "<script>alert('Error unarchiving Asset: $errorDetails'); window.location.href = 'index.php';</script>";
  }

  // Reset autocommit to true for subsequent queries
  mysqli_autocommit($conn, true);
}




// SQL and script for handling form submission
if (isset($_POST['submit_disposal'])) {
  $assetId = $_POST['asset_id'];
  $disposalValue = $_POST['disposal_value'];

  if ($disposalValue < 0) {
    echo '<script>alert("Disposal value cannot be negative.");</script>';
    echo '<script>window.location.href = "index.php";</script>';
    exit();
}

  // Perform the update query
  $updateQuery = "UPDATE assets SET disposals = '$disposalValue' WHERE asset_id = '$assetId'";
  $updateResult = mysqli_query($conn, $updateQuery);

  if ($updateResult) {
      echo '<script>alert("Asset successfully disposed!");</script>';
      echo '<script>window.location.href = "index.php";</script>';
      exit();
  } else {
      printf("Error updating record: %s\n", mysqli_error($conn));
      exit();
  }
}
?>
<script>
  // Get the asset class dropdown element
  const assetClassDropdown = document.querySelector('[name="asset_class_filter"]');

  // Attach an event listener to the dropdown
  assetClassDropdown.addEventListener('change', function() {
    // Submit the form when the dropdown changes
    document.getElementById('filterForm').submit();
  });
</script>

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