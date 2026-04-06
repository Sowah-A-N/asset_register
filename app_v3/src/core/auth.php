<?php
// Stub — Phase 3 will flesh these out fully.
// Defined here so bootstrap.php and the router don't error during Phase 1.

function require_auth() {
    // placeholder — full implementation in Phase 3
}

function require_role(...$roles) {
    // placeholder — full implementation in Phase 3
}

function current_user() {
    return $_SESSION['username'] ?? null;
}

function current_role() {
    return $_SESSION['role'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['username']);
}
