<?php
csrf_verify();
require_role('hod_ict', 'schedule_officer');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

$serial      = post('serial_number');
$old_location= post('old_location');
$old_user    = post('old_user');
$new_location= post('new_location');
$new_user    = post('new_user');
$notes       = post('notes') ?: 'Asset moved';

if (!$serial || !$old_location || !$new_location) {
    set_flash('error', 'Serial number and locations are required.');
    header('Location: ' . $base_url . '/assets/moved');
    exit;
}

$stmt = mysqli_prepare($conn,
    'INSERT INTO moved_assets (serial_number, notes, old_location, old_user, new_location, new_user)
     VALUES (?, ?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'ssssss', $serial, $notes, $old_location, $old_user, $new_location, $new_user);

if (mysqli_stmt_execute($stmt)) {
    // Also update location/user on the asset record (match by serial number)
    $upd = mysqli_prepare($conn,
        'UPDATE assets SET location = ?, `user` = ? WHERE serial_number = ? AND disposed = 0 AND archived = 0');
    mysqli_stmt_bind_param($upd, 'sss', $new_location, $new_user, $serial);
    mysqli_stmt_execute($upd);
    mysqli_stmt_close($upd);

    audit_log($conn, 'asset.move', 'moved_assets', (int)mysqli_insert_id($conn),
        "Moved SN:{$serial} from {$old_location} to {$new_location}");
    set_flash('success', 'Asset move recorded.');
} else {
    set_flash('error', 'Failed to record move: ' . mysqli_error($conn));
}
mysqli_stmt_close($stmt);
header('Location: ' . $base_url . '/assets/moved');
exit;
