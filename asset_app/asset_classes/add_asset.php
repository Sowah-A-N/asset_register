<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Classes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<a href='index.php'><button>Add Asset Class</button></a>
<?php
// Database connection details
$servername = "localhost";
    $username = "root";
    $password = "abokoma";
    $dbname = "asset_register";

// Create connection
$conn= new mysqli('localhost','root','abokoma','asset_register');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// SQL query to fetch data from the asset_location table
$sql = "SELECT ast_id AS id, asset_class, dep_rate, estimated_life, opening_bal FROM asset_classes";
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    // Print an error message and the SQL error details
    echo "Error executing the query: " . $conn->error;
} else {
    // Check if there are results
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>asset_class</th><th>dep_rate</th><th>estimated_life</th><th>opening_bal</th></tr>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"] . "</td><td>" . $row["asset_class"] . "</td><td>" . $row["dep_rate"] . "</td><td>" . $row["estimated_life"] . "</td><td>" . $row["opening_bal"] . "</td></tr>";
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