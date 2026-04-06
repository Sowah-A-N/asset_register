<?php
// Typed, sanitised input helpers.

function get($key, $default = '') {
    $val = $_GET[$key] ?? $default;
    return is_string($val) ? trim($val) : $default;
}

function post($key, $default = '') {
    $val = $_POST[$key] ?? $default;
    return is_string($val) ? trim($val) : $default;
}

function get_int($key, $default = 0) {
    return isset($_GET[$key]) ? (int)$_GET[$key] : $default;
}

function post_int($key, $default = 0) {
    return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
}

function post_float($key, $default = 0.0) {
    return isset($_POST[$key]) ? (float)$_POST[$key] : $default;
}

// Output escaping — use on every echoed value
function esc($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
