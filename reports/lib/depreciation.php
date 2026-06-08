<?php
declare(strict_types=1);

/**
 * ════════════════════════════════════════════════════════════════════
 * RMU Asset Register — Canonical Depreciation Engine (v2)
 * ════════════════════════════════════════════════════════════════════
 * The SINGLE source of truth for asset depreciation. All reports must
 * use this — no report computes depreciation independently, and NOTHING
 * here writes to the database (reports are read-only; figures are
 * recomputed live from the assets table on every render).
 *
 * Accounting policy (approved):
 *   • Method            : straight-line by useful life, computed monthly
 *   • Depreciable base  : (historical cost − residual value), floored at 0
 *   • Driver            : useful life in years  →  months = life × 12
 *   • First year        : pro-rated from the acquisition month (inclusive)
 *   • Cap               : accumulated depreciation never exceeds the
 *                         depreciable base (NBV never falls below residual)
 *   • WIP classes       : asset_classes.depreciated = 0  → no depreciation
 *   • Disposals         : depreciation charged up to and incl. disposal
 *                         month in the disposal year; nil thereafter
 * ════════════════════════════════════════════════════════════════════
 */

/**
 * Months an asset has been in service from acquisition up to the end of a
 * given (year, month), inclusive of the acquisition month — clamped to the
 * asset's total depreciable life.
 */
function rmu_months_in_service(
    int $acqYear, int $acqMonth,
    int $throughYear, int $throughMonth,
    int $totalMonths
): int {
    if ($throughYear < $acqYear ||
        ($throughYear === $acqYear && $throughMonth < $acqMonth)) {
        return 0; // not yet acquired as at the reference point
    }
    $elapsed = ($throughYear - $acqYear) * 12 + ($throughMonth - $acqMonth) + 1;
    if ($elapsed < 0) $elapsed = 0;
    if ($elapsed > $totalMonths) $elapsed = $totalMonths; // fully depreciated
    return $elapsed;
}

/**
 * Compute the full depreciation position of ONE asset for ONE report year.
 *
 * @param float       $cost            Historical cost (assets.additions)
 * @param float       $residual        Residual/salvage value (assets.active_res_value)
 * @param int         $lifeYears       Useful life in years (asset_classes.estimated_life)
 * @param string      $acquisitionDate 'YYYY-MM-DD'
 * @param bool        $classDepreciates asset_classes.depreciated (false = WIP, no depreciation)
 * @param int         $reportYear      Year being reported
 * @param string|null $disposalDate    'YYYY-MM-DD' if disposed, else null
 *
 * @return array{
 *   in_service:bool, disposed_in_year:bool, depreciable_base:float,
 *   monthly:float, months_this_year:int, depreciation_expense:float,
 *   accumulated_start:float, accumulated_end:float,
 *   nbv_start:float, nbv_end:float, monthly_breakdown:array<int,float>
 * }
 */
function rmu_depreciate_asset(
    float $cost,
    float $residual,
    int $lifeYears,
    string $acquisitionDate,
    bool $classDepreciates,
    int $reportYear,
    ?string $disposalDate = null
): array {
    // ── Base figures ────────────────────────────────────────────────
    $depreciableBase = max($cost - $residual, 0.0);
    $totalMonths     = max($lifeYears, 0) * 12;
    $monthly         = ($totalMonths > 0 && $classDepreciates)
                     ? $depreciableBase / $totalMonths
                     : 0.0;

    $acqTs    = strtotime($acquisitionDate);
    $acqYear  = (int)date('Y', $acqTs);
    $acqMonth = (int)date('n', $acqTs);

    // Disposal context
    $disposalYear  = $disposalDate ? (int)date('Y', strtotime($disposalDate)) : null;
    $disposalMonth = $disposalDate ? (int)date('n', strtotime($disposalDate)) : null;
    $disposedInYear   = ($disposalYear === $reportYear);
    $disposedPriorYear = ($disposalYear !== null && $disposalYear < $reportYear);

    $empty = [
        'in_service'           => false,
        'disposed_in_year'     => false,
        'depreciable_base'     => $depreciableBase,
        'monthly'              => $monthly,
        'months_this_year'     => 0,
        'depreciation_expense' => 0.0,
        'accumulated_start'    => 0.0,
        'accumulated_end'      => 0.0,
        'nbv_start'            => 0.0,
        'nbv_end'              => 0.0,
        'monthly_breakdown'    => array_fill(1, 12, 0.0),
    ];

    // Not yet acquired, or already disposed in a previous year → nothing to show
    if ($reportYear < $acqYear || $disposedPriorYear) {
        return $empty;
    }

    // ── Accumulated depreciation at start (end of prior year) ───────
    $monthsThroughPriorYear = rmu_months_in_service($acqYear, $acqMonth, $reportYear - 1, 12, $totalMonths);
    $accumStart = $monthsThroughPriorYear * $monthly;

    // ── Accumulated depreciation at end of this year ────────────────
    // If disposed this year, depreciation stops at the disposal month.
    $endMonth = $disposedInYear ? (int)$disposalMonth : 12;
    $monthsThroughThisYear = rmu_months_in_service($acqYear, $acqMonth, $reportYear, $endMonth, $totalMonths);
    $accumEnd = $monthsThroughThisYear * $monthly;

    $monthsThisYear = $monthsThroughThisYear - $monthsThroughPriorYear;
    if ($monthsThisYear < 0) $monthsThisYear = 0;

    $expense = $accumEnd - $accumStart;

    // ── Net book value ──────────────────────────────────────────────
    $nbvStart = $cost - $accumStart;
    $nbvEnd   = $cost - $accumEnd;

    // ── Per-month breakdown for this year ───────────────────────────
    // Determine which calendar months in this year carry a charge.
    $breakdown = array_fill(1, 12, 0.0);
    $firstChargeMonth = ($reportYear === $acqYear) ? $acqMonth : 1;
    $lastChargeMonth  = $endMonth;
    // Respect the life cap: only charge months that fall within remaining life.
    $remainingMonthsAtYearStart = $totalMonths - $monthsThroughPriorYear;
    $charged = 0;
    for ($m = $firstChargeMonth; $m <= $lastChargeMonth; $m++) {
        if ($charged >= $remainingMonthsAtYearStart) break; // fully depreciated mid-year
        $breakdown[$m] = $monthly;
        $charged++;
    }

    return [
        'in_service'           => true,
        'disposed_in_year'     => $disposedInYear,
        'depreciable_base'     => $depreciableBase,
        'monthly'              => $monthly,
        'months_this_year'     => $monthsThisYear,
        'depreciation_expense' => $expense,
        'accumulated_start'    => $accumStart,
        'accumulated_end'      => $accumEnd,
        'nbv_start'            => $nbvStart,
        'nbv_end'              => $nbvEnd,
        'monthly_breakdown'    => $breakdown,
    ];
}

/**
 * Base year for the legacy opening-balance pool. The opening_balance and
 * total_accum_depr_start in `asset_class_opbal_year` for THIS year are the
 * authoritative brought-forward figures (from the prior audited accounts).
 * Later years in that table are ignored — they were corrupted by the old
 * report write side-effect; we roll forward from this base in memory.
 */
const RMU_POOL_BASE_YEAR = 2024;

/**
 * Depreciate the legacy class POOL (the brought-forward opening balance) for a
 * report year, rolling forward in memory from the base year. Read-only.
 *
 * The pool is the value carried over from the prior audited accounts that is
 * NOT itemised in the `assets` table. Individual assets (post-base-year
 * additions) are depreciated separately and added on top by the caller.
 *
 * @param float $openingBalance     Pool cost b/f as at 1 Jan of the base year
 * @param float $accumDeprStart     Accumulated depreciation b/f as at 1 Jan base year
 * @param int   $expectedLifeMonths Pool useful life in months
 * @param bool  $depreciates        Pool depreciated flag (e.g. Land = false)
 * @param int   $reportYear
 * @param int   $baseYear           Defaults to RMU_POOL_BASE_YEAR
 *
 * @return array  Same shape as rmu_depreciate_asset() (cost = openingBalance)
 */
function rmu_depreciate_pool(
    float $openingBalance,
    float $accumDeprStart,
    int $expectedLifeMonths,
    bool $depreciates,
    int $reportYear,
    int $baseYear = RMU_POOL_BASE_YEAR
): array {
    $monthly = ($depreciates && $expectedLifeMonths > 0)
             ? $openingBalance / $expectedLifeMonths
             : 0.0;

    // Accumulated depreciation can never exceed the pool cost (NBV floor 0).
    $accumStart = $accumDeprStart;
    if ($reportYear > $baseYear) {
        $monthsBefore = ($reportYear - $baseYear) * 12;
        $accumStart  += $monthsBefore * $monthly;
    }
    if ($accumStart > $openingBalance) $accumStart = $openingBalance;
    if ($accumStart < 0)               $accumStart = 0.0;

    $remaining = max($openingBalance - $accumStart, 0.0);
    $yearCharge = min(12 * $monthly, $remaining);
    $accumEnd   = $accumStart + $yearCharge;

    // Spread the year's charge across the months (each month = monthly, capped).
    $breakdown = array_fill(1, 12, 0.0);
    $left = $remaining;
    for ($m = 1; $m <= 12 && $left > 0; $m++) {
        $charge = min($monthly, $left);
        $breakdown[$m] = $charge;
        $left -= $charge;
    }

    return [
        'in_service'           => $openingBalance > 0,
        'disposed_in_year'     => false,
        'depreciable_base'     => $openingBalance,
        'monthly'              => $monthly,
        'months_this_year'     => $yearCharge > 0 ? 12 : 0,
        'depreciation_expense' => $yearCharge,
        'accumulated_start'    => $accumStart,
        'accumulated_end'      => $accumEnd,
        'nbv_start'            => $openingBalance - $accumStart,
        'nbv_end'              => $openingBalance - $accumEnd,
        'monthly_breakdown'    => $breakdown,
    ];
}

/* ════════════════════════════════════════════════════════════════════
 * USD translation (IAS 21 — historical rate per item)
 * ════════════════════════════════════════════════════════════════════
 * Fixed assets are non-monetary items at historical cost: each is translated
 * at the exchange rate on its transaction date (the rate stored on the asset),
 * NOT re-translated at a closing rate. Depreciation is linear, so dividing a
 * GHS position's monetary fields by that item's rate gives the USD position.
 */

/** Divide all monetary fields of a position by a rate (GHS → USD). */
function rmu_convert_position(array $pos, float $rate): array {
    if ($rate <= 0) return $pos;
    foreach (['depreciable_base','monthly','depreciation_expense',
              'accumulated_start','accumulated_end','nbv_start','nbv_end'] as $f) {
        $pos[$f] = $pos[$f] / $rate;
    }
    foreach ($pos['monthly_breakdown'] as $m => $v) {
        $pos['monthly_breakdown'][$m] = $v / $rate;
    }
    return $pos;
}

/**
 * USD class aggregate — each individual asset translated at ITS OWN historical
 * rate (assets.dollar_rate_used). Assets with a zero/invalid stored rate fall
 * back to $activeRate and are flagged (rate_substituted = true).
 *
 * @param array $assets     Rows incl. dollar_rate_used + the usual fields
 * @param float $activeRate Current active dollar rate (fallback)
 * @return array ['lines'=>..., 'totals'=>...]  monetary totals are in USD
 */
function rmu_depreciate_class_usd(
    array $assets, int $lifeYears, bool $classDepreciates,
    int $reportYear, float $activeRate
): array {
    $lines = [];
    $tot = ['cost'=>0.0,'depreciable_base'=>0.0,'expense'=>0.0,
            'accumulated_start'=>0.0,'accumulated_end'=>0.0,
            'nbv_start'=>0.0,'nbv_end'=>0.0,'monthly'=>array_fill(1,12,0.0)];

    foreach ($assets as $a) {
        $ghs = rmu_depreciate_asset(
            (float)$a['additions'], (float)($a['active_res_value'] ?? 0),
            $lifeYears, $a['acquisition_date'], $classDepreciates,
            $reportYear, $a['disposal_date'] ?? null
        );
        if (!$ghs['in_service']) continue;

        $rate = (float)($a['dollar_rate_used'] ?? 0);
        $substituted = false;
        if ($rate <= 0) { $rate = $activeRate; $substituted = true; }

        $usd = rmu_convert_position($ghs, $rate);
        $usd['rate_used']        = $rate;
        $usd['rate_substituted'] = $substituted;
        $usd['cost_usd']         = ($rate > 0) ? (float)$a['additions'] / $rate : 0.0;
        $lines[] = $usd + ['asset' => $a];

        $tot['cost']              += $usd['cost_usd'];
        $tot['depreciable_base']  += $usd['depreciable_base'];
        $tot['expense']           += $usd['depreciation_expense'];
        $tot['accumulated_start'] += $usd['accumulated_start'];
        $tot['accumulated_end']   += $usd['accumulated_end'];
        $tot['nbv_start']         += $usd['nbv_start'];
        $tot['nbv_end']           += $usd['nbv_end'];
        for ($m = 1; $m <= 12; $m++) $tot['monthly'][$m] += $usd['monthly_breakdown'][$m];
    }
    return ['lines' => $lines, 'totals' => $tot];
}

/**
 * Aggregate a whole asset class for a report year by summing the per-asset
 * positions. Pure read — caller supplies the asset rows and class meta.
 *
 * @param array  $assets   Rows from `assets` (each needs: additions,
 *                         active_res_value, acquisition_date, disposal_date|null)
 * @param int    $lifeYears        asset_classes.estimated_life
 * @param bool   $classDepreciates asset_classes.depreciated
 * @param int    $reportYear
 * @return array  Totals + per-asset lines
 */
function rmu_depreciate_class(
    array $assets,
    int $lifeYears,
    bool $classDepreciates,
    int $reportYear
): array {
    $lines = [];
    $tot = [
        'cost' => 0.0, 'depreciable_base' => 0.0, 'expense' => 0.0,
        'accumulated_start' => 0.0, 'accumulated_end' => 0.0,
        'nbv_start' => 0.0, 'nbv_end' => 0.0,
        'monthly' => array_fill(1, 12, 0.0),
    ];

    foreach ($assets as $a) {
        $pos = rmu_depreciate_asset(
            (float)$a['additions'],
            (float)($a['active_res_value'] ?? 0),
            $lifeYears,
            $a['acquisition_date'],
            $classDepreciates,
            $reportYear,
            $a['disposal_date'] ?? null
        );
        if (!$pos['in_service']) continue;

        $lines[] = $pos + ['asset' => $a];
        $tot['cost']              += (float)$a['additions'];
        $tot['depreciable_base']  += $pos['depreciable_base'];
        $tot['expense']           += $pos['depreciation_expense'];
        $tot['accumulated_start'] += $pos['accumulated_start'];
        $tot['accumulated_end']   += $pos['accumulated_end'];
        $tot['nbv_start']         += $pos['nbv_start'];
        $tot['nbv_end']           += $pos['nbv_end'];
        for ($m = 1; $m <= 12; $m++) $tot['monthly'][$m] += $pos['monthly_breakdown'][$m];
    }

    return ['lines' => $lines, 'totals' => $tot];
}

/* ════════════════════════════════════════════════════════════════════
 * Untracked-asset disposals from the legacy pool (accounting treatment)
 * ════════════════════════════════════════════════════════════════════
 * The pool (asset_class_opbal_year) is a non-itemised brought-forward lump.
 * When an UNTRACKED item of a class is disposed (logged in
 * untracked_asset_disposals), proper accounting removes it from the pool:
 *   • its cost leaves the register at the disposal date,
 *   • its accumulated depreciation is removed with it,
 *   • depreciation is charged only up to the disposal month,
 *   • its carrying amount leaves (gain/loss is outside this read model).
 *
 * The pool isn't itemised, so each disposed item is depreciated straight line
 * over the class life from its own acquisition date (the same basis as the
 * pool) and netted out:  class = pool(full) + Σ(real − gross).
 *
 * Returns a position DELTA (mostly negative) to add to the GHS pool position.
 * USD reports convert the netted pool at the pool's single historical rate,
 * keeping the pool one historical-rate item (IAS 21-consistent).
 */
function rmu_untracked_disposal_delta(
    array $disposals,
    int $lifeYears,
    int $reportYear,
    bool $classDepreciates = true
): array {
    $d = [
        'depreciable_base' => 0.0, 'accumulated_start' => 0.0,
        'depreciation_expense' => 0.0, 'accumulated_end' => 0.0,
        'nbv_start' => 0.0, 'nbv_end' => 0.0,
        'monthly_breakdown' => array_fill(1, 12, 0.0),
        'disposed_count' => 0, 'disposed_value' => 0.0,
    ];

    foreach ($disposals as $row) {
        $disp = $row['date_of_disposal'] ?? null;
        if (!$disp || $disp === '0000-00-00') continue;        // not actually disposed
        $disposalYear = (int)date('Y', strtotime($disp));
        if ($disposalYear > $reportYear) continue;             // disposed later → still held this year

        $value = (float)$row['value'];
        $acq   = (!empty($row['acquisition_date']) && $row['acquisition_date'] !== '0000-00-00')
               ? $row['acquisition_date'] : ($reportYear . '-01-01');

        // gross = what the lump pool implicitly carries for this item (no disposal)
        $gross = rmu_depreciate_asset($value, 0.0, $lifeYears, $acq, $classDepreciates, $reportYear, null);
        // real depreciation actually charged this year (up to disposal; 0 if disposed earlier)
        $dispd = rmu_depreciate_asset($value, 0.0, $lifeYears, $acq, $classDepreciates, $reportYear, $disp);

        $disposedInYear = ($disposalYear === $reportYear);
        $realAccumStart = $disposedInYear ? $gross['accumulated_start'] : 0.0;
        $realNbvStart   = $disposedInYear ? ($value - $gross['accumulated_start']) : 0.0;
        $realCharge     = $disposedInYear ? $dispd['depreciation_expense'] : 0.0;

        $d['depreciable_base']     += 0.0 - $value;                              // cost out
        $d['accumulated_start']    += $realAccumStart - $gross['accumulated_start'];
        $d['depreciation_expense'] += $realCharge     - $gross['depreciation_expense'];
        $d['accumulated_end']      += 0.0 - $gross['accumulated_end'];           // accum out
        $d['nbv_start']            += $realNbvStart - $gross['nbv_start'];
        $d['nbv_end']              += 0.0 - $gross['nbv_end'];                   // nbv out
        for ($m = 1; $m <= 12; $m++) {
            $realM = $disposedInYear ? $dispd['monthly_breakdown'][$m] : 0.0;
            $d['monthly_breakdown'][$m] += $realM - $gross['monthly_breakdown'][$m];
        }
        $d['disposed_count']++; $d['disposed_value'] += $value;
    }
    return $d;
}

/** Add an untracked-disposal delta onto an EXISTING pool position. */
function rmu_apply_pool_delta(array $pool, array $delta): array
{
    foreach (['depreciable_base','accumulated_start','depreciation_expense','accumulated_end','nbv_start','nbv_end'] as $k) {
        $pool[$k] += $delta[$k];
    }
    for ($m = 1; $m <= 12; $m++) $pool['monthly_breakdown'][$m] += $delta['monthly_breakdown'][$m];
    return $pool;
}

/** Load untracked disposals grouped by trimmed class name (one query). */
function rmu_load_untracked_disposals(mysqli $conn): array
{
    $out = [];
    $res = @mysqli_query($conn, "SELECT TRIM(class) AS class, value, acquisition_date, date_of_disposal FROM untracked_asset_disposals");
    if ($res) while ($r = mysqli_fetch_assoc($res)) $out[rtrim($r['class'])][] = $r;
    return $out;
}
