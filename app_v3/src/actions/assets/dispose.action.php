<?php
// Action: asset_dispose — marks asset as disposed, copies to disposals table
csrf_verify();
require_role('hod_ict', 'schedule_officer');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$id       = post_int('asset_id');

if (!$id) {
    set_flash('error', 'Invalid asset ID.');
    header('Location: ' . $base_url . '/assets');
    exit;
}

require_once SRC . '/queries/assets.php';
$asset = get_asset_by_id($conn, $id);
if (!$asset) {
    set_flash('error', 'Asset not found.');
    header('Location: ' . $base_url . '/assets');
    exit;
}

$disposal_value       = post_float('disposal_value');
$disposal_month       = post_int('disposal_month');
$estimated_life_months= post_int('estimated_life_months') ?: 60;

// Insert into disposals table
$ins = mysqli_prepare($conn,
    'INSERT INTO disposals
        (asset_name, grv_number, serial_number, pv_number, id_number,
         supplier_name, asset_class, sub_class, asset_type, location, `user`,
         acquisition_date, current_year, historical_cost, additions,
         disposal_value, active_res_value, dollar_rate_used,
         disposal_month, estimated_life_months)
     VALUES (?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?)');

mysqli_stmt_bind_param($ins, 'ssssssssssssidddddii',
    $asset['asset_name'], $asset['grv_number'], $asset['serial_number'],
    $asset['pv_number'], $asset['id_number'],
    $asset['supplier_name'], $asset['asset_class'], $asset['sub_class'],
    $asset['asset_type'], $asset['location'], $asset['user'],
    $asset['acquisition_date'], $asset['current_year'],
    $asset['historical_cost'], $asset['additions'],
    $disposal_value, $asset['active_res_value'], $asset['dollar_rate_used'],
    $disposal_month, $estimated_life_months
);

if (!mysqli_stmt_execute($ins)) {
    mysqli_stmt_close($ins);
    set_flash('error', 'Failed to record disposal: ' . mysqli_error($conn));
    header('Location: ' . $base_url . '/assets');
    exit;
}
mysqli_stmt_close($ins);

// Mark as disposed in assets table
$upd = mysqli_prepare($conn,
    'UPDATE assets SET disposed = 1, disposal_value = ? WHERE asset_id = ?');
mysqli_stmt_bind_param($upd, 'di', $disposal_value, $id);
mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

audit_log($conn, 'asset.dispose', 'assets', $id, 'Disposed: ' . $asset['asset_name']);
set_flash('success', 'Asset "' . $asset['asset_name'] . '" disposed successfully.');
header('Location: ' . $base_url . '/assets');
exit;
