<?php
// Returns a MySQLi connection for the requested database.
// Usage: $conn = get_db_connection();        // main DB
//        $dsu  = get_db_connection('dsu');   // legacy DSU DB
//
// Connections are cached per key for the lifetime of the request.

function get_db_connection($db = 'main') {
    static $pool = [];

    if (isset($pool[$db])) {
        return $pool[$db];
    }

    if ($db === 'dsu') {
        $host = defined('DSU_DB_HOST') ? DSU_DB_HOST : 'localhost';
        $name = defined('DSU_DB_NAME') ? DSU_DB_NAME : 'asset_register';
        $user = defined('DSU_DB_USER') ? DSU_DB_USER : 'root';
        $pass = defined('DSU_DB_PASS') ? DSU_DB_PASS : '';
    } else {
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $name = defined('DB_NAME') ? DB_NAME : 'asset_register_new';
        $user = defined('DB_USER') ? DB_USER : 'root';
        $pass = defined('DB_PASS') ? DB_PASS : '';
    }

    $conn = mysqli_connect($host, $user, $pass, $name);

    if (!$conn) {
        error_log('DB connection failed (' . $db . '): ' . mysqli_connect_error());
        die('A database error occurred. Please try again later.');
    }

    mysqli_set_charset($conn, 'utf8mb4');

    $pool[$db] = $conn;
    return $conn;
}
