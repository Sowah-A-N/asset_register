<?php
/**
 * accountant/init.php — role bootstrap
 *
 * Include this at the top of every page in the accountant/ module:
 *
 *   require_once '../init.php';          // pages one level deep
 *   require_once '../../init.php';       // pages two levels deep
 *   // etc.
 *
 * Sets up: secure session, authentication, RBAC, $username, $conn.
 */

require_once __DIR__ . '/../security.php';
secure_session_start();
require_auth('../login/');
require_role('accountant');

$username = $_SESSION['username'];

// Database connection — uses config/database.php (reads from .env)
$_d = __DIR__;
while (!file_exists($_d . '/config/database.php') && $_d !== '/') {
    $_d = dirname($_d);
}
require_once $_d . '/config/database.php';
unset($_d);
$conn = get_db_connection();
