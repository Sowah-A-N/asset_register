<?php
/**
 * FILE: schedule_officer/reports/index2.php
 *
 * DEPRECATED. This page computed per-class depreciation using
 * active_res_value (residual value) as the cost basis — an incorrect
 * accounting treatment — and ignored the legacy brought-forward pool.
 *
 * It is superseded by class_reports1/, which uses the canonical
 * read-only engine (pool + individual assets, cost − residual basis).
 * Preserve old links by redirecting, carrying the asset_class through.
 */
session_start();
$class = $_GET['asset_class'] ?? '';
$target = 'class_reports1/' . ($class !== '' ? '?asset_class=' . urlencode($class) : '');
header('Location: ' . $target);
exit();
