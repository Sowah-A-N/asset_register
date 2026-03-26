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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $ast_id = $_GET['id'];

    // Retrieve data for the selected ast_id
    $select_query = "SELECT * FROM asset_classes WHERE ast_id = $ast_id";
    $result = $conn->query($select_query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Asset not found.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission if the submit button is clicked
    if (isset($_POST['submit'])) {
        // Check if ast_id is set
        if (isset($_GET['id'])) {
            $ast_id = $_GET['id'];
        // Retrieve form data
        $newAssetClass = $_POST['asset_class'];
        $newDepRate = $_POST['dep_rate'];
        $newEstimatedLife = $_POST['estimated_life'];
        $newOpeningBalance = $_POST['opening_balance'];

        echo "ID: $ast_id<br>";
        echo "Asset Class: $newAssetClass<br>";
        echo "Depreciation Rate: $newDepRate<br>";
        echo "Estimated Life: $newEstimatedLife<br>";
        echo "Opening Balance: $newOpeningBalance<br>";

        // Update query
        $update_query = "UPDATE asset_classes SET
                        asset_class = '$newAssetClass',
                        dep_rate = '$newDepRate',
                        estimated_life = '$newEstimatedLife',
                        opening_balance = '$newOpeningBalance'
                        WHERE ast_id = $ast_id";

        if ($conn->query($update_query) === TRUE) {
            echo '<script type="text/javascript">alert("Asset Class Edited Succesfully");window.location=\'index.php\';</script>';
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset</title>
</head>
<body>
    <h2>Edit Asset</h2>

    <?php if (isset($row)) : ?>
        <form method="post" action="">
            <label for="asset_class">Asset Class:</label>
            <input type="text" name="asset_class" value="<?php echo $row['asset_class']; ?>" required><br>

            <label for="dep_rate">Depreciation Rate:</label>
            <input type="text" name="dep_rate" value="<?php echo $row['dep_rate']; ?>" required><br>

            <label for="estimated_life">Estimated Life:</label>
            <input type="text" name="estimated_life" value="<?php echo $row['estimated_life']; ?>" required><br>

            <label for="opening_balance">Opening Balance:</label>
            <input type="text" name="opening_balance" value="<?php echo $row['opening_balance']; ?>" required><br>

            <input type="submit" name="submit" value="Update">
        </form>
    <?php endif; ?>

    <p><a href="index.php">Back to Main Page</a></p>
</body>
</html>
