<?php
// flash.php — One-request flash messages stored in the session.
// Usage:
//   set_flash('success', 'Asset saved.');
//   set_flash('error',   'Something went wrong.');
//
// Then in templates:
//   <?php render_flash(); ?>

// ─────────────────────────────────────────────────────────────────────────────
// Store a flash message for the next request.
// $type: 'success' | 'error' | 'warning' | 'info'
// ─────────────────────────────────────────────────────────────────────────────
function set_flash(string $type, string $message): void {
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

// ─────────────────────────────────────────────────────────────────────────────
// Retrieve and clear all flash messages.
// Returns array of ['type' => ..., 'message' => ...] or empty array.
// ─────────────────────────────────────────────────────────────────────────────
function get_flash(): array {
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $messages;
}

// ─────────────────────────────────────────────────────────────────────────────
// Output Bootstrap 5 alert HTML for all pending flash messages.
// Call once per page, typically at the top of the <main> area.
// ─────────────────────────────────────────────────────────────────────────────
function render_flash(): void {
    $messages = get_flash();
    if (!$messages) return;

    // Bootstrap 5 type mapping
    $map = [
        'success' => 'success',
        'error'   => 'danger',
        'warning' => 'warning',
        'info'    => 'info',
    ];

    foreach ($messages as $msg) {
        $bsType = $map[$msg['type']] ?? 'secondary';
        echo '<div class="alert alert-' . esc($bsType) . ' alert-dismissible fade show" role="alert">';
        echo esc($msg['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}
