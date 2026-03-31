<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

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

// SQL query to fetch data from the asset_location table
$sql = "SELECT sup_id AS id, name, number, location FROM suppliers";
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    // Print an error message and the SQL error details
    error_log($conn->error); echo '<script>alert("A database error occurred."); window.location=\'index.php\';</script>';
} else {
    // Check if there are results
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>name</th><th>location</th><th>number</th><th>Actions</th></tr>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td><td>" . $row["location"] . "</td><td>" . $row["number"] . "</td>";

            // Edit button
           // echo "<td><a href='edit_asset.php?id=" . $row["id"] . "'>Edit</a></td>";

            // Archive button
            echo "<td><a href='archive_supplier.php?action=archive&id=" . $row["id"] . "'>Archive</a></td></tr>";
        }

        echo "</table>";
    } else {
        echo "0 results";
    }
}

// Close connection
$conn->close();
?>
