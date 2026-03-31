<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Locations</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<a href='index.php'><button>Add Location</button></a>
<?php
// Database connection details
$servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "asset_register";

// Create connection
$conn= new mysqli('localhost','root','','asset_register');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// SQL query to fetch data from the asset_location table
$sql = "SELECT loc_id AS id, location FROM asset_location";
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    // Print an error message and the SQL error details
    error_log($conn->error); echo '<script>alert("A database error occurred."); window.location=\'index.php\';</script>';
} else {
    // Check if there are results
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Location</th></tr>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"] . "</td><td>" . $row["location"] . "</td></tr>";
        }

        echo "</table>";
    } else {
        echo "0 results";
    }
}

// Close connection
$conn->close();
?>

</body>
</html>