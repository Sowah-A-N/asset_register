<?php
// audit.php — Immutable audit trail.
// Writes to audit_log every time a write action occurs.
// Called from action files after a successful DB mutation.

// ─────────────────────────────────────────────────────────────────────────────
// Record one audit event.
//
// @param $conn      mysqli connection
// @param string $action      Short action label, e.g. 'asset.create'
// @param string $table_name  Table affected, e.g. 'assets'
// @param int|null $record_id Primary key of the affected row
// @param string $detail      Human-readable summary of what changed (optional)
// ─────────────────────────────────────────────────────────────────────────────
function audit_log($conn, string $action, string $table_name = '', ?int $record_id = null, string $detail = ''): void {
    $user_id  = current_user_id();
    $username = current_user();
    $role     = current_role();
    $ip       = _get_client_ip();

    $stmt = mysqli_prepare($conn,
        'INSERT INTO audit_log (user_id, username, role, action, table_name, record_id, detail, ip_address)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

    if (!$stmt) {
        // Non-fatal: log to error_log but don't crash the request
        error_log('audit_log: mysqli_prepare failed — ' . mysqli_error($conn));
        return;
    }

    $user_id_val = $user_id ?: null;
    mysqli_stmt_bind_param($stmt, 'issssiis',
        $user_id_val,
        $username,
        $role,
        $action,
        $table_name,
        $record_id,
        $detail,
        $ip
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// ─────────────────────────────────────────────────────────────────────────────
// Return the best available client IP address.
// Considers X-Forwarded-For for proxied environments.
// ─────────────────────────────────────────────────────────────────────────────
function _get_client_ip(): string {
    // Only trust X-Forwarded-For if explicitly configured for reverse-proxy use
    if (defined('TRUST_PROXY') && TRUST_PROXY === 'true') {
        $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if ($forwarded !== '') {
            // X-Forwarded-For can be a comma-separated list; take the first
            $ips = explode(',', $forwarded, 2);
            return trim($ips[0]);
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '';
}
