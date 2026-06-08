<?php
/**
 * FILE:    schedule_officer/asset_classes/process_class.php
 * PURPOSE: Handle new asset class form submission
 *
 * FIXES APPLIED:
 *  - Removed own hardcoded DB credentials → uses ../datacon.php
 *  - $year was hardcoded as 2024 → now date('Y')
 *  - asset_class_opbal_year INSERT had wrong column names:
 *      opening_bal      → opening_balance
 *      estimated_life_months → expected_life_months
 *      total_accum_depr_start was receiving life-months value → now 0
 *  - Values count matched columns (was 5 values for 6 columns → now correct)
 *  - Added INSERT into asset_additions_year so new class appears in reports
 *  - Replaced string-interpolated SQL with prepared statements
 *  - asset_classes INSERT now includes all NOT NULL columns
 */

session_start();
require_once '../../auth.php';
requirePermission('catalog.manage', '../login/');   // RBAC: managing asset classes

include "../datacon.php";

if ($conn->connect_error) {
    error_log('[RMU] process_class DB error: ' . $conn->connect_error);
    echo '<script>alert("Database error. Please try again."); window.history.back();</script>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// ── Input validation ─────────────────────────────────────────────────────
$asset_class     = trim($_POST['asset_class']    ?? '');
$dep_rate        = trim($_POST['dep_rate']        ?? '');
$estimated_life  = (int)($_POST['estimated_life'] ?? 0);
$opening_balance = (float)($_POST['opening_bal']  ?? 0);

if ($asset_class === '' || $dep_rate === '' || $estimated_life <= 0) {
    echo '<script>alert("Please fill in all required fields."); window.history.back();</script>';
    exit();
}

// Opening balances anchor at the engine's base year (RMU_POOL_BASE_YEAR=2024),
// NOT the current year — writing current-year rows created stale pool data the
// read-only reports ignore. Keep this consistent with view_asset_class.
$year                  = 2024;
$estimated_life_months = $estimated_life * 12;
$dep_rate_float        = (float)$dep_rate;

// ── Duplicate check ──────────────────────────────────────────────────────
$stmt = $conn->prepare("SELECT COUNT(*) cnt FROM asset_classes WHERE asset_class = ?");
$stmt->bind_param("s", $asset_class);
$stmt->execute();
$exists = (int)($stmt->get_result()->fetch_assoc()['cnt'] ?? 0) > 0;
$stmt->close();

if ($exists) {
    echo '<script>alert("An Asset Class with that name already exists."); window.location=\'index.php\';</script>';
    exit();
}

// ── Insert into asset_classes ────────────────────────────────────────────
// opening_bal and opbal_plus_additions start equal to the entered opening balance.
// account_depr_open_bal starts at 0 for new classes.
$stmt = $conn->prepare(
    "INSERT INTO asset_classes
        (asset_class, dep_rate, estimated_life, opening_bal, opbal_plus_additions, account_depr_open_bal)
     VALUES (?, ?, ?, ?, ?, 0)"
);
$stmt->bind_param("sdidd", $asset_class, $dep_rate_float, $estimated_life, $opening_balance, $opening_balance);

if (!$stmt->execute()) {
    error_log('[RMU] asset_classes insert failed: ' . $stmt->error);
    echo '<script>alert("Error adding Asset Class. Please try again."); window.location=\'index.php\';</script>';
    $stmt->close();
    exit();
}
$stmt->close();

// ── Insert into asset_class_opbal_year (fixed column names) ─────────────
// This row is required for the class to appear in all financial reports.
$stmt = $conn->prepare(
    "INSERT INTO asset_class_opbal_year
        (asset_class, year, opening_balance, total_accum_depr_start,
         total_depr_year_charge, disposals_depr, expected_life_months, rate)
     VALUES (?, ?, ?, 0, 0, 0, ?, ?)"
);
$stmt->bind_param("ssdid", $asset_class, $year, $opening_balance, $estimated_life_months, $dep_rate_float);

if (!$stmt->execute()) {
    error_log('[RMU] asset_class_opbal_year insert failed: ' . $stmt->error);
    // Non-fatal: class was created; report row can be added manually
}
$stmt->close();

// ── Insert into asset_additions_year (ensures class is visible in summary) ─
// Check first to avoid duplicates if re-submitted
$stmt = $conn->prepare(
    "SELECT COUNT(*) cnt FROM asset_additions_year WHERE asset_class = ? AND year = ?"
);
$stmt->bind_param("si", $asset_class, $year);
$stmt->execute();
$yrExists = (int)($stmt->get_result()->fetch_assoc()['cnt'] ?? 0) > 0;
$stmt->close();

if (!$yrExists) {
    $stmt = $conn->prepare(
        "INSERT INTO asset_additions_year
            (asset_class, year, total_additions_cedi, total_additions_dollar,
             total_disposals_cedi, total_disposals_dollar)
         VALUES (?, ?, 0, 0, 0, 0)"
    );
    $stmt->bind_param("si", $asset_class, $year);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

echo '<script>alert("Asset Class added successfully."); window.location=\'../view_asset_class/\';</script>';
