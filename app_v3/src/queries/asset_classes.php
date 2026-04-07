<?php
// queries/asset_classes.php — Shared queries for asset_classes, sub_classes, locations, suppliers, asset_type

function get_all_asset_classes($conn): array {
    $result = mysqli_query($conn, 'SELECT ast_id, asset_class, dep_rate, estimated_life, depreciated FROM asset_classes ORDER BY asset_class ASC');
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function get_sub_classes_for_class($conn, string $asset_class): array {
    $stmt = mysqli_prepare($conn, 'SELECT T_id, sub_class FROM asset_class_sub_classes WHERE asset_class = ? ORDER BY sub_class ASC');
    mysqli_stmt_bind_param($stmt, 's', $asset_class);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    mysqli_stmt_close($stmt);
    return $rows;
}

function get_all_locations($conn): array {
    $result = mysqli_query($conn, 'SELECT loc_id, location FROM asset_location ORDER BY location ASC');
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function get_all_suppliers($conn): array {
    $result = mysqli_query($conn, 'SELECT sup_id, name FROM suppliers ORDER BY name ASC');
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function get_all_asset_types($conn): array {
    $result = mysqli_query($conn, 'SELECT type_id, asset_type FROM asset_type ORDER BY asset_type ASC');
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function get_all_asset_users($conn): array {
    $result = mysqli_query($conn, 'SELECT staff_id, staff_first_name, staff_last_name FROM asset_users ORDER BY staff_last_name, staff_first_name ASC');
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}
