<?php
/**
 * DEPRECATED: adding asset users is now done via the "Add User" modal on the
 * Asset Users page. Redirect there (single canonical user-management page).
 */
require_once '../../auth.php';
requirePermission('assetuser.view', '../login/');
header('Location: ../asset_users/');
exit();
