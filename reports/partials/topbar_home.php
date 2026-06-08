<?php
/**
 * FILE: reports/partials/topbar_home.php
 * PURPOSE: Role-aware "home" button for the report top-bars. Operational roles
 *          (Schedule Officer / Admin) get a direct "Dashboard" link back to the
 *          asset app; oversight / read-only roles get "Portal".
 *
 * Set $RPREFIX (relative path to the web root) before including:
 *   '../../' for a report page (/reports/<page>/), '../' for the hub (/reports/).
 */
$RPREFIX = $RPREFIX ?? '../../';
$__op = function_exists('can') && (can('asset.create') || can('catalog.manage'));
?>
<a href="<?= $RPREFIX . ($__op ? 'asset_app/dashboard/' : 'portal/') ?>"
   class="btn btn-outline-primary btn-sm no-print">
  <i class="bi bi-house-door me-1"></i><?= $__op ? 'Dashboard' : 'Portal' ?>
</a>
