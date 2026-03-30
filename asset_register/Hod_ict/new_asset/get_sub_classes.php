<?php
require_once '../init.php';

$assetClassId = trim($_GET['asset_class_id'] ?? '');

if ($assetClassId === '') {
    echo "<option value=''>Select a sub-class</option>";
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT T_id, sub_class FROM asset_class_sub_classes WHERE asset_class = ?");
mysqli_stmt_bind_param($stmt, 's', $assetClassId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . esc($row['T_id']) . "'>" . esc($row['sub_class']) . "</option>";
    }
} else {
    echo "<option value=''>No sub-classes available</option>";
}

mysqli_stmt_close($stmt);
