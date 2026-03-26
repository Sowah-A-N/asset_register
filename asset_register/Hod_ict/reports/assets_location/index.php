<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Assets Locations</title>
</head>
<body>
    <center>
        <h1 class="text-dark font-weight-bold mb-2"> <b>View Asset Locations</b> </h1>
        <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
            <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0"></div>
        </div>
    </center>
    <center>
    <?php
        include "../datacon.php";

        $query = "SELECT * FROM asset_location";

        $result = $conn->query($query);

        if($result->num_rows > 0){
            echo "<table class='table-auto border-collapse mb-6'>";
            echo "<thead><tr>
                    <th class='border px-4 py-2'>Location ID</th>
                    <th class='border px-4 py-2'>Location</th>
                  </tr></thead>";
             
            $serial_number = 1;
            while($row = $result->fetch_assoc()){
                echo "<tr class='hover:bg-gray-100'>";
                echo "<td class='border px-4 py-2'>" . $serial_number . "</td>";
                echo "<td class='border px-4 py-2'>" . $row['location'] . "</td>";
                echo "</tr>";
                $serial_number++;
            }

            echo "</table>";
        }
    ?>
    </center>
</body>
</html>
