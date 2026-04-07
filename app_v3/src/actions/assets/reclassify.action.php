<?php
csrf_verify();
require_role('hod_ict', 'schedule_officer', 'accountant');

$base_url      = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$id            = post_int('asset_id');
$new_class     = post('new_asset_class');
$new_sub_class = post('new_sub_class');
$reason        = post('reason');

if (!$id || $new_class === '' || $reason === '') {
    set_flash('error', 'All required fields must be filled.');
    header('Location: ' . $base_url . '/assets/reclassify?id=' . $id);
    exit;
}

require_once SRC . '/queries/assets.php';
$asset = get_asset_by_id($conn, $id);
if (!$asset) {
    set_flash('error', 'Asset not found.');
    header('Location: ' . $base_url . '/assets');
    exit;
}

$old_class = $asset['asset_class'];
if ($old_class === $new_class) {
    set_flash('error', 'New class must be different from the current class.');
    header('Location: ' . $base_url . '/assets/reclassify?id=' . $id);
    exit;
}

// Update the asset
$upd = mysqli_prepare($conn,
    'UPDATE assets SET asset_class = ?, sub_class = ? WHERE asset_id = ?');
mysqli_stmt_bind_param($upd, 'ssi', $new_class, $new_sub_class, $id);
mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

// Log the reclassification
$changed_by = current_user();
$acc = mysqli_prepare($conn,
    'INSERT INTO asset_category_changes
        (asset_id, asset_name, old_asset_class, new_asset_class, old_sub_class, new_sub_class, reason, changed_by)
     VALUES (?,?,?,?,?,?,?,?)');
mysqli_stmt_bind_param($acc, 'isssssss',
    $id, $asset['asset_name'], $old_class, $new_class,
    $asset['sub_class'], $new_sub_class, $reason, $changed_by);
mysqli_stmt_execute($acc);
mysqli_stmt_close($acc);

audit_log($conn, 'asset.reclassify', 'assets', $id,
    "Reclassified from '{$old_class}' to '{$new_class}'. Reason: {$reason}");
set_flash('success', 'Asset reclassified from "' . $old_class . '" to "' . $new_class . '".');
header('Location: ' . $base_url . '/assets');
exit;
