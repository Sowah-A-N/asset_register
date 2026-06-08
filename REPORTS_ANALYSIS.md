# Reports Subsystem — Accounting Analysis (Read-Only Audit)

**Scope:** `schedule_officer/reports/` · **Status:** Analysis only, no code changed
**Lens:** This is accounting software. The standard applied here is *ledger integrity* — reports must be reproducible, read-only, internally consistent, and use one defensible depreciation method.

---

## 1. Live vs Dead Reports

There are ~90 PHP files under `reports/`, but most are developer scratch copies (`class_reports2/3/10`, `reportsCLass/…`, `functions copy.php`, `functions copy 2.php`, `class/class_reports*`). The **actually reachable** reports — from `reports/sidebar.html` and the hub cards — are:

| Report dir | Menu label | Purpose | Engine |
|------------|-----------|---------|--------|
| `class_reports1/` | Class Reports | Per-class monthly depreciation schedule (GHS) | **On-the-fly** (canonical) |
| `class_reports_usd/` | Class Reports USD | Same, USD | On-the-fly |
| `asset_summary/` | Asset Summary | Class-level summary (GHS) | **Aggregate-table** |
| `asset_summary_usd/` | Asset Summary USD | Same, USD | Aggregate-table |
| `assets_category/` | Assets by Category | (stub) | — |
| `assets_location/` | Assets by Location | (stub) | — |
| `all_assets_usd/` | All Assets USD | Asset list | — |
| `index2.php` | (hub class cards) | Per-class asset calculations | Inline (3rd engine) |

Everything else is dead. **Recommendation:** quarantine the scratch copies before any fix work — right now it is impossible to tell which `functions.php` is authoritative, and a fix applied to the wrong copy does nothing.

---

## 2. The Depreciation Method (as actually coded)

The live engine (`class_reports1/functions.php → calculationsByClass`) implements **straight-line depreciation by useful life, computed monthly**:

```
monthly depreciation = historical_cost (additions) / (estimated_life_years × 12)
```

- **First year** is pro-rated from the acquisition month: `months = 12 − acquisitionMonth + 1` (an asset acquired in March depreciates 10 months in year 1).
- **Middle years**: full 12 months.
- **Final year**: the remaining months.
- **Work-in-progress classes** (`asset_classes.depreciated = 0`, e.g. *Building Works in Progress*) are correctly **excluded** from depreciation — a sound accounting treatment.
- **Disposals in the report year**: depreciation is charged up to and including the disposal month, then removed from later months.

The accumulated-depreciation roll-forward (`calculateAccumulatedDepreciation`) is **arithmetically correct**: `(full_years × 12 + months_in_first_year) × monthly`.

This is a defensible method. The problems are not the method — they are (a) it is implemented three different ways, (b) it has concrete bugs, and (c) reports write back to the ledger.

---

## 3. CRITICAL — Running a Report Mutates the Ledger

`getUntrackedAssetsByYear()` is called every time the **Class Report** is rendered. It does **not just read** — it writes:

```php
// class_reports1/functions.php  (≈ line 139)
$sql4 = "UPDATE `asset_class_opbal_year` SET
         `total_depr_year_charge` = :tdyc,
         `disposals_depr` = :dd
         WHERE `asset_class` = :a AND `year` = :y";
$dm->inputData($sql4, $params4);
```

…and **inserts** a new `asset_class_opbal_year` row when one doesn't exist for the year.

**Why this is serious for accounting software:**
- `asset_class_opbal_year` is the stored source of truth for class opening balances and yearly depreciation charge. Viewing a report **overwrites** it.
- The **Asset Summary** report *reads* this same table. So Asset Summary's numbers reflect *whoever last opened a Class Report, for whichever year* — not an independent calculation. The two reports are silently coupled through a mutable side-effect.
- Running the same report twice, or running 2024 then 2025, rewrites stored figures. Results are **not reproducible**, which is the cardinal sin of a fixed-asset register feeding financial statements.

**Recommendation:** depreciation computation must be separated from reporting. Reports read; a deliberate, logged "post depreciation for year X" action writes. No report should issue an `UPDATE`/`INSERT`.

---

## 4. Three Engines, Three Cost Bases (Inconsistent Numbers)

The same depreciation question is answered by three different code paths that **disagree**:

| Engine | File | Depreciable base | Notes |
|--------|------|------------------|-------|
| On-the-fly per-asset | `class_reports1/functions.php:517` | `additions` (historical cost) ✅ | Correct base |
| Aggregate class | `asset_summary` via `asset_class_opbal_year` | `opening_balance` (class) | Whatever was last written by §3 |
| Inline per-asset | `index2.php:197`, `calculations/view_calculations.php:35` | `active_res_value` (residual!) ❌ | **Wrong** — uses salvage value as cost |

So the hub's class cards (`index2.php`) and the standalone calculation page compute depreciation on the **residual value**, while the Class Report computes it on **historical cost**. For the same asset in the same year, these produce different depreciation, accumulated depreciation, and net book value. **There is no single source of truth for an asset's depreciation.**

---

## 5. Confirmed Calculation Bugs in the Live Engine

All verified present in `class_reports1/functions.php` (the canonical on-the-fly engine):

| # | Location | Bug | Accounting impact |
|---|----------|-----|-------------------|
| 5.1 | line ~731 | `… / intdiv($monthsBetweenFirstAndLastYear, 12)` divides by **0** when the middle period is < 12 months | Report **crashes** (division by zero) for any asset whose life leaves a sub-12-month middle band |
| 5.2 | line ~827 | Middle-year monthly figures written to `$lastYearData["monthsDisplay"]` instead of `$yearsBetweenData` | The 12 monthly columns render **blank** for mid-life assets; annual charge shown without monthly support |
| 5.3 | lines ~896 / ~1034 | Grand total = `opening_balance − disposals + totalAdditions`, but `opening_balance` **already includes** prior additions | **Cost / additions overstated** in the grand-total row |
| 5.4 | line ~517 | Depreciable base is `cost`, not `cost − residual_value` | If a residual (`active_res_value`) is ever set, the asset is **over-depreciated** (NBV driven below salvage). Currently most residuals are 0, so latent. |
| 5.5 | line ~501/517 | `dep_rate` column is **displayed but not used**; depreciation is purely life-based | If anyone sets `dep_rate ≠ 1/life`, the **shown rate and the computed charge diverge**. Today they happen to agree (0.2 ↔ 5 yrs). |
| 5.6 | §3 + two disposal tables | Class engine reads disposals from `untracked_asset_disposals`; per-asset engine reads from `disposals` | The two disposal sources can disagree → inconsistent disposal depreciation |

---

## 6. Broken & Mislabeled Reports

| Report | Problem |
|--------|---------|
| **`asset_summary_usd/`** | `assetClassSummary()` selects `open_bal_usd`, `total_accum_start_usd`, `net_book_value_usd`, etc. — **none of these columns exist** in `asset_class_opbal_year`. The query errors on every load. **This report has never returned data.** |
| **`all_assets_usd/`** | Titled "All Assets (USD)" but shows **no monetary column at all** (Name, Class, Type, Location, Date only). Identical to the GHS `all_assets/` except the detail-link target. No USD anywhere. |
| **`assets_category/`** | Titled "View Asset Classes" — just dumps the `asset_classes` table (opening bal, rate, life). Not an assets-by-category report. No auth check. |
| **`assets_location/`** | Lists `asset_location` **names only** — not assets grouped by location. A user expecting "what's at each location" gets a list of location labels. No auth check. |
| **`generate.php`** (PDF, class_reports) | References a flat `$assetData[0]['Asset Name']` / `$depreciationScheduleData` structure that **does not match** what `calculationsByClass()` returns (nested `assetsData` / `newAssetName`). Produces an empty PDF. (Not wired to the UI — export uses `csv.php`.) |

---

## 7. Currency (USD) Treatment

USD figures are derived as **`GHS_amount / dollar_rate_used`**, where `dollar_rate_used` is the rate **stored on the asset at entry time**.

- **Division by zero**: assets with `dollar_rate_used = 0` (present in the data) yield `NULL`/`Infinity` USD. Also surfaces in the `assets.additions_dollar` generated column.
- **Accounting note**: using the *historical* per-asset rate is a defensible policy (records cost at transaction-date rate), but it must be applied **consistently** and never mixed with a period-end rate. Currently USD reports are mostly broken (§6), so the policy is moot until rebuilt — but the rebuild should make the rate policy an explicit, documented choice.

---

## 8. Source-Table Integrity

The aggregate reports depend on two tables that are **not maintained by the write paths**:

- **`asset_class_opbal_year`** — only ever written by the report side-effect (§3) or, partially, by `process_class.php` (which had wrong column names until this session's fix). Opening balances are otherwise hand-entered.
- **`asset_additions_year`** — *not updated when an asset is added* (`new_asset/index.inc.php` updates `asset_classes.opbal_plus_additions` but never this table). `asset_summary` `JOIN`s on it, so a class with no row for the year **silently disappears** from the summary.

**Consequence:** the aggregate reports drift out of step with the actual `assets` table the moment any asset is added, moved, or disposed.

---

## 9. Access Control

The reports **hub** (`reports/index.php`) is now guarded (`report.view`, added this session). But the **individual report directories** are not:

- `class_reports1/`, `class_reports_usd/`, `asset_summary/`, `asset_summary_usd/` — `session_start()` only, **no permission check**.
- `assets_category/`, `assets_location/`, `all_assets_usd/` — **no session check at all**.

Given §3 (reports write to the ledger), an unauthenticated hit on a class report URL could **mutate accounting data**. These need `requirePermission('report.view')` at minimum, and the write side-effect removed.

---

## 10. Recommended Direction (for approval — not yet implemented)

In priority order, accounting-integrity first:

1. **Make reports read-only.** Remove the `UPDATE`/`INSERT` from `getUntrackedAssetsByYear()`. Move depreciation *posting* to a separate, permissioned, logged action ("Post depreciation for year Y"). Reports then only read posted figures.
2. **Pick one engine and one cost base.** Standardise on the `class_reports1` on-the-fly method (historical cost, life-based) as the single source of truth; retire the `index2.php` / `view_calculations.php` residual-value path. Make Asset Summary aggregate *from the same engine*, not from a separately-mutated table.
3. **Fix the three live calculation bugs** (5.1 div-by-zero, 5.2 blank monthly columns, 5.3 grand-total double count). Decide policy on residual value (5.4) and the `dep_rate` column (5.5).
4. **Rebuild the USD reports** on a documented rate policy; guard against rate = 0.
5. **Repair source-table maintenance**: update `asset_additions_year` / `asset_class_opbal_year` from the asset write paths (or drop them in favour of computing from `assets` live).
6. **Guard every report directory** and remove the unauthenticated stubs.
7. **Quarantine the dead duplicate report folders** so the canonical code is unambiguous.

No change should be made until the **method and policy decisions** in steps 1–2 and 5.4/5.5 are confirmed — they are accounting-policy choices, not just code fixes.

---

*Prepared as a read-only audit. Companion to README.md §7–§8 (which catalogues the same defects at code-line level).*
