<?php
include "../datacon.php";

$sqlAssets = "SELECT * FROM assets";
$resultAssets = mysqli_query($conn, $sqlAssets);

if (!$resultAssets) {
    die("Error in SQL query (Assets): " . mysqli_error($conn));
}

echo "<h2>All Assets</h2>";
echo "<table border='1'>";
echo "<tr>
<th>Asset ID</th>
<th>Asset Name</th>
<th>Asset Class</th>
<th>Asset Type</th>
<th>Location</th>
<th>Acquisition Date</th>
<th>Additions(GHS)</th>
<th>Dollar Rate</th>
        <th>Calculations</th>
      </tr>";

while ($row = mysqli_fetch_assoc($resultAssets)) {
    echo "<tr>";
    echo "<td>" . $row['asset_id'] . "</td>";
            echo "<td>" . $row['asset_name'] . "</td>";
            echo "<td>" . $row['asset_class'] . "</td>";
            echo "<td>" . $row['asset_type'] . "</td>";
            echo "<td>" . $row['location'] . "</td>";
            echo "<td>" . $row['acquisition_date'] . "</td>";
            echo "<td>" . $row['additions'] . "</td>";
            echo "<td>" . $row['dollar_rate_used'] . "</td>";
    // ... display other asset details ...
    echo "<td><a href='view_calculations.php?asset_id=" . $row['asset_id'] . "'>Calculations</a></td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($conn);
?>
