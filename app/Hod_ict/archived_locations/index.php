<?php
require_once '../init.php';
if (isset($_POST['add'])) {

  $location_name = mysqli_real_escape_string($conn, $_POST['name']);

  if (empty($location_name)) {
    echo "<script> alert('Check Details'); window.location='index.php' </script> ";
    exit();
  } else {

    // SQL query to check if the asset location already exists
    $check_query = "SELECT COUNT(*) as count FROM asset_location WHERE location = '$location_name'";
    $result = $conn->query($check_query);

    // Fetch the result row
    $row = $result->fetch_assoc();

    if (isset($row['count']) && $row['count'] > 0) {
      // Asset location already exists, display an error message
      echo '<script type="text/javascript">alert("Asset location already exists.");window.location=\'index.php\';</script>';
    } else {
      // Asset location does not exist, proceed with insertion
      $insert_query = "INSERT INTO asset_location (location) VALUES ('$location_name')";

      if ($conn->query($insert_query) === TRUE) {
        echo '<script type="text/javascript">alert("Asset location successfully added.");window.location=\'index.php\';</script>';
      } else {
        echo '<script type="text/javascript">alert("Error adding asset location.");window.location=\'index.php\';</script>';
        error_log($conn->error);
      }
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
    <title>View Archived Locations
      
    </title>
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
                  <p class="mb-1 text-black"><?php echo($username) ?></p>
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
    <?php include "../sidebar.html" ?>
  
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row" id="proBanner">
            </div>
            <div class="d-xl-flex justify-content-between align-items-start">
              <h2 class="text-dark font-weight-bold mb-2"> List of Archived Locations </h2>
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
                        $sql="SELECT * FROM asset_location_archive ORDER BY loc_ID DESC LIMIT 50";
                        $result=mysqli_query($conn, $sql);
                        if (!$result) {
                            error_log(mysqli_error($conn));
                            exit();
                        } ?>
                        <br />
                      <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">S/n</th>
                        
                          <th scope="col">Asset Location</th>
                
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $serial_number=1;
                                    while($row= mysqli_fetch_array($result))
                                   {
                                        echo "<td>$serial_number</td>"; 
                                        echo "<td>".$row['location']."</td>";
                                        
                                        echo "<form action='index.php' method='POST'>";
                                        echo "<input type='hidden' name='id' value='" . $row['loc_id'] . "'>";
                                       
                                        echo "<td><button type='submit' class='btn btn-outline-success' name='unarchive_location'>Unarchive</button></td>";
                                        echo "</form></tr>";
                                        
                                        $serial_number++;
                                   }
                    
                               ?>

<?php
// Server-side code for handling archive action for suppliers
// Server-side code for handling unarchive action for suppliers
if (isset($_POST['unarchive_location'])) {
  $unarchiveLocationId = $_POST['id'];

  // Use a transaction to ensure atomicity
  mysqli_autocommit($conn, false);

  // Get the location name from the archived table
  $getLocationQuery = "SELECT location FROM asset_location_archive WHERE loc_id = '$unarchiveLocationId'";
  $locationResult = $conn->query($getLocationQuery);

  if ($locationResult && $locationResult->num_rows > 0) {
      $locationData = $locationResult->fetch_assoc();
      $location_name = $locationData['location'];

      // Check if the location already exists in the asset_location table
      $check_query = "SELECT COUNT(*) as count FROM asset_location WHERE location = '$location_name'";
      $result = $conn->query($check_query);

      // Fetch the result row
      $row = $result->fetch_assoc();

      if (isset($row['count']) && $row['count'] > 0) {
          // Location already exists, display an error message
          echo '<script type="text/javascript">alert("Location already exists.");window.location=\'index.php\';</script>';
      } else {
          // Location does not exist, proceed with insertion
          $insert_query = "INSERT INTO asset_location (location) VALUES ('$location_name')";

          // Move the record from the "asset_location_archive" table to the "asset_location" table
          $moveToAssetLocationsQuery = "INSERT INTO asset_location SELECT * FROM asset_location_archive WHERE loc_id = '$unarchiveLocationId'";
          $deleteFromAssetLocationsArchiveQuery = "DELETE FROM asset_location_archive WHERE loc_id = '$unarchiveLocationId'";

          $moveToAssetLocationsResult = mysqli_query($conn, $moveToAssetLocationsQuery);
          $deleteFromAssetLocationsArchiveResult = mysqli_query($conn, $deleteFromAssetLocationsArchiveQuery);

          if ($moveToAssetLocationsResult && $deleteFromAssetLocationsArchiveResult) {
              // Commit the transaction
              mysqli_commit($conn);
              echo '<script>alert("Asset location successfully unarchived!"); window.location.href = "index.php";</script>';
          } else {
              // Rollback the transaction on failure
              mysqli_rollback($conn);
              echo '<script>alert("Error unarchiving asset location!"); window.location.href = "index.php";</script>';
          }

          // Reset autocommit to true for subsequent queries
          mysqli_autocommit($conn, true);
      }
  } else {
      // Handle the case where the location data is not found
      echo '<script>alert("Error retrieving location data!"); window.location.href = "index.php";</script>';
  }
}


?>
                  
                  
                  <script>
                                    function capitalizeFirstLetter(input) {
                                    // Capitalize the first letter of each word
                                    input.value = input.value.replace(/\b\w/g, (char) => char.toUpperCase());
                                }

                                  </script>
                    </div></p>
                            </div>
                      </div>
                      <div class="col-xl-3  col-lg-6 col-sm-6 grid-margin stretch-card">
                          <!-- Modal -->
                          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Enter Supplier Details</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>

                                <div class="modal-body">
                                  <form method="POST" action="index.php" name="add_claim">

                                  <div class="form-group">
                                    <input type="text" class="form-control" name="name" placeholder="Enter Name of Supplier" required oninput="capitalizeFirstLetter(this)">
                                  </div>

                                  <div class="form-group">
                                    <input type="text" class="form-control" name="location" placeholder="Enter Location of Supplier" required oninput="capitalizeFirstLetter(this)">
                                  </div>

                                  <div class="form-group">
                                    <input type="text" class="form-control" name="number" minlength="10"  maxlength="10" placeholder="Enter Contact of Supplier" required>
                                  </div>
 
                                </div>
                                <div class="modal-footer">
                                  <button type="submit" class="btn btn-success" name="add">Submit</button>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>

                        </div>
                      </div>
                      <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
                        </div>
                      </div>
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
    <script>
    $('#myModal').on('shown.bs.modal', function () {
      $('#myInput').trigger('focus')
    })
    </script>
  </body>
</html>