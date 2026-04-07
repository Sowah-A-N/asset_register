<?php
csrf_verify();
require_role('hod_ict');

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$action   = post('_action');

// ── Delete ──────────────────────────────────────────────────────────────────
if ($action === 'user_delete') {
    $id = post_int('user_id');
    if ($id && $id !== current_user_id()) {
        $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        audit_log($conn, 'user.delete', 'users', $id, 'User deleted');
        set_flash('success', 'User deleted.');
    } else {
        set_flash('error', 'Cannot delete yourself.');
    }
    header('Location: ' . $base_url . '/admin/users');
    exit;
}

// ── Edit ────────────────────────────────────────────────────────────────────
if ($action === 'user_edit') {
    $id           = post_int('user_id');
    $username     = post('username');
    $display_name = post('display_name');
    $role         = post('role');
    $is_active    = post_int('is_active');
    $password     = $_POST['password'] ?? '';

    if (!$id || !$username || !$role) {
        set_flash('error', 'Required fields missing.');
        header('Location: ' . $base_url . '/admin/users');
        exit;
    }

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = mysqli_prepare($conn,
            'UPDATE users SET username=?, display_name=?, role=?, is_active=?, password_hash=?
             WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'sssisi', $username, $display_name, $role, $is_active, $hash, $id);
    } else {
        $stmt = mysqli_prepare($conn,
            'UPDATE users SET username=?, display_name=?, role=?, is_active=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'sssii', $username, $display_name, $role, $is_active, $id);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    audit_log($conn, 'user.edit', 'users', $id, "Edited: {$username}");
    set_flash('success', 'User updated.');
    header('Location: ' . $base_url . '/admin/users');
    exit;
}

// ── Create ───────────────────────────────────────────────────────────────────
$username     = post('username');
$display_name = post('display_name');
$role         = post('role');
$password     = $_POST['password'] ?? '';

if (!$username || !$display_name || !$role || strlen($password) < 8) {
    set_flash('error', 'All fields required; password must be at least 8 characters.');
    header('Location: ' . $base_url . '/admin/users');
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
$stmt = mysqli_prepare($conn,
    'INSERT INTO users (username, display_name, role, password_hash) VALUES (?,?,?,?)');
mysqli_stmt_bind_param($stmt, 'ssss', $username, $display_name, $role, $hash);

if (mysqli_stmt_execute($stmt)) {
    $new_id = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    audit_log($conn, 'user.create', 'users', $new_id, "Created: {$username}");
    set_flash('success', "User '{$username}' created.");
} else {
    mysqli_stmt_close($stmt);
    set_flash('error', mysqli_errno($conn) === 1062
        ? "Username '{$username}' already exists."
        : 'Database error: ' . mysqli_error($conn));
}
header('Location: ' . $base_url . '/admin/users');
exit;
// ── Asset user save (staff who hold assets) ──────────────────────────────────
if ($action === 'asset_user_save') {
    require_role('hod_ict', 'schedule_officer');
    $staff_id   = post('staff_id');
    $first_name = post('staff_first_name');
    $last_name  = post('staff_last_name');
    $department = post('department');
    if (!$staff_id) {
        set_flash('error', 'Staff ID required.');
        header('Location: ' . $base_url . '/admin/asset-users');
        exit;
    }
    $s = mysqli_prepare($conn,
        'INSERT INTO asset_users (staff_id, staff_first_name, staff_last_name, department)
         VALUES (?,?,?,?)');
    mysqli_stmt_bind_param($s, 'ssss', $staff_id, $first_name, $last_name, $department);
    if (mysqli_stmt_execute($s)) {
        audit_log($conn, 'asset_user.create', 'asset_users', (int)mysqli_insert_id($conn),
            "Added staff: {$staff_id}");
        set_flash('success', 'Staff member added.');
    } else {
        set_flash('error', mysqli_errno($conn) === 1062
            ? "Staff ID '{$staff_id}' already exists." : mysqli_error($conn));
    }
    mysqli_stmt_close($s);
    header('Location: ' . $base_url . '/admin/asset-users');
    exit;
}
