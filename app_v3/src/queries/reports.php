<?php
// queries/reports.php — Shared report queries
// All functions use prepared statements; return arrays of assoc rows.

// ── Year range helper ────────────────────────────────────────────────────────
function get_report_years($conn): array {
    $row = mysqli_fetch_assoc(mysqli_query($conn,
        'SELECT MIN(year) AS min_year, MAX(year) AS max_year FROM asset_class_opbal_year'));
    $min = $row ? (int)$row['min_year'] : (int)date('Y');
    $max = (int)date('Y');
    return range($max, max($min, $max - 10));
}

// ── Asset class summary (the big summary report) ─────────────────────────────
// Returns one row per asset class for the given year, joining opbal + additions.
function get_asset_class_summary($conn, int $year): array {
    $stmt = mysqli_prepare($conn,
        "SELECT
            a.asset_class,
            a.year,
            a.opening_balance,
            a.total_accum_depr_start,
            a.total_depr_year_charge,
            a.total_accum_depr_end,
            a.disposals_depr,
            a.net_book_value,
            a.expected_life_months,
            a.rate,
            a.depreciated,
            COALESCE(b.total_additions_cedi,   0) AS total_additions_cedi,
            COALESCE(b.total_additions_dollar, 0) AS total_additions_dollar,
            COALESCE(b.total_disposals_cedi,   0) AS total_disposals_cedi,
            COALESCE(b.total_disposals_dollar, 0) AS total_disposals_dollar
         FROM asset_class_opbal_year a
         LEFT JOIN asset_additions_year b
            ON a.asset_class COLLATE utf8mb4_general_ci = b.asset_class COLLATE utf8mb4_general_ci
           AND a.year = b.year
         WHERE a.year = ?
         ORDER BY a.asset_class");
    if (!$stmt) return [];
    mysqli_stmt_bind_param($stmt, 'i', $year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    mysqli_stmt_close($stmt);
    return $rows;
}

// ── Additions report: assets added in a given year ────────────────────────────
function get_additions_by_year($conn, int $year): array {
    $stmt = mysqli_prepare($conn,
        'SELECT * FROM assets WHERE current_year = ? AND disposed = 0 AND archived = 0
         ORDER BY asset_class, acquisition_date');
    mysqli_stmt_bind_param($stmt, 'i', $year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    mysqli_stmt_close($stmt);
    return $rows;
}

// ── All active assets list ────────────────────────────────────────────────────
function get_all_active_assets($conn, string $class_filter = ''): array {
    if ($class_filter !== '') {
        $stmt = mysqli_prepare($conn,
            'SELECT * FROM assets WHERE asset_class = ? AND disposed = 0 AND archived = 0
             ORDER BY asset_class, acquisition_date');
        mysqli_stmt_bind_param($stmt, 's', $class_filter);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn,
            'SELECT * FROM assets WHERE disposed = 0 AND archived = 0
             ORDER BY asset_class, acquisition_date');
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

// ── Fully depreciated assets (net_book_value approaches 0) ───────────────────
// Uses asset_class_opbal_year where net_book_value <= 0
function get_fully_depreciated($conn, int $year): array {
    $stmt = mysqli_prepare($conn,
        'SELECT * FROM asset_class_opbal_year WHERE year = ? AND net_book_value <= 0
         ORDER BY asset_class');
    mysqli_stmt_bind_param($stmt, 'i', $year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    mysqli_stmt_close($stmt);
    return $rows;
}

// ── Quarterly breakdown: additions by class, grouped by quarter ───────────────
function get_quarterly_by_class($conn, int $year): array {
    $stmt = mysqli_prepare($conn,
        "SELECT
            asset_class,
            QUARTER(acquisition_date) AS quarter,
            SUM(additions)            AS total_cedi,
            SUM(additions / dollar_rate_used) AS total_usd
         FROM assets
         WHERE current_year = ? AND disposed = 0 AND archived = 0
         GROUP BY asset_class, QUARTER(acquisition_date)
         ORDER BY asset_class, quarter");
    mysqli_stmt_bind_param($stmt, 'i', $year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    mysqli_stmt_close($stmt);
    return $rows;
}

// ── Assets by location ─────────────────────────────────────────────────────────
function get_assets_by_location($conn): array {
    $result = mysqli_query($conn,
        'SELECT location, COUNT(*) AS asset_count, SUM(additions) AS total_value
         FROM assets WHERE disposed = 0 AND archived = 0
         GROUP BY location ORDER BY total_value DESC');
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

// ── Depreciation charge (per asset, given year) ────────────────────────────────
// Monthly straight-line: additions / (estimated_life * 12) * months_in_year
function get_depreciation_charge($conn, int $year): array {
    $stmt = mysqli_prepare($conn,
        "SELECT
            a.asset_id, a.asset_name, a.id_number, a.asset_class,
            a.location, a.acquisition_date,
            a.additions,
            ac.dep_rate,
            ac.estimated_life,
            ac.estimated_life_months,
            (a.additions / NULLIF(ac.estimated_life_months, 0)) AS monthly_dep,
            (a.additions / NULLIF(ac.estimated_life_months, 0) * 12) AS annual_dep
         FROM assets a
         LEFT JOIN asset_classes ac ON ac.asset_class COLLATE utf8mb4_general_ci
                                     = a.asset_class   COLLATE utf8mb4_general_ci
         WHERE a.current_year <= ? AND a.disposed = 0 AND a.archived = 0
           AND ac.depreciated = 1
         ORDER BY a.asset_class, a.asset_name");
    mysqli_stmt_bind_param($stmt, 'i', $year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    mysqli_stmt_close($stmt);
    return $rows;
}
