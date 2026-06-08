<?php

session_start();
if (!isset($_SESSION['user_name']))
{
    header("Location:/staff_allowance/login/");
    die();
}
$username=$_SESSION['user_name'];
include "../datacon.php";
 ?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>View Breakdown</title>
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
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
              <a class="nav-link" href="/staff_allowance/dashboard/">
                <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
                <span class="menu-title">Admin Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/staff_allowance/staff/">
                <span class="icon-bg"><i class="mdi mdi-account menu-icon"></i></span>
                <span class="menu-title">Staff Options</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/staff_allowance/spouse/">
                <span class="icon-bg"><i class="mdi mdi-office-building menu-icon"></i></span>
                <span class="menu-title">Spouse Options</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/staff_allowance/wards/">
                <span class="icon-bg"><i class="mdi mdi-book-open-outline menu-icon"></i></span>
                <span class="menu-title">Ward Options</span>
              </a>
            </li>
            <li class="nav-item sidebar-user-actions">
              <div class="user-details">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <div class="d-flex align-items-center">
                      <div class="sidebar-profile-img">
                      </div>
                      <div class="sidebar-profile-text">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <li class="nav-item sidebar-user-actions">
              <div class="sidebar-user-menu">
                <a href="logout.php" class="nav-link"><i class="mdi mdi-logout menu-icon"></i>
                  <span class="menu-title">Log Out</span></a>
              </div>
            </li>
          </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row" id="proBanner">
            </div>
            <div class="d-xl-flex justify-content-between align-items-start">
              <h2 class="text-dark font-weight-bold mb-2"> Breakdown of Transactions </h2>
              <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
                <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
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
                        if(isset($_POST['view']))
                        {
                            $hidden_id= mysqli_real_escape_string($conn, $_POST['id']);
                            $sql="SELECT * FROM staff WHERE staff_ID='$hidden_id' ";
                            $result=mysqli_query($conn, $sql);
                            $row=mysqli_fetch_array($result);
                            $first_name=$row['first_name'];
                            $last_name=$row['last_name'];
                            $position=$row['position'];
                            $staff_ID=$row['staff_ID'];
                            $health_allowance=$row['health_allowance'];
                            $dental_allowance=$row['dental_allowance'];
                            $optical_allowance=$row['optical_allowance'];

                            $sql="SELECT transaction.benefactor, transaction.txn_date, transaction.entryDate, transaction.issue_description, transaction.transaction_value_USD, transaction.transaction_value_GHS, transaction.dollar_rate_used, transaction.transaction_type_ID, transaction.transaction_ID, benefits.benefit_description, benefits.benefit_type_ID
                                FROM benefits
                                INNER JOIN transaction ON benefits.benefit_type_ID=transaction.transaction_type_ID 
                                WHERE transaction.benefactor='$hidden_id'";
                                $result=mysqli_query($conn, $sql);
                                if(!$result)
                                {
                                        echo "Not Selected";
                                        echo "Errormessage:".mysqli_error($conn);
                                        exit();
                                        
                                }
                                
                                
                                
                                
                                ?>
                                
                                <div class="card " style="width: 30rem;">
                                <ul class="list-group list-group-flush">
                                  <li class="list-group-item"><b>NAME OF STAFF:</b> <?php print("$first_name $last_name"); ?></li>
                                  <li class="list-group-item"><b>POSITION OF STAFF:</b> <?php print("$position");?></li>
                                  <li class="list-group-item"><b>HEALTH ALLOWANCE LEFT (USD):</b> <?php print("$health_allowance"); ?></li>
                                  <li class="list-group-item"><b>DENTAL ALLOWANCE LEFT (USD):</b> <?php print("$dental_allowance"); ?></li>
                                  <li class="list-group-item"><b>OPTICAL ALLOWANCE LEFT (USD):</b> <?php print("$optical_allowance"); ?></li>
                                </ul>
                                </div>

                                <br />
                                <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">S/n</th>
                                    <th scope="col">Trans. Amount (USD)</th>
                                    <th scope="col">Trans. Amount (GHS)</th>
                                    <th scope="col">Dollar Rate Used</th>
                                    <th scope="col">Type of Claim</th>
                                    <th scope="col">Claim Description</th>
                                    <th scope="col">Date on Receipt</th>
                                    <th scope="col">Date Entered</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $serial_number=1;
                                            while($row= mysqli_fetch_array($result))
                                            {
                                                echo "<td>$serial_number</td>"; 
                                                echo "<td>".$row['transaction_value_USD']."</td>";
                                                echo "<td>".$row['transaction_value_GHS']."</td>";
                                                echo "<td>".$row['dollar_rate_used']."</td>";
                                                echo "<td>".$row['benefit_description']."</td>";
                                                echo "<td>".$row['issue_description']."</td>";
                                                echo "<td>".$row['txn_date']."</td>";
                                                echo "<td>".$row['entryDate']."</td>";
                                                echo "</form></tr>"; 
                                                
                                                $serial_number++;
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