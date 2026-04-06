-- =============================================================================
-- Seed: asset_classes + asset_type + asset_location defaults
-- Run after 001_initial_schema.sql on a fresh install
-- =============================================================================

USE asset_register_new;

-- Default asset types
INSERT IGNORE INTO `asset_type` (`asset_type`) VALUES ('Owned'), ('Leased');

-- Default asset classes (from live data)
INSERT IGNORE INTO `asset_classes`
    (`asset_class`, `dep_rate`, `estimated_life`, `depreciation`, `depreciated`)
VALUES
    ('Motor Vehicles',            0.20,  5,  0, 1),
    ('Land And Buildings',        0.02, 50,  0, 1),
    ('Computer & Accessories',    0.10, 10,  0, 1),
    ('Furnitures & Fixtures',     0.10, 10,  0, 1),
    ('Office Equipment',          0.20,  5,  0, 1),
    ('Machine And Equipment',     0.20,  5,  0, 1),
    ('Teaching Equipment',        0.20,  5,  0, 1),
    ('Building Works in Progress',0.00, 50,  1, 0),
    ('Library Books',             0.20,  5,  0, 1),
    ('Bridge Simulator',          0.20,  5,  0, 1),
    ('GMDSS Simulator',           0.20,  5,  0, 1),
    ('Forklift',                  0.20,  5,  0, 1),
    ('Refurbished Road',          0.065,15,  0, 1),
    ('Solar Equipment',           0.25,  4,  0, 1),
    ('Swimming Pool',             0.10, 10,  0, 1),
    ('School Band',               0.30,  3,  0, 1),
    ('Stanchion Base',            0.20,  5,  0, 1),
    ('Academic Gown',             0.20,  5,  0, 1),
    ('Talif V-Sat Project',       0.20,  5,  0, 1);

-- Default users (password = 'password' hashed — CHANGE BEFORE GOING LIVE)
-- Hash of 'password': $2y$12$... (bcrypt cost 12)
INSERT IGNORE INTO `users` (`username`, `display_name`, `role`, `password_hash`) VALUES
    ('hod_ict',         'HOD ICT',          'hod_ict',         '$2y$12$kKpLHOWD5N7pkmz8.xF0N.FhAGP.x0CClBUaE8eNwgcX./FbW.gDa'),
    ('schedule_officer','Schedule Officer',  'schedule_officer','$2y$12$kKpLHOWD5N7pkmz8.xF0N.FhAGP.x0CClBUaE8eNwgcX./FbW.gDa'),
    ('budget_officer',  'Budget Officer',    'budget_officer',  '$2y$12$kKpLHOWD5N7pkmz8.xF0N.FhAGP.x0CClBUaE8eNwgcX./FbW.gDa'),
    ('accountant',      'Accountant',        'accountant',      '$2y$12$kKpLHOWD5N7pkmz8.xF0N.FhAGP.x0CClBUaE8eNwgcX./FbW.gDa'),
    ('director_finance','Director Finance',  'director_finance','$2y$12$kKpLHOWD5N7pkmz8.xF0N.FhAGP.x0CClBUaE8eNwgcX./FbW.gDa'),
    ('sia',             'Senior Internal Auditor','sia',         '$2y$12$kKpLHOWD5N7pkmz8.xF0N.FhAGP.x0CClBUaE8eNwgcX./FbW.gDa'),
    ('dsu',             'DSU',               'dsu',             '$2y$12$kKpLHOWD5N7pkmz8.xF0N.FhAGP.x0CClBUaE8eNwgcX./FbW.gDa');
