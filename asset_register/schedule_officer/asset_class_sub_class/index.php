<?php
require_once '../init.php';
if(isset($_POST['add']))
{

  
  $sub_class=mysqli_real_escape_string($conn, $_POST['sub_class']);
  $asset_class=mysqli_real_escape_string($conn, $_POST['asset_class']);

$query= "select asset_class from asset_classes where ast_id='$asset_class'";
$result = $conn->query($query);
$row=mysqli_fetch_array($result);
$asset_name=$row["asset_class"];

   

  if (!isset($sub_class)||!isset($asset_class))
  {
      echo "<script> alert('check details'); window.location='index.php' </script> ";  
      exit();
  }
  else
  {
    // SQL query to check if the supplier already exists
    $check_query = "SELECT COUNT(*) as count FROM asset_class_sub_classes WHERE sub_class = '$sub_class'";
    $result = $conn->query($check_query);

    // Fetch the result row
    $row = $result->fetch_assoc();

    if (isset($row['count']) && $row['count'] > 0) {
        // Supplier already exists, display an error message
        echo '<script type="text/javascript">alert("Asset Sub-Class already exists.");window.location=\'index.php\';</script>';
    } 
    
    else {
       
      $insert_query = "INSERT INTO asset_class_sub_classes (sub_class, asset_class ) 
      VALUES ('$sub_class', '$asset_name')";


        if ($conn->query($insert_query) === TRUE) {
          echo '<script type="text/javascript">alert("Asset Sub-Class added Successfully.");window.location=\'index.php\';</script>';

        } else {
            echo "Error adding Sub-Class: " . $conn->error;
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
    <title>View/Add Asset Sub-Class</title>
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
              <h2 class="text-dark font-weight-bold mb-2"> Filter Sub-Classes by Asset Class </h2>
              <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
                <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
                  <button class="btn btn-outline-primary" type="button" id="dropdownMenuButton1" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#exampleModal"> Add a Sub-Class</button>
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
<<!--Table goes here -->
<form method="POST" id="filterForm">
    <div class="form-group">
        <label for="asset_class_filter">Filter by Asset Class:</label>
        <select name="asset_class_filter" class="form-control" id="asset_class_filter">
            <option value="">All Asset Classes</option>
            <?php
            // Fetch all asset classes from the database
            $sqlAssetClasses = "SELECT * FROM asset_classes";
            $resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);
            if (!$resultAssetClasses) {
                error_log(mysqli_error($conn)); die('A database error occurred.');
            }
            while ($rowAssets = mysqli_fetch_assoc($resultAssetClasses)) {
                $assetClass = $rowAssets['asset_class'];
                echo "<option value='$assetClass'>$assetClass</option>";
            }
            ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary" name="filter">Filter</button>
</form>
<?php
// Handle form submission for filter button
if(isset($_POST['filter'])) {
    // Get selected asset class filter value
    $assetClassFilter = mysqli_real_escape_string($conn, $_POST['asset_class_filter']);

    // Construct SQL query based on the selected asset class filter value
    if (!empty($assetClassFilter)) {
        $sql = "SELECT * FROM asset_class_sub_classes WHERE asset_class = '$assetClassFilter' ";
    } else {
        // If no specific asset class is selected, fetch all assets
        $sql = "SELECT * FROM asset_class_sub_classes ORDER BY asset_class ASC";
    }

    // Execute the SQL query
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        error_log(mysqli_error($conn));
        exit();
    }
} else {
    // If form is not submitted, fetch all assets
    $sql = "SELECT * FROM asset_class_sub_classes";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        error_log(mysqli_error($conn));
        exit();
    }
}

// Output the table
?>
<br />
<table class="table">
    <thead>
        <tr>
            <th scope="col">S/n</th>
            <th scope="col">Asset Sub-Class</th>
            <th scope="col">Asset Class</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $serial_number = 1;
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>$serial_number</td>";
            echo "<td>" . $row['sub_class'] . "</td>";
            echo "<td>" . $row['asset_class'] . "</td>";
            echo "</tr>";
            $serial_number++;
        }
        ?>
    </tbody>
</table>

  
                    
                    </div></p>
                            </div>
                      </div>
                      <div class="col-xl-3  col-lg-6 col-sm-6 grid-margin stretch-card">
                          <!-- Modal -->
                          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Enter New Sub-Class</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>


                                  <script>
                                    function capitalizeFirstLetter(input) {
                                    // Capitalize the first letter of each word
                                    input.value = input.value.replace(/\b\w/g, (char) => char.toUpperCase());
                                }

                                  </script>

                                    
                                      

                                <div class="modal-body">
                                  <form method="POST" action="index.php" name="add_claim">

                                  
                                  

                                  <div class="form-group">
                                  <label for="assetClass">Asset Class:</label>
                                          <select id="asset_class" name="asset_class" required>
                                  <option hidden value="">Select Asset Class</option>
                                  <?php
                                  $sql = "SELECT * FROM asset_classes ";
                                  $result = mysqli_query($conn, $sql);
                                  $row = mysqli_fetch_array($result);
                                  $asset_class=$row['asset_class'];

                                  do {
                                      $selected = ($row['ast_id'] == $asset_class) ? 'selected' : '';
                                      echo "<option value='" . $row['ast_id'] . "' $selected>" . $row['asset_class'] . "</option>";
                                  } while ($row = mysqli_fetch_array($result));
                                  echo "</select>"; ?>
                                    </select>

                                   <br/><br/>
                                    <div class="form-group">
                                  <label for="sub_class">Name of Sub-Class:</label>
                                    <input type="text" class="form-control" name="sub_class"  id='sub_class' placeholder="Enter Name of Sub-class" required oninput="capitalizeFirstLetter(this)">
                                    
                                   
                                  
 
                                    
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