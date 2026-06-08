<?php
/**
 * DEPRECATED handler. Untracked-asset creation now happens via the modal on
 * view_untracked (which posts to itself with a prepared statement). This old
 * raw-SQL endpoint is neutralised — guarded and redirected.
 */
require_once '../../auth.php';
requirePermission('asset.create', '../login/');
header('Location: ../view_untracked/');
exit();
