<?php
// Bootstrap — loaded by public/index.php on every request.
// Order matters: env → session → DB → security helpers.

define('ROOT',    dirname(__DIR__));
define('SRC',     ROOT . '/src');
define('STORAGE', ROOT . '/storage');

// 1. Load environment variables
require_once SRC . '/config/env.php';

// 2. Error reporting
if (defined('APP_DEBUG') && APP_DEBUG === 'true') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// 3. Session (secure settings — started before any output)
$session_name     = defined('SESSION_NAME')    ? SESSION_NAME    : 'ar_v3_session';
$session_lifetime = defined('SESSION_LIFETIME') ? (int)SESSION_LIFETIME : 1800;

ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', $session_lifetime);

session_name($session_name);
session_start();

// Enforce idle timeout
if (isset($_SESSION['_last_activity'])) {
    if (time() - $_SESSION['_last_activity'] > $session_lifetime) {
        session_unset();
        session_destroy();
        session_start();
    }
}
$_SESSION['_last_activity'] = time();

// 4. DB connection factory
require_once SRC . '/config/database.php';

// Open main connection — available as $conn throughout the app
$conn = get_db_connection('main');

// 5. Security helpers (auth, csrf, audit, request, flash)
require_once SRC . '/core/auth.php';
require_once SRC . '/core/csrf.php';
require_once SRC . '/core/request.php';
require_once SRC . '/core/audit.php';
require_once SRC . '/core/flash.php';

// 6. Role config
require_once SRC . '/config/roles.php';
