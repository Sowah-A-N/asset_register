<?php
/**
 * FILE:    schedule_officer/login/forgot_password/reset_password.inc.php
 * PURPOSE: Step 2 of password reset — verify the reset code and update password.
 *
 * Flow:
 *   reset_password.html → POST here → verify username + code →
 *   UPDATE admin_logs.user_password → DELETE used code → redirect to login
 */

include "datacon.php";

if (mysqli_connect_errno()) {
    error_log('[RMU] reset_password DB error: ' . mysqli_connect_error());
    echo "<script>alert('Service temporarily unavailable. Please try again.');
          window.location='reset_password.html';</script>";
    exit();
}

if (!isset($_POST['resetPassword'])) {
    header('Location: reset_password.html');
    exit();
}

// ── Collect and validate inputs ──────────────────────────────────────────
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
    echo "<script>alert('Passwords do not match. Please try again.'); window.history.back();</script>";
    exit();
}

$username_safe = mysqli_real_escape_string($conn, $username);

// ── Fetch the stored reset code for this user ────────────────────────────
$sql    = "SELECT password_reset_code FROM password_reset_code
           WHERE user_email = '$username_safe'
           ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('No reset code found for this username.\\n\\nPlease start the password reset process again.');
          window.location='index.html';</script>";
    exit();
}

$row         = mysqli_fetch_assoc($result);
$stored_hash = $row['password_reset_code'];

// ── Verify the code ──────────────────────────────────────────────────────
if (!password_verify($code_input, $stored_hash)) {
    echo "<script>alert('Invalid reset code. Please check and try again.'); window.history.back();</script>";
    exit();
}

// ── Verify the username exists in admin_logs ─────────────────────────────
$checkSql    = "SELECT username FROM admin_logs WHERE username = '$username_safe' LIMIT 1";
$checkResult = mysqli_query($conn, $checkSql);

if (!$checkResult || mysqli_num_rows($checkResult) === 0) {
    echo "<script>alert('Username not found.'); window.history.back();</script>";
    exit();
}

// ── Update the password ──────────────────────────────────────────────────
$new_hashed  = password_hash($new_password, PASSWORD_DEFAULT);
$updateSql   = "UPDATE admin_logs SET user_password = '$new_hashed'
                WHERE username = '$username_safe'";
$updateResult = mysqli_query($conn, $updateSql);

if (!$updateResult || mysqli_affected_rows($conn) === 0) {
    error_log('[RMU] Password update failed for user: ' . $username);
    echo "<script>alert('Error updating password. Please try again.'); window.history.back();</script>";
    exit();
}

// ── Delete the used reset code ───────────────────────────────────────────
mysqli_query($conn, "DELETE FROM password_reset_code WHERE user_email = '$username_safe'");

// ── Success ──────────────────────────────────────────────────────────────
echo "<script>
    alert('Password reset successfully!\\n\\nYou can now log in with your new password.');
    window.location = '../index.html';
</script>";
exit();
