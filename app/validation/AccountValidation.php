<?php

function validateDealerProfileUpdate($data)
{
    $name      = trim($data['name'] ?? '');
    $shop_name = trim($data['shop_name'] ?? '');
    $area      = trim($data['area'] ?? '');
    $phone     = trim($data['phone'] ?? '');
    $email     = trim($data['email'] ?? '');

    $errors = [];

    if ($name === '') $errors[] = "Owner name is required.";
    if ($shop_name === '') $errors[] = "Shop name is required.";
    if ($area === '' || $area === 'Select Area') $errors[] = "Area is required.";
    if ($phone === '') $errors[] = "Phone is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    if ($phone !== '' && !preg_match('/^01[0-9]{9}$/', $phone)) {
        $errors[] = "Phone must be like 01XXXXXXXXX.";
    }

    $clean = [
        'name'      => $name,
        'shop_name' => $shop_name,
        'area'      => $area,
        'phone'     => $phone,
        'email'     => $email
    ];

    return [$errors, $clean];
}

function validateDealerPasswordChange($data)
{
    $current = $data['current_password'] ?? '';
    $new     = $data['new_password'] ?? '';
    $confirm = $data['confirm_password'] ?? '';

    $errors = [];

    if (strlen($new) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($new !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    return [$errors, [
        'current' => $current,
        'new' => $new
    ]];
}

