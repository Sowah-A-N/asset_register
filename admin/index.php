<?php
/**
 * FILE: /admin/index.php — User & Role Management (System Admin only).
 *
 * Lets a system_admin: create accounts, assign/clear RBAC roles per account,
 * and remove accounts. Editing the role→permission matrix is read-only here
 * (a deliberate guardrail). CSRF-protected; self-lockout guards in place.
 */
require_once '../auth.php';
requirePermission('user.manage', '../login/');
require_once '../config.php';   // $conn

$username = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$initial  = mb_strtoupper(mb_substr($username, 0, 1));
$myId     = (int)($_SESSION['user_id'] ?? 0);
$csrf     = csrf_token();

/* ── POST actions ──────────────────────────────────────────── */
function redirect_msg(string $type, string $msg): never {
    $u = '?msg=' . urlencode($msg) . '&t=' . $type;
    echo "<script>window.location='index.php$u';</script>"; exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) redirect_msg('err', 'Security check failed. Please retry.');

    // Create account
    if (isset($_POST['add_user'])) {
        $u = trim($_POST['new_username'] ?? '');
        $p = $_POST['new_password'] ?? '';
        $roleIds = array_map('intval', $_POST['roles'] ?? []);
        if ($u === '' || strlen($p) < 6) redirect_msg('err', 'Username required and password must be at least 6 characters.');
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) c FROM admin_logs WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $u); mysqli_stmt_execute($stmt);
        if ((int)(mysqli_stmt_get_result($stmt)->fetch_assoc()['c'] ?? 0) > 0) redirect_msg('err', 'A user with that username already exists.');
        mysqli_stmt_close($stmt);
        // legacy user_role column: first selected role's key (display only)
        $legacy = 'user';
        if ($roleIds) {
            $rk = mysqli_query($conn, "SELECT role_key FROM roles WHERE role_id=" . (int)$roleIds[0] . " LIMIT 1");
            $legacy = mysqli_fetch_assoc($rk)['role_key'] ?? 'user';
        }
        $hash = password_hash($p, PASSWORD_DEFAULT);
        mysqli_begin_transaction($conn); $ok = true;
        $s = mysqli_prepare($conn, "INSERT INTO admin_logs (username, user_role, user_password) VALUES (?,?,?)");
        mysqli_stmt_bind_param($s, "sss", $u, $legacy, $hash);
        $ok = mysqli_stmt_execute($s); $newId = mysqli_insert_id($conn); mysqli_stmt_close($s);
        if ($ok && $roleIds) {
            $s = mysqli_prepare($conn, "INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?,?)");
            foreach ($roleIds as $rid) { mysqli_stmt_bind_param($s, "ii", $newId, $rid); $ok = $ok && mysqli_stmt_execute($s); }
            mysqli_stmt_close($s);
        }
        $ok ? mysqli_commit($conn) : mysqli_rollback($conn);
        mysqli_autocommit($conn, true);
        redirect_msg($ok ? 'ok' : 'err', $ok ? "User '$u' created." : 'Could not create user.');
    }

    // Update a user's roles
    if (isset($_POST['save_roles'])) {
        $uid = (int)($_POST['user_id'] ?? 0);
        if ($uid === $myId) redirect_msg('err', "You can't change your own roles (sign in as another admin to do that).");
        $roleIds = array_map('intval', $_POST['roles'] ?? []);
        mysqli_begin_transaction($conn); $ok = true;
        $s = mysqli_prepare($conn, "DELETE FROM user_roles WHERE user_id = ?");
        mysqli_stmt_bind_param($s, "i", $uid); $ok = mysqli_stmt_execute($s); mysqli_stmt_close($s);
        if ($ok && $roleIds) {
            $s = mysqli_prepare($conn, "INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?,?)");
            foreach ($roleIds as $rid) { mysqli_stmt_bind_param($s, "ii", $uid, $rid); $ok = $ok && mysqli_stmt_execute($s); }
            mysqli_stmt_close($s);
        }
        $ok ? mysqli_commit($conn) : mysqli_rollback($conn);
        mysqli_autocommit($conn, true);
        redirect_msg($ok ? 'ok' : 'err', $ok ? 'Roles updated.' : 'Could not update roles.');
    }

    // Remove account
    if (isset($_POST['delete_user'])) {
        $uid = (int)($_POST['user_id'] ?? 0);
        if ($uid === $myId) redirect_msg('err', "You can't remove your own account.");
        // prevent removing the last system_admin
        $admins = (int)(mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT COUNT(DISTINCT ur.user_id) c FROM user_roles ur JOIN roles r ON r.role_id=ur.role_id WHERE r.role_key='system_admin'"))['c'] ?? 0);
        $isAdmin = (int)(mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT COUNT(*) c FROM user_roles ur JOIN roles r ON r.role_id=ur.role_id WHERE r.role_key='system_admin' AND ur.user_id=$uid"))['c'] ?? 0) > 0;
        if ($isAdmin && $admins <= 1) redirect_msg('err', 'Cannot remove the last System Administrator.');
        mysqli_begin_transaction($conn); $ok = true;
        $s = mysqli_prepare($conn, "DELETE FROM user_roles WHERE user_id = ?"); mysqli_stmt_bind_param($s, "i", $uid); $ok = mysqli_stmt_execute($s); mysqli_stmt_close($s);
        if ($ok) { $s = mysqli_prepare($conn, "DELETE FROM admin_logs WHERE table_id = ?"); mysqli_stmt_bind_param($s, "i", $uid); $ok = mysqli_stmt_execute($s); mysqli_stmt_close($s); }
        $ok ? mysqli_commit($conn) : mysqli_rollback($conn);
        mysqli_autocommit($conn, true);
        redirect_msg($ok ? 'ok' : 'err', $ok ? 'Account removed.' : 'Could not remove account.');
    }
}

/* ── Data ──────────────────────────────────────────────────── */
$roles = [];
$rr = mysqli_query($conn, "SELECT role_id, role_key, role_name FROM roles ORDER BY role_name ASC");
while ($r = mysqli_fetch_assoc($rr)) $roles[(int)$r['role_id']] = $r;

// permissions per role (for the reference panel)
$rolePerms = [];
$pr = mysqli_query($conn,
    "SELECT rp.role_id, p.permission_key FROM role_permissions rp JOIN permissions p ON p.permission_id=rp.permission_id ORDER BY p.module, p.permission_key");
while ($r = mysqli_fetch_assoc($pr)) $rolePerms[(int)$r['role_id']][] = $r['permission_key'];

// users with their role ids + names
$users = [];
$ur = mysqli_query($conn,
    "SELECT al.table_id, al.username,
            COALESCE(GROUP_CONCAT(DISTINCT ur.role_id),'') role_ids,
            COALESCE(GROUP_CONCAT(DISTINCT r.role_name ORDER BY r.role_name SEPARATOR ', '),'') role_names
     FROM admin_logs al
     LEFT JOIN user_roles ur ON ur.user_id = al.table_id
     LEFT JOIN roles r ON r.role_id = ur.role_id
     GROUP BY al.table_id ORDER BY al.username ASC");
while ($r = mysqli_fetch_assoc($ur)) $users[] = $r;

$flash = isset($_GET['msg']) ? ['t'=>($_GET['t'] ?? 'ok'), 'm'=>$_GET['msg']] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User & Role Management — RMU Asset Register</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/v2.css">
  <link rel="shortcut icon" href="../assets/img/rmulog.png">
</head>
<body class="bg-page">
  <header class="rmu-topnav" style="margin:0">
    <a href="../portal/" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Portal</a>
    <div class="topnav-breadcrumb" style="flex:1"><h1>User &amp; Role Management</h1><small><?= count($users) ?> accounts · <?= count($roles) ?> roles</small></div>
    <div class="topnav-actions">
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-person-plus me-1"></i>Add User</button>
      <div class="topnav-divider"></div>
      <div class="dropdown">
        <button class="topnav-avatar dropdown-toggle" data-bs-toggle="dropdown">
          <div class="av-circle"><?= $initial ?></div>
          <div class="av-info"><div class="av-name"><?= $username ?></div><div class="av-role">System Administrator</div></div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="../portal/"><i class="bi bi-grid-1x2 me-2"></i>Portal</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="../logout/"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
        </ul>
      </div>
    </div>
  </header>

  <main class="rmu-content" style="max-width:1100px;margin:0 auto">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['t']==='ok'?'success':'danger' ?> py-2" style="font-size:.85rem">
        <i class="bi bi-<?= $flash['t']==='ok'?'check-circle':'exclamation-circle' ?> me-1"></i><?= htmlspecialchars($flash['m']) ?>
      </div>
    <?php endif; ?>

    <!-- Users -->
    <div class="rmu-card section-gap">
      <div class="rmu-card-header"><div class="rmu-card-title">Accounts</div></div>
      <div class="rmu-card-body" style="padding:0">
        <table class="rmu-table" style="box-shadow:none;border:none">
          <thead><tr><th>S/N</th><th>Username</th><th>Assigned Roles</th><th style="text-align:right">Actions</th></tr></thead>
          <tbody>
            <?php $sn=1; foreach ($users as $u): $self = ((int)$u['table_id'] === $myId); ?>
              <tr>
                <td><?= $sn++ ?></td>
                <td><?= htmlspecialchars($u['username']) ?><?= $self ? ' <span class="status-pill status-active ms-1">you</span>' : '' ?></td>
                <td><?= $u['role_names'] !== '' ? htmlspecialchars($u['role_names']) : '<span class="text-muted">— none —</span>' ?></td>
                <td style="text-align:right;white-space:nowrap">
                  <?php if ($self): ?>
                    <span class="text-muted" style="font-size:.78rem">protected</span>
                  <?php else: ?>
                    <button class="btn btn-sm btn-outline-primary btn-roles"
                            data-id="<?= (int)$u['table_id'] ?>" data-name="<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>"
                            data-roles="<?= htmlspecialchars($u['role_ids'], ENT_QUOTES) ?>"><i class="bi bi-person-gear me-1"></i>Roles</button>
                    <form method="POST" action="index.php" style="display:inline" onsubmit="return confirm('Remove account &quot;<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>&quot;? This cannot be undone.');">
                      <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                      <input type="hidden" name="user_id" value="<?= (int)$u['table_id'] ?>">
                      <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Roles reference (read-only) -->
    <div class="rmu-card">
      <div class="rmu-card-header"><div class="rmu-card-title">Roles &amp; Permissions</div>
        <small class="text-muted">Reference — what each role can do</small></div>
      <div class="rmu-card-body">
        <div class="row g-3">
          <?php foreach ($roles as $rid => $r): ?>
            <div class="col-md-6 col-lg-4">
              <div style="border:1px solid var(--card-border);border-radius:10px;padding:.9rem 1rem;height:100%">
                <div style="font-weight:700;font-size:.88rem"><?= htmlspecialchars($r['role_name']) ?></div>
                <div class="text-muted" style="font-size:.7rem;margin-bottom:.5rem"><?= count($rolePerms[$rid] ?? []) ?> permissions</div>
                <?php foreach (($rolePerms[$rid] ?? []) as $pk): ?>
                  <span class="status-pill status-archived" style="font-size:.66rem;margin:1px"><?= htmlspecialchars($pk) ?></span>
                <?php endforeach; ?>
                <?php if (empty($rolePerms[$rid])): ?><span class="text-muted" style="font-size:.75rem">no permissions</span><?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </main>

  <!-- Add User modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
      <form method="POST" action="index.php">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <div class="modal-header"><h5 class="modal-title">Add User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Username</label>
            <input type="text" class="form-control" name="new_username" required></div>
          <div class="mb-3"><label class="form-label">Password</label>
            <input type="password" class="form-control" name="new_password" minlength="6" required>
            <div class="form-text">Minimum 6 characters. The user can change it later.</div></div>
          <label class="form-label">Roles</label>
          <div class="row g-2">
            <?php foreach ($roles as $rid => $r): ?>
              <div class="col-6"><div class="form-check">
                <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $rid ?>" id="ar<?= $rid ?>">
                <label class="form-check-label" for="ar<?= $rid ?>" style="font-size:.85rem"><?= htmlspecialchars($r['role_name']) ?></label>
              </div></div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_user" class="btn btn-primary btn-sm">Create User</button></div>
      </form>
    </div></div>
  </div>

  <!-- Manage Roles modal -->
  <div class="modal fade" id="rolesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
      <form method="POST" action="index.php">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="user_id" id="rmUserId">
        <div class="modal-header"><h5 class="modal-title">Manage Roles — <span id="rmName"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="row g-2">
            <?php foreach ($roles as $rid => $r): ?>
              <div class="col-6"><div class="form-check">
                <input class="form-check-input rm-role" type="checkbox" name="roles[]" value="<?= $rid ?>" id="rr<?= $rid ?>">
                <label class="form-check-label" for="rr<?= $rid ?>" style="font-size:.85rem"><?= htmlspecialchars($r['role_name']) ?></label>
              </div></div>
            <?php endforeach; ?>
          </div>
          <div class="form-text mt-2">Changes take effect the next time the user signs in.</div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="save_roles" class="btn btn-primary btn-sm">Save Roles</button></div>
      </form>
    </div></div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
  const rolesModal = new bootstrap.Modal(document.getElementById('rolesModal'));
  document.querySelectorAll('.btn-roles').forEach(b => b.addEventListener('click', () => {
    document.getElementById('rmUserId').value = b.dataset.id;
    document.getElementById('rmName').textContent = b.dataset.name;
    const have = (b.dataset.roles || '').split(',').filter(Boolean);
    document.querySelectorAll('.rm-role').forEach(cb => cb.checked = have.includes(cb.value));
    rolesModal.show();
  }));
  </script>
</body>
</html>
