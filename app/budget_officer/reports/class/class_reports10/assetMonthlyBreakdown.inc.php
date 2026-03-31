<?php

    include "../functions.php";
    include "./datacon.php";

    //Include global connection variable
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $asset_id = $_POST["asset_id"];
        $year = $_POST["year"];

        // Fetch asset details
        $query = $pdo->prepare("SELECT * FROM assets WHERE id = :asset_id");
        $query->execute(['asset_id' => $asset_id]);
        $asset = $query->fetch(PDO::FETCH_ASSOC);

        if (!$asset) {
            echo "<p>Asset not found.</p>";
            exit;
        }

          // Get monthly depreciation
        $monthly_depreciation = calculateMonthlyDepreciationForYear(
            $asset['historical_cost'],
            $asset['useful_life'],
            $asset['acquisition_date'],
            $year);
        
    }

    echo "<table border='1'>";
    echo "<tr><th>Month</th><th>Depreciation</th><th>Accumulated Depreciation</th><th>Net Book Value</th></tr>";

    foreach ($monthly_depreciation as $month => $values) {
        echo "<tr>
            <td>{$month}</td>
            <td>$ {$values['monthly_depreciation']}</td>
            <td>$ {$values['accumulated_depreciation']}</td>
            <td>$ {$values['net_book_value']}</td>
        </tr>";
    }

    echo "</table>";

