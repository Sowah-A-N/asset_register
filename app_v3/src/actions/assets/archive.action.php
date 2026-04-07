<?php
// Handles: asset_archive and asset_restore
csrf_verify();
require_role('hod_ict', 'schedule_officer');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$action   = post('_action');
$id       = post_int('asset_id');

if (!$id) {
    set_flash('error', 'Invalid asset ID.');
    header('Location: ' . $base_url . '/assets');
    exit;
}

if ($action === 'asset_archive') {
    $now  = date('Y-m-d H:i:s');
    $stmt = mysqli_prepare($conn,
        'UPDATE assets SET archived = 1, archived_at = ? WHERE asset_id = ?');
    mysqli_stmt_bind_param($stmt, 'si', $now, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    audit_log($conn, 'asset.archive', 'assets', $id, 'Archived');
    set_flash('success', 'Asset archived.');
    header('Location: ' . $base_url . '/assets');
    exit;
}

if ($action === 'asset_restore') {
    $stmt = mysqli_prepare($conn,
        'UPDATE assets SET archived = 0, archived_at = NULL WHERE asset_id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    audit_log($conn, 'asset.restore', 'assets', $id, 'Restored from archive');
    set_flash('success', 'Asset restored to active.');
    header('Location: ' . $base_url . '/assets/archive');
    exit;
}

set_flash('error', 'Unknown action.');
header('Location: ' . $base_url . '/assets');
exit;
