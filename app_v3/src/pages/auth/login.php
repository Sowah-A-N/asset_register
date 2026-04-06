<?php
// Redirect to dashboard if already logged in
if (is_logged_in()) {
    header('Location: ' . rtrim(APP_URL, '/') . '/dashboard');
    exit;
}

$page_title = 'Login';
$content = function() {
    $base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
?>
<div class="card auth-card">
    <div class="card-header bg-primary text-white text-center py-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-building me-2"></i>Asset Register
        </h4>
        <small class="opacity-75">Sign in to your account</small>
    </div>
    <div class="card-body p-4">
        <form method="post" action="<?= $base_url ?>/" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="login">

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username"
                       class="form-control" autocomplete="username"
                       value="<?= esc($_GET['u'] ?? '') ?>" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control" autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
            </button>
        </form>
    </div>
    <div class="card-footer text-center bg-white border-0 pb-4">
        <a href="<?= $base_url ?>/forgot-password" class="text-muted small">
            Forgot your password?
        </a>
    </div>
</div>
<?php }; // end $content

require SRC . '/templates/layouts/base.php';
