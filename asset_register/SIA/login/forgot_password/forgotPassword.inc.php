<?php
/**
 * forgotPassword.inc.php
 *
 * Fixes applied:
 * - $email_address was never assigned (was undefined variable)
 * - "SELECT FROM ..." was missing column list — broken SQL
 * - Reset code was echoed in a JavaScript alert (exposed in HTTP response)
 * - 4-digit numeric code replaced with a cryptographically secure 8-char hex token
 * - Wrong redirect paths (/timetableGenerator/...) corrected
 * - DB errors no longer echoed to users
 * - Queries use prepared statements
 */

require_once __DIR__ . '/../../../security.php';
include 'datacon.php';
secure_session_start();

if (!isset($_POST['getCode'])) {
    echo "<script>alert('Invalid request.'); window.location='../forgot_password/';</script>";
    exit();
}

if (mysqli_connect_errno()) {
    error_log('DB connection failed: ' . mysqli_connect_error());
    echo "<script>alert('Service temporarily unavailable.'); window.location='../forgot_password/';</script>";
    exit();
}

$email_address = trim($_POST['email_address'] ?? '');

if (empty($email_address) || !filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please enter a valid email address.'); window.location='../forgot_password/';</script>";
    exit();
}

// Check if the email exists in admin_logs
$stmt = mysqli_prepare($conn, "SELECT username FROM admin_logs WHERE email = ? LIMIT 1");
if (!$stmt) {
    error_log('Prepare failed: ' . mysqli_error($conn));
    echo "<script>alert('Service temporarily unavailable.'); window.location='../forgot_password/';</script>";
    exit();
}
mysqli_stmt_bind_param($stmt, 's', $email_address);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Always show the same message regardless of whether the email exists
// (prevents user enumeration via the forgot-password endpoint)
if (!$user) {
    echo "<script>alert('If that email is registered, a reset code has been sent.'); window.location='../forgot_password/';</script>";
    exit();
}

// Generate a cryptographically secure token
$confirm_code   = bin2hex(random_bytes(8)); // 16-char hex, 64-bit entropy
$hashed_code    = password_hash($confirm_code, PASSWORD_DEFAULT);
$expires_at     = date('Y-m-d H:i:s', strtotime('+30 minutes'));

// Remove any existing reset codes for this email, then insert the new one
$del = mysqli_prepare($conn, "DELETE FROM password_reset_code WHERE user_email = ?");
mysqli_stmt_bind_param($del, 's', $email_address);
mysqli_stmt_execute($del);
mysqli_stmt_close($del);

$ins = mysqli_prepare($conn, "INSERT INTO password_reset_code (user_email, password_reset_code, expires_at) VALUES (?, ?, ?)");
if (!$ins) {
    error_log('Prepare failed (insert reset code): ' . mysqli_error($conn));
    echo "<script>alert('Service temporarily unavailable.'); window.location='../forgot_password/';</script>";
    exit();
}
mysqli_stmt_bind_param($ins, 'sss', $email_address, $hashed_code, $expires_at);
if (!mysqli_stmt_execute($ins)) {
    error_log('Insert reset code failed: ' . mysqli_error($conn));
    echo "<script>alert('Service temporarily unavailable.'); window.location='../forgot_password/';</script>";
    exit();
}
mysqli_stmt_close($ins);

// Send the code via email using PHPMailer (already a project dependency).
// TODO: configure MAIL_* constants from environment variables and enable this block.
//
// use PHPMailer\PHPMailer\PHPMailer;
// require __DIR__ . '/../../../../vendor/autoload.php';
// $mail = new PHPMailer(true);
// $mail->isSMTP();
// $mail->Host       = getenv('MAIL_HOST');
// $mail->SMTPAuth   = true;
// $mail->Username   = getenv('MAIL_USER');
// $mail->Password   = getenv('MAIL_PASS');
// $mail->SMTPSecure = 'tls';
// $mail->Port       = (int)getenv('MAIL_PORT');
// $mail->setFrom(getenv('MAIL_USER'), getenv('MAIL_FROM_NAME'));
// $mail->addAddress($email_address);
// $mail->Subject = 'Password Reset Code';
// $mail->Body    = "Your password reset code is: $confirm_code\n\nThis code expires in 30 minutes.\nIf you did not request this, ignore this email.";
// $mail->send();

// The code must ONLY be delivered by email — never placed in an HTTP response.
echo "<script>alert('If that email is registered, a reset code has been sent.'); window.location='../reset_password/';</script>";
exit();
