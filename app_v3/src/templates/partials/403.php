<?php http_response_code(403); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden — Asset Register</title>
</head>
<body>
    <h1>403 — Access Denied</h1>
    <p>You do not have permission to access this page.</p>
    <p><a href="<?= defined('APP_URL') ? esc(APP_URL) : '/' ?>/dashboard">Return to dashboard</a></p>
</body>
</html>
