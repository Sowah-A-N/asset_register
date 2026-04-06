<?php
// Top navigation bar (Bootstrap 5)
$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$role     = current_role();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= $base_url ?>/dashboard">
            <i class="bi bi-building me-2"></i>Asset Register
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <span class="nav-link text-white-50 small">
                        <i class="bi bi-person-circle me-1"></i><?= esc(current_user()) ?>
                        <span class="badge bg-secondary ms-1"><?= esc($role) ?></span>
                    </span>
                </li>
                <li class="nav-item">
                    <form method="post" action="<?= $base_url ?>/" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_action" value="logout">
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
