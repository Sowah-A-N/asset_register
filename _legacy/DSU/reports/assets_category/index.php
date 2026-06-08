<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

</head>
<body>
    <center>


            <h1 class="text-dark font-weight-bold mb-2"> <b>View Assets Classes</b> </h1>
            <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
              <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
              
          </center>

          <center>

    <?php

        include "../datacon.php";

        $query = "SELECT * FROM asset_classes";

        $result = $conn->query($query);

        if($result->num_rows > 0){
            echo "<table class='table-auto border-collapse mb-6'>";
            echo "<thead><tr><th class='border px-4 py-2'>Asset ID</th>
                    <th class='border px-4 py-2'>Asset Class</th>
                    <th class='border px-4 py-2'>Opening Balance(GHS)</th>
                    <th class='border px-4 py-2'>Depreciation Rate</th>
                    <th class='border px-4 py-2'>Estimated Life(Years)</th>
                    </thead>";
                    
            echo "<tbody>";

            $serial_number = 1; // Initialize serial number counter

                while ($row = $result->fetch_assoc()) {
                    echo "<tr class=''>
                            <td class='border px-4 py-2'>" . $serial_number . "</td>
                           
                            <td class='border px-4 py-2'>" . $row['asset_class'] . "</td>
                            <td class='border px-4 py-2'>" . number_format($row['opening_bal'], 2, '.', ',') . "</td>
                            <td class='border px-4 py-2'>" . $row['dep_rate'] . "</td>
                            <td class='border px-4 py-2'>" . $row['estimated_life'] . "</td>
                          </tr>";
                    $serial_number++; // Increment serial number
                }
            echo "</tbody></table>";
            

        }

    ?>
   </center> 
</body>
</html>