-- ════════════════════════════════════════════════════════════════
-- RMU Asset Register v2 — RBAC Schema (Phase A)
-- ════════════════════════════════════════════════════════════════
-- Additive and idempotent. Does NOT modify `admin_logs` (users table).
-- Safe to run on the existing asset_register_new database.
-- Apply via phpMyAdmin → asset_register_new → SQL tab → paste → Go.
-- ════════════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── roles ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id`     INT NOT NULL AUTO_INCREMENT,
  `role_key`    VARCHAR(50)  NOT NULL,
  `role_name`   VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uq_role_key` (`role_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ── permissions ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id`  INT NOT NULL AUTO_INCREMENT,
  `permission_key` VARCHAR(80) NOT NULL,
  `module`         VARCHAR(50) NOT NULL,
  `description`    VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `uq_permission_key` (`permission_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ── role_permissions (role ↔ permission) ─────────────────────────
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id`       INT NOT NULL,
  `permission_id` INT NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  KEY `fk_rp_perm` (`permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`)
      REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`)
      REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ── user_roles (user ↔ role) ─────────────────────────────────────
-- user_id references admin_logs.table_id. No FK to admin_logs so this
-- script never touches that table (backward compatibility).
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id`     INT NOT NULL,
  `role_id`     INT NOT NULL,
  `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `role_id`),
  KEY `fk_ur_role` (`role_id`),
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`)
      REFERENCES `roles` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ════════════════════════════════════════════════════════════════
-- SEED DATA
-- ════════════════════════════════════════════════════════════════

-- ── Roles ────────────────────────────────────────────────────────
INSERT INTO `roles` (`role_key`, `role_name`, `description`) VALUES
  ('schedule_officer', 'Schedule Officer',   'Full operational control of the asset register'),
  ('dsu',              'DSU',                'Departmental view of assets, locations and reports'),
  ('accountant',       'Accountant',         'Financial reporting and asset valuation'),
  ('budget_officer',   'Budget Officer',     'Budget oversight and asset reporting'),
  ('sia',              'Senior Internal Auditor', 'Audit-level read access and report export'),
  ('director_finance', 'Director of Finance','Executive oversight and reporting'),
  ('system_admin',     'System Administrator','Manages users, roles and permissions')
ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`), `description` = VALUES(`description`);

-- ── Permissions ──────────────────────────────────────────────────
INSERT INTO `permissions` (`permission_key`, `module`, `description`) VALUES
  ('asset.view',      'assets',   'View assets'),
  ('asset.create',    'assets',   'Register new assets'),
  ('asset.edit',      'assets',   'Edit asset details'),
  ('asset.move',      'assets',   'Transfer asset location/user'),
  ('asset.archive',   'assets',   'Archive assets'),
  ('asset.dispose',   'assets',   'Dispose / write off assets'),
  ('catalog.view',    'catalog',  'View classes, sub-classes, locations, types'),
  ('catalog.manage',  'catalog',  'Manage classes, sub-classes, locations, types'),
  ('supplier.view',   'suppliers','View suppliers'),
  ('supplier.manage', 'suppliers','Manage suppliers'),
  ('assetuser.view',  'people',   'View asset users'),
  ('assetuser.manage','people',   'Manage asset users'),
  ('rate.view',       'rate',     'View dollar rate'),
  ('rate.set',        'rate',     'Set / update dollar rate'),
  ('report.view',     'reports',  'View reports'),
  ('report.export',   'reports',  'Export / print reports'),
  ('user.manage',     'admin',    'Manage system user accounts'),
  ('role.manage',     'admin',    'Manage roles and permissions')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`), `module` = VALUES(`module`);

-- ── Role → Permission assignments ────────────────────────────────

-- schedule_officer: full operational control (no admin)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.permission_id
FROM roles r JOIN permissions p
WHERE r.role_key = 'schedule_officer'
  AND p.permission_key IN (
    'asset.view','asset.create','asset.edit','asset.move','asset.archive','asset.dispose',
    'catalog.view','catalog.manage','supplier.view','supplier.manage',
    'assetuser.view','assetuser.manage','rate.view','rate.set',
    'report.view','report.export')
;

-- dsu: view assets, catalog, reports
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.permission_id
FROM roles r JOIN permissions p
WHERE r.role_key = 'dsu'
  AND p.permission_key IN (
    'asset.view','catalog.view','rate.view','report.view','report.export')
;

-- accountant: financial reporting
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.permission_id
FROM roles r JOIN permissions p
WHERE r.role_key = 'accountant'
  AND p.permission_key IN (
    'asset.view','rate.view','report.view','report.export')
;

-- budget_officer: budget oversight (no export)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.permission_id
FROM roles r JOIN permissions p
WHERE r.role_key = 'budget_officer'
  AND p.permission_key IN (
    'asset.view','rate.view','report.view')
;

-- sia: audit read + export
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.permission_id
FROM roles r JOIN permissions p
WHERE r.role_key = 'sia'
  AND p.permission_key IN (
    'asset.view','report.view','report.export')
;

-- director_finance: executive oversight + export
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.permission_id
FROM roles r JOIN permissions p
WHERE r.role_key = 'director_finance'
  AND p.permission_key IN (
    'asset.view','rate.view','report.view','report.export')
;

-- system_admin: everything
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.permission_id
FROM roles r JOIN permissions p
WHERE r.role_key = 'system_admin'
;

-- ── User → Role assignments (maps existing 6 admin_logs accounts) ─
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`)
SELECT al.table_id, r.role_id
FROM admin_logs al
JOIN roles r ON r.role_key = CASE al.user_role
    WHEN 'S/O'        THEN 'schedule_officer'
    WHEN 'DSU'        THEN 'dsu'
    WHEN 'Accountant' THEN 'accountant'
    WHEN 'B/O'        THEN 'budget_officer'
    WHEN 'SIA'        THEN 'sia'
    WHEN 'D/F'        THEN 'director_finance'
    WHEN 'ICT'        THEN 'system_admin'
    ELSE NULL
END;

-- ════════════════════════════════════════════════════════════════
-- Verification queries (optional — run to confirm seed)
-- ════════════════════════════════════════════════════════════════
-- SELECT al.username, r.role_name
--   FROM admin_logs al
--   JOIN user_roles ur ON ur.user_id = al.table_id
--   JOIN roles r       ON r.role_id  = ur.role_id;
--
-- SELECT r.role_name, COUNT(rp.permission_id) AS permission_count
--   FROM roles r
--   LEFT JOIN role_permissions rp ON rp.role_id = r.role_id
--   GROUP BY r.role_id ORDER BY permission_count DESC;
