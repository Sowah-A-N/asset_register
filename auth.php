<?php
declare(strict_types=1);

/**
 * Authentication & session middleware.
 * Replace the copy-pasted session block at the top of every protected page with:
 *
 *   require_once '../../auth.php';
 *   requireAuth('../login/');
 *
 * This file does NOT open a DB connection — include config.php separately if needed.
 */

// ── Session bootstrap ─────────────────────────────────────
function rmu_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,           // expire on browser close
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            // 'secure' => true,       // enable when HTTPS is deployed
        ]);
        session_start();
    }
}

// ── Guard ─────────────────────────────────────────────────
function requireAuth(string $loginPath = '../login/'): void {
    rmu_session();
    if (empty($_SESSION['username'])) {
        header('Location: ' . $loginPath);
        exit();
    }
    // Regenerate session ID periodically to prevent fixation
    if (empty($_SESSION['_last_regen']) || time() - $_SESSION['_last_regen'] > 900) {
        session_regenerate_id(true);
        $_SESSION['_last_regen'] = time();
    }
}

// ── Helpers ───────────────────────────────────────────────
function currentUser(): string {
    return $_SESSION['username'] ?? '';
}

/** Generate or retrieve the CSRF token for this session. */
function csrf_token(): string {
    rmu_session();
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/** Verify the CSRF token submitted with a POST form. */
function csrf_verify(): bool {
    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return hash_equals(csrf_token(), $token);
}

/**
 * HTML-escape a value for safe output.
 * Shorthand: e($row['name'])
 */
function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/* ══════════════════════════════════════════════════════════════
   RBAC — Role-Based Access Control (Phase A — inert until called)
   ══════════════════════════════════════════════════════════════
   These functions are defined but NOT yet wired into login or page
   guards. They are defensive: if the RBAC tables do not exist yet,
   loadUserAuthorization() sets empty roles/permissions and login
   still succeeds — so this code is safe to ship before the schema
   (dbs/rbac_schema.sql) is applied. Wiring happens in Phase B.
   ────────────────────────────────────────────────────────────── */

/**
 * Load a user's roles and permissions into the session.
 * Call ONCE at login, after credentials are verified.
 *
 * @param mysqli $conn   Active DB connection
 * @param int    $userId admin_logs.table_id of the authenticated user
 */
function loadUserAuthorization(mysqli $conn, int $userId): void {
    rmu_session();

    $_SESSION['user_id']     = $userId;
    $_SESSION['roles']       = [];
    $_SESSION['permissions'] = [];

    // Roles — fail silently if RBAC tables are absent (pre-migration safety)
    $stmt = @$conn->prepare(
        "SELECT DISTINCT r.role_key
         FROM user_roles ur
         JOIN roles r ON r.role_id = ur.role_id
         WHERE ur.user_id = ?"
    );
    if ($stmt === false) return;   // tables not created yet — stay backward compatible
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $_SESSION['roles'][] = $row['role_key'];
    }
    $stmt->close();

    // Permissions (aggregated across all the user's roles)
    $stmt = @$conn->prepare(
        "SELECT DISTINCT p.permission_key
         FROM user_roles ur
         JOIN role_permissions rp ON rp.role_id = ur.role_id
         JOIN permissions p       ON p.permission_id = rp.permission_id
         WHERE ur.user_id = ?"
    );
    if ($stmt === false) return;
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $_SESSION['permissions'][] = $row['permission_key'];
    }
    $stmt->close();
}

/** All role keys held by the current user. */
function userRoles(): array {
    return $_SESSION['roles'] ?? [];
}

/** True if the current user holds the given role. */
function hasRole(string $roleKey): bool {
    return in_array($roleKey, $_SESSION['roles'] ?? [], true);
}

/**
 * True if the current user has the given permission.
 * Use for conditional rendering: if (can('asset.dispose')) { ... }
 */
function can(string $permissionKey): bool {
    return in_array($permissionKey, $_SESSION['permissions'] ?? [], true);
}

/**
 * Page/action guard. Requires login AND the given permission.
 * Renders a 403 page and exits if the permission is missing.
 *
 * Phase B usage (top of a protected page):
 *   require_once '../../auth.php';
 *   requirePermission('asset.create');
 */
function requirePermission(string $permissionKey, string $loginPath = '../login/'): void {
    requireAuth($loginPath);
    if (can($permissionKey)) return;

    http_response_code(403);
    $user = currentUser();
    echo <<<HTML
<!doctype html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Access Denied — RMU Asset Register</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
       background:#f0f4f8;display:flex;align-items:center;justify-content:center;
       min-height:100vh;margin:0;color:#0f1728}
  .box{background:#fff;border-radius:14px;box-shadow:0 8px 24px rgba(15,23,40,.1);
       padding:3rem;max-width:440px;text-align:center}
  .ic{font-size:3.5rem;color:#dc2626;margin-bottom:1rem}
  h1{font-size:1.3rem;font-weight:700;margin:0 0 .5rem}
  p{color:#6b7280;font-size:.9rem;margin:0 0 1.5rem}
  .btn{display:inline-block;background:#2563eb;color:#fff;text-decoration:none;
       padding:.6rem 1.4rem;border-radius:8px;font-weight:600;font-size:.88rem}
  code{background:#f1f5f9;padding:.15rem .4rem;border-radius:4px;font-size:.82rem}
</style></head>
<body><div class="box">
  <div class="ic"><i class="bi bi-shield-lock"></i></div>
  <h1>Access Denied</h1>
  <p>Your account does not have permission to perform this action
     (<code>{$permissionKey}</code>).<br>If you believe this is an error,
     contact your system administrator.</p>
  <a class="btn" href="../dashboard/"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
</div></body></html>
HTML;
    exit();
}
