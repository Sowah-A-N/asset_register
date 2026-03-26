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

if (isset($_POST['submit_archive'])) {
  $archiveAssetId = mysqli_real_escape_string($conn,$_POST['asset_id']);
  

  // Use a transaction to ensure atomicity
  mysqli_autocommit($conn, false);

  // Move the record from the "assets" table to the "assets_archive" table
  $moveToArchiveQuery = "INSERT INTO asset_register.assets_archive SELECT * FROM asset_register.assets WHERE asset_id = '$archiveAssetId'";
  $deleteFromAssetsQuery = "DELETE FROM assets WHERE asset_id = '$archiveAssetId'";

  // Check for errors during the archive process
  $moveToArchiveResult = mysqli_query($conn, $moveToArchiveQuery);
  $deleteFromAssetsResult = mysqli_query($conn, $deleteFromAssetsQuery);

  if (!$moveToArchiveResult || !$deleteFromAssetsResult) {
      // Rollback the transaction on failure
      mysqli_rollback($conn);
      echo '<script>alert("Error archiving Asset or deleting from assets table: ' . mysqli_error($conn) . '"); window.location.href = "index.php";</script>';
      exit();
  }

  // Commit the transaction
  mysqli_commit($conn);
  echo '<script>alert("Asset successfully archived!"); window.location.href = "index.php";</script>';

  // Reset autocommit to true for subsequent queries
  mysqli_autocommit($conn, true);
}

if (isset($_POST['submit_move'])) {
  $new_location = mysqli_escape_string($conn,$_POST['new_location']);
  $notes = mysqli_escape_string($conn, $_POST['notes']);
  $identification_number=mysqli_escape_string($conn, $_POST['asset_id']);
  $new_user = mysqli_escape_string($conn, $_POST['new_user']);

  

  if (empty($new_location)||empty($notes)||empty($new_user))
  {
      echo "<script> alert('Check Details'); window.location='index.php' </script> ";  
      exit();
  }
  
  $sqlCurrentLocation = "SELECT location FROM assets WHERE serial_number = $identification_number";
  $resultCurrentLocation = mysqli_query($conn, $sqlCurrentLocation);

  $sqlCurrentUser = "SELECT user FROM assets WHERE serial_number = $identification_number";
  $resultCurrentUser = mysqli_query($conn, $sqlCurrentUser);

  if(!$resultCurrentUser)
  {
    echo "Error fetching current user: " . mysqli_error($conn);
  }

  else{
     $rowCurrentUser = mysqli_fetch_array($resultCurrentUser);
      $old_user = $rowCurrentUser[0];
      if($old_user==$new_user){
        echo "<script> alert('Users are the same'); window.location='index.php' </script> ";  
      exit();
  }}
  
  if (!$resultCurrentLocation) {
      echo "Error fetching current location: " . mysqli_error($conn);
      // Handle the error accordingly
  } else {
      
      $rowCurrentLocation = mysqli_fetch_array($resultCurrentLocation);
      $currentLocation = $rowCurrentLocation[0];
      if($currentLocation==$new_location){
        echo "<script> alert('Locations are the same'); window.location='index.php' </script> ";  
      exit();
      }



      else{

      
      
 // Perform the update query
  $updateQuery = "UPDATE assets SET location = '$new_location', user = '$new_user' WHERE serial_number = '$identification_number'";
  
  $updateResult = mysqli_query($conn, $updateQuery);

  $insert_query = "INSERT INTO moved_assets (serial_number , notes, old_location, old_user, New_location, new_user) 
  VALUES ('$identification_number', '$notes','$currentLocation', '$old_user', '$new_location', '$new_user')";
  // echo "Update Query: $updateQuery <br>";
  // echo "Insert Query: $insert_query <br>";
  $inserted_query=mysqli_query($conn, $insert_query);


  if ($updateResult) {
      echo '<script>alert("Asset Location Successfully Changed!");</script>';
        echo '<script>window.location.href = "index.php";</script>';
      exit();
  } else {
      printf("Error changing Location: %s\n", mysqli_error($conn));
      exit();
  }
}
}
}

// if (isset($_POST['submit_disposal'])) {
//    $assetId = $_POST['asset_id'];
  
//   // // Fetch the asset's additions value from the database
//    $sqlFetchAdditions = "SELECT * FROM assets WHERE asset_id = '$assetId'";
//    $resultFetchAdditions = mysqli_query($conn, $sqlFetchAdditions);

//   if (!$resultFetchAdditions) {
//       echo '<script>alert("Error fetching additions value.");</script>';
//       echo '<script>window.location.href = "index.php";</script>';
//       exit();
//   }

//   $rowAdditions = mysqli_fetch_assoc($resultFetchAdditions);
//   $disposalValue = $rowAdditions['additions'];

// $assetName = $rowAdditions['asset_name'];
// $asset_class = $rowAdditions['asset_class'];
// $asset_sub_class = $rowAdditions['sub_class'];
// $grv_number = $rowAdditions['grv_number'];
// $serial_number =$rowAdditions['serial_number'];
// $pv_number = $rowAdditions['pv_number'];
// $supplier  = $rowAdditions['supplier_name'];
// $user=$rowAdditions['user'];
// $assetType  = $rowAdditions['asset_type'];
// $location = $rowAdditions['location'];
// $historicalCost = $rowAdditions['additions'];
// $active_res_value = $rowAdditions['active_res_value'];
// $acquisitionDate = $rowAdditions['acquisition_date'];
// $assetid=$rowAdditions['id_number'];





//   if ($disposalValue < 0) {
//       echo '<script>alert("Disposal value cannot be negative.");</script>';
//       echo '<script>window.location.href = "index.php";</script>';
//       exit();
//   }
   
//   $sql = "INSERT INTO disposals (asset_name, asset_class, sub_class, grv_number, serial_number, pv_number, id_number,  supplier_name, asset_type, location, user, acquisition_date, current_year, additions, active_res_value, dollar_rate_used, disposal_value)
//                           VALUES ('$assetName','$asset_class', '$asset_sub_class', '$grv_number', '$serial_number',  '$pv_number', '$asset_id', '$supplier', '$assetType',  '$location',  '$user', '$acquisitionDate', '$currentYear', '$historicalCost', '$active_res_value', '$rate_used', '$disposalValue')";
  
//   // // Perform the update query
//   $updateQuery = "UPDATE assets SET disposals = 1 WHERE asset_id = '$assetId'";
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

if (isset($_POST['submit_disposal'])) {
  // Ensure the connection is included
  $assetId = $_POST['asset_id'];

  // Fetch the asset's details
  $sqlFetchAdditions = "SELECT * FROM assets WHERE asset_id = ?";
  $stmt = mysqli_prepare($conn, $sqlFetchAdditions);
  mysqli_stmt_bind_param($stmt, "s", $assetId);
  mysqli_stmt_execute($stmt);
  $resultFetchAdditions = mysqli_stmt_get_result($stmt);

  if (!$resultFetchAdditions || mysqli_num_rows($resultFetchAdditions) == 0) {
      echo '<script>alert("Asset not found."); window.location.href = "index.php";</script>';
      exit();
  }

  $rowAdditions = mysqli_fetch_assoc($resultFetchAdditions);

  // Assign values
  $disposalValue = $rowAdditions['additions'];
  if ($disposalValue < 0) {
      echo '<script>alert("Disposal value cannot be negative."); window.location.href = "index.php";</script>';
      exit();
  }

  // Ensure necessary variables exist
  $currentYear = date("Y");  // Assuming disposal year is the current year
  $rate_used = 

  // Insert into disposals table
  $sql = "INSERT INTO disposals (asset_name, asset_class, sub_class, grv_number, serial_number, pv_number, id_number, supplier_name, asset_type, location, user, acquisition_date, current_year, additions, active_res_value, dollar_rate_used, disposal_value) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "sssssssssssssssss", 
      $rowAdditions['asset_name'], 
      $rowAdditions['asset_class'], 
      $rowAdditions['sub_class'], 
      $rowAdditions['grv_number'], 
      $rowAdditions['serial_number'], 
      $rowAdditions['pv_number'], 
      $rowAdditions['id_number'], 
      $rowAdditions['supplier_name'], 
      $rowAdditions['asset_type'], 
      $rowAdditions['location'], 
      $rowAdditions['user'], 
      $rowAdditions['acquisition_date'], 
      $currentYear, 
      $rowAdditions['additions'], 
      $rowAdditions['active_res_value'], 
      $rowAdditions['dollar_rate_used'], 
      $disposalValue
  );

  if (!mysqli_stmt_execute($stmt)) {
      printf("Error inserting disposal record: %s\n", mysqli_error($conn));
      exit();
  }

  // Update the assets table
  $updateQuery = "UPDATE assets SET disposals = 1 WHERE asset_id = ?";
  $stmt = mysqli_prepare($conn, $updateQuery);
  mysqli_stmt_bind_param($stmt, "s", $assetId);

  if (mysqli_stmt_execute($stmt)) {
      echo '<script>alert("Asset successfully disposed!"); window.location.href = "index.php";</script>';
      exit();
  } else {
      printf("Error updating record: %s\n", mysqli_error($conn));
      exit();
  }
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>View Active Assets</title>
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
            <h2 class="text-dark font-weight-bold mb-2"> Filter Active Assets by Classes </h2>
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
          $sql = "SELECT * FROM assets WHERE asset_class = '$assetClassFilter' AND disposals = 0";
          $result = mysqli_query($conn, $sql);

          if (!$result) {
              printf("Error: %s\n", mysqli_error($conn));
              exit();
          }
      } else {
          // If no specific asset class is selected, fetch all assets
          $sql = "SELECT * FROM assets WHERE disposals = 0";
          $result = mysqli_query($conn, $sql);

          if (!$result) {
              printf("Error: %s\n", mysqli_error($conn));
              exit();
          }
      }
  } else {
      // If no filter is provided, fetch all assets
      $sql = "SELECT * FROM assets WHERE disposals = 0";
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
                          <th scope="col">Sub Class</th>
                          <th scope="col">Assigned User</th>
                          <th scope="col">Serial Number</th>
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
                          echo "<td>$serial_number</td>";
                          echo "<td>" . $row['asset_name'] . "</td>";
                          echo "<td>" . $row['asset_class'] . "</td>";
                          echo "<td>" . $row['sub_class'] . "</td>";
                          echo "<td>" . $row['user'] . "</td>";
                          echo "<td>" . $row['serial_number'] . "</td>"; 
                          echo "<td>" . $row['grv_number'] . "</td>"; 
                          echo "<td>" . $row['id_number'] . "</td>"; 
                          echo "<td>" . $row['pv_number'] . "</td>";
                          echo "<td>" . $row['disposals'] . "</td>"; 
                          echo "<td>" . $row['asset_type'] . "</td>"; 
                          echo "<td>" . $row['location'] . "</td>";
                          echo "<td>" . date("d F, Y", strtotime($row['acquisition_date'])) . "</td>";

                          echo "<td>" . number_format($row['additions'], 2, '.', ',') . "</td>"; 
                          echo "<td>" . $row['dollar_rate_used'] . "</td>"; ?>
     

    <td>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#moveModal<?php echo $row['serial_number']; ?>">
            Move Asset
        </button>
    </td>

   
    <td>
    <form action="index.php" method="POST">
        <input type="hidden" name="submit_archive" value="1">
        <input type="hidden" name="asset_id" value="<?php echo $row['asset_id']; ?>">
        <button type="submit" name="submit_archive" class="btn btn-danger">Archive</button>
    </form>
</td>
<td>
<form action="index.php" method="POST">
        <input type="hidden" name="submit_disposal" value="1">
        <input type="hidden" name="asset_id" value="<?php echo $row['asset_id']; ?>">
        <button type="submit" name="submit_disposal" class="btn btn-success">Dispose</button>
    </form>
 </td>

    <?php
    echo "</tr>";

  
    $identification_number = $row['serial_number'];
    $old_location= $row['location'];
    $old_user=$row['user'];
    $user;
    

    // Move location Modal
              echo '<div class="modal fade" id="moveModal' . $row['serial_number'] . '" tabindex="-1" role="dialog" aria-labelledby="moveModalLabel" aria-hidden="true">';
              echo '  <div class="modal-dialog" role="document">';
              echo '    <div class="modal-content">';
              echo '      <div class="modal-header">';
              echo '        <h5 class="modal-title" id="moveModalLabel">Move Asset Location</h5>';
              echo '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
              echo '          <span aria-hidden="true">&times;</span>';
              echo '        </button>';
              echo '      </div>';
              echo '      <div class="modal-body">';
              echo '        <form action="index.php" method="POST">';
              echo '          <input type="hidden" name="asset_id" value="' . $row['serial_number'] . '">';
              echo '          <div class="form-group">';
             echo' <label for="location">Current Location:</label>';
              echo"<input type='text' id='location' name='location' value= ' $old_location'  readonly>";


              echo '          <div class="form-group">';
              echo' <label for="location">Current User:</label>';
               echo"<input type='text' id='user' name='user' value= ' $old_user'  readonly>";


              echo '              <label for="asset_location">Select New Asset Location:</label>';
              echo '              <select id="asset_location" name="new_location" class="form-control" required>';
              echo '                  <option hidden value="">Select New Asset Location</option>';
              // Fetch locations from the database
              $sqlLocations = "SELECT location FROM asset_location";
              $resultLocations = mysqli_query($conn, $sqlLocations);
              if ($resultLocations && mysqli_num_rows($resultLocations) > 0) {
                  while ($rowLocation = mysqli_fetch_assoc($resultLocations)) {
                      $location = $rowLocation['location'];
                      echo "<option value='$location'>$location</option>";
                  }
              }
              echo '              </select>';
              echo '          </div>';


             
              echo '              <label for="asset_location">Select New User:</label>';
              echo '              <select id="asset_user" name="new_user" class="form-control" required>';
              echo '                  <option hidden value="">Select New Asset User</option>';
              // Fetch Users from the database
              $sqlUsers = "SELECT staff_first_name , staff_last_name FROM asset_users";
              $resultUsers = mysqli_query($conn, $sqlUsers);
              if ($resultUsers && mysqli_num_rows($resultUsers) > 0) {
                  while ($row_user = mysqli_fetch_assoc($resultUsers)) {
                      $user = $row_user['staff_first_name']. ' '.$row_user['staff_last_name'];
                      echo "<option value='$user'>$user</option>";
                  }
              }
              echo '              </select>';
              echo '          </div>';



              echo '          <div class="form-group">';
              echo '              <label for="notes">Notes:</label>';
              echo '              <input type="text" name="notes" class="form-control" required oninput="capitalizeFirstLetter(this)">';
              echo '          </div>';
              echo '          <button type="submit" class="btn btn-primary" name="submit_move">Submit</button>';
              echo '        </form>';
              echo '      </div>';
              echo '    </div>';
              echo '  </div>';
              echo '</div>';


                  $serial_number++;
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