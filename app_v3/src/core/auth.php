<?php
// auth.php — Session-based authentication helpers.
// All functions are stateless; they read from $_SESSION.
// $conn is expected globally where DB queries are needed.

// ─────────────────────────────────────────────────────────────────────────────
// Require the user to be logged in.
// Redirects to /login if not authenticated.
// ─────────────────────────────────────────────────────────────────────────────
function require_auth() {
    if (!is_logged_in()) {
        $base = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
        header('Location: ' . $base . '/login');
        exit;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Require the user to hold one of the given roles.
// Redirects to /login if not authenticated; sends 403 if wrong role.
// ─────────────────────────────────────────────────────────────────────────────
function require_role(string ...$roles) {
    require_auth();
    $role = current_role();
    if (!in_array($role, $roles, true)) {
        http_response_code(403);
        require SRC . '/templates/partials/403.php';
        exit;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Returns true if there is a valid authenticated session.
// ─────────────────────────────────────────────────────────────────────────────
function is_logged_in(): bool {
    return isset($_SESSION['user_id'])
        && isset($_SESSION['user_role'])
        && !empty($_SESSION['user_id']);
}

// ─────────────────────────────────────────────────────────────────────────────
// Returns the current user ID (int) or 0 if not logged in.
// ─────────────────────────────────────────────────────────────────────────────
function current_user_id(): int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
}

// ─────────────────────────────────────────────────────────────────────────────
// Returns the current user's display name or empty string.
// ─────────────────────────────────────────────────────────────────────────────
function current_user(): string {
    return isset($_SESSION['username']) ? (string)$_SESSION['username'] : '';
}

// ─────────────────────────────────────────────────────────────────────────────
// Returns the current user's role slug or empty string.
// ─────────────────────────────────────────────────────────────────────────────
function current_role(): string {
    return isset($_SESSION['user_role']) ? (string)$_SESSION['user_role'] : '';
}

// ─────────────────────────────────────────────────────────────────────────────
// Login: verify username + password, populate session.
// Returns true on success, false on failure.
// ─────────────────────────────────────────────────────────────────────────────
function login_user($conn, string $username, string $password): bool {
    $stmt = mysqli_prepare($conn,
        'SELECT id, username, display_name, role, password_hash
         FROM users
         WHERE username = ? AND is_active = 1
         LIMIT 1');
    if (!$stmt) return false;

    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user) return false;
    if (!password_verify($password, $user['password_hash'])) return false;

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    $_SESSION['user_id']      = (int)$user['id'];
    $_SESSION['username']     = $user['display_name'] ?: $user['username'];
    $_SESSION['user_role']    = $user['role'];
    $_SESSION['_last_activity'] = time();

    return true;
}

// ─────────────────────────────────────────────────────────────────────────────
// Logout: destroy session cleanly.
// ─────────────────────────────────────────────────────────────────────────────
function logout_user(): void {
    session_unset();
    session_destroy();

    // Start a fresh session so subsequent set-cookie doesn't break
    session_start();
    session_regenerate_id(true);
}
