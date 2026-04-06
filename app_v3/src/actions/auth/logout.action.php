<?php
// Action: logout — POST _action=logout
csrf_verify();

$user_id = current_user_id();
$username = current_user();
audit_log($conn, 'auth.logout', 'users', $user_id, 'Logout');

logout_user();

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
header('Location: ' . $base_url . '/login');
exit;
