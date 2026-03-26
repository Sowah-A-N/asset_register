<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location:../login/");
  die();
}
$username = $_SESSION['username'];
include "../datacon.php";

if(isset($_POST['add']))
{

  $first_name=mysqli_real_escape_string($conn, $_POST['F_name']);
  $last_name=mysqli_real_escape_string($conn, $_POST['L_name']);
  $staff_id=mysqli_real_escape_string($conn, $_POST['Staff_ID']);
  $department=mysqli_real_escape_string($conn, $_POST['department']);

  $sql="SELECT dep_name FROM department WHERE t_id='$department' ";
  $result=mysqli_query($conn, $sql);
  $row=mysqli_fetch_array($result);
  $department_db=$row[0];

 

  if (empty($first_name) || empty($last_name) || empty($staff_id) || empty( $department))
  {
      echo "<script> alert('Check Details');  </script> ";  
      exit();
  }
  // else
  // {
  //   // Hashing the password using bcrypt with a cost factor of 12
  //   $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

  //    // Check if email already exists
  //    $checkEmailQuery = "SELECT * FROM user_details WHERE Email = '$email'";
  //    $result = $conn->query($checkEmailQuery);
 
  //    if ($result->num_rows > 0) {
  //        // Email already exists, display an error message
  //        echo '<script>alert("Email already exists. Please Enter a different email.");window.location=\'index.php\';</script>';
  //        exit();
  //    }

    // SQL query to check if the Staff ID already exists
    $check_query = "SELECT COUNT(*) as count FROM asset_users WHERE staff_id = '$staff_id'";
    $result = $conn->query($check_query);

    // Fetch the result row
    $row = $result->fetch_assoc();

    if (isset($row['count']) && $row['count'] > 0) {
        // Staff ID already exists, display an error message
        echo '<script type="text/javascript">alert("Staff ID Number already exists.");window.location=\'index.php\';</script>';
    } 
    else {
        // Supplier does not exist, proceed with insertion
        $insert_query = "INSERT INTO asset_users (staff_id, staff_first_name, staff_last_name, department) 
                        VALUES ('$staff_id', '$first_name', '$last_name', '$department_db')";

        if ($conn->query($insert_query) === TRUE) {
          echo '<script type="text/javascript">alert("New User added Successfully.");window.location=\'index.php\';</script>';
        } else {
            echo "Error adding User: " . $conn->error;
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
    <title>View/Add Users</title>
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
              <h2 class="text-dark font-weight-bold mb-2"> List of Users </h2>
              <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
                <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
                  <button class="btn btn-outline-primary" type="button" id="dropdownMenuButton1" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#exampleModal"> Add a New User</button>
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
                        $sql="SELECT * FROM asset_users ";
                        $result=mysqli_query($conn, $sql);
                        if (!$result) {
                            printf("Error: %s\n", mysqli_error($conn));
                            exit();
                        } ?>
                        <br />
                      <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">S/n</th>
                          <th scope="col">Staff ID Number</th>
                          <th scope="col">Staff First Name</th>
                          <th scope="col">Staff Last Name</th>
                          <th scope="col">Department</th>

                          
                        </tr>
                      </thead>
                      <tbody>
                                          <?php
                    $serial_number = 1;
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>$serial_number</td>";
                        echo "<td>" . $row['staff_id']  . "</td>";
                        echo "<td>" . $row['staff_first_name'] . "</td>";
                        echo "<td>" . $row['staff_last_name']  . "</td>";
                        echo "<td>" . $row['department']  . "</td>";
                       
                        
                       

                        // Add form within the loop to ensure each row has its own form
                        echo "<form action='index.inc.php' method='POST'>";
                        echo "<input type='hidden' name='id' value='" . $row['staff_id'] . "'>";
                       
                        
                        echo "</form>";

                        echo "</tr>";

                        $serial_number++;
                    }
                    ?>
  
                    
                    </div></p>
                            </div>
                      </div>
                      <div class="col-xl-3  col-lg-6 col-sm-6 grid-margin stretch-card">
                          <!-- Modal -->
                          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Enter New User</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>

                                <div class="modal-body">
                                  <form method="POST" action="index.php" name="add_claim">

                                  <div class="form-group">
                                  <label for="staffName">First Name:</label>
                                    <input type="text" class="form-control" name="F_name" id="F_name" placeholder="Enter First Name" required oninput="capitalizeFirstLetter(this)">
                                  </div>

                                  <div class="form-group">
                                  <label for="staffName">Last Name:</label>
                                    <input type="text" class="form-control" name="L_name" id="L_name" placeholder="Enter Last Name" required oninput="capitalizeFirstLetter(this)">
                                  </div>

                                  <div class="form-group">
                                  <label for="staffId">Staff ID:</label>
                                    <input type="text" class="form-control" name="Staff_ID"  id="staff_ID" placeholder="Enter Staff ID number" required oninput="capitalizeFirstLetter(this)">
                                  </div>

                                  <div class="form-group">
                                  <label for="Department">Select Department: </label>';
                                  <select id="department" name="department" required>
                                    <option hidden value="">Select Department</option>
                                    <?php
                                    $sql = "SELECT * FROM department ";
                                    $result = mysqli_query($conn, $sql);
                                    $row = mysqli_fetch_array($result);

                                    do {
                                        echo "<option value='" . $row['t_id'] . "'>" . $row['dep_name'] . "</option>";
                                    } while ($row = mysqli_fetch_array($result));
                                    echo "</select>";
                                    ?>
                                     </select>
                                  



                                  
                                  <button type="submit" class="btn btn-success" name="add">Submit</button>
                                  </form>
                                </div>
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
                        ;
                    </script>
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