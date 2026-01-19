<?php

function validateProfileImageUpload(array $file): array
{
    $errors = [];

    if (empty($file) || !isset($file['error'])) {
        $errors[] = "Please select a photo.";
        return [$errors, null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Upload failed. Please try again.";
        return [$errors, null];
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        $errors[] = "File size must be less than 5MB.";
    }

    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg'])) {
        $errors[] = "Only JPG/JPEG files are allowed.";
    }

    $clean = [
        'ext' => $ext
    ];

    return [$errors, $clean];
}
