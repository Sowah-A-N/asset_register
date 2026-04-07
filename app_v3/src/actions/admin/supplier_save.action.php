<?php
csrf_verify();
require_role('hod_ict', 'schedule_officer');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$name     = post('name');
$location = post('location');
$number   = post('number');

if ($name === '') {
    set_flash('error', 'Supplier name required.');
    header('Location: ' . $base_url . '/admin/suppliers');
    exit;
}
$stmt = mysqli_prepare($conn, 'INSERT INTO suppliers (name, location, number) VALUES (?,?,?)');
mysqli_stmt_bind_param($stmt, 'sss', $name, $location, $number);
if (mysqli_stmt_execute($stmt)) {
    audit_log($conn, 'supplier.create', 'suppliers', (int)mysqli_insert_id($conn), "Added: {$name}");
    set_flash('success', "Supplier '{$name}' added.");
} else {
    set_flash('error', mysqli_errno($conn) === 1062 ? "'{$name}' already exists." : mysqli_error($conn));
}
mysqli_stmt_close($stmt);
header('Location: ' . $base_url . '/admin/suppliers');
exit;
