<?php
include "../datacon.php";
$year = date("Y");

// Initialize variables to store the input values
$staffId = isset($_POST["staff_id"]) ? $_POST["staff_id"] : '';
$staff_first_name = isset($_POST["staff_first_name"]) ? $_POST["staff_first_name"] : '';
$staff_last_name = isset($_POST["staff_last_name"]) ? $_POST["staff_last_name"]: '';
$staff_department = isset($_POST["department"]) ? $_POST["department"]: '';
$assigned_asset_id = isset($_POST["assigned_asset_id"]) ? $_POST["assigned_asset_id"] : '';

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <!-- Include Flatpickr library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="card">

        <form action="index.inc.php" method="post">
            <h2>Add New User</h2>

            <button onclick = "location.href = './xlsUpload.php'">Upload Excel File</button>

            <label for="staffId">Staff ID:</label>
            <input type="text" id="staff_id" name="staff_id" required >

            <label for="staff_first_name">First Name:</label>
            <input type="text" id="staff_first_name" name="staff_first_name" oninput="capitalizeFirstLetter(this)" required>               
            
            <label for="staff_last_name">Last Name:</label>
            <input type="text" id="staff_last_name" name="staff_last_name" oninput="capitalizeFirstLetter(this)" required>
            
            <label for="department">Department:</label>
            <input type="text" id="department" name="department" oninput="capitalizeFirstLetter(this)" required>
            
            <label for="assigned_asset_id">Assigned Asset ID:</label>
            <input type="text" id="assigned_asset_id" name="assigned_asset_id" required>
                        
     
            <button type=submit id="add_new_user_btn" name="add_new_user_btn" class="float-left submit-button">Add User</button>
        </form>        
      
    </div>

    <!-- <script>
        async function getData(url = "", data = {}) {
            const response = await fetch(url, {
                method: "GET"
            });

            if (response.ok) {
                const result = await response.text(); // Assuming your backend sends a plain text response
                return result;
            } else {
                console.error("Error:", response.status, response.statusText);
                return null;
            }
        }

        let asset_class = document.querySelector("#asset_class");
        asset_class.addEventListener("change", function() {
            getData("index.inc.php?asset_class=" + this.value).then((data) => {
                console.log("Response:", data);


                // Do something with the data, for example, update the opening balance field
                document.querySelector("#opening_bal").value = data;
            });
        });

        
    </script> -->

</body>

</html>