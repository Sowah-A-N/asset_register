<?php
// Action: forgot_password — POST _action=forgot_password
// Generates a reset token and stores its hash.
// In this system there is no email sending — admin must relay the token manually.

csrf_verify();

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$username = post('username');

if ($username === '') {
    set_flash('error', 'Please enter your username.');
    header('Location: ' . $base_url . '/forgot-password');
    exit;
}

// Look up user
$stmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? AND is_active = 1 LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Always show a success message to prevent username enumeration
if ($user) {
    // Invalidate any previous tokens for this user
    $del = mysqli_prepare($conn, 'DELETE FROM password_reset_tokens WHERE user_id = ?');
    mysqli_stmt_bind_param($del, 'i', $user['id']);
    mysqli_stmt_execute($del);
    mysqli_stmt_close($del);

    // Generate token
    $raw_token  = bin2hex(random_bytes(32));          // 64 hex chars
    $token_hash = hash('sha256', $raw_token);
    $expires_at = date('Y-m-d H:i:s', time() + 3600); // 1 hour

    $ins = mysqli_prepare($conn,
        'INSERT INTO password_reset_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($ins, 'iss', $user['id'], $token_hash, $expires_at);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);

    // Log — the raw token is shown to admins via audit_log detail
    audit_log($conn, 'auth.password_reset_requested', 'users', $user['id'],
        'Reset token generated. Admin token: ' . $raw_token);
}

set_flash('success', 'If that username exists, a reset token has been generated. Contact your administrator.');
header('Location: ' . $base_url . '/forgot-password');
exit;
