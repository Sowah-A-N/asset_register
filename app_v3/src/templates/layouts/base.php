<?php
// base.php — Master layout.
// Pages include this via:
//   $page_title = 'My Page';
//   $content    = function() { ?> ... HTML ... <?php };
//   require SRC . '/templates/layouts/base.php';
//
// Or use the render_page() helper below.

$page_title = $page_title ?? 'Asset Register';
$body_class = $body_class ?? '';
$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title) ?> — Asset Register</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/app.css">
</head>
<body class="<?= esc($body_class) ?>">

<?php if (is_logged_in()): ?>
    <?php require SRC . '/templates/navbar.php'; ?>

    <div class="d-flex" id="wrapper">
        <?php require SRC . '/templates/sidebar.php'; ?>

        <div id="page-content-wrapper" class="flex-grow-1">
            <div class="container-fluid p-4">
                <?php render_flash(); ?>
                <?php if (isset($content) && is_callable($content)) ($content)(); ?>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Guest layout (login, forgot password) -->
    <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
        <div class="w-100" style="max-width:420px;">
            <?php render_flash(); ?>
            <?php if (isset($content) && is_callable($content)) ($content)(); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Bootstrap 5 JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmM73FBIQNHJFoZU4V5LXKQ2mD7L"
        crossorigin="anonymous"></script>
<!-- Chart.js (loaded on pages that need it) -->
<?php if (!empty($load_chartjs)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<?php endif; ?>
<!-- App JS -->
<script src="<?= $base_url ?>/assets/js/app.js"></script>
</body>
</html>
