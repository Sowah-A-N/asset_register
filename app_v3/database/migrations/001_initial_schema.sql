-- =============================================================================
-- Migration 001: Initial v3 schema (asset_register_new)
-- Use this to create the database from scratch on a fresh install.
-- Existing installs: run 003_v3_schema_changes.sql instead.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `asset_register_new`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE asset_register_new;

-- ─────────────────────────────────────────────────────────────────────────────
-- users  (replaces admin_logs)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(100) NOT NULL,
    `display_name`  VARCHAR(150) NOT NULL DEFAULT '',
    `email`         VARCHAR(254)          DEFAULT NULL,
    `role`          VARCHAR(30)  NOT NULL DEFAULT '',
    `password_hash` VARCHAR(255) NOT NULL,
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_username` (`username`),
    KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- password_reset_tokens
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `id`         INT         NOT NULL AUTO_INCREMENT,
    `user_id`    INT         NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL,
    `expires_at` DATETIME    NOT NULL,
    `used_at`    DATETIME             DEFAULT NULL,
    `created_at` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_token_hash` (`token_hash`),
    KEY `idx_prt_user_id`    (`user_id`),
    KEY `idx_prt_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_classes
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_classes` (
    `ast_id`                 INT           NOT NULL AUTO_INCREMENT,
    `asset_class`            VARCHAR(100)  NOT NULL,
    `account_depr_open_bal`  DECIMAL(30,2) NOT NULL DEFAULT '0.00',
    `opening_bal`            DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `opbal_plus_additions`   DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `dep_rate`               FLOAT         NOT NULL DEFAULT 0,
    `estimated_life`         INT           NOT NULL DEFAULT 0,
    `estimated_life_months`  INT GENERATED ALWAYS AS (`estimated_life` * 12) VIRTUAL,
    `depreciation`           INT           NOT NULL DEFAULT 0,
    `depreciated`            TINYINT(1)             DEFAULT 1,
    PRIMARY KEY (`ast_id`),
    UNIQUE KEY `uniq_asset_class` (`asset_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_class_opbal_year
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_class_opbal_year` (
    `id`                     INT           NOT NULL AUTO_INCREMENT,
    `asset_class`            VARCHAR(255)           DEFAULT NULL,
    `opening_balance`        DECIMAL(20,2)          DEFAULT NULL,
    `total_accum_depr_start` DECIMAL(20,2)          DEFAULT '0.00',
    `total_depr_year_charge` DECIMAL(20,2)          DEFAULT '0.00',
    `disposals_depr`         DECIMAL(20,2)          DEFAULT '0.00',
    `total_accum_depr_end`   DECIMAL(20,2) GENERATED ALWAYS AS
                               ((`total_depr_year_charge` + `total_accum_depr_start`) - `disposals_depr`) VIRTUAL,
    `net_book_value`         DECIMAL(20,2) GENERATED ALWAYS AS
                               (`opening_balance` - `total_accum_depr_end`) VIRTUAL,
    `year`                   YEAR                   DEFAULT NULL,
    `expected_life_months`   INT                    DEFAULT 0,
    `rate`                   DECIMAL(6,2)           DEFAULT '0.00',
    `depreciated`            TINYINT(1)             DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_acoy_class_year` (`asset_class`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_class_sub_classes
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_class_sub_classes` (
    `T_id`           INT          NOT NULL AUTO_INCREMENT,
    `sub_class`      VARCHAR(250) NOT NULL,
    `sub_class_code` VARCHAR(250) NOT NULL DEFAULT '',
    `asset_class`    VARCHAR(254) NOT NULL,
    PRIMARY KEY (`T_id`),
    KEY `idx_acsc_class` (`asset_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- assets
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `assets` (
    `asset_id`                INT           NOT NULL AUTO_INCREMENT,
    `asset_name`              VARCHAR(150)  NOT NULL,
    `grv_number`              VARCHAR(20)            DEFAULT 'N/A',
    `serial_number`           VARCHAR(50)            DEFAULT 'N/A',
    `pv_number`               VARCHAR(50)            DEFAULT 'N/A',
    `id_number`               VARCHAR(100)           DEFAULT 'N/A',
    `supplier_name`           VARCHAR(250)  NOT NULL,
    `asset_class`             VARCHAR(50)   NOT NULL,
    `sub_class`               VARCHAR(254)  NOT NULL,
    `asset_type`              VARCHAR(50)   NOT NULL,
    `location`                VARCHAR(50)   NOT NULL DEFAULT 'on campus',
    `user`                    VARCHAR(150)           DEFAULT NULL,
    `acquisition_date`        DATE          NOT NULL,
    `current_year`            INT           NOT NULL DEFAULT 0,
    `historical_cost`         DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `additions`               DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `additions_dollar`        DECIMAL(20,2) GENERATED ALWAYS AS (`additions` / `dollar_rate_used`) VIRTUAL,
    `disposal_value`          DECIMAL(20,2) NOT NULL DEFAULT '0.00',   -- renamed from disposals
    `disposed`                INT           NOT NULL DEFAULT 0,
    `archived`                TINYINT(1)    NOT NULL DEFAULT 0,
    `archived_at`             DATETIME               DEFAULT NULL,
    `active_res_value`        DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `dollar_rate_used`        DOUBLE        NOT NULL DEFAULT 1,
    `transferred_at`          DATETIME               DEFAULT NULL,
    `transferred_to_asset_id` INT                    DEFAULT NULL,
    `date_added`              TIMESTAMP              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`asset_id`),
    KEY `idx_assets_class`    (`asset_class`),
    KEY `idx_assets_type`     (`asset_type`),
    KEY `idx_assets_location` (`location`),
    KEY `idx_assets_disposed` (`disposed`),
    KEY `idx_assets_archived` (`archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- assets_archive  (moved assets, snapshot at archive time)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `assets_archive` (
    `asset_id`        INT           NOT NULL AUTO_INCREMENT,
    `asset_name`      VARCHAR(150)  NOT NULL,
    `grv_number`      VARCHAR(20)            DEFAULT 'N/A',
    `serial_number`   VARCHAR(50)            DEFAULT 'N/A',
    `pv_number`       VARCHAR(50)            DEFAULT 'N/A',
    `id_number`       VARCHAR(100)           DEFAULT 'N/A',
    `supplier_name`   VARCHAR(250)  NOT NULL,
    `asset_class`     VARCHAR(50)   NOT NULL,
    `sub_class`       VARCHAR(254)  NOT NULL,
    `asset_type`      VARCHAR(50)   NOT NULL,
    `location`        VARCHAR(50)   NOT NULL,
    `user`            VARCHAR(150)           DEFAULT NULL,
    `acquisition_date` DATE         NOT NULL,
    `current_year`    INT           NOT NULL,
    `historical_cost` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `additions`       DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `disposals`       DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `active_res_value` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `dollar_rate_used` DOUBLE       NOT NULL,
    `date_added`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`asset_id`),
    KEY `idx_archive_class`    (`asset_class`),
    KEY `idx_archive_type`     (`asset_type`),
    KEY `idx_archive_location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_additions_year
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_additions_year` (
    `id`                    INT           NOT NULL AUTO_INCREMENT,
    `asset_class`           VARCHAR(255)  NOT NULL,
    `year`                  INT           NOT NULL,
    `total_additions_cedi`  DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `total_additions_dollar` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `total_disposals_cedi`  DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `total_disposals_dollar` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    PRIMARY KEY (`id`),
    KEY `idx_aay_class_year` (`asset_class`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_allocation
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_allocation` (
    `t_id`           INT         NOT NULL AUTO_INCREMENT,
    `staff_id`       VARCHAR(40) NOT NULL,
    `asset_sn_number` VARCHAR(40) NOT NULL,
    PRIMARY KEY (`t_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_location
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_location` (
    `loc_id`   INT          NOT NULL AUTO_INCREMENT,
    `location` VARCHAR(100) NOT NULL,
    `loc_code` VARCHAR(250) NOT NULL DEFAULT '',
    PRIMARY KEY (`loc_id`),
    UNIQUE KEY `uniq_location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_type
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_type` (
    `type_id`    INT         NOT NULL AUTO_INCREMENT,
    `asset_type` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_users  (staff who hold / use assets)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_users` (
    `t_id`             INT         NOT NULL AUTO_INCREMENT,
    `staff_id`         VARCHAR(40) NOT NULL,
    `staff_first_name` VARCHAR(50)          DEFAULT NULL,
    `staff_last_name`  VARCHAR(50)          DEFAULT NULL,
    `department`       VARCHAR(100)         DEFAULT NULL,
    PRIMARY KEY (`t_id`),
    UNIQUE KEY `uniq_staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- disposals  (permanent record of disposed assets)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `disposals` (
    `asset_id`              INT           NOT NULL AUTO_INCREMENT,
    `asset_name`            VARCHAR(150)  NOT NULL,
    `grv_number`            VARCHAR(20)            DEFAULT 'N/A',
    `serial_number`         VARCHAR(50)             DEFAULT 'N/A',
    `pv_number`             VARCHAR(50)             DEFAULT 'N/A',
    `id_number`             VARCHAR(100)            DEFAULT 'N/A',
    `supplier_name`         VARCHAR(250)  NOT NULL,
    `asset_class`           VARCHAR(50)   NOT NULL,
    `sub_class`             VARCHAR(254)  NOT NULL,
    `asset_type`            VARCHAR(50)   NOT NULL,
    `location`              VARCHAR(50)   NOT NULL,
    `user`                  VARCHAR(150)            DEFAULT NULL,
    `acquisition_date`      DATE          NOT NULL,
    `current_year`          INT           NOT NULL,
    `historical_cost`       DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `additions`             DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `disposal_value`        DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `active_res_value`      DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `dollar_rate_used`      DOUBLE        NOT NULL,
    `date_of_disposal`      TIMESTAMP              DEFAULT CURRENT_TIMESTAMP,
    `disposal_month`        INT                    DEFAULT 0,
    `estimated_life_months` INT                    DEFAULT 0,
    `disposal_depreciation` DECIMAL(20,2) GENERATED ALWAYS AS
                              ((`additions` / `estimated_life_months`) * `disposal_month`) VIRTUAL,
    PRIMARY KEY (`asset_id`),
    KEY `idx_disposals_class`    (`asset_class`),
    KEY `idx_disposals_type`     (`asset_type`),
    KEY `idx_disposals_location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- dollar_rate
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `dollar_rate` (
    `table_id`    INT         NOT NULL AUTO_INCREMENT,
    `dollar_rate` DOUBLE      NOT NULL,
    `rate_status` VARCHAR(20) NOT NULL DEFAULT 'INACTIVE',
    `action_by`   VARCHAR(100) NOT NULL,
    `date_added`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`table_id`),
    KEY `idx_dr_status` (`rate_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- moved_assets
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `moved_assets` (
    `t_id`           INT          NOT NULL AUTO_INCREMENT,
    `serial_number`  VARCHAR(100) NOT NULL,
    `notes`          TEXT         NOT NULL,
    `old_location`   VARCHAR(100) NOT NULL,
    `old_user`       VARCHAR(100) NOT NULL,
    `new_location`   VARCHAR(100) NOT NULL,   -- was New_location in v1/v2
    `new_user`       VARCHAR(100) NOT NULL,
    `date_of_action` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`t_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- suppliers
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `suppliers` (
    `sup_id`   INT          NOT NULL AUTO_INCREMENT,
    `name`     VARCHAR(250) NOT NULL,
    `location` VARCHAR(250) NOT NULL DEFAULT '',
    `number`   VARCHAR(20)  NOT NULL DEFAULT '',
    PRIMARY KEY (`sup_id`),
    UNIQUE KEY `uniq_sup_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- department  (kept for reference; not actively used in v3 routing)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `department` (
    `t_id`     INT         NOT NULL AUTO_INCREMENT,
    `dep_id`   VARCHAR(37) NOT NULL,
    `dep_name` VARCHAR(70) NOT NULL,
    PRIMARY KEY (`t_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_classes_archive
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_classes_archive` (
    `ast_id`      INT          NOT NULL AUTO_INCREMENT,
    `asset_class` VARCHAR(50)  NOT NULL,
    `opening_bal` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
    `dep_rate`    FLOAT         NOT NULL DEFAULT 0,
    PRIMARY KEY (`ast_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_location_archive
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_location_archive` (
    `loc_id`   INT          NOT NULL AUTO_INCREMENT,
    `location` VARCHAR(100) NOT NULL,
    `loc_code` VARCHAR(250) NOT NULL DEFAULT '',
    PRIMARY KEY (`loc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- audit_log
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT                      DEFAULT NULL,
    `username`   VARCHAR(100)    NOT NULL DEFAULT '',
    `role`       VARCHAR(50)     NOT NULL DEFAULT '',
    `action`     VARCHAR(200)    NOT NULL,
    `table_name` VARCHAR(100)             DEFAULT NULL,
    `record_id`  INT                      DEFAULT NULL,
    `detail`     TEXT                     DEFAULT NULL,
    `ip_address` VARCHAR(45)     NOT NULL DEFAULT '',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_audit_user_id`    (`user_id`),
    KEY `idx_audit_username`   (`username`),
    KEY `idx_audit_action`     (`action`),
    KEY `idx_audit_record_id`  (`record_id`),
    KEY `idx_audit_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- asset_category_changes
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `asset_category_changes` (
    `change_id`       INT          NOT NULL AUTO_INCREMENT,
    `asset_id`        INT          NOT NULL,
    `asset_name`      VARCHAR(255) NOT NULL,
    `old_asset_class` VARCHAR(100) NOT NULL,
    `new_asset_class` VARCHAR(100) NOT NULL,
    `old_sub_class`   VARCHAR(100)          DEFAULT NULL,
    `new_sub_class`   VARCHAR(100)          DEFAULT NULL,
    `reason`          TEXT                  DEFAULT NULL,
    `changed_by`      VARCHAR(100) NOT NULL,
    `changed_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`change_id`),
    KEY `idx_acc_asset_id`        (`asset_id`),
    KEY `idx_acc_old_asset_class` (`old_asset_class`),
    KEY `idx_acc_new_asset_class` (`new_asset_class`),
    KEY `idx_acc_changed_at`      (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────────────────────
-- End of migration 001
-- ─────────────────────────────────────────────────────────────────────────────
