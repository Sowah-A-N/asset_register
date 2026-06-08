<?php
/**
 * FILE:    DSU/login/forgot_password/forgotPassword.inc.php
 * PURPOSE: Step 1 of DSU password reset — verify username, generate reset code.
 *
 * FIXES APPLIED (identical to schedule_officer version):
 *  - Was reading $_POST['role'] (non-existent) → now reads 'username'
 *  - SQL was "SELECT  FROM ..." (syntax error) → fixed
 *  - All redirects pointed to /timetableGenerator/... → corrected
 *  - Was querying user_logs (non-existent) → now queries admin_logs
 *  - Code upgraded from 4 random digits to 6-char alphanumeric
 */

include "datacon.php";

if (mysqli_connect_errno()) {
    error_log('[RMU-DSU] forgot_password DB error: ' . mysqli_connect_error());
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

// Verify username exists in admin_logs
$sql    = "SELECT username FROM admin_logs WHERE username = '$identifier' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<script>alert('Username not found. Please check and try again.');
          window.location='index.html';</script>";
    exit();
}

// Remove any existing reset codes for this user
mysqli_query($conn, "DELETE FROM password_reset_code WHERE user_email = '$identifier'");

// Generate a 6-character alphanumeric reset code (excludes ambiguous chars)
$chars        = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$confirm_code = '';
for ($i = 0; $i < 6; $i++) {
    $confirm_code .= $chars[random_int(0, strlen($chars) - 1)];
}

$hashed_code = password_hash($confirm_code, PASSWORD_DEFAULT);

$insertSql = "INSERT INTO password_reset_code (user_email, password_reset_code)
              VALUES ('$identifier', '$hashed_code')";

if (!mysqli_query($conn, $insertSql)) {
    error_log('[RMU-DSU] Reset code insert failed: ' . mysqli_error($conn));
    echo "<script>alert('Error generating reset code. Please try again.');
          window.location='index.html';</script>";
    exit();
}

echo "<script>
    alert('Your password reset code is:\\n\\n  {$confirm_code}\\n\\n' +
          'Please write this code down — you will need it on the next screen.');
    window.location = 'reset_password.html';
</script>";
exit();
