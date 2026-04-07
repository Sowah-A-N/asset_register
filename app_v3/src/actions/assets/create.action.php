<?php
// Action: asset_create_step1 / asset_create_step2 / asset_create_back

$base_url = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$action   = post('_action');

// ── Back to Step 1 ─────────────────────────────────────────────────────────
if ($action === 'asset_create_back') {
    csrf_verify();
    $_SESSION['_asset_create_step'] = 1;
    header('Location: ' . $base_url . '/assets/create');
    exit;
}

// ── Step 1: validate & save draft to session ────────────────────────────────
if ($action === 'asset_create_step1') {
    csrf_verify();
    require_role('hod_ict', 'schedule_officer');

    $supplier = post('supplier_name') === '__other__'
        ? post('supplier_name_other')
        : post('supplier_name');

    $draft = [
        'asset_name'      => post('asset_name'),
        'asset_class'     => post('asset_class'),
        'sub_class'       => post('sub_class'),
        'supplier_name'   => $supplier,
        'asset_type'      => post('asset_type'),
        'location'        => post('location'),
        'user'            => post('asset_user'),
        'acquisition_date'=> post('acquisition_date'),
        'current_year'    => post_int('current_year'),
        'dollar_rate_used'=> post_float('dollar_rate_used'),
        'additions'       => post_float('additions'),
        'historical_cost' => post_float('historical_cost'),
        'active_res_value'=> post_float('active_res_value'),
        'grv_number'      => post('grv_number') ?: 'N/A',
        'serial_number'   => post('serial_number') ?: 'N/A',
        'pv_number'       => post('pv_number') ?: 'N/A',
        'id_number'       => post('id_number') ?: 'N/A',
    ];

    // Basic validation
    $errors = [];
    if ($draft['asset_name'] === '') $errors[] = 'Asset name is required.';
    if ($draft['asset_class'] === '') $errors[] = 'Asset class is required.';
    if ($draft['supplier_name'] === '') $errors[] = 'Supplier is required.';
    if ($draft['location'] === '') $errors[] = 'Location is required.';
    if ($draft['acquisition_date'] === '') $errors[] = 'Acquisition date is required.';
    if ($draft['additions'] < 0) $errors[] = 'Additions must be a positive number.';

    if ($errors) {
        foreach ($errors as $e) set_flash('error', $e);
        $_SESSION['_asset_draft'] = $draft;
        header('Location: ' . $base_url . '/assets/create');
        exit;
    }

    $_SESSION['_asset_draft']       = $draft;
    $_SESSION['_asset_create_step'] = 2;
    header('Location: ' . $base_url . '/assets/create');
    exit;
}

// ── Step 2: save to DB ──────────────────────────────────────────────────────
if ($action === 'asset_create_step2') {
    csrf_verify();
    require_role('hod_ict', 'schedule_officer');

    if (empty($_SESSION['_asset_draft'])) {
        set_flash('error', 'Session expired. Please start again.');
        header('Location: ' . $base_url . '/assets/create');
        exit;
    }

    $d = $_SESSION['_asset_draft'];

    // Update ID fields from step 2 form
    $d['grv_number']    = post('grv_number') ?: 'N/A';
    $d['serial_number'] = post('serial_number') ?: 'N/A';
    $d['pv_number']     = post('pv_number') ?: 'N/A';
    $d['id_number']     = post('id_number');

    if ($d['id_number'] === '') {
        set_flash('error', 'Asset ID number is required.');
        header('Location: ' . $base_url . '/assets/create');
        exit;
    }

    $stmt = mysqli_prepare($conn,
        'INSERT INTO assets
            (asset_name, grv_number, serial_number, pv_number, id_number,
             supplier_name, asset_class, sub_class, asset_type, location, `user`,
             acquisition_date, current_year, historical_cost, additions,
             disposal_value, active_res_value, dollar_rate_used)
         VALUES (?,?,?,?,?, ?,?,?,?,?,?, ?,?,?,?, ?,?,?)');

    mysqli_stmt_bind_param($stmt, 'ssssssssssssiddddd',
        $d['asset_name'],
        $d['grv_number'],
        $d['serial_number'],
        $d['pv_number'],
        $d['id_number'],
        $d['supplier_name'],
        $d['asset_class'],
        $d['sub_class'],
        $d['asset_type'],
        $d['location'],
        $d['user'],
        $d['acquisition_date'],
        $d['current_year'],
        $d['historical_cost'],
        $d['additions'],
        0.00,       // disposal_value
        $d['active_res_value'],
        $d['dollar_rate_used']
    );

    if (mysqli_stmt_execute($stmt)) {
        $new_id = (int)mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Clear draft
        unset($_SESSION['_asset_draft'], $_SESSION['_asset_create_step']);

        audit_log($conn, 'asset.create', 'assets', $new_id, 'Created: ' . $d['asset_name']);
        set_flash('success', 'Asset "' . $d['asset_name'] . '" saved successfully.');
        header('Location: ' . $base_url . '/assets');
        exit;
    }

    mysqli_stmt_close($stmt);
    set_flash('error', 'Database error: ' . mysqli_error($conn));
    header('Location: ' . $base_url . '/assets/create');
    exit;
}
