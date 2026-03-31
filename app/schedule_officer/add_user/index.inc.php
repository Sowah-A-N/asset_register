<?php
include "../datacon.php";
$year = date("Y");



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staffId = mysqli_escape_string($conn,$_POST["staff_id"]);
    $staff_first_name = mysqli_escape_string($conn,$_POST["staff_first_name"]);
    $staff_last_name = mysqli_escape_string($conn,$_POST["staff_last_name"]);
    $staff_department = mysqli_escape_string($conn,$_POST["department"]);
    $assigned_asset_id = mysqli_escape_string($conn,$_POST["assigned_asset_id"]);  
    
    $newUserSql = "INSERT INTO asset_users(`staff_id`, `staff_first_name`, `staff_last_name`, `department`, `assigned_asset_id`) 
                    VALUES ('$staffId', '$staff_first_name', '$staff_last_name', '$staff_department', '$assigned_asset_id')";

    if ($conn -> query($newUserSql)){
        echo "User added successfully";
    } else {
        echo "Error executing query : " . $conn -> error;
    }

} else {
    echo "Internal error : " . $conn -> error;

}


?>
