<?php
csrf_verify();
require_role('hod_ict', 'schedule_officer', 'accountant');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$rate     = post_float('dollar_rate');

if ($rate <= 0) {
    set_flash('error', 'Rate must be greater than 0.');
    header('Location: ' . $base_url . '/admin/dollar-rate');
    exit;
}

// Deactivate existing active rate
mysqli_query($conn,
    "UPDATE dollar_rate SET rate_status = 'INACTIVE' WHERE rate_status = 'ACTIVE'");

// Insert new active rate
$by   = current_user();
$stmt = mysqli_prepare($conn,
    "INSERT INTO dollar_rate (dollar_rate, rate_status, action_by) VALUES (?, 'ACTIVE', ?)");
mysqli_stmt_bind_param($stmt, 'ds', $rate, $by);
mysqli_stmt_execute($stmt);
$new_id = (int)mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

audit_log($conn, 'dollar_rate.set', 'dollar_rate', $new_id,
    "New active rate: {$rate} set by {$by}");
set_flash('success', 'Dollar rate set to GH₵ ' . number_format($rate, 4) . ' per $1.');
header('Location: ' . $base_url . '/admin/dollar-rate');
exit;

// ── Asset type save ──────────────────────────────────────────────────────────
if (($action ?? '') === 'asset_type_save') {
    csrf_verify();
    require_role('hod_ict', 'schedule_officer');
    $base_url  = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
    $type_name = post('asset_type');
    if ($type_name === '') {
        set_flash('error', 'Type name required.');
    } else {
        $s = mysqli_prepare($conn, 'INSERT INTO asset_type (asset_type) VALUES (?)');
        mysqli_stmt_bind_param($s, 's', $type_name);
        if (mysqli_stmt_execute($s)) {
            set_flash('success', "Type '{$type_name}' added.");
        } else {
            set_flash('error', 'Already exists or DB error.');
        }
        mysqli_stmt_close($s);
    }
    header('Location: ' . $base_url . '/admin/asset-types');
    exit;
}
