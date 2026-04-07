<?php
csrf_verify();
require_role('hod_ict', 'schedule_officer', 'accountant');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$id       = post_int('ast_id'); // present on edit, 0 on create
$name     = post('asset_class');
$rate     = post_float('dep_rate');
$life     = post_int('estimated_life');
$dep      = post_int('depreciated') ? 1 : 0;

if ($name === '') {
    set_flash('error', 'Class name is required.');
    header('Location: ' . $base_url . '/admin/asset-classes');
    exit;
}

if ($id) {
    $stmt = mysqli_prepare($conn,
        'UPDATE asset_classes SET asset_class=?, dep_rate=?, estimated_life=?, depreciated=? WHERE ast_id=?');
    mysqli_stmt_bind_param($stmt, 'sdiii', $name, $rate, $life, $dep, $id);
    $label = 'Updated';
} else {
    $stmt = mysqli_prepare($conn,
        'INSERT INTO asset_classes (asset_class, dep_rate, estimated_life, depreciated) VALUES (?,?,?,?)');
    mysqli_stmt_bind_param($stmt, 'sdii', $name, $rate, $life, $dep);
    $label = 'Created';
}

if (mysqli_stmt_execute($stmt)) {
    $rid = $id ?: (int)mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    audit_log($conn, 'asset_class.' . strtolower($label), 'asset_classes', $rid, "{$label}: {$name}");
    set_flash('success', "Asset class '{$name}' {$label}.");
} else {
    mysqli_stmt_close($stmt);
    set_flash('error', mysqli_errno($conn) === 1062
        ? "'{$name}' already exists."
        : 'Database error: ' . mysqli_error($conn));
}
header('Location: ' . $base_url . '/admin/asset-classes');
exit;
