<?php
// csrf.php — Double-submit CSRF protection.
// Token is stored in the session and compared on every non-GET request.

// ─────────────────────────────────────────────────────────────────────────────
// Returns the current CSRF token, generating one if it doesn't exist yet.
// ─────────────────────────────────────────────────────────────────────────────
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// ─────────────────────────────────────────────────────────────────────────────
// Returns a hidden input field containing the CSRF token.
// Usage: echo csrf_field();
// ─────────────────────────────────────────────────────────────────────────────
function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . esc(csrf_token()) . '">';
}

// ─────────────────────────────────────────────────────────────────────────────
// Verifies the submitted CSRF token.
// Terminates with 403 if the token is missing or invalid.
// ─────────────────────────────────────────────────────────────────────────────
function csrf_verify(): void {
    $submitted = $_POST['_csrf'] ?? '';
    $stored    = $_SESSION['csrf_token'] ?? '';

    if (!$stored || !$submitted || !hash_equals($stored, $submitted)) {
        http_response_code(403);
        // Clear the invalid token so the next form gets a fresh one
        unset($_SESSION['csrf_token']);
        header('Content-Type: text/plain');
        exit('Invalid or missing CSRF token. Please go back and try again.');
    }
}
