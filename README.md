# RMU Asset Register

A web-based Fixed Asset Management System for **Regional Maritime University (RMU)**, built to track, manage, value, and report on all institutional assets across multiple financial roles.

---

## Table of Contents

1. [What the System Does](#1-what-the-system-does)
2. [How It Works](#2-how-it-works)
3. [User Roles](#3-user-roles)
4. [Data Model](#4-data-model)
5. [Optimal Approach (Current Stack)](#5-optimal-approach-current-stack)
6. [Known Issues — Application Layer](#6-known-issues--application-layer)
7. [Known Issues — Auto-Calculations & Database Layer](#7-known-issues--auto-calculations--database-layer)
8. [Known Issues — Reports Hub & Asset Summary](#8-known-issues--reports-hub--asset-summary)
9. [Version 2 — Complete Overhaul Plan](#9-version-2--complete-overhaul-plan)

---

## 1. What the System Does

The RMU Asset Register is a **fixed asset lifecycle management platform**. It gives the university's finance and administration teams a single place to:

| Capability | Description |
|---|---|
| **Register assets** | Record every university asset — vehicles, computers, furniture, machinery, buildings — with acquisition details, cost, supplier, location, and assigned user |
| **Track asset lifecycle** | Move assets between locations, transfer between users, archive inactive items, and formally dispose of write-offs |
| **Depreciation management** | Calculate and report on annual depreciation per asset class using configurable rates and estimated lifespans |
| **Multi-currency valuation** | Store historical cost in GHS (cedis) and maintain USD equivalents using a managed exchange rate (dollar rate) |
| **Report generation** | Produce financial-grade reports: all assets, assets by class, accumulation of depreciation, disposals, year additions, historical cost schedules, and individual asset ledgers |
| **Supplier management** | Maintain a register of vendors and archive or remove them |
| **Untracked asset log** | Record assets that exist on campus but haven't been formally registered yet |
| **Role-based access** | Each department (Finance, Audit, Budget, DSU) sees only what is relevant to their function |

---

## 2. How It Works

### Technology Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.x (procedural, `mysqli` extension) |
| Database | MySQL 8.x (via WAMP/phpMyAdmin locally) |
| Frontend | HTML5, CSS3, Bootstrap 4, vanilla JavaScript |
| PDF/Excel export | TCPDF, PhpSpreadsheet (via Composer) |
| Asset ID generation | Custom PHP + JS composite key (`RMU / DEPT / SUBCLASS / COUNTER / YEAR`) |

### Application Flow

```
Browser Request
    │
    ├─ /asset_register/
    │       └─ index.php  ──── landing / redirect page
    │
    ├─ /{role}/login/
    │       ├─ index.html          ── login form
    │       ├─ login.inc.php       ── authenticates against admin_logs table,
    │       │                         sets $_SESSION['username'], redirects to dashboard
    │       └─ forgot_password/    ── generates a 4-digit reset code, emails it
    │
    ├─ /{role}/dashboard/
    │       └─ index.php           ── session-guarded; shows KPI summary cards
    │
    ├─ /{role}/{feature}/
    │       ├─ index.php           ── view/form page (session-guarded)
    │       └─ index.inc.php       ── form processor (POST handler, DB writes)
    │
    ├─ /{role}/reports/{report-type}/
    │       └─ index.php           ── SQL aggregation + HTML table or PDF/Excel export
    │
    └─ /{role}/logout/
            └─ index.php           ── destroys session, redirects to login
```

### Asset Lifecycle

```
Add New Asset  ──►  Active Assets  ──►  Move (location/user transfer)
                          │
                    ┌─────┴─────┐
                    ▼           ▼
                 Archive     Dispose
               (removable) (permanent write-off → disposals table)
```

### Depreciation Logic

Each **asset class** carries a `dep_rate` (percentage) and `estimated_life` (years). When a depreciation calculation is triggered:

```
Asset Cost Closing Balance  = Opening Balance + Additions − Disposals
Depreciation Cost           = Closing Balance × dep_rate
Total Accumulated Depr.     = Opening Accum. Depr. + Depreciation Cost
Closing Carrying Value      = Closing Balance − Accum. Depr. Closing Balance
```

---

## 3. User Roles

| Role | Login Table | Primary Database | Key Capabilities |
|---|---|---|---|
| **Schedule Officer** | `admin_logs` | `asset_register_new` | Full asset CRUD, suppliers, locations, users, dollar rate, reports |
| **DSU** (Dean of Students Unit) | `admin_logs` | `asset_register` | View asset locations, sub-classes, limited reports |
| **SIA** (Senior Internal Auditor) | `admin_logs` | `asset_register` | Audit-level read access, view all assets |
| **Accountant** | `admin_logs` | `asset_register` | Financial reports, depreciation schedules |
| **Budget Officer** | `admin_logs` | `asset_register` | Budget-aligned asset and report views |
| **Director of Finance** | `admin_logs` | `asset_register` | Oversight dashboard |

---

## 4. Data Model

### Core Tables

```
admin_logs          ── system user accounts (username, hashed password, role)
assets              ── primary asset ledger
assets_archive      ── soft-deleted / retired assets
asset_classes       ── categories (Motor Vehicles, Computers, etc.) with dep_rate
asset_class_sub_classes ── sub-categories per class
asset_type          ── Owned / Leased / etc.
asset_location      ── physical locations on campus
asset_users         ── staff members (first name, last name, department)
suppliers           ── vendors
disposals           ── formal write-off records
moved_assets        ── movement audit trail (who moved what, when, from/to)
dollar_rate         ── exchange rate history (one ACTIVE record at a time)
calculations        ── computed depreciation values per asset
asset_additions_year── annual totals per class (GHS + USD)
password_reset_code ── one-time codes for password resets
```

### Key Relationships

```
assets.asset_class  ──► asset_classes.asset_class
assets.sub_class    ──► asset_class_sub_classes.sub_class
assets.asset_type   ──► asset_type.asset_type
assets.location     ──► asset_location.location
assets.user         ──► asset_users (composite: first_name + last_name)
assets.supplier_name──► suppliers.name
assets.dollar_rate_used ──► dollar_rate.dollar_rate (at time of entry)
```

---

## 5. Optimal Approach (Current Stack)

These are targeted improvements that keep the system within PHP + MySQL + Bootstrap without rewriting from scratch. They fix the architecture without introducing new technology.

### 5.1 Consolidate Configuration

Create a single shared config file at the project root and include it everywhere. Eliminate the per-folder `datacon.php` copies.

```
/asset_register/
    config.php          ← ONE place: DB host, name, user, password, base URL
    auth.php            ← session_start() + role guard + redirect (include at top of every protected page)
    db.php              ← returns a shared $conn, checks connection once
```

Every role's current `datacon.php` becomes `include '../../config.php';`.

### 5.2 Centralise Authentication

Move session handling into `auth.php`. Every protected page starts with:

```php
require_once '../../auth.php';   // starts session, checks $_SESSION['username'], redirects if not set
```

This replaces the copy-pasted `session_start(); if (!isset($_SESSION['username'])) { ... }` block found in every single page.

### 5.3 Sanitise All Inputs with Prepared Statements

Replace all string-interpolated SQL queries with `mysqli_prepare` + `bind_param`. The pattern to use everywhere:

```php
$stmt = mysqli_prepare($conn, "SELECT * FROM assets WHERE asset_class = ?");
mysqli_stmt_bind_param($stmt, "s", $assetClassFilter);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
```

### 5.4 Standardise Error Handling

Never echo `mysqli_error()` to the browser. Log it server-side:

```php
// config.php (dev only — remove in production)
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');
```

Show the user only a generic message.

### 5.5 Fix Redirects to Use Relative Paths

Replace all hardcoded absolute paths (`/timetableGenerator/...`, `/staff_allowance/...`, `/admin_dashboard/...`) with paths relative to the file or a `BASE_URL` constant defined in `config.php`.

```php
// config.php
define('BASE_URL', '/asset_register');

// usage
header('Location: ' . BASE_URL . '/schedule_officer/login/');
```

### 5.6 Separate Form Logic from Display

Each page that currently mixes PHP processing with HTML output should be split:

```
index.php       ← HTML template only; reads $data set by controller
process.php     ← POST handler; validates, writes DB, redirects (PRG pattern)
```

This prevents the double-submission problem and keeps templates readable.

### 5.7 Folder Structure (Recommended Consolidation)

```
/asset_register/
├── config.php
├── auth.php
├── db.php
├── index.php
│
├── /assets/                    ← shared CSS, JS, images, vendor libs
│   ├── css/
│   ├── js/
│   └── vendor/
│
├── /roles/
│   ├── schedule_officer/
│   │   ├── login/
│   │   ├── dashboard/
│   │   ├── assets/             ← add, view, edit, archive, dispose
│   │   ├── suppliers/
│   │   ├── locations/
│   │   ├── users/
│   │   ├── set_rate/
│   │   └── reports/
│   ├── dsu/
│   ├── sia/
│   ├── accountant/
│   ├── budget_officer/
│   └── director_finance/
│
├── /api/                       ← AJAX endpoint scripts only (get_sub_classes.php etc.)
├── /exports/                   ← PDF and Excel output scripts
└── /logs/                      ← server-side error logs (gitignored)
```

---

## 6. Known Issues — Application Layer

Issues are ranked by impact: **Critical** (breaks a page entirely), **High** (feature non-functional), **Medium** (logic error or security gap), **Low** (code quality).

---

### Critical

| # | File | Line | Issue | Fix |
|---|---|---|---|---|
| C1 | `schedule_officer/view_assets/index.php` | 195 | **PHP parse error** — `$rate_used =` is an incomplete statement with no value. The entire page (Edit, Move, Archive, Dispose, asset table) throws a fatal error and returns blank. | `$rate_used = $rowAdditions['dollar_rate_used'];` |
| C2 | `{role}/login/forgot_password/forgotPassword.inc.php` | 12, 17, 25 | **Forgot password completely broken** — reads `$_POST['role']` but form sends `email_address`; `$email_address` is never assigned so the empty-check always triggers; SQL is `SELECT  FROM ...` (missing column list — syntax error). Affects both DSU and Schedule Officer. | Assign `$email_address = mysqli_real_escape_string($conn, $_POST['email_address']);`; fix SQL to `SELECT code_status FROM password_reset_code ...` |
| C3 | `{role}/login/forgot_password/forgotPassword.inc.php` | 52, 59, 85, 92, 106 | **All redirects point to `/timetableGenerator/...`** — wrong application, copy-paste residue. Password reset flow lands the user in a 404. | Replace with correct relative path for each role (e.g., `../../index.html` or `BASE_URL . '/schedule_officer/login/forgot_password/'`) |
| C4 | `{role}/login/forgot_password/login.inc.php` | 55 | **Session variable mismatch** — sets `$_SESSION['user_name']` (with underscore) but every dashboard checks `$_SESSION['username']` (no underscore). User is immediately bounced to login after the forgot-password flow succeeds. | `$_SESSION['username'] = $user_name;` |

---

### High

| # | File | Line | Issue | Fix |
|---|---|---|---|---|
| H1 | All `{role}/login/login.inc.php` files | 75–76 | **Wrong `else` redirect** — all six role login scripts redirect to `SIA/login/` when the page is accessed without a POST. All non-SIA roles send users to the wrong login page. | Remove or change to `header('Location: index.html');` for each respective role |
| H2 | `schedule_officer/datacon.php` vs `DSU/datacon.php` | — | **Database name mismatch** — Schedule Officer connects to `asset_register_new`; DSU, Accountant, Budget Officer, Director of Finance connect to `asset_register`. The two roles see different data. | Standardise all roles to one database; use a single `config.php` |
| H3 | `accountant/datacon.php` | 4 | **Hardcoded password `abokoma`** in source code. Any developer who reads the repo can access the production database. | Move all credentials to a `.env` file or server environment variables; add `.env` to `.gitignore` |
| H4 | `schedule_officer/sidebar.html` | 33, 122 | **Duplicate `id="assetsDropdown"`** — the "Assets" and "Archives" dropdown menus share the same Bootstrap collapse ID. Clicking "Archives" opens the Assets menu instead; Archives is inaccessible. | Change the Archives dropdown to `id="archivesDropdown"` and update its `href` accordingly |
| H5 | `schedule_officer/set_rate/index.php` | 159–166 | **Table rows missing `<tr>` tags** — the dollar rate history loop outputs `<td>` cells without any `<tr>` wrapper, and outputs `</form></tr>` (neither of which are opened). Table is malformed. | Add `echo "<tr>";` before each row and `echo "</tr>";` after; remove `</form>` |
| H6 | `schedule_officer/add_user/index.inc.php` | 17–19 | **No redirect after adding a user** — outputs plain text "User added successfully" to a blank page; user is stranded with no navigation. | Replace with `echo "<script>alert('User added successfully'); window.location='index.php';</script>";` |
| H7 | `schedule_officer/new_asset/index.php` | 136 | **Extra `</select>` closing tag** — the supplier dropdown already echoes `</select>` inside PHP, then another `</select>` appears in the raw HTML. Form structure is malformed; subsequent fields may not submit correctly. | Remove the extra `</select>` at line 136 |
| H8 | `schedule_officer/asset_location/process_add_location.php` | 22 | **Wrong redirect after adding location** — sends user to `../dashboard/` instead of back to the locations page. | `window.location='../view_asset_location/';` |
| H9 | `schedule_officer/calculations/view_calculations.php` | — | **No authentication guard** — page is publicly accessible without a valid session. | Add `session_start(); if (!isset($_SESSION['username'])) { header("Location:../login/"); die(); }` |
| H10 | `schedule_officer/set_rate/uploadexcel.php` | 46 | **Inserts into wrong table** — Excel upload writes to `lecturers_data` (a timetable generator table), not any asset register table. The feature does nothing useful for this system. | Rewrite to parse and insert asset data, or remove the feature entirely |
| H11 | All pages, all roles | — | **"Change Password" links are broken** — all navbar "Change Password" / "User Settings" links point to `/admin_dashboard/change_password/` or `/staff_allowance/change_password/` — neither exists in this app. No change-password feature is built. | Build a minimal change-password page per role or remove the links |
| H12 | `schedule_officer/asset_classes/process_class.php` | 2–9 | **Own hardcoded DB connection** — instead of `include "../datacon.php"`, this file opens its own `mysqli` connection with hardcoded credentials. Diverges from the rest of the codebase. | Replace with `include "../datacon.php";` |
| H13 | `schedule_officer/asset_classes/process_class.php` | 25 | **Year hardcoded as `2024`** — all new asset classes are stamped with the wrong year. | `$year = date('Y');` |

---

### Medium

| # | File | Line | Issue | Fix |
|---|---|---|---|---|
| M1 | `{role}/login/forgot_password/login.inc.php` | 22, 33, 46 | Error redirects inside forgot-password login still go to `/timetableGenerator/login/` | Replace with correct role login path |
| M2 | `schedule_officer/new_asset/index.php` | 65–107 | Two sub-class dropdowns rendered simultaneously — one POST-only (named `asset_sub_class`), one always-visible (named `asset_class_sub_classes`). Only the second is submitted; first is dead weight and confuses users | Remove the redundant first dropdown; keep and improve the always-visible one with AJAX filtering by asset class |
| M3 | `schedule_officer/new_asset/index.inc.php` | 53 | Lifespan calculation: `strtotime($acquisitionDate)-1` subtracts one second, not one year — can produce wrong year boundary | `$lifespanYears = $currentYear - date('Y', strtotime($acquisitionDate)) + 1;` |
| M4 | `schedule_officer/reports/index.php` | 101 | Loads non-existent `../static/chart.min.js` alongside CDN Chart.js — the local 404 may produce console noise | Remove the broken local reference; use only the CDN version |
| M5 | `DSU/view_asset_location/index.inc.php` | 6 | Unauthenticated users redirected to `/staff_allowance/login/` — wrong application | `header("Location: ../login/");` |
| M6 | Multiple files | — | SQL injection vectors: `asset_location/process_add_location.php` line 19, `asset_classes/process_class.php` lines 19–21, `view_assets/index.php` line 381, `calculations/view_calculations.php` line 10, `new_asset/index.inc.php` line 121 — user input interpolated directly into SQL strings | Use prepared statements throughout (see §5.3) |
| M7 | `schedule_officer/new_asset/index.php` | 130–134 | `do-while` loop for suppliers, locations, types, and users will crash with a PHP warning if any table is empty (accesses `$row` when `mysqli_fetch_array` returns `false`) | Replace `do { } while()` with standard `while ($row = mysqli_fetch_array($result)) { }` |
| M8 | Multiple files | — | `mysqli_escape_string()` is deprecated; `mysqli_real_escape_string()` is the correct function — and even that is not sufficient without prepared statements | Replace deprecated calls; migrate to prepared statements |
| M9 | `schedule_officer/new_asset/index.php` | 171–173 | Dead `XMLHttpRequest` with empty target URL (`xhr.open('POST', '', true)`) that is never sent | Remove |
| M10 | `schedule_officer/reports/index.php` | 224 | Links to `index2.php` which does not exist in the reports directory | Point to the correct report sub-page |
| M11 | Multiple files | — | `mysqli_error($conn)` echoed directly to the browser (view_assets line 42, disposals line 49, forgotPassword line 49) — leaks schema details to users | Log server-side; show generic user message |

---

### Low

| # | File | Line | Issue |
|---|---|---|---|
| L1 | `schedule_officer/dashboard/index.php` | 47 | `$dollar_rate = $dollar_rate = $row['dollar_rate'];` — double assignment |
| L2 | `schedule_officer/new_asset/index.php` | 20 | Uses `$_SESSION` without `session_start()` at top of file |
| L3 | `schedule_officer/add_user/index.php` | — | No session guard; page accessible without login |
| L4 | `schedule_officer/new_asset/update_array.php` | 38 | `dept_subclass_counter` is never incremented; all generated asset IDs end in `0` |
| L5 | `schedule_officer/dashboard/index.php` | 101 | Navbar "Change Password" uses a commented-out malformed `</a>` producing invalid HTML (`</a>` appears twice) |
| L6 | Various | — | Many pages include `../datacon.php` twice (once at top, once inside a PHP block mid-page) |

---

---

## 7. Known Issues — Auto-Calculations & Database Layer

The system has four layers of calculation logic: MySQL generated columns, the per-asset depreciation script, the data-maintenance layer that feeds reports, and the report calculation engine. Every layer has problems that compound each other.

---

### Layer 1 — MySQL Virtual/Generated Columns

**`assets.additions_dollar` — Division by zero**
```sql
GENERATED ALWAYS AS ((additions / dollar_rate_used)) VIRTUAL
```
Assets in the database with `dollar_rate_used = 0` (at least two exist in current data: records 165 and 170) cause MySQL to silently return `NULL` for the generated value. No error surfaces — the USD column just shows blank for those assets. Every report that uses `additions_dollar` silently understates totals by those amounts.

**Fix:** `IF(dollar_rate_used = 0, NULL, additions / dollar_rate_used)`

---

**`disposals.disposal_depreciation` — Always evaluates to zero**
```sql
GENERATED ALWAYS AS (((additions / estimated_life_months) * disposal_month)) VIRTUAL
```
Both `estimated_life_months` and `disposal_month` default to `0` and are **never populated** by the disposal INSERT in `view_assets/index.php`. That INSERT does not include either column:
```php
$sql = "INSERT INTO disposals (asset_name, asset_class, ..., disposal_value) VALUES (?, ...)";
// disposal_month and estimated_life_months are missing
```
So `disposal_depreciation` evaluates to `(cost / 0) * 0` → `NULL` or `0` for every disposal ever recorded. The field, the formula, and the table column all exist but the data pipeline never feeds them. Disposal depreciation figures across all financial reports are wrong.

**Fix:** Include `disposal_month` (the integer month of disposal, 1–12) and `estimated_life_months` (from the asset class) in the disposal INSERT.

---

**`asset_class_opbal_year.net_book_value` — Ignores current-year additions**
```sql
GENERATED ALWAYS AS ((opening_balance - total_accum_depr_end)) VIRTUAL
```
`opening_balance` is set at the start of the year and never updated mid-year. New assets added during the year increase the real cost base of the class, but this generated column only ever reflects the position as of 1 January. As a result, Net Book Value in the Asset Summary report is **understated by the full value of every mid-year addition**.

**Fix:** Net Book Value must be computed dynamically: `(opening_balance + additions_for_year - disposals_for_year) - total_accum_depr_end`, not as a static generated column.

---

### Layer 2 — `view_calculations.php` and `reports/index2.php` (Per-Asset Calculations)

These two files share identical calculation logic and identical bugs.

**Bug 1 — Wrong base for opening balance**
```php
$assetCostOpeningBalance = $rowAsset['active_res_value'];  // line 197 in index2.php, line 35 in view_calculations
```
`active_res_value` is the **residual/salvage value** — what the asset is worth at the end of its life. For the vast majority of current assets it is `0.00`. Using it as the cost opening balance means every calculation chain that follows starts from zero or the wrong number. The correct source is the historical cost (`additions` column) or the class `opening_bal`.

**Bug 2 — Disposal flag treated as money**
```php
$assetCostClosingBalance = $assetCostOpeningBalance + $rowAsset['additions'] - $rowAsset['disposals'];
```
`$rowAsset['disposals']` is the integer flag `0` (not disposed) or `1` (disposed). It is not a monetary amount. Subtracting the integer 1 shaves one cedi off the closing balance, which is meaningless. The actual disposal value should come from joining with the `disposals` table.

**Bug 3 — Accumulated depreciation closing balance formula is inverted**
```php
$accountDepreciationClosingBalance = $totalAccumulatedDepreciation - $rowAsset['active_res_value'];
```
Standard accounting: `Accumulated Depreciation (Closing) = Accumulated Depreciation (Opening) + Depreciation Charge for Year`. Subtracting the residual value from the accumulated depreciation has no basis in the depreciation schedule methodology. This produces wrong closing balance figures throughout.

**Bug 4 — `view_calculations.php` INSERT omits all NOT NULL columns**

The INSERT into the `calculations` table does not include `asset_id`, `asset_name`, `asset_class`, `asset_type`, `location`, `acquisition_date`, `date_added`, or `year` — all of which are `NOT NULL`. MySQL rejects the INSERT. The calculation results are shown to the user but **never persisted**. The table data visible in the database is from a previous, different system import. Each page load also has no UPSERT logic — even if the INSERT worked, it would append a new duplicate row every single time.

**Bug 5 — `acc_depr_opening_bal` queried from `other_values` table inside a per-asset loop**

A table called `other_values` is queried once per asset with no WHERE clause to get the accumulated depreciation opening balance. This is used as a global value shared across all assets of all classes, which makes no sense — accumulated depreciation is specific to each asset or asset class. Additionally, querying this table N times inside a loop (N+1 problem) means a page with 176 assets fires 176 extra database queries for this one value.

---

### Layer 3 — Data Maintenance (New Asset / New Asset Class)

**`new_asset/index.inc.php` — `asset_additions_year` never updated**

When a new asset is added, the code correctly updates `opbal_plus_additions` in `asset_classes`. But it never touches `asset_additions_year`. The Asset Summary report JOINs `asset_class_opbal_year` with `asset_additions_year` on both class name and year — if no row exists in `asset_additions_year` for the current year and class, that class **disappears entirely from the summary table**. The `asset_additions_year` table is effectively a hand-maintained static log that goes stale the moment any asset is added via the UI.

**`process_class.php` — Wrong column names in `asset_class_opbal_year` INSERT**

When a new asset class is created, the code inserts a year record:
```php
INSERT INTO asset_class_opbal_year
    (asset_class, year, opening_bal, total_accum_depr_start, estimated_life_months, rate)
    VALUES (...)
```

| Code column | Actual table column | Result |
|---|---|---|
| `opening_bal` | `opening_balance` | Column not found → SQL ERROR |
| `estimated_life_months` | `expected_life_months` | Column not found → SQL ERROR |
| `total_accum_depr_start` | receives `$estimated_life_months` value | Wrong value even if column name were fixed |

This INSERT **always fails silently**. No year row is ever created when you add a new asset class through the UI. Since all depreciation schedule reports require a matching `asset_class_opbal_year` row, every new asset class added after initial setup is invisible in every financial report.

---

### Layer 4 — Report Calculation Engine (`functions.php`)

**Division by zero in "between-years" depreciation (line 735)**
```php
"newDepreciationExpense" => $newMonthlyDepreciation * $monthsBetweenFirstAndLastYear
                            / (intdiv($monthsBetweenFirstAndLastYear, $monthsInYear))
```
`intdiv($monthsBetweenFirstAndLastYear, 12)` returns `0` when `$monthsBetweenFirstAndLastYear < 12`. This happens for any asset with a lifespan short enough that there are fewer than 12 months between its first and last depreciation year. PHP throws a **Division by zero** fatal error and the report page crashes entirely for such assets.

**Monthly display stored in wrong variable for "between-years" (line 832)**
```php
// Inside the "between" years branch:
$lastYearData["monthsDisplay"][$month] = "-";   // BUG: should be $yearsBetweenData
```
The per-month depreciation display is written to `$lastYearData["monthsDisplay"]` instead of `$yearsBetweenData["monthsDisplay"]`. When `displayData()` tries to render the monthly columns for a "between" year asset it iterates over an empty array — **those 12 monthly depreciation columns are always blank** for any asset in a mid-life year.

**Grand total double-counts additions (line 1010)**
```php
$g_totalAdditions = ($assetClassData['asset_class_info']['opening_balance']
                    - $assetClassData['total_disposals']
                    + $assetData["assetsTotals"]["totalAdditions"]);
```
`opening_balance` from `asset_class_opbal_year` already incorporates all historical additions for that class. Adding `totalAdditions` (the sum of `additions` from individual asset rows for the selected year) on top counts the current year's acquisitions twice. The Grand Total row in every class depreciation report is inflated.

**PDO mixed with `mysqli` — hardcoded credentials (line 102)**
```php
$dm = new DatabaseMethods("asset_register_new", "root");  // PDO instance
```
The `getUntrackedAssetsByYear()` function opens a separate PDO connection with a hardcoded database name and username, while everything else uses `mysqli` via `datacon.php`. This is a second authentication path that bypasses central config, breaks when credentials change, and mixes two incompatible PHP database APIs in the same request.

---

### Auto-Calculation Issue Summary

| # | Layer | What's broken | Visible effect |
|---|---|---|---|
| AC1 | Generated column | `additions_dollar` ÷ 0 when rate is 0 | USD value is NULL for those assets; totals understated |
| AC2 | Generated column | `disposal_depreciation` inputs never set | All disposal depreciation figures are 0 or NULL |
| AC3 | Generated column | `net_book_value` ignores current-year additions | NBV understated by all mid-year acquisitions |
| AC4 | PHP calc | `active_res_value` used as cost opening balance | All per-asset depreciation chains start from wrong number |
| AC5 | PHP calc | `disposals` flag (0/1) subtracted as monetary amount | Closing balance off by 0 or 1 cedi |
| AC6 | PHP calc | `accountDepreciationClosingBalance` formula inverted | Accumulated depreciation figures incorrect |
| AC7 | PHP calc | `view_calculations.php` INSERT omits NOT NULL columns | Nothing ever saved to calculations table |
| AC8 | PHP calc | `acc_depr_opening_bal` queried N times in a loop | 176+ extra DB queries per page; shared-class value applied per-asset |
| AC9 | Data maint. | `asset_additions_year` never updated on asset add | Asset classes disappear from summary reports after new assets are added |
| AC10 | Data maint. | `process_class.php` INSERT uses wrong column names | New asset classes never get year records; invisible in all financial reports |
| AC11 | Report engine | `intdiv` divisor can be 0 | Division by zero crash on short-lifespan assets |
| AC12 | Report engine | Monthly display stored in wrong variable | 12 monthly columns always blank for mid-life assets |
| AC13 | Report engine | Grand total adds opening_balance + year additions | Grand total row is double-counted / inflated |
| AC14 | Report engine | PDO + mysqli mixed, hardcoded credentials | Fragile dual-connection; breaks on credential change |

---

## 8. Known Issues — Reports Hub & Asset Summary

---

### 8.1 Reports Hub (`reports/index.php`) — Chart & Asset Class Cards

**Undefined PHP variables crash the chart JS**

The chart data arrays are only initialised inside a conditional:
```php
if ($resultClassQuery->num_rows > 0) {
    $assetClassData = [];
    $openingBalanceData = [];
    $opBalPlusAdditionsData = [];
    // ...populated here
}
```
Below that, unconditionally in the `<script>` block:
```javascript
const assetClasses = <?php echo json_encode($assetClassData) ?>;
```
If the query returns zero rows or fails, `$assetClassData` is undefined and PHP emits a notice that gets injected directly into the JavaScript string — **breaking the entire script block**. The chart and all the class cards below it fail to render.

**Fix:** Initialise all three arrays to `[]` before the `if` block.

---

**Broken local Chart.js path + CDN loaded simultaneously**

```html
<script src='../static/chart.min.js'></script>   <!-- 404 — path does not exist -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  <!-- loads fine -->
```
The 404 on the local path generates a console error on every page load. Remove the non-existent local reference; keep only the CDN version (or bundle it properly in `assets/`).

---

**Asset class cards link to `index2.php` which has no sidebar or navigation**

Each asset class card:
```php
echo "<a href=index2.php/?asset_class=" . urlencode($row['asset_class']).">";
```
`index2.php` is a bare page with no navbar, no sidebar, and no session check. It loads the per-asset calculation table but the user has no way to navigate back to the reports hub except the single "Return to Dashboard" button (which goes to `../index.php` — the reports hub, not the main dashboard). The page is also missing the `?` separator in the URL — `index2.php/?asset_class=` should be `index2.php?asset_class=`.

---

**Asset class filter on `reports/index.php` is unescaped**

```php
$filter = " WHERE asset_class = '" . $conn->real_escape_string($_GET['asset_class']) . "'";
```
This is escaped, which is good — but when no filter is provided, `$filter = "WHERE 1"` (no leading space), so the full query becomes `SELECT * FROM assets  WHERE 1 ORDER BY...`. This works but reveals a mismatched approach — half the branches use a leading space, one does not. Minor but a flag for future issues.

---

### 8.2 `reports/index2.php` — Per-Asset Class Calculations Page

**Assignment instead of comparison crashes the heading logic (line 127)**
```php
} else if (($selectedAssetSubClass = "")) {
```
This uses `=` (assignment) not `==` (comparison). PHP assigns empty string to `$selectedAssetSubClass` and evaluates the condition as `false`. The heading branch for "no sub-class selected" is never reached. More critically, **it resets `$selectedAssetSubClass` to `""` after it was already set from POST**, so the sub-class filter query always falls back to "All Assets" regardless of what the user selected. The sub-class filter appears to work visually (the dropdown shows the selection) but has no effect on the results.

---

**Debug `echo` left in production (line 80)**
```php
echo $selectedAssetSubClass;
```
This prints the raw selected sub-class value directly into the page HTML before the table, visible to users.

---

**Accumulated Depreciation Opening Balance column is always blank (line 275)**
```php
echo "<td class='py-2 px-4 border-b'>" . "" . "</td>";
```
The column header says "Accumulated Depr. Opening Balance" but the cell always outputs an empty string. This is a placeholder that was never wired up. The value should come from the asset class `account_depr_open_bal` field.

---

**Totals row uses `isset()` multiplication (line 310)**
```php
$t_class_dep_cost = isset($a_opening_bal) * isset($a_class_depr_charge);
```
`isset()` returns a boolean (1 or 0). Multiplying two booleans gives `1 * 1 = 1` or some zero variant — not the actual calculated depreciation cost. This variable is also never used anywhere after this line.

---

**Totals row double-adds last-row values (lines 326–331)**
The totals variables are accumulated correctly inside the `while` loop per asset. But in the totals row output, each total is further added to the value from the last loop iteration:
```php
// Opening balance total adds the loop variable from the last row again
number_format($totalAssetCostOpBal + $a_asset_cost_op_bal, 2)

// Closing balance total adds the class opening balance on top
number_format($a_opening_bal + $totalAssetCostCloseBal, 2)

// Closing carrying value adds last-row value again
number_format($totalCloseCarryValue + $a_closing_carry_value, 2)
```
Every total column in the footer row is inflated by one extra iteration's worth of data.

---

**N+1 database query problem**

Inside the main `while ($row = mysqli_fetch_assoc($sqlClassResult))` loop, for each asset the code fires three additional queries:
1. `SELECT * FROM assets WHERE asset_id = $assetId` — redundant, `$row` already has all this data from the outer JOIN query
2. `SELECT opening_bal, opbal_plus_additions, estimated_life FROM asset_classes WHERE asset_class = ...` — same class for every asset in the loop; should be queried once
3. `SELECT dep_rate FROM asset_classes WHERE asset_class = ...` — same class, same issue
4. `SELECT acc_depr_opening_bal FROM other_values` — a global value, no WHERE clause, queried once per asset

For 176 assets, this is up to **704 redundant queries per page load**. All of this data is either already in `$row` (from the JOIN) or constant across the loop.

---

**Division by zero on USD conversion (line 284)**
```php
$totalDollarAdditions += ($row['additions'] / $row['dollar_rate_used']);
```
Assets with `dollar_rate_used = 0` cause a PHP division by zero warning. The `$totalDollarAdditions` variable is then never displayed anywhere in the output — it is computed but unused.

---

### 8.3 Asset Summary GHS (`reports/asset_summary/`)

**No session check on `index.php`**

`asset_summary/index.php` has no `session_start()` and no `$_SESSION['username']` guard. The page is publicly accessible without logging in.

---

**Year range hardcoded from 2024**
```php
$years = range(2024, $year);
```
The dropdown only shows years from 2024 onwards. Historical reports for 2023 and earlier (which the `asset_class_opbal_year` table has data for) are inaccessible.

---

**Page loads with empty content — no default year shown**

On initial page load, `#content` is empty. There is no default data. The user must interact with the year dropdown first to see anything. There is no indication that they need to do this — the page appears broken.

---

**`CSS transform: scale(0.85)` breaks horizontal scroll**
```css
#content { transform: scale(0.85); transform-origin: top left; }
```
CSS transform does not reduce the element's layout box — the content still occupies its full width in the flow. Applying `scale(0.85)` makes the content appear smaller but the scrollbar range stays the same size as the unscaled content, so horizontal scrolling on wide tables is broken. Mouse click coordinates are also offset by the scale factor, meaning buttons inside the scaled area do not respond correctly at the visual click position.

---

**Bootstrap version mismatch**
```html
<!-- CSS: 5.3.3 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/...">
<!-- JS: 5.3.0 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/..."></script>
```
CSS and JS are on different Bootstrap 5 minor versions. No breakage today but components that changed between versions could behave unexpectedly.

---

**Empty year selection sends blank to `assetClassSummary()`**

If the user selects the "--Select Year--" placeholder, an empty string is POSTed and `assetClassSummary('')` queries `WHERE a.year = ''`. MySQL returns no rows; the function returns an empty array; `foreach` loops over nothing and outputs an empty table with only headers. There is no user message explaining that a year must be selected.

---

**`assetClassSummary()` uses string interpolation in SQL**
```php
$assetClassSummarySql = "... WHERE a.year = '{$year}' ...";
```
`$year` comes directly from `$_POST['summaryYear']` with no sanitisation. This is a SQL injection point.

---

### 8.4 Asset Summary USD (`reports/asset_summary_usd/`)

**`assetClassSummary()` queries columns that do not exist in the database**

The USD version's `functions.php` queries:
```sql
SELECT a.open_bal_usd, a.total_accum_start_usd, a.total_depr_year_charge_usd,
       a.total_accum_end_usd, a.disposals_depr_usd, a.net_book_value_usd
FROM asset_class_opbal_year a ...
```
The `asset_class_opbal_year` table has **none of these columns**. The actual columns are `opening_balance`, `total_accum_depr_start`, `total_depr_year_charge`, `total_accum_depr_end`, `disposals_depr`, and `net_book_value`. MySQL returns "Unknown column" for all of them. **The entire USD Asset Summary report page always fails** — it has never returned data since it was written.

---

**Reports Hub & Asset Summary Issue Summary**

| # | File | Line | Issue | Effect |
|---|---|---|---|---|
| R1 | `reports/index.php` | 129–131 | PHP arrays undefined if query returns 0 rows | Chart JS crashes; class cards don't render |
| R2 | `reports/index.php` | 101–102 | Non-existent local Chart.js path + CDN duplicate | 404 console error on every load |
| R3 | `reports/index.php` | 224 | URL missing `?` separator; `index2.php` has no nav | Broken link; user stranded on navigation-less page |
| R4 | `reports/index2.php` | 127 | `=` instead of `==` resets sub-class filter | Sub-class filter never actually applied to results |
| R5 | `reports/index2.php` | 80 | Raw `echo $selectedAssetSubClass` | Debug output visible in page HTML |
| R6 | `reports/index2.php` | 275 | Accumulated Depr. Opening Balance column always blank | Column header present, data always empty |
| R7 | `reports/index2.php` | 310 | `isset()` used for arithmetic | `$t_class_dep_cost` is always 0 or 1, never the real value |
| R8 | `reports/index2.php` | 326–331 | Last-row values added again in totals row | Every total footer cell is inflated |
| R9 | `reports/index2.php` | 168–239 | N+1 query: 3–4 extra queries per asset | Up to 704 extra DB queries for 176 assets |
| R10 | `reports/index2.php` | 284 | Division by zero on `dollar_rate_used = 0` | PHP warning; unused variable |
| R11 | `asset_summary/index.php` | — | No session check | Page accessible without login |
| R12 | `asset_summary/index.php` | 36 | Year range hardcoded from 2024 | Historical reports for 2023 and earlier inaccessible |
| R13 | `asset_summary/index.php` | 49 | `#content` empty on load | Page appears broken; no default display |
| R14 | `asset_summary/index.php` | 55–57 | `transform: scale(0.85)` on content | Broken horizontal scroll; click coordinates offset |
| R15 | `asset_summary/index.php` | 23–26 | Bootstrap CSS 5.3.3 / JS 5.3.0 mismatch | Potential component inconsistencies |
| R16 | `asset_summary/index.php` | 74 | Empty year string sent to SQL | Empty table, no user feedback |
| R17 | `asset_summary/index.php` | 43 | `$year` from POST used in SQL string | SQL injection |
| R18 | `asset_summary_usd/` | — | All SQL columns in USD query don't exist | Entire USD summary always returns MySQL error |

---

## 9. Version 2 — Complete Overhaul Plan

Version 2 is a full ground-up rebuild keeping the same domain logic but using a proper MVC structure, clean folder layout, a component-driven UI, and secure-by-default patterns.

---

### 9.1 Technology Choices

| Concern | V1 (Current) | V2 (Recommended) |
|---|---|---|
| PHP structure | Procedural, mixed with HTML | MVC — controllers, models, views separated |
| Routing | Folder-per-page (dozens of directories) | Single entry point `index.php` + URL router |
| Auth | Copy-pasted session blocks | Middleware class; one call per protected route |
| DB access | Raw `mysqli` with string interpolation | PDO with prepared statements; thin model layer |
| Frontend framework | Bootstrap 4 (mixed versions) | Bootstrap 5 (one version, one copy) |
| UI components | Inline HTML generated by PHP `echo` strings | Separate `.html` / `.php` template partials |
| JS | Scattered `<script>` blocks in every file | One bundled `app.js`; AJAX via `fetch` API |
| Config | Multiple `datacon.php` copies | Single `.env` + one `config.php` |
| Asset pipeline | CDN links mixed with local copies | Single `assets/` folder; versioned |
| Error handling | `die()`, `mysqli_error()` echoed to browser | Try/catch; log to file; user sees generic message |
| Reports / exports | Inline HTML tables + TCPDF | Decoupled export service (PDF + Excel) |

---

### 9.2 Folder Structure

```
/asset_register/
│
├── .env                        ← DB credentials, BASE_URL, mail config (never committed)
├── .env.example                ← template with placeholder values
├── .htaccess                   ← route all requests to index.php
├── composer.json               ← PhpSpreadsheet, TCPDF, PHPMailer, vlucas/phpdotenv
├── index.php                   ← single entry point; boots the app
│
├── /app/
│   ├── /Config/
│   │   └── config.php          ← loads .env; defines constants
│   │
│   ├── /Core/
│   │   ├── Router.php          ← maps URL patterns to controllers
│   │   ├── Database.php        ← PDO singleton
│   │   ├── Auth.php            ← session management; role-based access guards
│   │   ├── Request.php         ← wraps $_POST / $_GET safely
│   │   └── Response.php        ← redirect(), json(), render() helpers
│   │
│   ├── /Models/
│   │   ├── Asset.php
│   │   ├── AssetClass.php
│   │   ├── AssetLocation.php
│   │   ├── AssetUser.php
│   │   ├── Supplier.php
│   │   ├── Disposal.php
│   │   ├── DollarRate.php
│   │   ├── Depreciation.php
│   │   └── User.php            ← admin_logs
│   │
│   ├── /Controllers/
│   │   ├── AuthController.php          ← login, logout, forgot password, reset
│   │   ├── DashboardController.php
│   │   ├── AssetController.php         ← CRUD, archive, dispose, move
│   │   ├── SupplierController.php
│   │   ├── LocationController.php
│   │   ├── UserController.php
│   │   ├── RateController.php
│   │   ├── ReportController.php
│   │   └── ExportController.php        ← PDF and Excel generation
│   │
│   └── /Middleware/
│       ├── AuthMiddleware.php           ← redirects unauthenticated requests
│       └── RoleMiddleware.php          ← blocks users who lack a required role
│
├── /views/
│   ├── /layouts/
│   │   ├── base.php            ← full HTML shell (head, navbar, sidebar, footer)
│   │   ├── sidebar.php         ← nav links; role-aware (shows only permitted links)
│   │   └── navbar.php
│   │
│   ├── /auth/
│   │   ├── login.php
│   │   ├── forgot_password.php
│   │   └── reset_password.php
│   │
│   ├── /dashboard/
│   │   └── index.php
│   │
│   ├── /assets/
│   │   ├── index.php           ← asset list with filter + search
│   │   ├── add.php             ← add new asset form
│   │   ├── edit.php            ← edit modal rendered as a partial
│   │   ├── move.php
│   │   ├── archive.php
│   │   ├── dispose.php
│   │   └── untracked.php
│   │
│   ├── /suppliers/
│   ├── /locations/
│   ├── /users/
│   ├── /rates/
│   ├── /reports/
│   │   ├── index.php           ← report hub with chart
│   │   ├── all_assets.php
│   │   ├── by_class.php
│   │   ├── depreciation.php
│   │   ├── disposals.php
│   │   ├── year_additions.php
│   │   └── individual.php
│   └── /partials/
│       ├── _alerts.php
│       ├── _pagination.php
│       └── _table_empty.php
│
├── /public/                    ← web root (point Apache/Nginx here)
│   ├── index.php               ← symlink or copy of root index.php
│   ├── /css/
│   │   ├── bootstrap.min.css   ← one copy; Bootstrap 5
│   │   └── app.css             ← project overrides only
│   ├── /js/
│   │   ├── bootstrap.bundle.min.js
│   │   ├── chart.umd.min.js    ← one copy of Chart.js
│   │   └── app.js              ← all project JS; uses fetch for AJAX
│   ├── /fonts/
│   └── /images/
│
├── /api/                       ← JSON endpoints for AJAX calls
│   ├── get_subclasses.php      ← returns sub-classes for a given class
│   ├── get_opening_balance.php
│   ├── generate_asset_id.php
│   └── search_assets.php
│
├── /exports/                   ← generated export files (gitignored)
└── /logs/                      ← php_errors.log (gitignored)
```

---

### 9.3 UI & UX Improvements

#### Dashboard
- Replace stat cards with a proper summary bar (total assets, total value GHS, total value USD, disposed count, classes)
- Add a sparkline trend chart per class (Chart.js bar — already partially implemented, just broken)
- Add a "Recent Activity" feed (last 10 additions, moves, disposals)

#### Asset Table (View Active Assets)
- Replace raw `<table>` with a DataTables-enhanced table: client-side search, sort, pagination, export button
- Action buttons (Edit / Move / Archive / Dispose) in one compact dropdown per row instead of four separate buttons
- Filter by class, location, and type simultaneously via a sidebar filter panel

#### Add New Asset Form
- Step-by-step wizard (3 steps: Classification → Details → Financial) instead of a single long form
- Real-time AJAX sub-class population when asset class is selected (already partially built in `get_sub_classes.php` — just wire it correctly)
- Auto-fill dollar rate from the active rate; show the user what rate will be used before submission
- Asset ID preview updates live as the user fills in the form

#### Reports
- Each report links to a dedicated clean table page — not a raw `<table border='1'>`
- Export buttons (Print / PDF / Excel) on every report page
- GHS and USD columns side by side on all financial reports

#### Login / Auth
- Single shared login page that uses a role dropdown or separate URLs
- Email-based password reset that actually sends a mail (PHPMailer is already a dependency)
- CSRF token on all POST forms

#### Sidebar (All Roles)
- Collapsible sections using Bootstrap 5 (no duplicate IDs)
- Active state highlights current section
- Role-aware: only render links the logged-in role has permission to access

---

### 9.4 Security Hardening

| Area | V2 Action |
|---|---|
| SQL injection | PDO prepared statements everywhere; no string interpolation in queries |
| Session fixation | Call `session_regenerate_id(true)` on login |
| CSRF | Generate and verify a token on every POST form |
| Password reset | Use `random_bytes(32)` for reset tokens instead of 4-digit shuffle; expire after 15 minutes |
| Error disclosure | `display_errors = Off` in production; all errors go to log file |
| Credentials | Move DB password to `.env`; remove hardcoded values from all PHP files |
| XSS | Wrap all `echo` of user-controlled data in `htmlspecialchars()` |
| File upload | Validate MIME type server-side (not just extension); store uploads outside web root |

---

### 9.5 Missing Features to Add in V2

| Feature | Rationale |
|---|---|
| **Change password page** | Currently linked from every role's navbar but does not exist |
| **Email notification on disposal** | Notify the Director of Finance when an asset is disposed |
| **Asset search** | Full-text search across asset name, serial number, GRV, PV number |
| **Bulk import via Excel** | The skeleton exists (`uploadexcel.php`) but inserts into the wrong table — rebuild it properly |
| **Audit log** | Record who changed what and when for every asset write operation |
| **Print-friendly asset tag** | One-click printable label (ID number, QR code, name) per asset |
| **Depreciation scheduler** | Auto-run depreciation calculation at year end instead of per-asset manual trigger |
| **Dashboard role segregation** | Each role's dashboard shows only the KPIs relevant to their function; currently all roles share the same template |

---

*README generated 2026-06-05. Covers v3-development branch.*
