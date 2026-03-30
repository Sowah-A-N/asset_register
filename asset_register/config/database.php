<?php
/**
 * config/database.php — Central database connection factory.
 *
 * Provides get_db_connection(string $db = 'main'): mysqli
 *
 * Supported $db keys and the .env variables they read:
 *
 *  'main'     → DB_HOST / DB_NAME / DB_USER / DB_PASS        (asset_register_new)
 *  'dsu'      → DB_HOST / DB_NAME_DSU / DB_USER / DB_PASS    (asset_register legacy)
 *  'registry' → DB_HOST / DB_NAME_REG / DB_USER_REG / DB_PASS_REG (roommanagement)
 *
 * Connections are cached per $db key so each process only opens one socket
 * per database.
 */

require_once __DIR__ . '/env.php';

/**
 * Return a MySQLi connection for the requested database group.
 * Terminates with a generic message (and logs the real error) on failure.
 */
function get_db_connection(string $db = 'main'): mysqli
{
    static $pool = [];

    if (isset($pool[$db])) {
        return $pool[$db];
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';

    switch ($db) {
        case 'dsu':
            $name = getenv('DB_NAME_DSU') ?: 'asset_register';
            $user = getenv('DB_USER')     ?: '';
            $pass = getenv('DB_PASS')     ?: '';
            break;

        case 'registry':
            $name = getenv('DB_NAME_REG')  ?: 'roommanagement';
            $user = getenv('DB_USER_REG')  ?: '';
            $pass = getenv('DB_PASS_REG')  ?: '';
            break;

        default: // 'main'
            $name = getenv('DB_NAME') ?: 'asset_register_new';
            $user = getenv('DB_USER') ?: '';
            $pass = getenv('DB_PASS') ?: '';
            break;
    }

    $conn = new mysqli($host, $user, $pass, $name);

    if ($conn->connect_errno) {
        error_log(sprintf(
            'DB connection failed [%s]: (%d) %s',
            $db, $conn->connect_errno, $conn->connect_error
        ));
        http_response_code(503);
        die('Service temporarily unavailable. Please try again later.');
    }

    $conn->set_charset('utf8mb4');
    $pool[$db] = $conn;
    return $conn;
}
