<?php
// Action: asset_edit
csrf_verify();
require_role('hod_ict', 'schedule_officer', 'accountant');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$id       = post_int('asset_id');

if (!$id) {
    set_flash('error', 'Invalid asset ID.');
    header('Location: ' . $base_url . '/assets');
    exit;
}

$stmt = mysqli_prepare($conn,
    'UPDATE assets SET
        asset_name      = ?,
        grv_number      = ?,
        serial_number   = ?,
        pv_number       = ?,
        id_number       = ?,
        supplier_name   = ?,
        asset_class     = ?,
        sub_class       = ?,
        asset_type      = ?,
        location        = ?,
        `user`          = ?,
        acquisition_date= ?,
        additions       = ?,
        dollar_rate_used= ?
     WHERE asset_id = ?');

$asset_name      = post('asset_name');
$grv_number      = post('grv_number') ?: 'N/A';
$serial_number   = post('serial_number') ?: 'N/A';
$pv_number       = post('pv_number') ?: 'N/A';
$id_number       = post('id_number') ?: 'N/A';
$supplier_name   = post('supplier_name');
$asset_class     = post('asset_class');
$sub_class       = post('sub_class');
$asset_type      = post('asset_type');
$location        = post('location');
$asset_user      = post('asset_user');
$acquisition_date= post('acquisition_date');
$additions       = post_float('additions');
$dollar_rate     = post_float('dollar_rate_used') ?: 1.0;

mysqli_stmt_bind_param($stmt, 'ssssssssssssddi',
    $asset_name, $grv_number, $serial_number, $pv_number, $id_number,
    $supplier_name, $asset_class, $sub_class, $asset_type, $location, $asset_user,
    $acquisition_date, $additions, $dollar_rate, $id
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    audit_log($conn, 'asset.edit', 'assets', $id, 'Updated: ' . $asset_name);
    set_flash('success', 'Asset updated successfully.');
} else {
    mysqli_stmt_close($stmt);
    set_flash('error', 'Database error: ' . mysqli_error($conn));
}

header('Location: ' . $base_url . '/assets');
exit;
