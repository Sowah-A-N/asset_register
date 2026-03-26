<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Asset Class</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="card">
        <div class="card-body"> <!-- Added card-body div -->
            <h2>Add Asset Class</h2>

            <form action="process_class.php" method="post">
                <label for="asset_class">Asset Class:</label>
                <input type="text" id="assetclass" name="asset_class" required>

                <label for="dep_rate">Depreciation Rate (%):</label>
                <input type="text" id="dep_rate" name="dep_rate" required>

                <label for="estimated_life">Estimated Life (Years):</label>
                <input type="text" id="estimated_life" name="estimated_life" required>

                <label for="opening_balance">Opening Balance (GHS):</label>
                <input type="text" id="opening_balance" name="opening_bal" required>

                <br>
                <input type="submit" value="Add Asset Class">
            </form>
        </div> <!-- End of card-body div -->
    </div>
</body>
</html>
