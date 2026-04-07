<?php
csrf_verify();
require_role('hod_ict', 'schedule_officer');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$id       = post_int('asset_id');
$id_number = post('id_number');

if (!$id || $id_number === '') {
    set_flash('error', 'Asset ID number is required.');
    header('Location: ' . $base_url . '/assets/add-id?id=' . $id);
    exit;
}

$grv    = post('grv_number') ?: 'N/A';
$serial = post('serial_number') ?: 'N/A';
$pv     = post('pv_number') ?: 'N/A';

$stmt = mysqli_prepare($conn,
    'UPDATE assets SET id_number = ?, grv_number = ?, serial_number = ?, pv_number = ?
     WHERE asset_id = ?');
mysqli_stmt_bind_param($stmt, 'ssssi', $id_number, $grv, $serial, $pv, $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    audit_log($conn, 'asset.add_id', 'assets', $id, "Assigned ID: {$id_number}");
    set_flash('success', 'Asset ID assigned successfully.');
    header('Location: ' . $base_url . '/assets/untracked');
} else {
    mysqli_stmt_close($stmt);
    set_flash('error', 'Database error: ' . mysqli_error($conn));
    header('Location: ' . $base_url . '/assets/add-id?id=' . $id);
}
exit;
