<?php
// Action: login — POST _action=login
// Verifies credentials and creates session.

csrf_verify();

$username = post('username');
$password = $_POST['password'] ?? '';   // raw — do NOT trim passwords

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

if ($username === '' || $password === '') {
    set_flash('error', 'Username and password are required.');
    header('Location: ' . $base_url . '/login');
    exit;
}

if (login_user($conn, $username, $password)) {
    audit_log($conn, 'auth.login', 'users', current_user_id(), 'Login successful');
    header('Location: ' . $base_url . '/dashboard');
    exit;
}

// Generic failure — don't reveal whether username or password was wrong
set_flash('error', 'Invalid username or password. Please try again.');
header('Location: ' . $base_url . '/login');
exit;
