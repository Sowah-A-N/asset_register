<?php
// queries/assets.php — Shared query functions for the assets table.
// All functions take $conn as first param and return arrays or scalars.
// Queries use prepared statements; no raw string interpolation.

// ─────────────────────────────────────────────────────────────────────────────
// Dashboard counts
// ─────────────────────────────────────────────────────────────────────────────

function count_active_assets($conn): int {
    $row = mysqli_fetch_row(mysqli_query($conn,
        'SELECT COUNT(*) FROM assets WHERE disposed = 0 AND archived = 0'));
    return (int)($row[0] ?? 0);
}

function count_disposed_assets($conn): int {
    $row = mysqli_fetch_row(mysqli_query($conn,
        'SELECT COUNT(*) FROM assets WHERE disposed = 1'));
    return (int)($row[0] ?? 0);
}

function count_archived_assets($conn): int {
    $row = mysqli_fetch_row(mysqli_query($conn,
        'SELECT COUNT(*) FROM assets WHERE archived = 1'));
    return (int)($row[0] ?? 0);
}

function count_asset_classes($conn): int {
    $row = mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM asset_classes'));
    return (int)($row[0] ?? 0);
}

function count_locations($conn): int {
    $row = mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM asset_location'));
    return (int)($row[0] ?? 0);
}

function count_suppliers($conn): int {
    $row = mysqli_fetch_row(mysqli_query($conn, 'SELECT COUNT(*) FROM suppliers'));
    return (int)($row[0] ?? 0);
}

function get_active_dollar_rate($conn): ?float {
    $row = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT dollar_rate FROM dollar_rate WHERE rate_status = 'ACTIVE' LIMIT 1"));
    return $row ? (float)$row['dollar_rate'] : null;
}

// ─────────────────────────────────────────────────────────────────────────────
// Dashboard chart: additions value by asset class (active assets)
// Returns array of ['asset_class' => ..., 'total' => ...]
// ─────────────────────────────────────────────────────────────────────────────
function get_assets_by_class_totals($conn): array {
    $result = mysqli_query($conn,
        'SELECT asset_class, SUM(additions) AS total
         FROM assets
         WHERE disposed = 0 AND archived = 0
         GROUP BY asset_class
         ORDER BY total DESC');
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// ─────────────────────────────────────────────────────────────────────────────
// Asset list (paginated)
// ─────────────────────────────────────────────────────────────────────────────
function get_assets_paginated($conn, int $limit, int $offset, array $filters = []): array {
    $where = 'WHERE disposed = 0 AND archived = 0';
    $params = [];
    $types  = '';

    if (!empty($filters['class'])) {
        $where .= ' AND asset_class = ?';
        $params[] = $filters['class'];
        $types   .= 's';
    }
    if (!empty($filters['location'])) {
        $where .= ' AND location = ?';
        $params[] = $filters['location'];
        $types   .= 's';
    }
    if (!empty($filters['search'])) {
        $where .= ' AND (asset_name LIKE ? OR id_number LIKE ? OR serial_number LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params[] = $s; $params[] = $s; $params[] = $s;
        $types   .= 'sss';
    }

    // Total count
    $count_sql = "SELECT COUNT(*) FROM assets $where";
    if ($params) {
        $cs = mysqli_prepare($conn, $count_sql);
        mysqli_stmt_bind_param($cs, $types, ...$params);
        mysqli_stmt_execute($cs);
        $cr = mysqli_stmt_get_result($cs);
        $total = (int)mysqli_fetch_row($cr)[0];
        mysqli_stmt_close($cs);
    } else {
        $total = (int)mysqli_fetch_row(mysqli_query($conn, $count_sql))[0];
    }

    // Data
    $sql = "SELECT * FROM assets $where ORDER BY date_added DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types   .= 'ii';

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);

    return ['total' => $total, 'rows' => $rows];
}

// ─────────────────────────────────────────────────────────────────────────────
// Get single asset by ID
// ─────────────────────────────────────────────────────────────────────────────
function get_asset_by_id($conn, int $id): ?array {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM assets WHERE asset_id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ?: null;
}
