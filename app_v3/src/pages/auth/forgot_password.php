<?php
if (is_logged_in()) {
    header('Location: ' . rtrim(APP_URL, '/') . '/dashboard');
    exit;
}

$page_title = 'Forgot Password';
$content = function() {
    $base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
?>
<div class="card auth-card">
    <div class="card-header bg-primary text-white text-center py-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-building me-2"></i>Asset Register
        </h4>
        <small class="opacity-75">Reset your password</small>
    </div>
    <div class="card-body p-4">
        <p class="text-muted small mb-3">
            Enter your username. If a matching account is found, a reset link
            will be sent to the administrator to relay to you.
        </p>
        <form method="post" action="<?= $base_url ?>/" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="forgot_password">

            <div class="mb-4">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username"
                       class="form-control" autocomplete="username"
                       required autofocus>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-envelope me-1"></i>Request Reset
            </button>
        </form>
    </div>
    <div class="card-footer text-center bg-white border-0 pb-4">
        <a href="<?= $base_url ?>/login" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Back to login
        </a>
    </div>
</div>
<?php };

require SRC . '/templates/layouts/base.php';
