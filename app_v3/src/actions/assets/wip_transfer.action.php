<?php
// Action: wip_transfer — capitalise a Building WIP asset into Land And Buildings
csrf_verify();
require_role('hod_ict', 'schedule_officer', 'accountant');

$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$wip_id     = post_int('wip_asset_id');
$new_name   = post('new_asset_name');
$new_id_no  = post('new_id_number');
$new_sub    = post('new_sub_class') ?: 'Land And Buildings';
$notes      = post('notes') ?: 'WIP capitalised into Land And Buildings';

if (!$wip_id || $new_name === '') {
    set_flash('error', 'WIP asset and new name are required.');
    header('Location: ' . $base_url . '/assets/wip-transfer');
    exit;
}

// Load WIP asset
require_once SRC . '/queries/assets.php';
$wip = get_asset_by_id($conn, $wip_id);
if (!$wip || $wip['asset_class'] !== 'Building Works in Progress') {
    set_flash('error', 'Invalid WIP asset.');
    header('Location: ' . $base_url . '/assets/wip-transfer');
    exit;
}

if ($new_id_no === '') {
    $new_id_no = 'CAP-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

// 1. Insert new Land And Buildings asset (copy financial values from WIP)
$ins = mysqli_prepare($conn,
    'INSERT INTO assets
        (asset_name, grv_number, serial_number, pv_number, id_number,
         supplier_name, asset_class, sub_class, asset_type, location, `user`,
         acquisition_date, current_year, historical_cost, additions,
         disposal_value, active_res_value, dollar_rate_used)
     VALUES (?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?, ?,?,?)');

$new_class = 'Land And Buildings';
mysqli_stmt_bind_param($ins, 'ssssssssssssiddddd',
    $new_name,
    $wip['grv_number'],
    $wip['serial_number'],
    $wip['pv_number'],
    $new_id_no,
    $wip['supplier_name'],
    $new_class,
    $new_sub,
    $wip['asset_type'],
    $wip['location'],
    $wip['user'],
    $wip['acquisition_date'],
    $wip['current_year'],
    $wip['historical_cost'],
    $wip['additions'],
    0.00,
    $wip['active_res_value'],
    $wip['dollar_rate_used']
);

if (!mysqli_stmt_execute($ins)) {
    mysqli_stmt_close($ins);
    set_flash('error', 'Failed to create capitalised asset: ' . mysqli_error($conn));
    header('Location: ' . $base_url . '/assets/wip-transfer');
    exit;
}
$new_asset_id = (int)mysqli_insert_id($conn);
mysqli_stmt_close($ins);

// 2. Mark WIP asset as transferred (don't delete — keep the audit trail)
$now = date('Y-m-d H:i:s');
$upd = mysqli_prepare($conn,
    'UPDATE assets SET transferred_at = ?, transferred_to_asset_id = ? WHERE asset_id = ?');
mysqli_stmt_bind_param($upd, 'sii', $now, $new_asset_id, $wip_id);
mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

// 3. Record in asset_category_changes
$changed_by = current_user();
$old_class  = 'Building Works in Progress';
$acc_ins = mysqli_prepare($conn,
    'INSERT INTO asset_category_changes
        (asset_id, asset_name, old_asset_class, new_asset_class, old_sub_class, new_sub_class, reason, changed_by)
     VALUES (?,?,?,?,?,?,?,?)');
mysqli_stmt_bind_param($acc_ins, 'isssssss',
    $wip_id, $wip['asset_name'], $old_class, $new_class,
    $wip['sub_class'], $new_sub, $notes, $changed_by);
mysqli_stmt_execute($acc_ins);
mysqli_stmt_close($acc_ins);

audit_log($conn, 'asset.wip_transfer', 'assets', $wip_id,
    "WIP '{$wip['asset_name']}' capitalised → asset_id:{$new_asset_id} ({$new_name})");

set_flash('success',
    "WIP asset capitalised successfully. New Land &amp; Buildings record ID: {$new_id_no}.");
header('Location: ' . $base_url . '/assets/wip-transfer');
exit;
