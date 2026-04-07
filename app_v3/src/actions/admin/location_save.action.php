<?php
csrf_verify();
require_role('hod_ict', 'schedule_officer', 'accountant', 'dsu');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$location = post('location');
$loc_code = post('loc_code');

if ($location === '') {
    set_flash('error', 'Location name required.');
    header('Location: ' . $base_url . '/admin/locations');
    exit;
}
$stmt = mysqli_prepare($conn, 'INSERT INTO asset_location (location, loc_code) VALUES (?,?)');
mysqli_stmt_bind_param($stmt, 'ss', $location, $loc_code);
if (mysqli_stmt_execute($stmt)) {
    audit_log($conn, 'location.create', 'asset_location', (int)mysqli_insert_id($conn), "Added: {$location}");
    set_flash('success', "Location '{$location}' added.");
} else {
    set_flash('error', mysqli_errno($conn) === 1062 ? "'{$location}' already exists." : mysqli_error($conn));
}
mysqli_stmt_close($stmt);
header('Location: ' . $base_url . '/admin/locations');
exit;
