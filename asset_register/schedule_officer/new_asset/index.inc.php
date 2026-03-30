<?php
require_once __DIR__ . '/../../security.php';
secure_session_start();
require_auth('../login/');

include '../datacon.php';

$year = date('Y');

// ── GET: return opening balance for AJAX (authenticated) ──────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['asset_class'])) {
    $asset_class_id = (int)$_GET['asset_class'];
    header('Content-Type: application/json');
    echo json_encode(['balance' => fetchOpeningBalance($asset_class_id)]);
    exit();
}

// ── POST: create a new asset ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

csrf_verify();

// Collect and sanitise inputs (prepared statements handle SQL injection;
// trim/cast enforces type expectations at the PHP layer).
$assetName      = trim($_POST['asset_name'] ?? '');
$asset_class    = (int)($_POST['asset_class_select'] ?? 0);
$asset_sub_class = (int)($_POST['asset_class_sub_classes'] ?? 0);
$grv_number     = trim($_POST['grvNumber'] ?? '');
$serial_number  = trim($_POST['identificationNumber'] ?? '');
$pv_number      = trim($_POST['pvNumber'] ?? '');
$supplier       = (int)($_POST['supplier'] ?? 0);
$user           = (int)($_POST['user'] ?? 0);
$asset_id       = trim($_POST['asset_id'] ?? '');

if (empty($assetName) || !$asset_class || !$asset_sub_class || empty($grv_number)
    || empty($serial_number) || empty($pv_number) || !$supplier || !$user || empty($asset_id)) {
    echo '<script>alert("Please fill in all required fields."); window.location="index.php";</script>';
    exit();
}

// Validate date
$rawDate = $_POST['acquisition_date'] ?? '';
$dateObj = DateTime::createFromFormat('d-m-Y', $rawDate)
        ?: DateTime::createFromFormat('Y-m-d', $rawDate);
if (!$dateObj) {
    echo '<script>alert("Invalid acquisition date."); window.location="index.php";</script>';
    exit();
}
$acquisitionDate = $dateObj->format('Y-m-d');

// ── Uniqueness checks (prepared statements) ────────────────────────────────
$checks = [
    ['grv_number',  $grv_number,    'GRV Number already exists.'],
    ['pv_number',   $pv_number,     'PV Number already exists.'],
    ['id_number',   $serial_number, 'ID/Serial Number already exists.'],
];
foreach ($checks as [$col, $val, $msg]) {
    $cs = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM assets WHERE `$col` = ?");
    mysqli_stmt_bind_param($cs, 's', $val);
    mysqli_stmt_execute($cs);
    $cnt = mysqli_fetch_assoc(mysqli_stmt_get_result($cs))['cnt'];
    mysqli_stmt_close($cs);
    if ($cnt > 0) {
        echo '<script>alert("' . addslashes($msg) . '"); window.location="index.php";</script>';
        exit();
    }
}

// ── Lifespan check ─────────────────────────────────────────────────────────
$currentYear  = (int)date('Y');
$acquisitionYear = (int)$dateObj->format('Y');
$lifespanYears   = $currentYear - $acquisitionYear;

$ls = mysqli_prepare($conn, "SELECT estimated_life FROM asset_classes WHERE ast_id = ? LIMIT 1");
mysqli_stmt_bind_param($ls, 'i', $asset_class);
mysqli_stmt_execute($ls);
$lrow = mysqli_fetch_assoc(mysqli_stmt_get_result($ls));
mysqli_stmt_close($ls);

if (!$lrow) {
    echo '<script>alert("Invalid asset class."); window.location="index.php";</script>';
    exit();
}
$estimatedLife = (int)$lrow['estimated_life'];

if ($lifespanYears > $estimatedLife) {
    echo '<script>alert("Asset expired. Lifespan exceeded."); window.location="index.php";</script>';
    exit();
}

// ── Fetch lookup values via prepared statements ────────────────────────────
$lookups = [
    ['SELECT dollar_rate  FROM dollar_rate                WHERE rate_status = ?',  's', 'ACTIVE',       'dollar_rate'],
    ['SELECT asset_class  FROM asset_classes              WHERE ast_id = ?',        'i', $asset_class,   'asset_class'],
    ['SELECT sub_class    FROM asset_class_sub_classes    WHERE T_id = ?',          'i', $asset_sub_class,'sub_class'],
    ['SELECT name         FROM suppliers                  WHERE sup_id = ?',        'i', $supplier,       'name'],
    ['SELECT asset_type   FROM asset_type                 WHERE type_id = ?',       'i', (int)($_POST['asset_type'] ?? 0), 'asset_type'],
    ['SELECT location     FROM asset_location             WHERE loc_id = ?',        'i', (int)($_POST['location'] ?? 0),   'location'],
];

$looked = [];
foreach ($lookups as [$sql, $type, $param, $key]) {
    $ls2 = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($ls2, $type, $param);
    mysqli_stmt_execute($ls2);
    $lr = mysqli_fetch_assoc(mysqli_stmt_get_result($ls2));
    mysqli_stmt_close($ls2);
    $looked[$key] = $lr ? array_values($lr)[0] : null;
}

// Fetch user full name
$us = mysqli_prepare($conn, "SELECT staff_first_name, staff_last_name FROM asset_users WHERE t_id = ? LIMIT 1");
mysqli_stmt_bind_param($us, 'i', $user);
mysqli_stmt_execute($us);
$ur = mysqli_fetch_assoc(mysqli_stmt_get_result($us));
mysqli_stmt_close($us);
$user_db = $ur ? trim($ur['staff_first_name'] . ' ' . $ur['staff_last_name']) : '';

$historicalCost   = (float)($_POST['additions'] ?? 0);
$active_res_value = (float)($_POST['active_res_value'] ?? 0);

$currentOpeningBalance = fetchOpeningBalance($asset_class);
if (!is_numeric($currentOpeningBalance)) {
    error_log('fetchOpeningBalance failed for asset_class=' . $asset_class);
    $currentOpeningBalance = 0;
}
$newOpeningBalance = (float)$currentOpeningBalance + $historicalCost;

// ── Insert asset (prepared statement) ──────────────────────────────────────
$ins = mysqli_prepare($conn,
    "INSERT INTO assets (asset_name, asset_class, sub_class, grv_number, serial_number,
        pv_number, id_number, supplier_name, asset_type, location, `user`,
        acquisition_date, current_year, additions, active_res_value, dollar_rate_used)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($ins, 'sssssssssssssdds',
    $assetName, $looked['asset_class'], $looked['sub_class'],
    $grv_number, $serial_number, $pv_number, $asset_id,
    $looked['name'], $looked['asset_type'], $looked['location'], $user_db,
    $acquisitionDate, $currentYear, $historicalCost, $active_res_value, $looked['dollar_rate']
);

if (!mysqli_stmt_execute($ins)) {
    error_log('Asset insert failed: ' . mysqli_error($conn));
    echo '<script>alert("Failed to add asset. Please try again."); window.location="index.php";</script>';
    exit();
}
mysqli_stmt_close($ins);

// ── Update opening balance ──────────────────────────────────────────────────
$upd = mysqli_prepare($conn, "UPDATE asset_classes SET opbal_plus_additions = ? WHERE ast_id = ?");
mysqli_stmt_bind_param($upd, 'di', $newOpeningBalance, $asset_class);
mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

echo "<script>alert('Asset Added Successfully'); window.location='../dashboard';</script>";
exit();

// ── Helper ─────────────────────────────────────────────────────────────────
function fetchOpeningBalance(int $asset_classId)
{
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT opening_bal, opbal_plus_additions FROM asset_classes WHERE ast_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $asset_classId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$row) {
        return 0;
    }
    $opbal = (float)$row['opbal_plus_additions'];
    return $opbal > 0 ? $opbal : (float)$row['opening_bal'];
}
