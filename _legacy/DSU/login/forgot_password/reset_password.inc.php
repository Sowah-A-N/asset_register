<?php
/**
 * FILE:    DSU/login/forgot_password/reset_password.inc.php
 * PURPOSE: Step 2 of DSU password reset — verify code, update password in admin_logs.
 */

include "datacon.php";

if (mysqli_connect_errno()) {
    error_log('[RMU-DSU] reset_password DB error: ' . mysqli_connect_error());
    echo "<script>alert('Service temporarily unavailable.');
          window.location='reset_password.html';</script>";
    exit();
}

if (!isset($_POST['resetPassword'])) {
    header('Location: reset_password.html');
    exit();
}

$username         = trim($_POST['username']         ?? '');
$code_input       = strtoupper(trim($_POST['reset_code']       ?? ''));
$new_password     = $_POST['new_password']     ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($username) || empty($code_input) || empty($new_password) || empty($confirm_password)) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

if (strlen($new_password) < 8) {
    echo "<script>alert('Password must be at least 8 characters.'); window.history.back();</script>";
    exit();
}

if ($new_password !== $confirm_password) {
    echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
    exit();
}

$username_safe = mysqli_real_escape_string($conn, $username);

// Fetch stored reset code
$result = mysqli_query($conn,
    "SELECT password_reset_code FROM password_reset_code
     WHERE user_email = '$username_safe'
     ORDER BY id DESC LIMIT 1");

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('No reset code found.\\n\\nPlease start the password reset process again.');
          window.location='index.html';</script>";
    exit();
}

$stored_hash = mysqli_fetch_assoc($result)['password_reset_code'];

if (!password_verify($code_input, $stored_hash)) {
    echo "<script>alert('Invalid reset code. Please check and try again.'); window.history.back();</script>";
    exit();
}

// Verify username exists
$check = mysqli_query($conn,
    "SELECT username FROM admin_logs WHERE username = '$username_safe' LIMIT 1");

if (!$check || mysqli_num_rows($check) === 0) {
    echo "<script>alert('Username not found.'); window.history.back();</script>";
    exit();
}

// Update password
$new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
$updated    = mysqli_query($conn,
    "UPDATE admin_logs SET user_password = '$new_hashed'
     WHERE username = '$username_safe'");

if (!$updated || mysqli_affected_rows($conn) === 0) {
    error_log('[RMU-DSU] Password update failed for: ' . $username);
    echo "<script>alert('Error updating password. Please try again.'); window.history.back();</script>";
    exit();
}

// Delete used code
mysqli_query($conn, "DELETE FROM password_reset_code WHERE user_email = '$username_safe'");

echo "<script>
    alert('Password reset successfully!\\n\\nYou can now log in with your new password.');
    window.location = '../index.html';
</script>";
exit();
