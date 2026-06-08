<?php
/**
 * FILE: schedule_officer/calculations/view_calculations.php
 *
 * DEPRECATED. This page computed a single asset's depreciation using
 * active_res_value as the cost basis (wrong), treated the disposals flag
 * (0/1) as a monetary amount, and attempted an INSERT into `calculations`
 * that omitted required NOT NULL columns (so it never persisted).
 *
 * Superseded by the canonical engine. The per-asset breakdown now appears
 * within the class schedule (reports/class_reports1/). Redirect there.
 */
session_start();
header('Location: ../../reports/class_reports1/');
exit();
