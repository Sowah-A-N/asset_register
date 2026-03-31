<?php 
    include "../datacon.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historical Cost Only</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

</head>
<body>
    <!--label for="">From:</label>
    <input type="date" name="from" id="from-date"><br />

    <label for="">To:</label>
    <input type="date" name="to" id="to-date"><br />

    <button type="submit">Generate</button-->

    <form action="" method="post" class="flex items-center space-x-4">
        <label for="asset_class_select" class="text-lg font-semibold">Select Asset Class:</label>
        <select name="asset_class_select" id="asset_class_select" class="p-2 border border-gray-300 rounded">
            <?php 
                 if(!($_POST['asset_class_select'])){
                    echo "<option value=\"\" selected> --Select Asset Class--</option>";
                 } else {
                    echo "<option value=\"\" selected>{$_POST['asset_class_select']}</option>";
                 }
            ?>            
            <?php
            $assetClassQuery = "SELECT * FROM asset_classes;";
            $assetClassResult = $conn->query($assetClassQuery);
            
            // Check if there are results
            if ($assetClassResult->num_rows > 0) {
                // Output data of each row
                while ($row = $assetClassResult->fetch_assoc()) {
                    echo "<option value=\"" . $row["asset_class"] . "\">" . $row["asset_class"] . "</option>";
                }
            } else {
                echo "<option value=\"\">No asset classes available</option>";
            }
            ?>
            
        </select>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Filter</button>
    </form>

    <?php

        include "../datacon.php";

        $historicalSumResult = 0;
        
        if(!isset($_POST['asset_class_select']) || $_POST['asset_class_select'] == "" ){
            $sqlQuery = "SELECT asset_name, asset_class, location, acquisition_date, additions, dollar_rate_used FROM assets ORDER BY location ASC, acquisition_date ASC;";
        } else {
            $selectedAssetClass = $_POST["asset_class_select"];
            $sqlQuery = "SELECT * FROM assets WHERE asset_class = '{$selectedAssetClass}';";
        }

        $result = $conn->query($sqlQuery);

        if($result->num_rows > 0){
            echo "<div class='h-screen overflow-y-scroll mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";

            if(isset($selectedAssetClass)){
             echo "<h2 class='font-serif text-lg'>Calculations for {$selectedAssetClass}</h2>";
            } 
            echo "<table class='table-auto mb-8'>";
            echo "<thead><tr><th class='border px-4 py-2 sticky top-0 '>Asset Name</th>
                    <th class='border px-4 py-2'>Asset Class</th>
                    <th class='border px-4 py-2'>Location</th>
                    <th class='border px-4 py-2'>Acquisition Date</th>
                    <th class='border px-4 py-2'>Historical Cost (GHs)</th>
                    <th class='border px-4 py-2'>Historical Cost (USD)</th></tr></thead>";
            echo "<tbody>";
    
            while($row = $result->fetch_assoc()){
                echo "<tr><td class='border px-4 py-2'>". $row['asset_name']."</td>
                        <td class='border px-4 py-2'>". $row['asset_class']."</td>
                        <td class='border px-4 py-2'>". $row['location']."</td>
                        <td class='border px-4 py-2'>" . date('d-m-Y', strtotime($row['acquisition_date'])) . "</td>
                        <td class='border px-4 py-2'>". number_format($row['additions'], 2, ".", ","). "</td>
                        <td class='border px-4 py-2'>". number_format(($row['additions']/$row['dollar_rate_used']), 2, ".", ","). "</td></tr>";

                 $historicalSumResult = $historicalSumResult + $row['additions'];
    
            };

            echo "<tr><td class='border px-4 py-2'>".""."</td>
                    <td class='border px-4 py-2'>".""."</td>
                    <td class='border px-4 py-2'>".""."</td>
                    <td class='border px-4 py-2'>" . "" . "</td>
                    <td class='border px-4 py-2'>".""."</td></tr>";

            echo "<tr><td class='border px-4 py-2'>".""."</td>
                    <td class='border px-4 py-2'>".""."</td>
                    <td class='border px-4 py-2'>".""."</td>
                    <td class='border px-4 py-2'>" . "Total Historical Cost" . "</td>
                    <td class='border px-4 py-2'>".number_format($historicalSumResult, 2, ".", ",")."</td></tr>";

             
                echo "</tbody>";
                echo "</div>";

                //echo $historicalSumResult;
        
        } else {
            echo "No records exist for this category!";
        }

    ?>
    
</body>

</html>