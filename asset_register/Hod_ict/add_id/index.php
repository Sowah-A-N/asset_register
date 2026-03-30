<?php

require_once '../init.php';
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// if (isset($_POST['submit_archive'])) {
//   $archiveAssetId = mysqli_real_escape_string($conn,$_POST['asset_id']);
  

//   // Use a transaction to ensure atomicity
//   mysqli_autocommit($conn, false);

//   // Move the record from the "assets" table to the "assets_archive" table
//   $moveToArchiveQuery = "INSERT INTO asset_register.assets_archive SELECT * FROM asset_register.assets WHERE asset_id = '$archiveAssetId'";
//   $deleteFromAssetsQuery = "DELETE FROM assets WHERE asset_id = '$archiveAssetId'";

//   // Check for errors during the archive process
//   $moveToArchiveResult = mysqli_query($conn, $moveToArchiveQuery);
//   $deleteFromAssetsResult = mysqli_query($conn, $deleteFromAssetsQuery);

//   if (!$moveToArchiveResult || !$deleteFromAssetsResult) {
//       // Rollback the transaction on failure
//       mysqli_rollback($conn);
//       echo '<script>alert("Error archiving Asset or deleting from assets table: ' . mysqli_error($conn) . '"); window.location.href = "index.php";</script>';
//       exit();
//   }

//   // Commit the transaction
//   mysqli_commit($conn);
//   echo '<script>alert("Asset successfully archived!"); window.location.href = "index.php";</script>';

//   // Reset autocommit to true for subsequent queries
//   mysqli_autocommit($conn, true);
// }

if (isset($_POST['submit_move'])) {
 
  $id_number = mysqli_escape_string($conn, $_POST['idNumber']);
  $identification_number=mysqli_escape_string($conn, $_POST['asset_id']);

  

  if (empty($id_number))
  {
      echo "<script> alert('Check Details'); window.location='index.php' </script> ";  
      exit();
  }
  else
  {
    // SQL query to check if the supplier already exists
    $check_query = "SELECT COUNT(*) as count FROM assets WHERE id_number = '$id_number'";
    $result = $conn->query($check_query);

    // Fetch the result row
    $row = $result->fetch_assoc();

    if (isset($row['count']) && $row['count'] > 0) {
        // Id Number already exists, display an error message
        echo '<script type="text/javascript">alert("ID Number already exists.");window.location=\'index.php\';</script>';
    } else {
        // Supplier does not exist, proceed with insertion
        $updateQuery = "UPDATE assets SET id_number = '$id_number' WHERE serial_number = '$identification_number'";

        if ($conn->query($updateQuery) === TRUE) {
          echo '<script type="text/javascript">alert("Asset ID Number Successfully added.");window.location=\'index.php\';</script>';
        } else {
          echo '<script type="text/javascript">alert("Error adding Asset ID Number.");window.location=\'index.php\';</script>'; $conn->error;
        }
    }

  }
}

// if (isset($_POST['submit_disposal'])) {
//    $assetId = $_POST['asset_id'];
  
//   // // Fetch the asset's additions value from the database
//    $sqlFetchAdditions = "SELECT additions FROM assets WHERE asset_id = '$assetId'";
//    $resultFetchAdditions = mysqli_query($conn, $sqlFetchAdditions);

//   if (!$resultFetchAdditions) {
//       echo '<script>alert("Error fetching additions value.");</script>';
//       echo '<script>window.location.href = "index.php";</script>';
//       exit();
//   }

//   $rowAdditions = mysqli_fetch_assoc($resultFetchAdditions);
//   $disposalValue = $rowAdditions['additions'];

//   if ($disposalValue < 0) {
//       echo '<script>alert("Disposal value cannot be negative.");</script>';
//       echo '<script>window.location.href = "index.php";</script>';
//       exit();
//   }

//   // // Perform the update query
//   $updateQuery = "UPDATE assets SET disposals = '$disposalValue' WHERE asset_id = '$assetId'";
//   $updateResult = mysqli_query($conn, $updateQuery);

//   if ($updateResult) {
//       echo '<script>alert("Asset successfully disposed!");</script>';
//       echo '<script>window.location.href = "index.php";</script>';
//       exit();
//   } else {
//       printf("Error updating record: %s\n", mysqli_error($conn));
//       exit();
//   }
 
// }


?>



<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Add ID/ Users to Assets</title>
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
  <style>
            input,
        select {
          grid-column: 2 / 3;
          grid-row: span 1;
          width: calc(100% - 10px);
          padding: 8px;
          box-sizing: border-box;
          margin-bottom: 5px; /* Adjusted margin to 5px */
          margin-left: 5px; /* Added margin to the left of the input/select */
        }
          </style>

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
            <h2 class="text-dark font-weight-bold mb-2"> Filter Assets by Classes </h2>
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
    error_log(mysqli_error($conn)); die('A database error occurred.');
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
          $sql = "SELECT * FROM assets WHERE asset_class = '$assetClassFilter' AND disposals = 0 AND id_number IS NULL ";
          $result = mysqli_query($conn, $sql);

          if (!$result) {
              error_log(mysqli_error($conn));
              exit();
          }
      } else {
          // If no specific asset class is selected, fetch all assets
          $sql = "SELECT * FROM assets WHERE disposals = 0 AND id_number IS NULL";
          $result = mysqli_query($conn, $sql);

          if (!$result) {
              error_log(mysqli_error($conn));
              exit();
          }
      }
  } else {
      // If no filter is provided, fetch all assets
      $sql = "SELECT * FROM assets WHERE disposals = 0 AND id_number IS NULL";
      $result = mysqli_query($conn, $sql);

      if (!$result) {
          error_log(mysqli_error($conn));
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
                          <th scope="col">Sub Class</th>
                          <th scope="col">serial Number</th>
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
                        $sn_number = 1;
                        while ($row = mysqli_fetch_array($result)) {
                          echo "<td>$sn_number</td>";
                          echo "<td>" . $row['asset_name'] . "</td>";
                          echo "<td>" . $row['asset_class'] . "</td>";
                          echo "<td>" . $row['sub_class'] . "</td>";
                          echo "<td>" . $row['serial_number'] . "</td>"; 
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
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#moveModal<?php echo $row['serial_number']; ?>">
            Assign ID Number
        </button>
    </td>

   
 

    <?php
    echo "</tr>";

  
    $identification_number = $row['id_number'];
    $location= $row['location'];
    $assetName = $row['asset_name'];
    $asset_class = $row['asset_class'];
    $asset_sub_class = $row['sub_class'];
    $grv_number =$row['grv_number'];
    $serial_number = $row['serial_number'];
    $pv_number = $row['pv_number'];
    $assetType  =$row['asset_type'];
    $historicalCost = $row['pv_number'];
    $active_res_value = $row['pv_number'];
    $acquisitionDate = $row['pv_number'];
    $dollar_rate =$row["dollar_rate_used"];

    


    // Show Details Modal
              echo '<div class="modal fade" id="moveModal' . $row['serial_number'] . '" tabindex="-1" role="dialog" aria-labelledby="moveModalLabel" aria-hidden="true">';
              echo '  <div class="modal-dialog" role="document">';
              echo '    <div class="modal-content">';
              echo '      <div class="modal-header">';
              echo '        <h5 class="modal-title" id="moveModalLabel">Add Asset ID Number</h5>';
              echo '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              echo '          <span aria-hidden="true">&times;</span>';
              echo '        </button>';
              echo '      </div>';
              echo '      <div class="modal-body">';
              echo '        <form action="index.php" method="POST">';
              echo '          <input type="hidden" name="asset_id" value="' . $row['serial_number'] . '">';
              echo '          <div class="form-group">';

              echo' <label for="location">Asset Name:</label>';
              echo"<input type='text' id='location' name='location' value= ' $assetName'  readonly>";

              echo' <label for="Asset name">Asset Class:</label>';
              echo"<input type='text' id='location' name='location' value= ' $asset_class'  readonly>";

              echo' <label for="Asset Sub Class">Sub Class:</label>';
              echo"<input type='text' id='location' name='location' value= ' $asset_sub_class'  readonly>";

              echo' <label for="Serial Number">Serial Number:</label>';
              echo"<input type='text' id='location' name='location' value= ' $serial_number'  readonly>";

              echo' <label for="location">Location:</label>';
              echo"<input type='text' id='location' name='location' value= ' $location'  readonly>";


            

            
              echo '          </div>';
              echo '          <div class="form-group">';
              echo '              <label for="Number">ID Number:</label>';
              echo '              <input type="text" name="idNumber" class="form-control" required >';

              echo '          </div>';
              echo '          <button type="submit" class="btn btn-primary" name="submit_move">Submit</button>';
              echo '        </form>';
              echo '      </div>';
              echo '    </div>';
              echo '  </div>';
              echo '</div>';


                  $sn_number++;
              }




              



?>



<script>


function capitalizeFirstLetter(input) {
            // Capitalize the first letter of each word
            input.value = input.value.replace(/\b\w/g, (char) => char.toUpperCase());
        }
    // Attach date picker functionality to the input field
    flatpickr("#acquisition_date", {
        dateFormat: "d-m-Y", // Set the desired date format
        allowInput: true, // Allow manual input
        maxDate: new Date().fp_incr(0), // Restrict dates to today and future
    });
</script>

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