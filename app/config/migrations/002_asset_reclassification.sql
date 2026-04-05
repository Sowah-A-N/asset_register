-- Migration 002: Asset Reclassification
-- Adds the asset_category_changes table to record all cross-category
-- reclassifications as auditable transactions (Issues 16 & 17).
-- Target database: asset_register_new
-- Run once against the main database before deploying the reclassification UI.

USE asset_register_new;

CREATE TABLE IF NOT EXISTS `asset_category_changes` (
    `change_id`       INT(11)      NOT NULL AUTO_INCREMENT,
    `asset_id`        INT(11)      NOT NULL,
    `asset_name`      VARCHAR(255) NOT NULL,
    `old_asset_class` VARCHAR(100) NOT NULL,
    `new_asset_class` VARCHAR(100) NOT NULL,
    `old_sub_class`   VARCHAR(100) DEFAULT NULL,
    `new_sub_class`   VARCHAR(100) DEFAULT NULL,
    `reason`          TEXT         DEFAULT NULL,
    `changed_by`      VARCHAR(100) NOT NULL,
    `changed_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`change_id`),
    KEY `idx_asset_id`        (`asset_id`),
    KEY `idx_old_asset_class` (`old_asset_class`),
    KEY `idx_new_asset_class` (`new_asset_class`),
    KEY `idx_changed_at`      (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Audit trail of asset reclassifications between categories';
