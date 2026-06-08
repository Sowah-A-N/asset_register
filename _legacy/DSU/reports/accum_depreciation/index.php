<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets Locations</title>
</head>
<body>
    <label for="">From:</label>
    <input type="date" name="from" id="from-date"><br />

    <label for="">To:</label>
    <input type="date" name="to" id="to-date"><br />

    <button type="submit">Generate</button>

    <?php

        include "../datacon.php";

        $query = "SELECT total_accumulated_depreciation FROM calculations";

        $result = $conn->query($query);

        if($result->num_rows > 0){
            echo "<div class='h-full overflow-y-scroll mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";
            echo "<table class='table-auto mb-8'>";
            echo "<thead><tr><th class='border px-4 py-2'>Asset Name</th>
                    <th class='border px-4 py-2'>Acquisition Date</th></tr></thead>";
            echo "<tbody>";
    
            while($row = $result->fetch_assoc()){
                echo "<tr><td class='border px-4 py-2'>". $row['asset_name']."</td>
                        <td class='border px-4 py-2'>". $row['acquisition_date']."</td></tr>";
    
                echo "</tbody>";
                echo "</div>";

        }
    }

    ?>
    
</body>

</html>