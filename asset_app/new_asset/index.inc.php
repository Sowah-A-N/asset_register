<?php
require_once '../../auth.php';
requirePermission('asset.create', '../login/');   // RBAC: was previously UNGUARDED
include "../datacon.php";
$year = date("Y");




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assetName = mysqli_escape_string($conn,$_POST["asset_name"]);
    $asset_class = mysqli_escape_string($conn,$_POST["asset_class_select"]);
    $asset_sub_class = mysqli_escape_string($conn,$_POST["asset_class_sub_classes"]);
    $grv_number = mysqli_escape_string($conn,$_POST["grvNumber"]);
    $serial_number = mysqli_escape_string($conn,$_POST["identificationNumber"]);
    $pv_number = mysqli_escape_string($conn,$_POST["pvNumber"]);
    $supplier  = mysqli_escape_string($conn,$_POST["supplier"]);
    $user= mysqli_escape_string($conn,$_POST["user"]);
    // $id_number= mysqli_escape_string($conn,$_POST["id_number"]);
    $asset_id= mysqli_escape_string($conn,$_POST["asset_id"]);

    // Check if GRV number already exists
    $grvCheckQuery = "SELECT COUNT(*) as count FROM assets WHERE grv_number = '$grv_number'";
    $grvCheckResult = mysqli_query($conn, $grvCheckQuery);
    $grvCount = mysqli_fetch_assoc($grvCheckResult)['count'];

    // Check if PV number already exists
    $pvCheckQuery = "SELECT COUNT(*) as count FROM assets WHERE pv_number = '$pv_number'";
    $pvCheckResult = mysqli_query($conn, $pvCheckQuery);
    $pvCount = mysqli_fetch_assoc($pvCheckResult)['count'];

    // Check if serial number already exists
    $supplierCheckQuery = "SELECT COUNT(*) as count FROM assets WHERE id_number = '$serial_number'";
    $supplierCheckResult = mysqli_query($conn, $supplierCheckQuery);
    $supplierCount = mysqli_fetch_assoc($supplierCheckResult)['count'];

    if ($grvCount > 0) {
        echo '<script type="text/javascript">alert("GRV Number already exists.");window.location=\'index.php\';</script>';
    } elseif ($pvCount > 0) {
        echo '<script type="text/javascript">alert("PV Number already exists.");window.location=\'index.php\';</script>';
    } elseif ($supplierCount > 0) {
        echo '<script type="text/javascript">alert("Serial Number already exists.");window.location=\'index.php\';</script>';
    } else {
        // Proceed with the rest of your code
        $assetType  = mysqli_escape_string($conn,$_POST["asset_type"]);
       
        $location = mysqli_escape_string($conn,$_POST["location"]);

        $historicalCost = mysqli_escape_string($conn,$_POST["additions"]);
        $active_res_value = mysqli_escape_string($conn,$_POST["active_res_value"]);
        $acquisitionDate = mysqli_escape_string($conn, date('Y-m-d', strtotime($_POST["acquisition_date"])));

        // Calculate lifespan in years
        $currentYear = date('Y');
        $lifespanYears = $currentYear - date('Y', strtotime($acquisitionDate)-1);

        // Fetch estimated life for the asset class
        $sql = "SELECT estimated_life FROM asset_classes WHERE ast_id = '$asset_class'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $estimatedLife = $row['estimated_life'] ;

        // Check if the asset has expired
        if ($lifespanYears > $estimatedLife) {
            echo '<script type="text/javascript">alert("Asset expired. Lifespan exceeded."); window.location=\'index.php\';</script>';
            exit;  // Stop execution
        }

        $sql="SELECT dollar_rate FROM dollar_rate WHERE rate_status='ACTIVE'";
        $result=mysqli_query($conn, $sql);
        $row=mysqli_fetch_array($result);
        $rate_used=$row[0];

        $sql="SELECT asset_class FROM asset_classes WHERE ast_id='$asset_class' ";
        $result=mysqli_query($conn, $sql);
        $row=mysqli_fetch_array($result);
        $asset_class_db=$row[0];

        
        $sql="SELECT sub_class FROM asset_class_sub_classes WHERE T_id='$asset_sub_class' ";
        $result=mysqli_query($conn, $sql);
        $row=mysqli_fetch_array($result);
        $sub_class_db=$row[0];

        $sql="SELECT name FROM suppliers WHERE sup_id='$supplier' ";
        $result=mysqli_query($conn, $sql);
        $row=mysqli_fetch_array($result);
        $supplier_db=$row[0];

        $sql="SELECT asset_type FROM asset_type WHERE type_id='$assetType' ";
        $result=mysqli_query($conn, $sql);
        $row=mysqli_fetch_array($result);
        $asset_Type_db=$row[0];

        $sql = "SELECT staff_first_name, staff_last_name FROM asset_users WHERE t_id= '$user'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $user_db = $row[0] .' '. $row[1];


        $sql = "SELECT location FROM asset_location WHERE loc_id='$location'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $location_db = $row[0];

        $currentOpeningBalance = fetchOpeningBalance($asset_class);

        // Add historical cost to the opening balance
        $newOpeningBalance = $currentOpeningBalance + $historicalCost;
        $sql = "INSERT INTO assets (asset_name, asset_class, sub_class, grv_number, serial_number, pv_number, id_number,  supplier_name, asset_type, location, user, acquisition_date, current_year, additions, active_res_value, dollar_rate_used)
        VALUES ('$assetName','$asset_class_db', '$sub_class_db', '$grv_number', '$serial_number',  '$pv_number', '$asset_id', '$supplier_db', '$asset_Type_db',  '" . mysqli_real_escape_string($conn, $location_db) . "',  '$user_db', '$acquisitionDate', '$currentYear', '$historicalCost', '$active_res_value', '$rate_used')";
        

        if ($conn->query($sql) == true) {
            $updateSql = "UPDATE asset_classes set opbal_plus_additions = $newOpeningBalance WHERE ast_id = $asset_class";
            $result = mysqli_query($conn, $updateSql);
            echo "<script>alert('Asset Added Successfully'); window.location='../dashboard';  </script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    $asset_class = $_GET["asset_class"];
    echo fetchOpeningBalance($asset_class);
}

function fetchOpeningBalance($asset_classId)
{
    global $conn;
    $sql = "SELECT opening_bal, opbal_plus_additions FROM asset_classes WHERE ast_id = $asset_classId";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        if ($row !== null && array_key_exists('opening_bal', $row) && array_key_exists('opbal_plus_additions', $row)) {
            $opening_balance = $row['opening_bal'];
            $opbal_plus_additions = $row['opbal_plus_additions'];

            if ($opbal_plus_additions == 0) {
                return $opening_balance;
            } else if ($opbal_plus_additions > 0) {
                return $opbal_plus_additions;
            } else {
                return "Error fetching opening balance";
            }
        } else {
            return "Error fetching opening balance: Unexpected result format";
        }
    } else {
        return "Error in SQL query: " . mysqli_error($conn);
    }
}


?>


