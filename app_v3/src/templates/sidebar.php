<?php
// Sidebar navigation (Bootstrap 5 + collapse groups)
$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$role     = current_role();
$uri      = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Helper: mark active nav item
function nav_active(string $segment): string {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $base = trim(parse_url(defined('APP_URL') ? APP_URL : '', PHP_URL_PATH), '/');
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = trim(substr($uri, strlen($base)), '/');
    }
    return str_starts_with($uri, $segment) ? 'active' : '';
}
?>
<nav id="sidebar" class="sidebar bg-dark text-white d-flex flex-column">
    <div class="sidebar-sticky p-2">
        <ul class="nav flex-column">

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="<?= $base_url ?>/dashboard"
                   class="nav-link text-white <?= nav_active('dashboard') ?>">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>

            <!-- Assets -->
            <?php if (in_array($role, ['hod_ict','schedule_officer','accountant','director_finance','sia','dsu'], true)): ?>
            <li class="nav-item mt-2">
                <a class="nav-link text-white-50 small text-uppercase" data-bs-toggle="collapse"
                   href="#menuAssets" role="button">
                    <i class="bi bi-boxes me-1"></i>Assets
                </a>
                <div class="collapse <?= nav_active('assets') ? 'show' : '' ?>" id="menuAssets">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/assets"
                               class="nav-link text-white <?= nav_active('assets') ?>">
                                <i class="bi bi-list-ul me-1"></i>All Assets
                            </a>
                        </li>
                        <?php if (in_array($role, ['hod_ict','schedule_officer'], true)): ?>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/assets/create"
                               class="nav-link text-white <?= nav_active('assets/create') ?>">
                                <i class="bi bi-plus-circle me-1"></i>Add Asset
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/assets/untracked"
                               class="nav-link text-white <?= nav_active('assets/untracked') ?>">
                                <i class="bi bi-question-circle me-1"></i>Untracked
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/assets/wip-transfer"
                               class="nav-link text-white <?= nav_active('assets/wip-transfer') ?>">
                                <i class="bi bi-arrow-left-right me-1"></i>WIP Transfer
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/assets/disposals"
                               class="nav-link text-white <?= nav_active('assets/disposals') ?>">
                                <i class="bi bi-trash me-1"></i>Disposals
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/assets/moved"
                               class="nav-link text-white <?= nav_active('assets/moved') ?>">
                                <i class="bi bi-arrow-repeat me-1"></i>Moved Assets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/assets/archive"
                               class="nav-link text-white <?= nav_active('assets/archive') ?>">
                                <i class="bi bi-archive me-1"></i>Archive
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <!-- Reports -->
            <li class="nav-item mt-2">
                <a class="nav-link text-white-50 small text-uppercase" data-bs-toggle="collapse"
                   href="#menuReports" role="button">
                    <i class="bi bi-bar-chart-line me-1"></i>Reports
                </a>
                <div class="collapse <?= nav_active('reports') ? 'show' : '' ?>" id="menuReports">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/reports"
                               class="nav-link text-white <?= nav_active('reports') ?>">
                                <i class="bi bi-file-earmark-text me-1"></i>Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/reports/summary"
                               class="nav-link text-white">
                                <i class="bi bi-table me-1"></i>Summary
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/reports/class"
                               class="nav-link text-white">
                                <i class="bi bi-diagram-3 me-1"></i>By Class
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/reports/additions"
                               class="nav-link text-white">
                                <i class="bi bi-calendar-plus me-1"></i>Additions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/reports/depreciation"
                               class="nav-link text-white">
                                <i class="bi bi-graph-down me-1"></i>Depreciation
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Admin -->
            <?php if (in_array($role, ['hod_ict','schedule_officer','accountant'], true)): ?>
            <li class="nav-item mt-2">
                <a class="nav-link text-white-50 small text-uppercase" data-bs-toggle="collapse"
                   href="#menuAdmin" role="button">
                    <i class="bi bi-gear me-1"></i>Administration
                </a>
                <div class="collapse <?= nav_active('admin') ? 'show' : '' ?>" id="menuAdmin">
                    <ul class="nav flex-column ms-3">
                        <?php if ($role === 'hod_ict'): ?>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/admin/users"
                               class="nav-link text-white <?= nav_active('admin/users') ?>">
                                <i class="bi bi-people me-1"></i>Users
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/admin/asset-classes"
                               class="nav-link text-white <?= nav_active('admin/asset-classes') ?>">
                                <i class="bi bi-tags me-1"></i>Asset Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/admin/locations"
                               class="nav-link text-white <?= nav_active('admin/locations') ?>">
                                <i class="bi bi-geo-alt me-1"></i>Locations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/admin/suppliers"
                               class="nav-link text-white <?= nav_active('admin/suppliers') ?>">
                                <i class="bi bi-shop me-1"></i>Suppliers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/admin/dollar-rate"
                               class="nav-link text-white <?= nav_active('admin/dollar-rate') ?>">
                                <i class="bi bi-currency-dollar me-1"></i>Dollar Rate
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/admin/asset-users"
                               class="nav-link text-white <?= nav_active('admin/asset-users') ?>">
                                <i class="bi bi-person-badge me-1"></i>Asset Users
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

        </ul>
    </div>
</nav>
