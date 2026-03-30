<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "asset_register";

// Create connection
$conn = new mysqli('localhost', 'root', '', 'asset_register');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get data from the form
    $asset_class = $_POST["asset_class"];
    $dep_rate = $_POST["dep_rate"];
    $estimated_life = $_POST["estimated_life"];
    $opening_balance = $_POST["opening_bal"];

    // SQL query to check if the asset class already exists
    $check_query = "SELECT COUNT(*) as count FROM asset_classes WHERE asset_class = '$asset_class'";
    $result = $conn->query($check_query);

    if ($result && $result->fetch_assoc()['count'] > 0) {
        // Asset class already exists, display an error message
        echo '<script type="text/javascript">alert("Asset Class already exists.");window.location=\'index.php\';</script>';
    } else {
        // Asset class does not exist, proceed with insertion
        $insert_query = "INSERT INTO asset_classes (asset_class, dep_rate, estimated_life, opening_bal) 
                        VALUES ('$asset_class', '$dep_rate', '$estimated_life', '$opening_balance')";

        if ($conn->query($insert_query) === TRUE) {
            echo '<script type="text/javascript">alert("Asset Class Added Successfully");window.location="../dashboard/";</script>';
        } else {
            error_log($conn->error); echo '<script>alert("A database error occurred."); window.location=\'index.php\';</script>';
        }
    }
}

// Close connection
$conn->close();
?>
