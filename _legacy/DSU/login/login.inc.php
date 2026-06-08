<?php
/**
 * FILE:    DSU/login/login.inc.php
 * PURPOSE: Authenticate a DSU user against admin_logs and start their session.
 *
 * PHASE B CHANGES (identical to schedule_officer):
 *  - Loads RBAC roles + permissions via loadUserAuthorization()
 *  - session_regenerate_id(true) on success
 *  - FIXED: else-branch no longer redirects to 'SIA/login/'
 *  - $_SESSION['username'] still set for backward compatibility
 */

session_start();
require_once "../../auth.php";   // RBAC helpers
include "../datacon.php";        // $conn

if (!isset($_POST['Login'])) {
    header("Location: index.html");
    exit();
}

if (!$conn) {
    error_log('[RMU-DSU] login DB connection failed');
    echo "<script>alert('Service temporarily unavailable. Please try again.'); window.location='index.html';</script>";
    exit();
}

$username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
$pass     = $_POST['pass'] ?? '';

if (empty($username) || empty($pass)) {
    echo "<script>alert('Please enter your username and password.'); window.location='index.html';</script>";
    exit();
}

$sql    = "SELECT table_id, username, user_password FROM admin_logs WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) < 1) {
    echo "<script>alert('Invalid credentials.'); window.location='index.html';</script>";
    exit();
}

$userRow = mysqli_fetch_assoc($result);

if (!password_verify($pass, $userRow['user_password'])) {
    echo "<script>alert('Invalid credentials.'); window.location='index.html';</script>";
    exit();
}

// ── Authenticated ────────────────────────────────────────────────────────
session_regenerate_id(true);
$_SESSION['username'] = $userRow['username'];

loadUserAuthorization($conn, (int)$userRow['table_id']);

header("Location: ../dashboard/?login=success");
exit();
