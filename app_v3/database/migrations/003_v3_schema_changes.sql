-- =============================================================================
-- Migration 003: v3 schema changes
-- Target DB:  asset_register_new
-- Safe to re-run: yes (all statements use IF EXISTS / IF NOT EXISTS guards)
-- Run AFTER:  001_initial_schema, 002_asset_reclassification
-- =============================================================================

USE asset_register_new;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1. Rename admin_logs → users  (app_login table)
--    admin_logs had: table_id, username, user_role, user_password
--    New users table needs: id, username, display_name, role, password_hash,
--    is_active, created_at, updated_at
-- ─────────────────────────────────────────────────────────────────────────────

-- 1a. Rename table if it hasn't been renamed yet
ALTER TABLE `admin_logs` RENAME TO `users`;

-- 1b. Rename primary key column
ALTER TABLE `users`
    CHANGE COLUMN `table_id` `id` INT NOT NULL AUTO_INCREMENT;

-- 1c. Rename username → username (already correct), user_password → password_hash
ALTER TABLE `users`
    CHANGE COLUMN `user_password` `password_hash` VARCHAR(255) NOT NULL;

-- 1d. Rename user_role → role
ALTER TABLE `users`
    CHANGE COLUMN `user_role` `role` VARCHAR(30) NOT NULL DEFAULT '';

-- 1e. Add missing columns (guards against re-run)
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `display_name` VARCHAR(150) NOT NULL DEFAULT '' AFTER `username`,
    ADD COLUMN IF NOT EXISTS `email`        VARCHAR(254)          DEFAULT NULL AFTER `display_name`,
    ADD COLUMN IF NOT EXISTS `is_active`    TINYINT(1)   NOT NULL DEFAULT 1   AFTER `role`,
    ADD COLUMN IF NOT EXISTS `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `is_active`,
    ADD COLUMN IF NOT EXISTS `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- 1f. Add unique index on username
ALTER TABLE `users`
    ADD UNIQUE INDEX IF NOT EXISTS `uniq_username` (`username`);

-- ─────────────────────────────────────────────────────────────────────────────
-- 2. Standardise role values in users
--    Old values → new slug values
--    'SIA'       → 'sia'
--    'S/O'       → 'schedule_officer'
--    'B/O'       → 'budget_officer'
--    'Accountant'→ 'accountant'
--    'D/F'       → 'director_finance'
--    'DSU'       → 'dsu'
--    'HOD/ICT'   → 'hod_ict'   (may not exist yet; safe)
-- ─────────────────────────────────────────────────────────────────────────────
UPDATE `users` SET `role` = 'sia'              WHERE `role` IN ('SIA', 'sia');
UPDATE `users` SET `role` = 'schedule_officer' WHERE `role` IN ('S/O', 'schedule_officer', 'Schedule Officer');
UPDATE `users` SET `role` = 'budget_officer'   WHERE `role` IN ('B/O', 'budget_officer', 'Budget Officer');
UPDATE `users` SET `role` = 'accountant'       WHERE `role` IN ('Accountant', 'accountant');
UPDATE `users` SET `role` = 'director_finance' WHERE `role` IN ('D/F', 'director_finance', 'Director Finance');
UPDATE `users` SET `role` = 'dsu'              WHERE `role` IN ('DSU', 'dsu');
UPDATE `users` SET `role` = 'hod_ict'          WHERE `role` IN ('HOD/ICT', 'hod_ict', 'HOD ICT');

-- Populate display_name from username where it is blank
UPDATE `users` SET `display_name` = `username` WHERE `display_name` = '';

-- ─────────────────────────────────────────────────────────────────────────────
-- 3. Fix assets table
--    a) Rename assets.disposals (INT, ambiguous) → disposal_value (DECIMAL)
--       Column currently stores integer 0/1 — in the disposals table the
--       correct decimal column is already called disposal_value, so we just
--       align naming here too.
--    b) Add WIP capitalisation columns: transferred_at, transferred_to_asset_id
-- ─────────────────────────────────────────────────────────────────────────────

-- 3a. Rename disposals → disposal_value (safe: CHANGE preserves data)
ALTER TABLE `assets`
    CHANGE COLUMN `disposals` `disposal_value` DECIMAL(20,2) NOT NULL DEFAULT '0.00';

-- 3b. WIP transfer columns
ALTER TABLE `assets`
    ADD COLUMN IF NOT EXISTS `transferred_at`       DATETIME DEFAULT NULL AFTER `date_added`,
    ADD COLUMN IF NOT EXISTS `transferred_to_asset_id` INT DEFAULT NULL AFTER `transferred_at`;

-- ─────────────────────────────────────────────────────────────────────────────
-- 4. Fix asset_additions_year engine (was MyISAM — causes FK issues + no ACID)
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `asset_additions_year` ENGINE = InnoDB;

-- ─────────────────────────────────────────────────────────────────────────────
-- 5. Drop obsolete tables
--    calculations  — replaced by runtime computation in PHP
--    other_values  — single-row scratch table, no longer used
-- ─────────────────────────────────────────────────────────────────────────────
DROP TABLE IF EXISTS `calculations`;
DROP TABLE IF EXISTS `other_values`;

-- ─────────────────────────────────────────────────────────────────────────────
-- 6. audit_log table (idempotent — already created by 001_security_v2 if run)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT             DEFAULT NULL,
    `username`   VARCHAR(100)    NOT NULL DEFAULT '',
    `role`       VARCHAR(50)     NOT NULL DEFAULT '',
    `action`     VARCHAR(200)    NOT NULL,
    `table_name` VARCHAR(100)    DEFAULT NULL,
    `record_id`  INT             DEFAULT NULL,
    `detail`     TEXT            DEFAULT NULL,
    `ip_address` VARCHAR(45)     NOT NULL DEFAULT '',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_audit_user_id`    (`user_id`),
    KEY `idx_audit_username`   (`username`),
    KEY `idx_audit_action`     (`action`),
    KEY `idx_audit_record_id`  (`record_id`),
    KEY `idx_audit_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Immutable audit trail for all write operations';

-- ─────────────────────────────────────────────────────────────────────────────
-- 7. asset_category_changes table (idempotent — already in 002)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_category_changes` (
    `change_id`       INT          NOT NULL AUTO_INCREMENT,
    `asset_id`        INT          NOT NULL,
    `asset_name`      VARCHAR(255) NOT NULL,
    `old_asset_class` VARCHAR(100) NOT NULL,
    `new_asset_class` VARCHAR(100) NOT NULL,
    `old_sub_class`   VARCHAR(100) DEFAULT NULL,
    `new_sub_class`   VARCHAR(100) DEFAULT NULL,
    `reason`          TEXT         DEFAULT NULL,
    `changed_by`      VARCHAR(100) NOT NULL,
    `changed_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`change_id`),
    KEY `idx_acc_asset_id`        (`asset_id`),
    KEY `idx_acc_old_asset_class` (`old_asset_class`),
    KEY `idx_acc_new_asset_class` (`new_asset_class`),
    KEY `idx_acc_changed_at`      (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Audit trail of asset reclassifications between categories';

-- ─────────────────────────────────────────────────────────────────────────────
-- 8. password_reset_tokens table (new for Phase 4 forgot-password flow)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `user_id`    INT          NOT NULL,
    `token_hash` VARCHAR(64)  NOT NULL,          -- SHA-256 hex of the raw token
    `expires_at` DATETIME     NOT NULL,
    `used_at`    DATETIME     DEFAULT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_token_hash` (`token_hash`),
    KEY `idx_prt_user_id`    (`user_id`),
    KEY `idx_prt_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Single-use password reset tokens; expire after 1 hour';

-- ─────────────────────────────────────────────────────────────────────────────
-- 9. Normalise moved_assets column capitalisation
--    `New_location` has a capital N — standardise to snake_case
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `moved_assets`
    CHANGE COLUMN `New_location` `new_location` VARCHAR(100)
        CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

-- ─────────────────────────────────────────────────────────────────────────────
-- 10. Add soft-delete / archive flag to assets (cleaner than separate table)
--     `archived` column used by the archive action; disposals keep separate table
-- ─────────────────────────────────────────────────────────────────────────────
ALTER TABLE `assets`
    ADD COLUMN IF NOT EXISTS `archived`    TINYINT(1) NOT NULL DEFAULT 0 AFTER `disposed`,
    ADD COLUMN IF NOT EXISTS `archived_at` DATETIME   DEFAULT NULL        AFTER `archived`;

-- ─────────────────────────────────────────────────────────────────────────────
-- End of migration 003
-- ─────────────────────────────────────────────────────────────────────────────
