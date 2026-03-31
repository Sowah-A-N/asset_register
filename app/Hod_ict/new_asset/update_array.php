<?php
require_once __DIR__ . '/../../security.php';
secure_session_start();

// Auth guard — this AJAX endpoint must require a valid session
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthenticated']);
    exit();
}

header('Content-Type: application/json');

// On GET (page reload), clear the stored asset ID components
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($_SESSION['assetIdArray']);
    echo json_encode([]);
    exit();
}

$selectedValue = $_POST['selected_value'] ?? '';
$selectName    = $_POST['select_name'] ?? '';

// Initialise the array if not already present
if (!isset($_SESSION['assetIdArray'])) {
    $_SESSION['assetIdArray'] = [
        'RMU_constant'          => 'RMU',
        'name_of_department'    => '',
        'item_specific_code'    => '',
        'dept_subclass_counter' => '0',
        'year'                  => substr(date('Y'), -2),
    ];
}

$assetIdArray = $_SESSION['assetIdArray'];

// Only allow known keys to be updated (whitelist)
$allowed = ['name_of_department', 'item_specific_code'];
if (in_array($selectName, $allowed, true)) {
    $assetIdArray[$selectName] = $selectedValue;
}

$_SESSION['assetIdArray'] = $assetIdArray;

echo json_encode($assetIdArray);
