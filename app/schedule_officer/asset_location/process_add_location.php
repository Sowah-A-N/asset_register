<?php
// Database connection details


// Create connection
include "../datacon.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $location = $_POST["location"];

    // SQL query to insert a new location into the asset_location table
    $sql = "INSERT INTO asset_location (location) VALUES ('$location')";

    if ($conn->query($sql) === TRUE) {
        echo '<script type="text/javascript">alert("Location Added Succesfully");window.location="../dashboard/";</script>';
    } else {
        error_log($conn->error); echo '<script>alert("A database error occurred."); window.location=\'index.php\';</script>';
    }
}

// Close connection
$conn->close();
?>