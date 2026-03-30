<?php
require_once '../init.php';

$ast_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$row = null;

if ($ast_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM asset_classes WHERE ast_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $ast_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    csrf_verify();
    $ast_id       = isset($_POST['ast_id']) ? (int)$_POST['ast_id'] : 0;
    $assetClass   = trim($_POST['asset_class']   ?? '');
    $depRate      = trim($_POST['dep_rate']       ?? '');
    $estimLife    = trim($_POST['estimated_life'] ?? '');
    $openingBal   = trim($_POST['opening_balance'] ?? '');

    if ($ast_id > 0 && $assetClass !== '') {
        $upd = mysqli_prepare($conn, "UPDATE asset_classes SET asset_class=?, dep_rate=?, estimated_life=?, opening_balance=? WHERE ast_id=?");
        mysqli_stmt_bind_param($upd, 'ssssi', $assetClass, $depRate, $estimLife, $openingBal, $ast_id);
        if (mysqli_stmt_execute($upd)) {
            echo "<script>alert('Asset Class updated successfully'); window.location='index.php';</script>";
        } else {
            error_log(mysqli_error($conn));
            echo "<script>alert('Update failed. Please try again.'); window.location='index.php';</script>";
        }
        mysqli_stmt_close($upd);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset Class</title>
</head>
<body>
    <h2>Edit Asset Class</h2>

    <?php if ($row): ?>
        <form method="post" action="edit_asset.php?id=<?= (int)$row['ast_id'] ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="ast_id" value="<?= (int)$row['ast_id'] ?>">

            <label>Asset Class:
                <input type="text" name="asset_class" value="<?= esc($row['asset_class']) ?>" required>
            </label><br>

            <label>Depreciation Rate:
                <input type="text" name="dep_rate" value="<?= esc($row['dep_rate']) ?>">
            </label><br>

            <label>Estimated Life:
                <input type="text" name="estimated_life" value="<?= esc($row['estimated_life']) ?>">
            </label><br>

            <label>Opening Balance:
                <input type="text" name="opening_balance" value="<?= esc($row['opening_balance']) ?>">
            </label><br>

            <input type="submit" name="submit" value="Update">
        </form>
    <?php else: ?>
        <p>Asset not found.</p>
    <?php endif; ?>

    <p><a href="index.php">Back</a></p>
</body>
</html>
