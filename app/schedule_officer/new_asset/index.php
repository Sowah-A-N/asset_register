<?php
include "../datacon.php";

// Initialize variables to store the input values
$asset_name = isset($_POST['asset_name']) ? $_POST['asset_name'] : '';
$asset_class = isset($_POST['asset_class_select']) ? $_POST['asset_class_select'] : '';
$asset_sub_class = isset($_POST['asset_sub_class']) ? $_POST['asset_sub_class'] : '';
$grvNumber = isset($_POST['grvNumber']) ? $_POST['grvNumber'] : '';
$identificationNumber = isset($_POST['identificationNumber']) ? $_POST['identificationNumber'] : '';
$pvNumber = isset($_POST['pvNumber']) ? $_POST['pvNumber'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : '';
$supplier = isset($_POST['supplier']) ? $_POST['supplier'] : '';
$additions = isset($_POST['additions']) ? $_POST['additions'] : '';
$active_res_value = isset($_POST['active_res_value']) ? $_POST['active_res_value'] : '';
$acquisition_date = isset($_POST['acquisition_date']) ? $_POST['acquisition_date'] : '';

$year = date('Y');
$assetIdYear = substr($year, -2);

$_SESSION['assetIdArray'] = [];

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

        <form action="index.inc.php" method="post" id="addNewAssetForm">
            <h2>Add New Asset</h2>
            <label for="assetName">Asset Name:</label>
            <input type="text" id="asset_name" name="asset_name" required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($asset_name); ?>">

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

            <?php
            if (isset($_POST['asset_class_select'])) {
                $selectedAssetClass = $_POST['asset_class_select'];

                $sql = "SELECT * FROM asset_class_sub_classes WHERE asset_class = '$selectedAssetClass' ";
                $result = mysqli_query($conn, $sql);

                echo "<label for='asset_sub_class'>Sub-Class:</label>";
                echo "<select id='asset_sub_class' name='asset_sub_class' required>";
                echo "<option hidden value=''>Select Sub-Class</option>";

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $selected = ($row['T_id'] == $asset_sub_class) ? 'selected' : '';
                        echo "<option value='" . $row['T_id'] . "' $selected>" . $row['sub_class'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No sub-classes available</option>";
                }

                #echo "</select>";
            }
            ?>
                
           

                    
            <label for="asset_class_sub_classes">Sub-Class:</label>
            <select id="asset_class_sub_classes" name="asset_class_sub_classes" data-array-id="item_specific_code" required>
                <option hidden value="">Select Sub-Class</option>
                <?php
                // Fetch and display all sub-classes from the database
                $sql = "SELECT * FROM asset_class_sub_classes ORDER BY sub_class ASC";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['T_id'] . "' data-value='". $row['sub_class_code']. "'>" . $row['sub_class'] . " ---> " . $row['sub_class_code']. "</option>";
                    }
                } else {
                    echo "<option value=''>No sub-classes available</option>";
                }
                ?>
            </select>


        
                    
                    <label for="grvNumber">GRV Number:</label>
                    <input type="text" id="grvNumber" name="grvNumber"  required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($grvNumber); ?>">
        
                    <label for="identificationNumber">Serial/ Chassis Number:</label>
                    <input type="text" id="identificationNumber" name="identificationNumber"  required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($identificationNumber); ?>">

                    <label for="pvNumber">PV Number:</label>
                    <input type="text" id="pvNumber" name="pvNumber" required oninput="capitalizeFirstLetter(this)" value="<?php echo htmlspecialchars($pvNumber); ?>">



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
                <select id="location" name="location" data-array-id="name_of_department" onchange="" required value="<?php echo htmlspecialchars($location); ?>">
                    <option hidden value="">Select Asset Location</option>
                    <?php
                    $sql = "SELECT * FROM asset_location ";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_array($result);

                    do {
                        echo "<option value='" . $row['loc_id'] . "' data-value='". $row['loc_code'] ."'>" . $row['location'] . "</option>";
                    } while ($row = mysqli_fetch_array($result));
                    echo "</select>"; ?>

                    <script>


                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '', true);

                    </script>


                     
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
                    <input type="text" id="acquisition_date" name="acquisition_date" placeholder="DD-MM-YYYY" required value="<?php echo htmlspecialchars($acquisition_date); ?>">
                    
                    <label for="currentYear">Asset ID:</label>
                    <input type="text" id="asset_id" name="asset_id" value="" readonly>
                  
                  <script>
                    function capitalizeFirstLetter(input) {
                                // Capitalize the first letter of each word
                                input.value = input.value.replace(/\b\w/g, (char) => char.toUpperCase());
                            }
                        // Attach date picker functionality to the input field
                        flatpickr("#acquisition_date", {
                            dateFormat: "d-m-Y", // Set the desired date format
                            allowInput: true, // Allow manual input
                            maxDate: new Date().fp_incr(0) // Restrict dates to today and future
                        });
                    </script>
                    <button type="submit">Submit</button><br>
                    
        </form>
        <button onclick="location.href = '../dashboard';" id="myButton" class="float-left submit-button">Return to Dashboard</button>
   
        <?php
           
        ?>

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

        let asset_class = document.querySelector("#asset_class_select");
        asset_class.addEventListener("change", function() {
            getData("index.inc.php?asset_class=" + this.value).then((data) => {
                console.log("Response:", data);


                // Do something with the data, for example, update the opening balance field
                document.querySelector("#opening_bal").value = data;
            });
        });

        
    </script>
    

    <script> 
        document.addEventListener('DOMContentLoaded', function() {
        // Initialize updatedArray with PHP array values upon page load
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_array.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status == 200) {
                // Parse the JSON response to get the initial associative array
                var updatedArray = JSON.parse(xhr.responseText);

                // Update the asset_id input field with the initial value from the PHP array
                var assetIdInput = document.getElementById('asset_id');
                assetIdInput.value = updatedArray['RMU_constant'] + '/' + updatedArray['name_of_department'] + '/'
                                     + updatedArray['item_specific_code'] + '/' + updatedArray['dept_subclass_counter'] + '/' + updatedArray['year'];

                // Attach change event listeners to select elements
                var selectElements = document.querySelectorAll('select[data-array-id]');
                selectElements.forEach(function(select) {
                    select.addEventListener('change', function() {
                        var selectedValue = select.options[select.selectedIndex].getAttribute('data-value');
                        var selectName = select.getAttribute('data-array-id');

                        // Prepare the AJAX request for updating the array
                        var xhrUpdate = new XMLHttpRequest();
                        xhrUpdate.open('POST', 'update_array.php', true);
                        xhrUpdate.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                        xhrUpdate.onload = function() {
                            if (xhrUpdate.status == 200) {
                                // Parse the JSON response to get the updated associative array
                                updatedArray = JSON.parse(xhrUpdate.responseText);

                                // Update the asset_id input field with the new value from the updated array
                                assetIdInput.value = updatedArray['RMU_constant'] + '/' + updatedArray['name_of_department']  + '/' + updatedArray['item_specific_code'] + '/'
                                                      + updatedArray['dept_subclass_counter'] + '/' + updatedArray['year'];
                            } else {
                                console.error('Request failed. Error code: ' + xhrUpdate.status);
                            }
                        };

                        // Send the selected value and name of the select element to the PHP file for updating the array
                        xhrUpdate.send('selected_value=' + encodeURIComponent(selectedValue) + '&select_name=' + encodeURIComponent(selectName));
                    });
                });
            } else {
                console.error('Request failed. Error code: ' + xhr.status);
            }
        };

        // Send initial AJAX request for fetching the PHP array values
        xhr.send();
    });

    </script> 

</body>



</html>