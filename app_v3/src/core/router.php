<?php
// Router — maps URL path and HTTP method to a page or action file.
// Called once by public/index.php via dispatch($conn).

function dispatch($conn) {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri    = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Strip the base path when running in a subdirectory
    // e.g. /asset_register/app_v3/public/dashboard → dashboard
    $base = trim(parse_url(defined('APP_URL') ? APP_URL : '', PHP_URL_PATH), '/');
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = trim(substr($uri, strlen($base)), '/');
    }

    // ----------------------------------------------------------------
    // POST actions — identified by hidden _action field in every form
    // ----------------------------------------------------------------
    if ($method === 'POST') {
        $action = post('_action');

        switch ($action) {
            // Auth
            case 'login':
                require SRC . '/actions/auth/login.action.php'; return;
            case 'logout':
                require SRC . '/actions/auth/logout.action.php'; return;
            case 'forgot_password':
                require SRC . '/actions/auth/forgot_password.action.php'; return;

            // Assets — create (two-step)
            case 'asset_create_step1':
            case 'asset_create_step2':
            case 'asset_create_back':
                require SRC . '/actions/assets/create.action.php'; return;

            // Assets — CRUD
            case 'asset_edit':
                require SRC . '/actions/assets/edit.action.php'; return;
            case 'asset_dispose':
                require SRC . '/actions/assets/dispose.action.php'; return;
            case 'asset_move':
                require SRC . '/actions/assets/move.action.php'; return;
            case 'asset_archive':
            case 'asset_restore':
                require SRC . '/actions/assets/archive.action.php'; return;
            case 'asset_reclassify':
                require SRC . '/actions/assets/reclassify.action.php'; return;
            case 'asset_add_id':
                require SRC . '/actions/assets/add_id.action.php'; return;
            case 'wip_transfer':
                require SRC . '/actions/assets/wip_transfer.action.php'; return;

            // Admin
            case 'user_create':
            case 'user_edit':
            case 'user_delete':
                require SRC . '/actions/admin/user_create.action.php'; return;
            case 'asset_class_save':
                require SRC . '/actions/admin/asset_class_save.action.php'; return;
            case 'supplier_save':
                require SRC . '/actions/admin/supplier_save.action.php'; return;
            case 'location_save':
                require SRC . '/actions/admin/location_save.action.php'; return;
            case 'dollar_rate_save':
                require SRC . '/actions/admin/dollar_rate_save.action.php'; return;

            default:
                http_response_code(400);
                exit('Unknown action.');
        }
    }

    // ----------------------------------------------------------------
    // GET pages
    // ----------------------------------------------------------------
    switch ($uri) {

        // Auth
        case '':
        case 'login':
            require SRC . '/pages/auth/login.php'; break;
        case 'forgot-password':
            require SRC . '/pages/auth/forgot_password.php'; break;

        // Dashboard
        case 'dashboard':
            require_auth();
            require SRC . '/pages/dashboard/index.php'; break;

        // Assets — readable by most roles; write actions gated in page/action
        case 'assets':
            require_auth();
            require SRC . '/pages/assets/list.php'; break;
        case 'assets/create':
            require_role('hod_ict', 'schedule_officer');
            require SRC . '/pages/assets/create.php'; break;
        case 'assets/edit':
            require_role('hod_ict', 'schedule_officer', 'accountant');
            require SRC . '/pages/assets/edit.php'; break;
        case 'assets/reclassify':
            require_role('hod_ict', 'schedule_officer', 'accountant');
            require SRC . '/pages/assets/reclassify.php'; break;
        case 'assets/disposals':
            require_auth();
            require SRC . '/pages/assets/disposals.php'; break;
        case 'assets/moved':
            require_auth();
            require SRC . '/pages/assets/moved.php'; break;
        case 'assets/archive':
            require_auth();
            require SRC . '/pages/assets/archive.php'; break;
        case 'assets/untracked':
            require_role('hod_ict', 'schedule_officer');
            require SRC . '/pages/assets/untracked.php'; break;
        case 'assets/add-id':
            require_role('hod_ict', 'schedule_officer');
            require SRC . '/pages/assets/add_id.php'; break;
        case 'assets/wip-transfer':
            require_role('hod_ict', 'schedule_officer', 'accountant');
            require SRC . '/pages/assets/wip_transfer.php'; break;

        // Reports
        case 'reports':
            require_auth();
            require SRC . '/pages/reports/index.php'; break;
        case 'reports/summary':
            require_auth();
            require SRC . '/pages/reports/summary.php'; break;
        case 'reports/summary-usd':
            require_auth();
            require SRC . '/pages/reports/summary_usd.php'; break;
        case 'reports/all-assets':
            require_auth();
            require SRC . '/pages/reports/all_assets.php'; break;
        case 'reports/all-assets-usd':
            require_auth();
            require SRC . '/pages/reports/all_assets_usd.php'; break;
        case 'reports/class':
            require_auth();
            require SRC . '/pages/reports/class_report.php'; break;
        case 'reports/depreciation':
            require_auth();
            require SRC . '/pages/reports/depreciation_charge.php'; break;
        case 'reports/additions':
            require_auth();
            require SRC . '/pages/reports/additions.php'; break;
        case 'reports/accum-depreciation':
            require_auth();
            require SRC . '/pages/reports/accum_depreciation.php'; break;
        case 'reports/fully-depreciated':
            require_auth();
            require SRC . '/pages/reports/fully_depreciated.php'; break;
        case 'reports/historical-cost':
            require_auth();
            require SRC . '/pages/reports/historical_cost.php'; break;
        case 'reports/disposals':
            require_auth();
            require SRC . '/pages/reports/disposals.php'; break;
        case 'reports/by-category':
            require_auth();
            require SRC . '/pages/reports/assets_by_category.php'; break;
        case 'reports/by-location':
            require_auth();
            require SRC . '/pages/reports/assets_by_location.php'; break;
        case 'reports/quarterly-class':
            require_auth();
            require SRC . '/pages/reports/quarterly_class.php'; break;
        case 'reports/quarterly-asset':
            require_auth();
            require SRC . '/pages/reports/quarterly_asset.php'; break;
        case 'reports/individual-asset':
            require_auth();
            require SRC . '/pages/reports/individual_asset.php'; break;

        // Exports
        case 'exports/assets':
        case 'exports/assets-pdf':
        case 'exports/disposals':
            require_auth();
            require SRC . '/pages/exports/excel.php'; break;
        case 'exports/pdf':
            require_auth();
            require SRC . '/pages/exports/pdf.php'; break;

        // Admin
        case 'admin/users':
            require_role('hod_ict');
            require SRC . '/pages/admin/users.php'; break;
        case 'admin/asset-classes':
            require_role('hod_ict', 'schedule_officer', 'accountant');
            require SRC . '/pages/admin/asset_classes.php'; break;
        case 'admin/suppliers':
            require_role('hod_ict', 'schedule_officer');
            require SRC . '/pages/admin/suppliers.php'; break;
        case 'admin/locations':
            require_role('hod_ict', 'schedule_officer', 'accountant', 'dsu');
            require SRC . '/pages/admin/locations.php'; break;
        case 'admin/asset-users':
            require_role('hod_ict', 'schedule_officer');
            require SRC . '/pages/admin/asset_users.php'; break;
        case 'admin/asset-types':
            require_role('hod_ict', 'schedule_officer');
            require SRC . '/pages/admin/asset_types.php'; break;
        case 'admin/dollar-rate':
            require_role('hod_ict', 'schedule_officer', 'accountant');
            require SRC . '/pages/admin/dollar_rate.php'; break;

        // 404
        default:
            http_response_code(404);
            require SRC . '/templates/partials/404.php';
            break;
    }
}
