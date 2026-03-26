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
    $sup_id = $_GET['id'];

    if ($action == 'archive') {
        // Archive Query
        $archive_query = "INSERT INTO suppliers_archive (sup_id, name, location, number)
                          SELECT sup_id, name, location, number
                          FROM suppliers
                          WHERE sup_id = $sup_id";
        $conn->query($archive_query);

        // Delete Query
        $delete_query = "DELETE FROM suppliers
                         WHERE sup_id = $sup_id";
        $conn->query($delete_query);

        // Redirect back to the main page after archiving
       // header("Location: add_asset.php");
        echo '<script type="text/javascript">alert("Supplier Archived Succesfully");window.location=\'add_supplier.php\';</script>';
        exit();
    }
}

// Close connection
$conn->close();
?>
