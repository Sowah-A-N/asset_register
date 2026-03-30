<?php
require_once __DIR__ . '/../../security.php';
include '../datacon.php';

secure_session_start();

if (!isset($_POST['Login'])) {
    echo "<script>alert('Invalid request.'); window.location='../login/';</script>";
    exit();
}

if (!$conn) {
    error_log('DB connection failed: ' . mysqli_connect_error());
    echo "<script>alert('Service temporarily unavailable. Please try again later.');</script>";
    exit();
}

$username = trim($_POST['username'] ?? '');
$pass     = $_POST['pass'] ?? '';

if (empty($username) || empty($pass)) {
    echo "<script>alert('Invalid username or password.');</script>";
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT username, user_password FROM admin_logs WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row    = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row || !password_verify($pass, $row['user_password'])) {
    echo "<script>alert('Invalid username or password.');</script>";
    exit();
}

session_regenerate_id(true);
$_SESSION['username'] = $row['username'];
$_SESSION['role']     = 'schedule_officer';
header('Location: ../dashboard/?login=success');
exit();
