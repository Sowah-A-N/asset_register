<?php
/**
 * DEPRECATED: the "Log Untracked Asset" form is now a modal on view_untracked.
 * Redirect there (preserves the sidebar "Log Untracked" link).
 */
require_once '../../auth.php';
requirePermission('asset.create', '../login/');
header('Location: ../view_untracked/');
exit();
