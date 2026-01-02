<?php

function validateSellerRegister($data)
{
    $name = trim($data['name'] ?? '');
    $area = trim($data['area'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirm = $data['confirm_password'] ?? '';

    $errors = [];

    if ($name === '') $errors[] = "Name is required.";
    if ($area === '' || $area === 'Select Area') $errors[] = "Area is required.";
    if ($phone === '') $errors[] = "Phone is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($password === '') $errors[] = "Password is required.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if ($phone !== '' && !preg_match('/^01[0-9]{9}$/', $phone)) {
        $errors[] = "Phone must be like 01XXXXXXXXX.";
    }

    $clean = [
        'name' => $name,
        'area' => $area,
        'phone' => $phone,
        'email' => $email,
        'password' => $password
    ];

    return [$errors, $clean];
}

function validateBuyerRegister($data)
{
    $owner_name = trim($data['owner_name'] ?? '');
    $shop_name  = trim($data['shop_name'] ?? '');
    $area       = trim($data['area'] ?? '');
    $phone      = trim($data['phone'] ?? '');
    $email      = trim($data['email'] ?? '');
    $password   = $data['password'] ?? '';
    $confirm    = $data['confirm_password'] ?? '';

    $errors = [];

    if ($owner_name === '') $errors[] = "Owner name is required.";
    if ($shop_name === '')  $errors[] = "Shop name is required.";
    if ($area === '' || $area === 'Select Area') $errors[] = "Area is required.";
    if ($phone === '') $errors[] = "Phone is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($password === '') $errors[] = "Password is required.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if ($phone !== '' && !preg_match('/^01[0-9]{9}$/', $phone)) {
        $errors[] = "Phone must be like 01XXXXXXXXX.";
    }

    $clean = [
        'owner_name' => $owner_name,
        'shop_name' => $shop_name,
        'area' => $area,
        'phone' => $phone,
        'email' => $email,
        'password' => $password
    ];

    return [$errors, $clean];
}

function validateLogin($data)
{
    $login = trim($data['login'] ?? '');
    $password = $data['password'] ?? '';

    $errors = [];
    if ($login === '') $errors[] = "Email or phone is required.";
    if ($password === '') $errors[] = "Password is required.";

    return [$errors, ['login' => $login, 'password' => $password]];
}
