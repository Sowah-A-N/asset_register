<?php
include "../datacon.php";

?>
<html lang="en">

<head>
    
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Untracked Assets Form</title>
    <!-- Include Flatpickr library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <h2>Untracked Assets</h2>
    <div class="card">
        <form action="index.inc.php" method="post">
            <label for="asset_name">Asset Name:</label>
            <input type="text" id="asset_name" name="asset_name" placeholder="Enter Asset Name" required>
            
            <label for="asset_value">Asset Value (GHS):</label>
            <input type="text" id="asset_value" name="asset_value" placeholder="Enter Asset Value" required>
            
            <label for="acquisition_date">Acquisition Date:</label>
            <input type="date" id="acquisition_date" placeholder="DD-MM-YYYY" name="acquisition_date" required>
            
            <label for="disposal_date">Date of Disposal:</label>
            <input type="date" id="disposal_date" placeholder="DD-MM-YYYY" name="disposal_date">
            
            <label for="asset_class_select" class="text-lg font-semibold">Asset Class:</label>
            <select name='asset_class_select' id='asset_class_select'>
                <option hidden value="">Select Asset Class</option>
                <?php
                $sql = "SELECT * FROM asset_classes ORDER BY asset_class ASC;";
                $result = mysqli_query($conn, $sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $selected = ($row['ast_id'] == $asset_class) ? 'selected' : '';
                        echo "<option value='" . $row["ast_id"] . "' $selected>" . $row["asset_class"] . "</option>";
                    }
                } else {
                    echo "<option value=\"\">No asset classes available</option>";
                }
                ?>
            </select>
            

            <label for="asset_value">Dollar Rate:</label>
            <input type="text" id="dollar_rate" name="dollar_rate" placeholder="Enter Dollar Rate" required>

            <button type="submit">Submit</button>
        </form>

        
        <button onclick="location.href = '../dashboard';" id="myButton" class="float-left submit-button">Return to Dashboard</button>
    </div>
    <script>
    flatpickr("#acquisition_date", {
        dateFormat: "d-m-Y", // Set the desired date format
        allowInput: true, // Allow manual input
        maxDate: new Date().fp_incr(0) // Restrict dates to today and past
    });

    flatpickr("#disposal_date", {
        dateFormat: "d-m-Y", 
        allowInput: true
    });
</script>

</body>
</html>
