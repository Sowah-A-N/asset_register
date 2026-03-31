<?php
require_once '../init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_name      = trim($_POST['asset_name']           ?? '');
    $asset_value     = trim($_POST['asset_value']          ?? '');
    $acquisition_raw = trim($_POST['acquisition_date']     ?? '');
    $disposal_raw    = trim($_POST['disposal_date']        ?? '');
    $asset_class_id  = trim($_POST['asset_class_select']   ?? '');
    $dollar_rate     = trim($_POST['dollar_rate']          ?? '');

    $acquisition_date = $acquisition_raw !== '' ? date('Y-m-d', strtotime($acquisition_raw)) : '';
    $disposal_date    = $disposal_raw    !== '' ? date('Y-m-d', strtotime($disposal_raw))    : null;

    // Resolve asset class name from ID
    $stmt = mysqli_prepare($conn, "SELECT asset_class FROM asset_classes WHERE ast_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $asset_class_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $classRow = mysqli_fetch_array($res);
    mysqli_stmt_close($stmt);
    $asset_class_db = $classRow[0] ?? '';

    $ins = mysqli_prepare($conn,
        "INSERT INTO untracked_asset_disposals (name, value, acquisition_date, date_of_disposal, class, dollar_rate)
         VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'ssssss',
        $asset_name, $asset_value, $acquisition_date, $disposal_date, $asset_class_db, $dollar_rate);

    if (mysqli_stmt_execute($ins)) {
        echo "<script>alert('Asset successfully added and disposed off!'); window.location.href='../view_untracked';</script>";
    } else {
        error_log(mysqli_error($conn));
        echo "<script>alert('A database error occurred.'); window.location.href='../view_untracked';</script>";
    }
    mysqli_stmt_close($ins);
}
?>
