<?php
include "../datacon.php";

// Include your database connection file here

$assetClassId = $_GET['asset_class_id'];

$sql = "SELECT * FROM asset_class_sub_classes WHERE asset_class = '$assetClassId'";
$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['T_id'] . "'>" . $row['sub_class'] . "</option>";
        }
    } else {
        echo "<option value=''>No sub-classes available for asset class ID: $assetClassId</option>";
    }
} else {
    echo "<option value=''>Error executing query: " . mysqli_error($conn) . "</option>";
}

mysqli_close($conn);
?>
