<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Additions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

</head>
<body>
    <!--label for="">From:</label>
    <input type="date" name="from" id="from-date"><br />

    <label for="">To:</label>
    <input type="date" name="to" id="to-date"><br />

    <button type="submit">Generate</button-->

    <form action="" method="post" class="flex items-center space-x-4">
        <label for="asset_year_select" class="text-lg font-semibold">Select Acquisition Year:</label>
        <select name="asset_year_select" id="asset_year_select" class="p-2 border border-gray-300 rounded">
            <option value="" selected> --Select Year--</option>
            
            <?php
            $currentYear = date("Y");
            $startYear = $currentYear - 51;

            for ($year = $currentYear; $year >= $startYear; $year--) {
                echo "<option value=\"$year\">$year</option>";
            }
            ?>
        </select>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Filter</button>
    </form>
   

    <?php

        $currentYear = date("Y");
        //echo $currentYear;

        $additionSum = 0;

        $selectedYear = (!isset($_POST['asset_year_select'])) ? $currentYear : $_POST['asset_year_select'];

        //$additionalQuery = " WHERE asset_class = {}";

        #if (!isset($_POST[])){ }

        include "../datacon.php";

        $query = "SELECT * FROM assets WHERE YEAR(acquisition_date) = {$selectedYear}";

        $result = $conn->query($query);

        if($result->num_rows > 0){
            echo "<div class='h-screen overflow-y-scroll mx-auto w-3/4 bg-white p-4 shadow-md rounded mt-4'>";
            echo "<h2 class='mt-6 text-xl font-bold mb-4'>Search Results for {$selectedYear}:</h2>";
            echo "<table class='table-auto mb-6'>";
            echo "<thead><tr><th class='border px-4 py-2 sticky top-0 '>Asset Name</th>
                    <th class='border px-4 py-2'>Asset Class</th>
                    <th class='border px-4 py-2'>Location</th>
                    <th class='border px-4 py-2'>Acquisition Date</th>
                    <th class='border px-4 py-2'>Dollar Rate Used</th>
                    <th class='border px-4 py-2'>Additions (GHS) </th>
                    <th class='border px-4 py-2'>Additions (USD) </th></tr></thead>";
            echo "<tbody>";

            while($row = $result->fetch_assoc()){
                echo "<tr id =". $row['asset_id'] ." class=' hover:bg-gray-100'>
                <td class='border px-4 py-2'>" . $row['asset_name'] . "</td>
                <td class='border px-4 py-2'>" . $row['asset_class'] . "</td>
                <td class='border px-4 py-2'>" . $row['location'] . "</td>
                <td class='border px-4 py-2'>" . date('d-m-Y', strtotime($row['acquisition_date'])) . "</td>
                <td class='border px-4 py-2'>" . $row['dollar_rate_used'] . "</td>
                <td class='border px-4 py-2'>" . number_format($row['additions'], 2, ".", ",") . "</td>
                <td class='border px-4 py-2'>" . number_format($row['additions']/$row['dollar_rate_used'], 2, ".", ",") . "</td>
                <td class='border px-4 py-2'>" . "<button class='bg-blue-500 text-white px-4 py-2 rounded' onclick=\"window.location.href='../individual_assets/index.php?asset_id={$row['asset_id']}'\">"."View Records"."</button>" . "</td></tr>";


                $additionSum = $additionSum + $row['additions'];
            }
            echo "<tr><td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>" . "" . "</td>
            <td class='border px-4 py-2'>".""."</td></tr>";

    echo "<tr><td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>".""."</td>
            <td class='border px-4 py-2'>" . "Additions for {$selectedYear} : " . "</td>
            <td class='border px-4 py-2'>".number_format($additionSum, 2, ".", ",")."</td></tr>";

     
        echo "</tbody>";
        echo "</div>";
        //echo "Additions for {$selectedYear} : {$additionSum}";


        }

    ?>
    
</body>

</html>