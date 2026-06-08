<?php
/**
 * FILE:    schedule_officer/login/forgot_password/forgotPassword.inc.php
 * PURPOSE: Step 1 of password reset — verify username, generate and store
 *          a reset code, show it to the user, redirect to reset_password.html
 *
 * FIXES APPLIED:
 *  - Form field was 'role' (non-existent) → now reads 'username' from POST
 *  - $email_address was never assigned → replaced with $identifier (username)
 *  - SQL was "SELECT  FROM ..." (syntax error) → fixed to SELECT code_status
 *  - All redirects pointed to /timetableGenerator/... → now use correct paths
 *  - Queries user_logs (non-existent) → now queries admin_logs
 *  - Reset code upgraded from 4 random digits to 6-char alphanumeric
 *  - Old reset codes for the same user are purged before creating a new one
 */

include "datacon.php";

if (mysqli_connect_errno()) {
    error_log('[RMU] forgot_password DB error: ' . mysqli_connect_error());
    echo "<script>alert('Service temporarily unavailable. Please try again.');
          window.location='index.html';</script>";
    exit();
}

if (!isset($_POST['getCode'])) {
    echo "<script>window.location='index.html';</script>";
    exit();
}

$identifier = trim($_POST['username'] ?? '');

if (empty($identifier)) {
    echo "<script>alert('Please enter your username.'); window.location='index.html';</script>";
    exit();
}

$identifier = mysqli_real_escape_string($conn, $identifier);

// ── Verify username exists in admin_logs ────────────────────────────────
$sql    = "SELECT username FROM admin_logs WHERE username = '$identifier' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('Username not found. Please check and try again.');
          window.location='index.html';</script>";
    exit();
}

// ── Remove any existing reset codes for this user ───────────────────────
mysqli_query($conn, "DELETE FROM password_reset_code WHERE user_email = '$identifier'");

// ── Generate a 6-character alphanumeric reset code ───────────────────────
// Excludes ambiguous characters (0, O, I, 1) for readability
$chars        = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$confirm_code = '';
for ($i = 0; $i < 6; $i++) {
    $confirm_code .= $chars[random_int(0, strlen($chars) - 1)];
}

$hashed_code = password_hash($confirm_code, PASSWORD_DEFAULT);

// ── Store the reset code ────────────────────────────────────────────────
$insertSql = "INSERT INTO password_reset_code (user_email, password_reset_code)
              VALUES ('$identifier', '$hashed_code')";

if (!mysqli_query($conn, $insertSql)) {
    error_log('[RMU] Reset code insert failed: ' . mysqli_error($conn));
    echo "<script>alert('Error generating reset code. Please try again.');
          window.location='index.html';</script>";
    exit();
}

// ── Show code to user and redirect to reset form ────────────────────────
echo "<script>
    alert('Your password reset code is:\\n\\n  {$confirm_code}\\n\\n' +
          'Please write this code down — you will need it on the next screen.');
    window.location = 'reset_password.html';
</script>";
exit();
