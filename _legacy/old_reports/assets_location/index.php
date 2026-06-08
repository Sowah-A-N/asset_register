<?php
// DEPRECATED stub (was unguarded and mislabeled). The Reports Hub now shows
// the assets-by-class and assets-by-location breakdowns. Redirect there.
require_once __DIR__.'/../../../auth.php';
requirePermission('report.view','../../login/');
header('Location: ../');
exit();
