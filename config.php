<?php
declare(strict_types=1);

/**
 * Central application configuration.
 * Include this instead of any role-specific datacon.php file.
 * Provides $conn for backward compatibility.
 */

// ── Environment ────────────────────────────────────────────
if (!defined('APP_ENV')) define('APP_ENV', 'development'); // change to 'production' on server

// ── Application identity ───────────────────────────────────
define('APP_NAME',    'RMU Asset Register');
define('APP_VERSION', '2.0.0');
define('APP_ORG',     'Regional Maritime University');
define('BASE_URL',    '/asset_register');

// ── Database ───────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'asset_register_new');

// ── Timezone ───────────────────────────────────────────────
date_default_timezone_set('Africa/Accra');

// ── Error handling ─────────────────────────────────────────
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors',     '1');
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    ini_set('error_log', $logDir . '/php_errors.log');
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// ── Database connection ────────────────────────────────────
function rmu_db(
    string $host = DB_HOST,
    string $user = DB_USER,
    string $pass = DB_PASS,
    string $name = DB_NAME
): mysqli {
    $c = mysqli_connect($host, $user, $pass, $name);
    if (!$c) {
        error_log('[RMU] DB connect failed: ' . mysqli_connect_error());
        http_response_code(503);
        exit('Database unavailable. Please try again shortly.');
    }
    mysqli_set_charset($c, 'utf8mb4');
    return $c;
}

// Backward-compat: every page that did include "datacon.php" gets $conn
$conn = rmu_db();
