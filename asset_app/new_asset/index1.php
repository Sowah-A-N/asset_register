<?php
include "../datacon.php";
$year = date("Y");

// Initialize variables to store the input values
$asset_name = isset($_POST['asset_name']) ? $_POST['asset_name'] : '';
$asset_class = isset($_POST['asset_class']) ? $_POST['asset_class'] : '';
$asset_sub_class = isset($_POST['asset_sub_class']) ? $_POST['asset_sub_class'] : '';
$serialNumber  = isset($_POST['serial_number']) ? $_POST['serial_number'] : '';
$grvNumber = isset($_POST['grvNumber']) ? $_POST['grvNumber'] : '';
$identificationNumber = isset($_POST['identificationNumber']) ? $_POST['identificationNumber'] : '';
$pvNumber = isset($_POST['pvNumber']) ? $_POST['pvNumber'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : '';
$supplier = isset($_POST['supplier']) ? $_POST['supplier'] : '';
$additions = isset($_POST['additions']) ? $_POST['additions'] : '';
$active_res_value = isset($_POST['active_res_value']) ? $_POST['active_res_value'] : '';
$acquisition_date = isset($_POST['acquisition_date']) ? $_POST['acquisition_date'] : '';

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Asset</title>
    <!-- Include Flatpickr library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="card">

        <form action="index.inc.php" method="post">
            <h2>Add New Asset</h2>
            <label for="assetName">Asset Name:</label>
            <input type="text" id="asset_name" name="asset_name" required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($asset_name); ?>">

            <label for="assetClass">Asset Class:</label>
            <select id="asset_class" name="asset_class" required>
                <option hidden value="">Select Asset Class</option>
                <?php
                $sql = "SELECT * FROM asset_classes ";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);

                do {
                    $selected = ($row['ast_id'] == $asset_class) ? 'selected' : '';
                    echo "<option value='" . $row['ast_id'] . "' $selected>" . $row['asset_class'] . "</option>";
                } while ($row = mysqli_fetch_array($result));
                echo "</select>"; ?>
            </select>

<script>
                // Wait for the DOM content to be fully loaded
                document.addEventListener('DOMContentLoaded', function() {
                    // Get a reference to the asset class select element
                    var assetClassSelect = document.getElementById('asset_class_select');
                    // Get a reference to the asset sub-class select element
                    var assetSubClassSelect = document.getElementById('asset_sub_class');

                    // Event listener for changes in the asset class select element
                    assetClassSelect.addEventListener('change', function() {
                        // Get the selected asset class value
                        var assetClass = assetClassSelect.value;

                        // Clear the existing options in the sub-class select element
                        assetSubClassSelect.innerHTML = '';

                        // If an asset class is selected
                        if (assetClass) {
                            // Prepare the AJAX request
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', 'fetch_subclasses.php', true); // Adjust the URL accordingly
                            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                            // Define the callback function when the request completes
                            xhr.onload = function() {
                                // If the request is successful
                                if (xhr.status == 200) {
                                    // Populate the sub-class select element with the response
                                    assetSubClassSelect.innerHTML = xhr.responseText;
                                } else {
                                    // Handle errors if any
                                    console.error('Request failed. Error code: ' + xhr.status);
                                }
                            };

                            // Send the AJAX request with the selected asset class
                            xhr.send('asset_class=' + encodeURIComponent(assetClass));
                        } else {
                            // If no asset class is selected, show default message
                            assetSubClassSelect.innerHTML = '<option>Select Asset Class First</option>';
                        }
                    });
                });
</script>

<label for="asset_sub_class">Sub-Class:</label>
<select id="asset_sub_class" name="asset_sub_class" required>
    <option hidden value="">Select Sub-Class</option>
    <?php
    // Fetch and display all sub-classes from the database
    $sql = "SELECT * FROM asset_class_sub_classes ";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['T_id'] . "'>" . $row['sub_class'] . "</option>";
        }
    } else {
        echo "<option value=''>No sub-classes available</option>";
    }
    ?>
</select>

<!-- <script>
   document.addEventListener('DOMContentLoaded', function() {
    var assetClassSelect = document.getElementById('asset_class_select');
    var assetSubClassSelect = document.getElementById('asset_sub_class');

    assetClassSelect.addEventListener('change', function() {
        var assetClass = assetClassSelect.value;

        if (assetClass) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'index_copy.php', true); // Use the same PHP file where your form is
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status == 200) {
                    assetSubClassSelect.innerHTML = xhr.responseText;
                } else {
                    console.error('Request failed. Error code: ' + xhr.status);
                }
            };

            // Send the selected asset class value to the PHP file
            xhr.send('asset_class=' + encodeURIComponent(assetClass));
        } else {
            assetSubClassSelect.innerHTML = '<option>Select Asset Class First</option>';
        }
    });
});

</script> -->


<label for="serialNumber">Serial Number:</label>
                    <input type="text" id="serial_number" name="serial_number" minlength="4"  maxlength="10" required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($serialNumber); ?>">

                    <label for="serialNumber">Identification Number: **</label>
                    <input type="text" id="id_number" name="id_number" minlength="4"  maxlength="20" >
                    
                    <label for="grvNumber">GRV Number:</label>
                <input type="text" id="grvNumber" name="grvNumber" minlength="4"  maxlength="6" required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($grvNumber); ?>">

        
                    

                    <label for="pvNumber">PV Number:</label>
                    <input type="text" id="pvNumber" name="pvNumber" minlength="4"  maxlength="8" required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($pvNumber); ?>">



        <label for="assetClass">Supplier Name:</label>
            <select id="supplier" name="supplier" required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($supplier); ?>">
                <option hidden value="">Select Supplier</option>
                <?php
                $sql = "SELECT * FROM suppliers ";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);

                do {
                    echo "<option value='" . $row['sup_id'] . "'>" . $row['name'] . "</option>";
                } while ($row = mysqli_fetch_array($result));
                echo "</select>"; ?>

            </select>
      

            <label for="Opening_bal">Opening Balance (GHS):</label>
            <input type="text" id="opening_bal" name="opening_bal" readonly>
            

            <label for="assetType">Asset Type:</label>
            <select id="asset_type" name="asset_type" required>
                <option hidden value="">Select Asset Type</option>
                <?php
                $sql = "SELECT * FROM asset_type ";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result);

                do {
                    echo "<option value='" . $row['type_id'] . "'>" . $row['asset_type'] . "</option>";
                } while ($row = mysqli_fetch_array($result));
                echo "</select>"; ?>



                <label for="location">Location:</label>
                <select id="location" name="location" required value="<?php echo htmlspecialchars($location); ?>">
                    <option hidden value="">Select Asset Location</option>
                    <?php
                    $sql = "SELECT * FROM asset_location ";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($result);

                    do {
                        echo "<option value='" . $row['loc_id'] . "'>" . $row['location'] . "</option>";
                    } while ($row = mysqli_fetch_array($result));
                    echo "</select>"; ?>

                    <label for="User">Assigned User:</label>
                <select id="user" name="user" required value="<?php echo htmlspecialchars($location); ?>">
                    <option hidden value="">Select Asset User</option>
                    <?php
                    $sql = "SELECT * FROM asset_users ";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($result);

                    do {
                        echo "<option value='" . $row['t_id'] . "'>" . $row['staff_first_name'] ." ". $row['staff_last_name']. "</option>";
                    } while ($row = mysqli_fetch_array($result));
                    echo "</select>"; ?>


                    <label for="historicalCost">Historical Cost (GHS):</label>
                    <input type="text" id="additions" name="additions" required value="<?php echo htmlspecialchars($additions); ?>">

                    <label for="historicalCost">Active Residual Value (GHS):</label>
                    <input type="text" id="active_res_value" name="active_res_value" required value="<?php echo htmlspecialchars($active_res_value); ?>">
                    


                    <label for="currentYear">Current Year:</label>
                    <input type="text" id="current_year" name="current_year" value="<?php echo $year;?>" readonly>

                    <label for="acquisitionDate">Acquisition Date:</label>
                    <input type="text" id="acquisition_date" name="acquisition_date" placeholder="DD-MM-YYYY"
                       required value="<?php echo htmlspecialchars($acquisition_date); ?>">
                    <script>


                    function capitalizeFirstLetter(input) {
                                // Capitalize the first letter of each word
                                input.value = input.value.replace(/\b\w/g, (char) => char.toUpperCase());
                            }
                       // Attach date picker functionality to the input field
                       flatpickr("#acquisition_date", {
                            dateFormat: "d-m-Y", // Set the desired date format
                            allowInput: true, // Allow manual input
                            maxDate: new Date().fp_incr(0), // Restrict dates to today and future
                        });
                    </script>





                    <button type="submit">Submit</button><br>


                    
        </form>
        <button onclick="location.href = '../dashboard';" id="myButton" class="float-left submit-button">Return to Dashboard</button>

      
    </div>

    <script>
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

        
    </script>

</body>

</html>