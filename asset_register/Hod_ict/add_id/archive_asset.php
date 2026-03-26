<?php
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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $ast_id = $_GET['id'];

    if ($action == 'archive') {
        // Archive Query
        $archive_query = "INSERT INTO asset_classes_archive (ast_id, asset_class, dep_rate, estimated_life, opening_balance)
                          SELECT ast_id, asset_class, dep_rate, estimated_life, opening_balance
                          FROM asset_classes
                          WHERE ast_id = $ast_id";
        $conn->query($archive_query);

        // Delete Query
        $delete_query = "DELETE FROM asset_classes
                         WHERE ast_id = $ast_id";
        $conn->query($delete_query);

        // Redirect back to the main page after archiving
       // header("Location: add_asset.php");
        echo '<script type="text/javascript">alert("Asset Class Archived Succesfully");window.location=\'add_asset.php\';</script>';
        exit();
    }
}

// Close connection
$conn->close();
?>
