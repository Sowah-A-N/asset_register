<?php
/**
 * security.php — shared security helpers (V2)
 *
 * Include once near the top of every entry-point PHP file:
 *
 *   require_once __DIR__ . '/../security.php';   // adjust depth as needed
 *   secure_session_start();
 *   require_auth('../login/');
 *   require_role('schedule_officer');             // optional per-page RBAC
 */

// ── Session ───────────────────────────────────────────────────────────────────

/**
 * Start a session with hardened cookie parameters and idle-timeout enforcement.
 * Call this instead of session_start() everywhere.
 */
function secure_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_start();

    // 30-minute idle timeout
    $idleLimit = 1800;
    if (isset($_SESSION['_last_activity'])
        && (time() - $_SESSION['_last_activity']) > $idleLimit) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['_last_activity'] = time();
}

// ── Authentication ────────────────────────────────────────────────────────────

/**
 * Redirect to $loginPath and exit if no authenticated session exists.
 */
function require_auth(string $loginPath = '../login/'): void
{
    if (empty($_SESSION['username'])) {
        header('Location: ' . $loginPath);
        exit;
    }
}

/**
 * Enforce that the logged-in user has exactly $requiredRole.
 * Sends HTTP 403 and exits on mismatch. Call after require_auth().
 *
 * Roles in use: schedule_officer | hod_ict | accountant | budget_officer |
 *               director_finance | sia | dsu | registry
 */
function require_role(string $requiredRole): void
{
    $actual = $_SESSION['role'] ?? '';
    if ($actual !== $requiredRole) {
        http_response_code(403);
        // Show a minimal page rather than a raw string so the layout stays intact
        include_once __DIR__ . '/partials/403.php';
        exit;
    }
}

// ── CSRF ──────────────────────────────────────────────────────────────────────

/**
 * Return (and lazily create) the session CSRF token.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Render a hidden CSRF input field for embedding in HTML forms.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
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

// ── Flash messages ────────────────────────────────────────────────────────────

/**
 * Queue a flash message for the next page load.
 *
 * @param string $type    Bootstrap alert type: success | danger | warning | info
 * @param string $message Plain-text message (will be HTML-escaped on output).
 */
function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * Retrieve and clear all queued flash messages.
 *
 * @return array<int, array{type: string, message: string}>
 */
function get_flash_messages(): array
{
    $msgs = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $msgs;
}

// ── Audit logging ─────────────────────────────────────────────────────────────

/**
 * Write one row to the audit_log table.
 *
 * Requires a $conn (mysqli) in scope. Safe to call even if the table does not
 * yet exist — errors are logged server-side and silently swallowed so they
 * never break a page.
 *
 * @param mysqli $conn
 * @param string $action    Verb: 'create_asset' | 'move_asset' | 'archive_asset' |
 *                          'dispose_asset' | 'login' | 'logout' | etc.
 * @param int|null  $assetId  The affected asset_id (null for non-asset actions).
 * @param string|null $detail  Optional free-text detail / JSON snapshot.
 */
function audit_log(mysqli $conn, string $action, ?int $assetId = null, ?string $detail = null): void
{
    $username  = $_SESSION['username'] ?? 'anonymous';
    $role      = $_SESSION['role']     ?? '';
    $ip        = $_SERVER['REMOTE_ADDR'] ?? '';

    $stmt = @mysqli_prepare(
        $conn,
        "INSERT INTO audit_log (username, role, action, asset_id, detail, ip_address, created_at)
         VALUES (?, ?, ?, ?, ?, ?, NOW())"
    );
    if (!$stmt) {
        error_log('audit_log: prepare failed — ' . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, 'sssiss', $username, $role, $action, $assetId, $detail, $ip);
    if (!mysqli_stmt_execute($stmt)) {
        error_log('audit_log: execute failed — ' . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
}

// ── Output escaping ───────────────────────────────────────────────────────────

/**
 * HTML-encode a value for safe output inside HTML content or attributes.
 */
function esc($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
