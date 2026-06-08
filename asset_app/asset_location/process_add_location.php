<?php
/**
 * FILE:    schedule_officer/asset_location/process_add_location.php
 * PURPOSE: Insert a new asset location.
 *
 * FIXES APPLIED:
 *  - RBAC: was completely UNGUARDED → requirePermission('catalog.manage')
 *  - SQL injection: $_POST['location'] was interpolated raw → prepared statement
 *  - No input validation → rejects empty / duplicate locations
 *  - Redirected to ../dashboard/ → now returns to the locations view
 */

require_once '../../auth.php';
requirePermission('catalog.manage', '../login/');

include "../datacon.php";

if ($conn->connect_error) {
    error_log('[RMU] process_add_location DB error: ' . $conn->connect_error);
    echo '<script>alert("Database error. Please try again."); window.history.back();</script>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$location = trim($_POST['location'] ?? '');

if ($location === '') {
    echo '<script>alert("Please enter a location name."); window.history.back();</script>';
    exit();
}

// Duplicate check (prepared)
$stmt = $conn->prepare("SELECT COUNT(*) AS c FROM asset_location WHERE location = ?");
$stmt->bind_param("s", $location);
$stmt->execute();
$exists = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0) > 0;
$stmt->close();

if ($exists) {
    echo '<script>alert("That location already exists."); window.location="../view_asset_location/";</script>';
    exit();
}

// Insert (prepared — no injection)
$stmt = $conn->prepare("INSERT INTO asset_location (location) VALUES (?)");
$stmt->bind_param("s", $location);

if ($stmt->execute()) {
    echo '<script>alert("Location added successfully."); window.location="../view_asset_location/";</script>';
} else {
    error_log('[RMU] Location insert failed: ' . $stmt->error);
    echo '<script>alert("Error adding location. Please try again."); window.history.back();</script>';
}
$stmt->close();
$conn->close();
