<?php
/**
 * FILE: /login/index.php — Shared sign-in for ALL roles (Phase C).
 *
 * One role-neutral login: authenticates against admin_logs, loads the user's
 * RBAC roles + permissions, then routes by role:
 *   schedule_officer → the full asset-management app
 *   dsu             → the DSU dashboard
 *   everyone else   → the role-aware /portal/ landing
 *
 * This replaces the per-role login folders (the oversight roles' old logins
 * were in the quarantined accommodation folders).
 */
require_once '../auth.php';
require_once '../config.php';   // provides $conn
rmu_session();

// Already signed in? route straight through.
if (!empty($_SESSION['username'])) {
    header('Location: ' . rmu_landing_for(userRoles()));
    exit();
}

$error = '';
if (isset($_POST['login'])) {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u === '' || $p === '') {
        $error = 'Please enter your username and password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT table_id, username, user_password FROM admin_logs WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $u);
        mysqli_stmt_execute($stmt);
        $row = mysqli_stmt_get_result($stmt)->fetch_assoc();
        mysqli_stmt_close($stmt);
        if (!$row || !password_verify($p, $row['user_password'])) {
            $error = 'Invalid credentials.';
        } else {
            session_regenerate_id(true);
            $_SESSION['username'] = $row['username'];
            loadUserAuthorization($conn, (int)$row['table_id']);
            header('Location: ' . rmu_landing_for(userRoles()));
            exit();
        }
    }
}

/** Decide where a user lands after sign-in, based on their roles. */
function rmu_landing_for(array $roles): string {
    if (in_array('schedule_officer', $roles, true)) return '../asset_app/dashboard/';
    // Everyone else — DSU, auditor, accountant, budget officer, director of
    // finance, system admin — uses the shared, permission-aware Portal.
    return '../portal/';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/v2.css">
  <link rel="shortcut icon" href="../assets/img/rmulog.png">
  <style>
    body{background:linear-gradient(135deg,#1e3a5f 0%,#0f1728 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem}
    .login-card{background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.35);width:100%;max-width:400px;overflow:hidden}
    .login-head{padding:2rem 2rem 1rem;text-align:center}
    .login-logo{width:64px;height:64px;margin:0 auto .75rem;display:block}
    .login-body{padding:0 2rem 2rem}
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-head">
      <img src="../assets/img/rmu.png" alt="RMU" class="login-logo">
      <h1 style="font-size:1.15rem;font-weight:800;color:var(--text-primary);margin:0">RMU Asset Register</h1>
      <p class="text-muted" style="font-size:.82rem;margin:.25rem 0 0">Sign in to continue</p>
    </div>
    <div class="login-body">
      <?php if ($error): ?>
        <div class="alert alert-danger py-2" style="font-size:.85rem"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="POST" action="">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="username" class="form-control" required autofocus
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">
          <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
        </button>
      </form>
    </div>
  </div>
</body>
</html>
