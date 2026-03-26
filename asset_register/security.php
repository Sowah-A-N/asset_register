<?php
/**
 * security.php — shared security helpers
 * Include ONCE near the top of every entry-point PHP file, BEFORE session_start().
 *
 * Usage:
 *   require_once __DIR__ . '/../security.php';   // adjust path as needed
 *   // session_start() is called inside secure_session_start()
 *   secure_session_start();
 */

/**
 * Start a session with hardened cookie parameters.
 * Call this instead of session_start() throughout the application.
 */
function secure_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return; // already started
    }

    session_set_cookie_params([
        'lifetime' => 0,             // session cookie (expires on browser close)
        'path'     => '/',
        'domain'   => '',            // current domain only
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,          // inaccessible to JavaScript
        'samesite' => 'Strict',      // no cross-site requests
    ]);

    session_start();

    // Enforce an idle timeout (30 minutes)
    $idleLimit = 1800;
    if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity']) > $idleLimit) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['_last_activity'] = time();
}

/**
 * Require an authenticated session. Redirects to $loginPath if not logged in.
 *
 * @param string $loginPath  Relative or absolute URL of the login page.
 */
function require_auth(string $loginPath = '../login/'): void
{
    if (!isset($_SESSION['username'])) {
        header('Location: ' . $loginPath);
        exit;
    }
}

// ─── CSRF ────────────────────────────────────────────────────────────────────

/**
 * Return (and lazily create) the CSRF token for this session.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Render a hidden CSRF input field ready to embed in any HTML form.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate the CSRF token submitted with a POST request.
 * Dies with HTTP 403 on failure.
 */
function csrf_verify(): void
{
    $submitted = $_POST['_csrf_token'] ?? '';
    if (!hash_equals(csrf_token(), $submitted)) {
        http_response_code(403);
        die('Invalid or missing CSRF token. Please go back and try again.');
    }
}

// ─── Output escaping ─────────────────────────────────────────────────────────

/**
 * HTML-encode a value for safe output inside HTML content or attributes.
 *
 * @param mixed $value
 */
function esc($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
