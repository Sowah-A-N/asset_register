<?php
include "../datacon.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $asset_name = mysqli_real_escape_string($conn, $_POST['asset_name']);
    $asset_value = mysqli_real_escape_string($conn, $_POST['asset_value']);
    $acquisition_date = mysqli_real_escape_string($conn, $_POST['acquisition_date']);
    $disposal_date = !empty($_POST['disposal_date']) ? mysqli_real_escape_string($conn, $_POST['disposal_date']) : NULL;
    $asset_class = mysqli_real_escape_string($conn, $_POST['asset_class_select']);
    $dollar_rate = mysqli_real_escape_string($conn, $_POST['dollar_rate']);

    // Convert date format from d-m-Y to Y-m-d for MySQL
    $acquisition_date = date("Y-m-d", strtotime($acquisition_date));
    if (!empty($disposal_date)) {
        $disposal_date = date("Y-m-d", strtotime($disposal_date));
    }


    $sql="SELECT asset_class FROM asset_classes WHERE ast_id='$asset_class' ";
    $result=mysqli_query($conn, $sql);
    $row=mysqli_fetch_array($result);
    $asset_class_db=$row[0];


    // Insert into untracked_assets_disposals table
    $sql = "INSERT INTO untracked_asset_disposals (name, value, acquisition_date, date_of_disposal, class, dollar_rate)
            VALUES ('$asset_name', '$asset_value', '$acquisition_date', " . ($disposal_date ? "'$disposal_date'" : "NULL") . ", '$asset_class_db', '$dollar_rate')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Asset successfully added and disposed off!'); window.location.href='../view_untracked';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close connection
    mysqli_close($conn);
}
?>
