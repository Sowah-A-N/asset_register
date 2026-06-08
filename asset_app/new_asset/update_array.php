<?php
// Start or resume a session
session_start();

// Check if the page is being reloaded
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Unset the session variable containing the asset ID array
    unset($_SESSION['assetIdArray']);
}

// Fetch the selected value and select name from the AJAX request
$selectedValue = isset($_POST['selected_value']) ? $_POST['selected_value'] : '';
$selectName = isset($_POST['select_name']) ? $_POST['select_name'] : '';

// Initialize the assetIdArray if not already initialized
if (!isset($_SESSION['assetIdArray'])) {
    $_SESSION['assetIdArray'] = [
        'RMU_constant' => 'RMU',
        'name_of_department' => '',
        'item_specific_code' => '',
        'dept_subclass_counter' => '0',
        'year' => substr(date('Y'), -2)
    ];
}

// Append the previously set values to the current assetIdArray
$assetIdArray = $_SESSION['assetIdArray'];

// Update the assetIdArray based on the select name and selected value
switch ($selectName) {
    case 'name_of_department':
        $assetIdArray['name_of_department'] = $selectedValue;
        break;
    case 'item_specific_code':
        $assetIdArray['item_specific_code'] = $selectedValue;
        break;
    case '':
        $assetIdArray[''] = $selectedValue;
        break;
    // Add more cases as needed for other select elements
}

// Encode the updated array as JSON and send it back as the response
echo json_encode($assetIdArray);

// Update the session with the modified assetIdArray
$_SESSION['assetIdArray'] = $assetIdArray;


