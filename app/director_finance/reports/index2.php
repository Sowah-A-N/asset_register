<?php
require_once '../init.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Welcome- Schedule Officer</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> 

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

    <!-- partial -->
    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row" id="proBanner">
        </div>
        <div class="d-xl-flex justify-content-between align-items-start">
          <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
            <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
              <!-- <a href='../generator/' target="_blank"><button class="btn bg-primary" type="button" id="dropdownMenuButton1" aria-haspopup="true" aria-expanded="false"> Generate Timetable</button></a> -->
            </div>
          </div>
        </div>
       
                  <?php
                    //echo "Page for " . $_GET['asset_class'];
                    
                     $selectedAssetClass = isset($_GET['asset_class']) ? $_GET['asset_class'] : "";
                     //echo $selectedAssetClass;
                     echo "<form method='POST' class='bg-white shadow-md rounded px-6 pt-6 pb-8 mb-4'>";
                     echo "<label for='asset_class_select' class='px-2 text-lg font-semibold'>Select Asset Sub-Class:</label>";
                     echo "<select name='asset_sub_class_select' id='asset_sub_class_select' class='px-4 p-2 border border-gray-300 rounded'>";         
         
                     $assetSubClassQuery = "SELECT * FROM asset_class_sub_classes WHERE asset_class = '$selectedAssetClass';";
                     $assetSubClassResult = $conn->query($assetSubClassQuery);
                     
                     // Check if there are results
                     if ($assetSubClassResult->num_rows > 0) {
                      echo "<option value=\"\">" . "--All Assets--" . "</option>";

                         // Output data of each row
                         while ($row = $assetSubClassResult->fetch_assoc()) {
                             echo "<option value=\"" . $row["sub_class"] . "\">" . $row["sub_class"] . "</option>";
                         }
                     } else {
                         echo "<option value=\"\">No sub-classes available</option>";
                     }
         
                     $selectedAssetSubClass = isset($_POST['asset_sub_class_select']) ? $_POST['asset_sub_class_select'] : "";
                     echo $selectedAssetSubClass;
                     echo "</select>";
                     echo "<button type='submit' class='m-5 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700'>" . "Filter" . "</button>";
                     echo "<button type='button' class='m-12 border-2 border-blue-600 rounded-lg px-3 py-2 text-blue-600 cursor-pointer hover:bg-blue-600 hover:text-white' onclick='returnToDashboard()'>Return to Dashboard</button>";

                     echo "</form><br /><br />";

                    // if (isset($selectedAssetClass)){
                    //    //$sqlClassQuery = "SELECT * FROM assets WHERE asset_class = '{$selectedAssetClass}'"; 
                    //    $sqlClassQuery = "SELECT assets.*, asset_classes.dep_rate, asset_classes.account_depr_open_bal, asset_classes.estimated_life, asset_classes.opening_bal 
                    //                       FROM assets JOIN asset_classes 
                    //                       ON assets.asset_class = asset_classes.asset_class 
                    //                       WHERE asset_classes.asset_class = '$selectedAssetClass'
                    //                       GROUP BY assets.sub_class;";

                    // Run the selected query based on user input
                    if (!isset($selectedAssetSubClass) || $selectedAssetSubClass == "" ) {
                      // Query 1: All Assets in selected class
                      $sqlClassQuery = "SELECT assets.*, asset_classes.dep_rate, asset_classes.account_depr_open_bal, asset_classes.estimated_life, asset_classes.opening_bal 
                                        FROM assets JOIN asset_classes 
                                        ON assets.asset_class = asset_classes.asset_class 
                                        WHERE asset_classes.asset_class = '$selectedAssetClass';";

                    } elseif (isset($selectedAssetSubClass)) {
                      //Query 2: All Assets in a selected asset class
                      $sqlClassQuery = "SELECT assets.*, asset_classes.dep_rate, asset_classes.account_depr_open_bal, asset_classes.estimated_life, asset_classes.opening_bal 
                                        FROM assets JOIN asset_classes 
                                        ON assets.asset_class = asset_classes.asset_class 
                                        WHERE assets.sub_class = '$selectedAssetSubClass';";
                      //echo "<script>alert($selectedAssetSubClass)</script>";
                    } else {
                      echo "Invalid user selection";
                      // You may choose to handle invalid selections differently (redirect, display an error message, etc.)
                      exit;
                    }

                       $sqlClassResult = $conn -> query($sqlClassQuery);

                       if(mysqli_num_rows($sqlClassResult) > 0) { //if this user has some data in the database
                          $totalAdditions = $totalAssetCostOpBal = $totalAssetCostCloseBal = 0;
                          $completeTotalAccumDepr = $totalAccDeprCloseBal = $totalCloseCarryValue = 0;
                          $totalDollarAdditions = $totalDepreciationCost = 0;
                                          
                        //  echo "<div class='h-screen overflow-auto mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";
                          if(isset($selectedAssetSubClass) && ($selectedAssetSubClass != "")){
                              echo "<div class='h-screen overflow-auto mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";
                              echo "<h2 class='font-san-serif font-bold text-lg'>Calculations for {$selectedAssetSubClass}</h2>";
                             } else if (($selectedAssetSubClass = "")) {
                              echo "<div class='h-screen overflow-auto mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";
                              echo "<h2 class='font-san-serif font-bold text-lg'>Calculations</h2>";
                             }
                          echo "<table class='table-auto mb-8'>";
                          echo "<thead><tr class='bg-gray-200 '>
                                  <th class='px-4 py-2 border'>Asset Sub-Class</th>
                                  <th class='px-4 py-2 border'>Asset Name</th>
                                  <th class='px-4 py-2 border'>Asset Type</th>
                                  <th class='px-4 py-2 border'>Location</th>
                                  <th class='px-4 py-2 border'>Acquisition Date</th><br />
                                  <th class='px-4 py-2 border'>Active Res Value</th>
                                  <th class='px-4 py-2 border'>Dollar Rate Used</th>
                                  <th class='px-4 py-2 border'>Date Added</th>
                                  <th class='py-2 px-4 border '>Asset Cost Opening Balance(GHC)</th>
                                  <th class='py-2 px-4 border '>Asset Cost Closing Balance(GHC)</th>
                                  <th class='py-2 px-4 border '>Accumulated Depr. Opening Balance(GHC)</th>
                                  <th class='py-2 px-4 border '>Depreciation Charge(GHC)</th>
                                  <th class='py-2 px-4 border '>Accumulated Depreciation Closing Balance(GHC)</th>
                                  <th class='py-2 px-4 border '>Closing Carrying Value(GHC)</th>
                                  <th class='py-2 px-4 border '>Current Lifetime</th>
                                  <th class='py-2 px-4 border '>Unexpired Lifetime</th></tr></thead>";
                  
                          echo "<tbody>";                             
                          
                        
                                  while ($row = mysqli_fetch_assoc($sqlClassResult)) {
                                    //print_r($row);
                                    $a_class = $row["asset_class"];
                                    $a_opening_bal = $row['opening_bal'];
                                    $a_lifetime = $row['estimated_life'];
                                    $a_class_active_res_value = 0;
                                    $a_class_depr_charge = $row['dep_rate'];
                                    $a_asset_cost_op_bal = $a_opening_bal - $a_class_active_res_value;
                                    $a_depr_cost_per_charge = $a_opening_bal * $a_class_depr_charge;
                                    $a_accum_depr_op_bal = $row['account_depr_open_bal'];
                                    $a_total_accum_depr = $a_accum_depr_op_bal + $a_depr_cost_per_charge;
                                    $a_closing_carry_value = $a_opening_bal - $a_total_accum_depr;
                                
                         
                  
                                      if (isset($row['asset_id'])) {
                                          $assetId = $row['asset_id'];                        
                                      
                                          // Fetch asset details
                                          $sqlAsset = "SELECT * FROM assets WHERE asset_id = $assetId";
                                          $resultAsset = mysqli_query($conn, $sqlAsset);
                                      
                                          if (!$resultAsset) {
                                              error_log(mysqli_error($conn)); die('A database error occurred.');
                                          }
                                      
                                          $rowAsset = mysqli_fetch_assoc($resultAsset);
                                          $a_name=$rowAsset['asset_name'];
                                      
                                          // Fetch opbal_plus_additions and estimated_life from asset_classes table
                                          $assetClass = $rowAsset['asset_class'];
                                          $sqlAssetClasses = "SELECT opening_bal, opbal_plus_additions, estimated_life FROM asset_classes WHERE asset_class = '$assetClass'";
                                          $resultAssetClasses = mysqli_query($conn, $sqlAssetClasses);
                                      
                                          if (!$resultAssetClasses) {
                                              error_log(mysqli_error($conn)); die('A database error occurred.');
                                          }
                                      
                                          $rowAssetClasses = mysqli_fetch_assoc($resultAssetClasses);
                                          $opbalPlusAdditions = isset($rowAssetClasses['opbal_plus_additions']) ? (is_numeric($rowAssetClasses['opbal_plus_additions']) ? $rowAssetClasses['opbal_plus_additions'] : 0) : 0;
                                          $openingBalance = isset($rowAssetClasses['opening_bal']) ? (is_numeric($rowAssetClasses['opening_bal']) ? $rowAssetClasses['opening_bal'] : 0) : 0;
                                          $estimatedLife = isset($rowAssetClasses['estimated_life']) ? $rowAssetClasses['estimated_life'] : 0 ;
                                      
                                          // Calculate assetCostOpeningBalance using opbal_plus_additions
                                          $assetCostOpeningBalance = $rowAsset['active_res_value'];
                  
                                          $totalAssetCostOpBal = $totalAssetCostOpBal + $assetCostOpeningBalance; //Calc for total asset cost opening balance
                                      
                                          // Calculate assetCostClosingBalance
                                          $assetCostClosingBalance = $assetCostOpeningBalance + $rowAsset['additions'] - $rowAsset['disposals'];
                  
                                          $totalAdditions = $totalAdditions + $rowAsset['additions']; //Calc for total additions
                                          $totalAssetCostCloseBal = $totalAssetCostCloseBal + $assetCostClosingBalance; //Calc for total asset cost closing balance
                                      
                                          // Fetch depreciation rate from asset_classes table
                                          $assetClass = $rowAsset['asset_class'];
                                          $sqlDepRate = "SELECT dep_rate FROM asset_classes WHERE asset_class = '$assetClass'";
                                          $resultDepRate = mysqli_query($conn, $sqlDepRate);
                                      
                                          if (!$resultDepRate) {
                                              error_log(mysqli_error($conn)); die('A database error occurred.');
                                          }
                                      
                                          $rowDepRate = mysqli_fetch_assoc($resultDepRate);
                                          $depRate = isset($rowDepRate['dep_rate']) ? $rowDepRate['dep_rate'] : 0;
                                      
                                          // Calculate depreciation cost
                                          $depreciationCost = $assetCostClosingBalance * $depRate;
                                      
                                          // Fetch acc_depr_opening_bal from other_values table
                                          $sqlAccDeprOpeningBal = "SELECT acc_depr_opening_bal FROM other_values";
                                          $resultAccDeprOpeningBal = mysqli_query($conn, $sqlAccDeprOpeningBal);
                                      
                                          if (!$resultAccDeprOpeningBal) {
                                              error_log(mysqli_error($conn)); die('A database error occurred.');
                                          }
                                      
                                          $rowAccDeprOpeningBal = mysqli_fetch_assoc($resultAccDeprOpeningBal);
                                          $accDeprOpeningBal = 0; // Default value if $rowAccDeprOpeningBal is null or the key is not set
                                      
                                        if (
                                            is_array($rowAccDeprOpeningBal) &&
                                            isset($rowAccDeprOpeningBal['acc_depr_opening_bal']) &&
                                            is_numeric($rowAccDeprOpeningBal['acc_depr_opening_bal'])
                                        ) {
                                            $accDeprOpeningBal = $rowAccDeprOpeningBal['acc_depr_opening_bal'];
                                        }
                                      
                                      // Now $accDeprOpeningBal contains the appropriate value
                                      
                                      
                                          // Calculate total_accumulated_depreciation
                                          $totalAccumulatedDepreciation = $accDeprOpeningBal + $depreciationCost;
                                          $completeTotalAccumDepr = $completeTotalAccumDepr + $totalAccumulatedDepreciation;
                                      
                                          // Calculate account_depreciation_closing_balance
                                          $accountDepreciationClosingBalance = $totalAccumulatedDepreciation - $rowAsset['active_res_value'];
                                          $totalAccDeprCloseBal = $totalAccDeprCloseBal + $accountDepreciationClosingBalance; //Calc for total account depreciation closing balance
                                      
                                          // Calculate closing_carrying_value
                                          $closingCarryingValue = $assetCostClosingBalance - $accountDepreciationClosingBalance;
                                          $totalCloseCarryValue = $totalCloseCarryValue + $closingCarryingValue; // Calc for total closing carrying value 
                  
                                          // Calculate current_lifetime
                                          $acquisitionYear = date('Y', strtotime($rowAsset['acquisition_date']));
                                          $currentLifetime = $rowAsset['current_year'] - $acquisitionYear + 1;
                                      
                                          // Calculate unexpired_lifetime
                                          $unexpiredLifetime = $estimatedLife - $currentLifetime;

                                           
                                      
                                         echo "<tr><td class='border px-4 py-2'>". $row['sub_class']."</td>
                                         <td class='border px-4 py-2'>". $row['asset_name']."</td>
                                         <td class='border px-4 py-2'>". $row['asset_type']."</td>
                                         <td class='border px-4 py-2'>". $row['location']."</td>
                                         <td class='border px-4 py-2'>".  date("d-m-Y",strtotime($row['acquisition_date']))."</td>
                                         <td class='border px-4 py-2'>". $row['active_res_value']."</td>
                                         <td class='border px-4 py-2'>". $row['dollar_rate_used']."</td>
                                         <td class='border px-4 py-2'>".  date("d-m-Y",strtotime($row['date_added']))."</td>
                                         <td class='py-2 px-4 border-b'>" . number_format($assetCostOpeningBalance, 2, ".", ",") . "</td>
                                         <td class='py-2 px-4 border-b'>" . number_format($assetCostClosingBalance, 2, ".", ",") . "</td>
                                         <td class='py-2 px-4 border-b'>" ."". "</td>
                                         <td class='py-2 px-4 border-b'>" . number_format($depreciationCost, 2, ".", ",") . "</td>
                                         <td class='py-2 px-4 border-b'>" . number_format($accountDepreciationClosingBalance, 2, ".", ",") . "</td>
                                         <td class='py-2 px-4 border-b'>" . number_format($closingCarryingValue, 2, ".", ",") . "</td>
                                         <td class='py-2 px-4 border-b'>" . $currentLifetime . "</td>
                                         <td class='py-2 px-4 border-b'>" . $unexpiredLifetime . "</td>";
                                          // ... add more columns for additional calculations as needed ...
                                          echo "</tr>";
                  
                                          $totalDollarAdditions += ($row['additions']/$row['dollar_rate_used']);
                                          $totalDepreciationCost += $depreciationCost;

                                      } else {
                                          echo "<p>No asset ID provided.</p>";
                                      }
                            }                 
                      
                  
                      echo "<tr><td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td>
                              <td class='border px-4 py-2'>".""."</td></tr>";
                  
                              $t_class_dep_cost = isset($a_opening_bal) * isset($a_class_depr_charge);
                              $a_asset_cost_op_bal = isset($a_asset_cost_op_bal) ? $a_asset_cost_op_bal : 0;
                              $a_opening_bal = isset($a_opening_bal) ? $a_opening_bal : 0;
                              $a_total_accum_depr = isset($a_total_accum_depr) ? $a_total_accum_depr : 0;
                              $a_closing_carry_value = isset($a_closing_carry_value) ? $a_closing_carry_value : 0;
                              $a_depr_cost_per_charge = isset($a_depr_cost_per_charge) ? $a_depr_cost_per_charge : 0;  
                              $totalDepreciationCost = isset($totalDepreciationCost) ? $totalDepreciationCost : 0;                 
                  
                      echo "<tr><td class='border px-4 py-2 font-bold'>"."Totals"."</td>
                          <td class='border px-4 py-2'>".""."</td>
                          <td class='border px-4 py-2'>".""."</td>
                          <td class='border px-4 py-2'>".""."</td>
                          <td class='border px-4 py-2'>".""."</td>
                          <td class='border px-4 py-2'>".""."</td>
                          <td class='border px-4 py-2'>".""."</td>
                          <td class='border px-4 py-2'>".""."</td>
                          <td class='border px-4 py-2 font-bold text-blue-600'>".(isset($totalAssetCostOpBal) ? number_format($totalAssetCostOpBal+$a_asset_cost_op_bal, 2, ".", ",") :"")."</td> <!--Asset Cost Opening Balance-->
                          <td class='border px-4 py-2 font-bold text-blue-600'>".(isset($totalAssetCostCloseBal) ? number_format($a_opening_bal+$totalAssetCostCloseBal, 2, ".", ",") :"")."</td> <!--Asset Cost Closing Balance--> 
                          <td class='border px-4 py-2 font-bold text-blue-600'>" . (isset($a_accum_depr_op_bal) ? number_format($a_accum_depr_op_bal, 2, ".", ",") : "") . "</td>
                          <td class='border px-4 py-2 font-bold text-blue-600'>" . (isset($totalDepreciationCost) ? number_format($totalDepreciationCost, 2, ".", ",") : "") . "</td>
                          <td class='border px-4 py-2 font-bold text-blue-600'>".(isset($totalAccDeprCloseBal) ? number_format($totalAccDeprCloseBal, 2, ".", ",") :"")."</td> <!--Acct Depr Closing Balance-->
                          <td class='border px-4 py-2 font-bold text-blue-600'>".(isset($totalCloseCarryValue) ? number_format($totalCloseCarryValue + $a_closing_carry_value, 2, ".", ",") :"")."</td> <!--Closing Carying Value-->
                          <td class='border px-4 py-2'>".""."</td>
                          <td class='border px-4 py-2'>".""."</td></tr>";
                      echo "</tbody>";
                      echo "</table>";
                      echo "</div>";    
                  
                  } else {
                      echo "<div class='h-full mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'><p>No records exist for this category</p></div>";
                  } 

                  ?>

                  <script>
                    function returnToDashboard(){
                      //When button is clicked, send user to dashboard
                      window.location.href = "../index.php";
                    }
                  </script>
                  
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