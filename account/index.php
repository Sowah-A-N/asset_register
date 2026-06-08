<?php
/**
 * FILE: /account/index.php — Self-service "Change my password".
 *
 * Any signed-in user can change their OWN password here (no admin needed).
 * CSRF-protected; verifies the current password before allowing the change.
 */
require_once '../auth.php';
requireAuth('../login/');
require_once '../config.php';   // $conn

$uid      = (int)($_SESSION['user_id'] ?? 0);
$username = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$initial  = mb_strtoupper(mb_substr($username, 0, 1));
$csrf     = csrf_token();
$msg = null; $err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $err = 'Security check failed. Please try again.';
    } else {
        $cur = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $cnf = $_POST['confirm_password'] ?? '';

        if ($uid <= 0) {
            $err = 'Your session is missing an account id — sign out and back in, then retry.';
        } elseif (strlen($new) < 8) {
            $err = 'New password must be at least 8 characters.';
        } elseif ($new !== $cnf) {
            $err = 'New password and confirmation do not match.';
        } else {
            $stmt = mysqli_prepare($conn, "SELECT user_password FROM admin_logs WHERE table_id = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "i", $uid);
            mysqli_stmt_execute($stmt);
            $hash = mysqli_stmt_get_result($stmt)->fetch_assoc()['user_password'] ?? '';
            mysqli_stmt_close($stmt);

            if (!$hash || !password_verify($cur, $hash)) {
                $err = 'Your current password is incorrect.';
            } elseif (password_verify($new, $hash)) {
                $err = 'The new password must be different from your current one.';
            } else {
                $newHash = password_hash($new, PASSWORD_DEFAULT);
                $u = mysqli_prepare($conn, "UPDATE admin_logs SET user_password = ? WHERE table_id = ?");
                mysqli_stmt_bind_param($u, "si", $newHash, $uid);
                $msg = mysqli_stmt_execute($u) ? 'Your password has been changed.' : 'Could not update your password. Please try again.';
                if ($msg !== 'Your password has been changed.') { $err = $msg; $msg = null; }
                mysqli_stmt_close($u);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/v2.css">
  <link rel="shortcut icon" href="../assets/img/rmu.png">
</head>
<body class="bg-page">
  <header class="report-topbar">
    <img src="../assets/img/rmu.png" alt="RMU" style="height:34px;width:34px;object-fit:contain;background:#fff;border-radius:6px">
    <div class="topnav-breadcrumb" style="flex:1">
      <h1 style="font-size:1rem;font-weight:700;margin:0">Change Password</h1>
      <small class="text-muted">Signed in as <?= $username ?></small>
    </div>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </header>

  <main class="rmu-content-full" style="max-width:480px;margin:0 auto">
    <?php if ($msg): ?>
      <div class="alert alert-success py-2" style="font-size:.85rem"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($msg) ?></div>
    <?php elseif ($err): ?>
      <div class="alert alert-danger py-2" style="font-size:.85rem"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <div class="rmu-card">
      <div class="rmu-card-header"><div class="rmu-card-title">Update your password</div></div>
      <div class="rmu-card-body">
        <form method="POST" autocomplete="off">
          <input type="hidden" name="_csrf" value="<?= $csrf ?>">
          <div class="mb-3">
            <label class="form-label">Current password</label>
            <input type="password" class="form-control" name="current_password" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">New password</label>
            <input type="password" class="form-control" name="new_password" minlength="8" required>
            <div class="form-text">At least 8 characters, and different from your current one.</div>
          </div>
          <div class="mb-4">
            <label class="form-label">Confirm new password</label>
            <input type="password" class="form-control" name="confirm_password" minlength="8" required>
          </div>
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-shield-lock me-1"></i>Change Password</button>
        </form>
      </div>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
