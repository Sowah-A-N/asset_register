<?php
require_auth();
require_once SRC . '/queries/reports.php';
$base_url   = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$page_title = 'Individual Asset';
$content = function() use ($base_url) {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold"><?php echo $GLOBALS['page_title']; ?></h4>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-printer me-1"></i>Print
    </button>
</div>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    This report is under construction. Full implementation follows in the next update.
</div>
<?php };
require SRC . '/templates/layouts/base.php';
