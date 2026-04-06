<?php
// Minimal 404 — will be replaced with full layout in Phase 5
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>404 – Page Not Found</title></head>
<body style="font-family:sans-serif;text-align:center;padding:4rem">
    <h1>404</h1>
    <p>Page not found.</p>
    <a href="<?= defined('APP_URL') ? esc(APP_URL) : '/' ?>">Go to login</a>
</body>
</html>
