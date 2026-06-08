<?php
/**
 * FILE:    schedule_officer/login/login.inc.php
 * PURPOSE: Authenticate a user against admin_logs and start their session.
 *
 * PHASE B CHANGES:
 *  - Now loads RBAC roles + permissions into the session via
 *    loadUserAuthorization() (defensive — safe if RBAC tables absent)
 *  - session_regenerate_id(true) on success (session-fixation hardening)
 *  - FIXED: the else-branch previously redirected ALL roles to 'SIA/login/'
 *    → now returns to this role's own login form
 *  - $_SESSION['username'] still set, so every existing page keeps working
 */

session_start();
require_once "../../auth.php";   // RBAC helpers
include "../datacon.php";        // $conn (asset_register_new)

if (!isset($_POST['Login'])) {
    // Direct access without submitting the form
    header("Location: index.html");
    exit();
}

if (!$conn) {
    error_log('[RMU] login DB connection failed');
    echo "<script>alert('Service temporarily unavailable. Please try again.'); window.location='index.html';</script>";
    exit();
}

$username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
$pass     = $_POST['pass'] ?? '';

if (empty($username) || empty($pass)) {
    echo "<script>alert('Please enter your username and password.'); window.location='index.html';</script>";
    exit();
}

// Fetch the account (single query — table_id needed for RBAC lookup)
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
session_regenerate_id(true);             // prevent session fixation
$_SESSION['username'] = $userRow['username'];

// Load roles + permissions for this user into the session (Phase B)
loadUserAuthorization($conn, (int)$userRow['table_id']);

header("Location: ../dashboard/?login=success");
exit();
