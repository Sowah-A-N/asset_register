<?php
include "functions.php";
include "datacon.php"; // Ensure this file properly initializes $conn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asset_id = $_POST["asset_id"];
    $year = $_POST["year"];

    // Ensure asset_id is set and numeric
    if (!isset($asset_id) || !is_numeric($asset_id)) {
        echo "<p>Invalid asset ID.</p>";
        exit;
    }

    // Prepare and execute the query using MySQLi
    $query = $conn->prepare("SELECT * FROM assets WHERE asset_id = ?");
    $query->bind_param("i", $asset_id); // 'i' means integer
    $query->execute();
    $result = $query->get_result();
    $asset = $result->fetch_assoc();

    $query = $conn->prepare("SELECT asset_class FROM assets WHERE asset_id = ?");
    $query->bind_param("i", $asset_id);
    $query->execute();
    $result = $query->get_result();
    $asset_class_result = $result->fetch_assoc();
    $asset_class = $asset_class_result['asset_class'];

    $query = $conn->prepare("SELECT estimated_life FROM asset_classes WHERE asset_class = ?");
    $query->bind_param("s", $asset_class);
    $query->execute();
    $result = $query->get_result();
    $estimated_life_result = $result->fetch_assoc();
    $estimated_life = $estimated_life_result['estimated_life'];

    $asset['useful_life'] = $estimated_life;

    if (!$asset) {
        echo "<p>Asset not found.</p>";
        exit;
    }
   
    // Get monthly depreciation
    $monthly_depreciation = calculateMonthlyDepreciationForYear(
        $asset['additions'],
        $asset['useful_life'],
        $asset['acquisition_date'],
        $year
    );

    // Close the query
    $query->close();
}

    
    // Convert the result to JSON
    $result = json_encode($monthly_depreciation);

    // Return the result as a JSON response
    header('Content-Type: application/json');    
    echo $result;

// echo "<table border='1'>";
// echo "<tr><th>Month</th><th>Depreciation</th><th>Accumulated Depreciation</th><th>Net Book Value</th></tr>";

// echo "<pre>";
// print_r($monthly_depreciation);
// echo "</pre>";


// foreach ($monthly_depreciation as $month => $values) {
//     echo "<tr>
//         <td>{$month}</td>
//         <td>$ {$values['depreciation']}</td>
//         <td>$ {$values['accumulatedDepreciation']}</td>
//         <td>$ {$values['netBookValue']}</td>
//     </tr>";
// }

// echo "</table>";

// Close database connection if needed (optional, as PHP closes it automatically)
$conn->close();
?>
