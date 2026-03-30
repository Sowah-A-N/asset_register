<?php
require_once '../init.php';
if (isset($_POST['add'])) {





  $location=mysqli_real_escape_string($conn, $_POST['location']);
  $loc_code=mysqli_real_escape_string($conn, $_POST['loc_code']);
 

  if (empty($location)||empty($location))
  {
      echo "<script> alert('Check Details'); window.location='index.php' </script> ";  
      exit();
  }
  else
  {
    // SQL query to check if the sub_class already exists
    $check_query_location = "SELECT COUNT(*) as count FROM asset_location WHERE location = '$location'";
    $result_location = $conn->query($check_query_location);
    $row_location = $result_location->fetch_assoc();

    // SQL query to check if the sub_code already exists
    $check_query_loc_code = "SELECT COUNT(*) as count FROM asset_location WHERE loc_code = '$loc_code'";
    $result_loc_code = $conn->query($check_query_loc_code);
    $row_loc_code = $result_loc_code->fetch_assoc();

   if ( $row_location['count'] > 0) {
    echo '<script type="text/javascript">alert("Location already exists.");window.location=\'index.php\';</script>';
} 
elseif ($row_loc_code['count'] > 0) {
    echo '<script type="text/javascript">alert("Location Code already exists.");window.location=\'index.php\';</script>';
} else {
        // Supplier does not exist, proceed with insertion
        $insert_query = "INSERT INTO asset_location (location, loc_code) 
                        VALUES ('$location',  '$loc_code')";

        if ($conn->query($insert_query) === TRUE) {
            echo "<script>alert(' Location Added Successfully'); </script>";
        } else {
            echo "Error adding location: " . $conn->error;
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
  <title>View Asset Locations</title>
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
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">

      <?php include "../sidebar.html" ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row" id="proBanner">
          </div>
          <div class="d-xl-flex justify-content-between align-items-start">
            <h2 class="text-dark font-weight-bold mb-2"> Added Asset Locations </h2>
            <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
            <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
                  <button class="btn btn-outline-primary" type="button" id="dropdownMenuButton1" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#exampleModal"> Add a Location</button>
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="d-sm-flex justify-content-between align-items-center transaparent-tab-border {">
              </div>
              <div class="tab-content tab-transparent-content">
                <div class="tab-pane fade show active" id="business-1" role="tabpanel" aria-labelledby="business-tab">
                  <div class="row">
                    <!--Table goes here -->
                    <?php
                    $sql = "SELECT * FROM asset_location";
                    $result = mysqli_query($conn, $sql);
                    if (!$result) {
                      error_log(mysqli_error($conn));
                      exit();
                    } ?>
                    <br />
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">S/n</th>
                          <th scope="col">Location</th>
                          <th scope="col">Loc Code</th>
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $serial_number = 1;
                        while ($row = mysqli_fetch_array($result)) {
                          echo "<td>$serial_number</td>";
                          echo "<td>" . $row['location'] . "</td>"; 
                          echo "<td>" . $row['loc_code'] . "</td>"; 
                          ?>
                          <td>
                <form action="index.php" method="POST">
                    <?php echo "<input type='hidden' name='id' value='" . $row['loc_id'] . "' ?>"; ?>
                    <button type="submit" class="btn btn-danger" name="archive_location">Archive</button>
                </form>
            </td>


                        <?php
                          echo "</form></tr>";
                          $serial_number++;
                        }

                        ?>
                        <?php
// Server-side code for handling archive action
if (isset($_POST['archive_location'])) {
    $archiveLocationId = $_POST['id'];

    // Use a transaction to ensure atomicity
    mysqli_autocommit($conn, false);

    // Move the record from the "asset_location" table to the "asset_location_archive" table
    $moveToArchiveQuery = "INSERT INTO asset_register.asset_location_archive SELECT * FROM asset_register.asset_location WHERE loc_id = '$archiveLocationId'";
    $deleteFromLocationQuery = "DELETE FROM asset_location WHERE loc_id = '$archiveLocationId'";

    // Check for errors during the archive process
    $moveToArchiveResult = mysqli_query($conn, $moveToArchiveQuery);
    if (!$moveToArchiveResult) {
        // Rollback the transaction on failure
        mysqli_rollback($conn);
        error_log(mysqli_error($conn)); echo '<script>alert("A database error occurred."); window.location.href = "index.php";</script>';
        exit();
    }

    // Check for errors during the deletion process
    $deleteFromLocationResult = mysqli_query($conn, $deleteFromLocationQuery);
    if (!$deleteFromLocationResult) {
        // Rollback the transaction on failure
        mysqli_rollback($conn);
        error_log(mysqli_error($conn)); echo '<script>alert("A database error occurred."); window.location.href = "index.php";</script>';
        exit();
    }

    // Commit the transaction
    mysqli_commit($conn);
    echo '<script>alert("Location successfully archived!"); window.location.href = "index.php";</script>';

    // Reset autocommit to true for subsequent queries
    mysqli_autocommit($conn, true);
}
?>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

                                  <script>
                                    function capitalizeFirstLetter(input) {
                                    // Capitalize the first letter of each word
                                    input.value = input.value.replace(/\b\w/g, (char) => char.toUpperCase());
                                   }

                                   </script>

        <div class="col-xl-3  col-lg-6 col-sm-6 grid-margin stretch-card">
                          <!-- Modal -->
                          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Enter New Location</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>

                                <div class="modal-body">
                                  <form method="POST" action="index.php" name="add_claim">

                                  <div class="form-group">
                                  <label for="sub_class">Enter New Location:</label>
                                    <input type="text" class="form-control" name="location" placeholder="Enter Location" required oninput="capitalizeFirstLetter(this)">
                                  </div>

                                  <div class="form-group">
                                  <label for="sub_class">Enter Location Code:</label>
                                    <input type="text" class="form-control" name="loc_code" placeholder="Enter Code" required oninput="capitalizeFirstLetter(this)">
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