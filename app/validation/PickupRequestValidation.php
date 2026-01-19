<?php

function validatePickupRequest(array $post): array
{
    $errors = [];

    $itemId  = trim($post['scrap_item_id'] ?? '');
    $weight  = trim($post['estimated_weight'] ?? '');
    $phone   = trim($post['contact_phone'] ?? '');
    $address = trim($post['address'] ?? '');
    $desired = trim($post['desired_datetime'] ?? '');

    if ($itemId === '' || !ctype_digit($itemId)) {
        $errors[] = "Scrap item is required.";
    }

    if ($weight === '' || !is_numeric($weight) || (float)$weight <= 0) {
        $errors[] = "Estimated weight must be a positive number.";
    }

    if ($phone === '') {
        $errors[] = "Phone number is required.";
    }

    if ($address === '') {
        $errors[] = "Address is required.";
    }

    if ($desired === '') {
        $errors[] = "Pickup date & time is required.";
    }

    $clean = [
        'scrap_item_id'    => (int)$itemId,
        'estimated_weight' => (float)$weight,
        'contact_phone'    => $phone,
        'address'          => $address,
        'desired_datetime' => $desired
    ];

    return [$errors, $clean];
}
