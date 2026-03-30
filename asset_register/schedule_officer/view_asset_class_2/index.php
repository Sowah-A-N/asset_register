<?php
require_once '../init.php';
if(isset($_POST['add']))
{

  $name=mysqli_real_escape_string($conn, $_POST['name']);
  $opening_balance=mysqli_real_escape_string($conn, $_POST['Opening_balance']);
  $acc_dep_opening_balance=mysqli_real_escape_string($conn, $_POST['acc_dep_open_bal']);
  $depreciation=mysqli_real_escape_string($conn, $_POST['depreciation_rate']);
  
   // Calculate estimated life based on dep_rate
   $estimated_life = $depreciation != 0 ? 1 / $depreciation : 0;

   

  if (!isset($name)||!isset($opening_balance)||!isset($depreciation)||!isset($estimated_life)||!isset($acc_dep_opening_balance))
  {
      echo "<script> alert('check details'); window.location='index.php' </script> ";  
      exit();
  }
  else
  {
    // SQL query to check if the supplier already exists
    $check_query = "SELECT COUNT(*) as count FROM asset_classes WHERE asset_class = '$name'";
    $result = $conn->query($check_query);

    // Fetch the result row
    $row = $result->fetch_assoc();

    if (isset($row['count']) && $row['count'] > 0) {
        // Supplier already exists, display an error message
        echo '<script type="text/javascript">alert("Asset Class already exists.");window.location=\'index.php\';</script>';
    } 
    
    else {
        // Supplier does not exist, proceed with insertion
        $insert_query = "INSERT INTO asset_classes (asset_class, account_depr_open_bal , Opening_bal, estimated_life, dep_rate ) 
                        VALUES ('$name', '$acc_dep_opening_balance', '$opening_balance', $estimated_life, '$depreciation')";

        if ($conn->query($insert_query) === TRUE) {
          echo '<script type="text/javascript">alert("Asset Class added Successfully.");window.location=\'index.php\';</script>';

        } else {
            echo "Error adding Supplier: " . $conn->error;
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
    <title>View/Add Asset Class</title>
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
              <h2 class="text-dark font-weight-bold mb-2"> List of Asset Classes </h2>
              <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
                <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
                  <button class="btn btn-outline-primary" type="button" id="dropdownMenuButton1" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#exampleModal"> Add an Asset Class</button>
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
                        $sql="SELECT * FROM asset_classes ORDER BY ast_ID DESC LIMIT 50";
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
                          <th scope="col">Asset Class</th>
                          <th scope="col">Opening Balance(GHS)</th>
                          <th scope="col">Account Depreciation Opening Balance(GHS)</th>
                          <th scope="col">Current Closing Balance(GHS)</th>
                          <th scope="col">Estimated Life(Years)</th>
                          <th scope="col">Depreciation Rate(%)</th>
                          
                        </tr>
                      </thead>
                      <tbody>
                                          <?php
                    $serial_number = 1;
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>$serial_number</td>";
                        echo "<td>" . $row['asset_class'] . "</td>";
                        echo "<td>" . number_format($row['opening_bal'], 2, '.', ',') . "</td>";
                        echo "<td>" . $row['account_depr_open_bal'] . "</td>";
                        echo "<td>" . number_format($row['opbal_plus_additions'], 2, '.', ',') . "</td>";
                        echo "<td>" . $row['estimated_life'] . "</td>";
                        echo "<td>" . $row['dep_rate'] . "</td>";
                        
                       

                        // Add form within the loop to ensure each row has its own form
                        echo "<form action='index.inc.php' method='POST'>";
                        echo "<input type='hidden' name='id' value='" . $row['ast_id'] . "'>";
                       
                        
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
                                  <h5 class="modal-title" id="exampleModalLabel">Enter New Asset Class</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>

                                <script>
    // Function to calculate and update depreciation rate
    function updateDepreciationRate() {
        var estimatedLife = parseFloat(document.getElementById('estimated_life').value);
        var depreciationRateField = document.getElementById('dep_rate');

        // Check if estimatedLife is a valid number and not 0
        if (!isNaN(estimatedLife) && estimatedLife !== 0) {
            // Calculate depreciation rate and round to 2 decimal places
            var depreciationRate = (1 / estimatedLife).toFixed(2);
            depreciationRateField.value = depreciationRate; // Update the depreciation rate field
        } else {
            depreciationRateField.value = ''; // Clear depreciation rate field if estimatedLife is invalid or 0
        }
    }
</script> 

                                  <script>
                                    function capitalizeFirstLetter(input) {
                                    // Capitalize the first letter of each word
                                    input.value = input.value.replace(/\b\w/g, (char) => char.toUpperCase());
                                }

                                  </script>

                                <div class="modal-body">
                                  <form method="POST" action="index.php" name="add_claim">

                                  <div class="form-group">
                                  <label for="name">Name of Asset Class:</label>
                                    <input type="text" class="form-control" name="name"  placeholder="Enter Asset Class Name" required oninput="capitalizeFirstLetter(this)">
                                  </div>

                                  <div class="form-group">
                                  <label for="opening_balance">Opening Balance (GHS):</label>
                                    <input type="text" class="form-control" name="Opening_balance" placeholder="Enter Opening Balance" required>
                                  </div>

                                  <div class="form-group">
                                  <label for="acc_dep_open_bal">Account Depreciation Opening Balance:</label>
                                    <input type="text" class="form-control" name="acc_dep_open_bal" id="acc_dep_open_bal"  placeholder="Enter Account Depreciation Opening Balance"  required>
                                  </div>

                                  <div class="form-group">
                                  <label for="estimatedLife">Estimated life:</label>                                   
                                    <input type="text" class="form-control" name="estimatedLife" id="estimated_life" placeholder="Enter Estimated Life" oninput="updateDepreciationRate()" required>
                                  </div>

                                  <div class="form-group">
                                  <label for="depreciationRate">Depreciation Rate:</label>
                                    <input type="text" class="form-control" name="depreciation_rate"  id="dep_rate" placeholder="" readonly>
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