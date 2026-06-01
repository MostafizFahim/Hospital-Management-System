<?php
$host = "localhost";
$user = "root";
$password = "root";
$database = "hmsDB";

$connect = mysqli_connect($host, $user, $password, $database);

if (!$connect) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($connect, "utf8mb4");

function db_escape($value)
{
    global $connect;
    return mysqli_real_escape_string($connect, trim((string) $value));
}

function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function hash_user_password($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function password_matches($password, $storedPassword)
{
    return password_verify($password, $storedPassword) || hash_equals((string) $storedPassword, (string) $password);
}

function password_is_legacy($storedPassword)
{
    return empty(password_get_info($storedPassword)['algo']);
}

function db_select_one($sql, $types = '', ...$params)
{
    global $connect;
    $stmt = mysqli_prepare($connect, $sql);
    if (!$stmt) {
        die("Database query failed: " . mysqli_error($connect));
    }
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result ? mysqli_fetch_assoc($result) : null;
}

function db_execute($sql, $types = '', ...$params)
{
    global $connect;
    $stmt = mysqli_prepare($connect, $sql);
    if (!$stmt) {
        die("Database query failed: " . mysqli_error($connect));
    }
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    return mysqli_stmt_execute($stmt);
}

function is_valid_image_upload($file, &$error = '')
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $error = "Image upload failed";
        return false;
    }

    $maxSize = 2 * 1024 * 1024; // 2 MB
    if ($file['size'] > $maxSize) {
        $error = "Image size must be less than 2MB";
        return false;
    }

    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowedMimeTypes, true)) {
        $error = "Only JPG, PNG, and GIF images are allowed";
        return false;
    }

    return true;
}

function save_uploaded_image($file, $destinationDir, &$error = '')
{
    if (!is_valid_image_upload($file, $error)) {
        return false;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($extension, $allowedExtensions, true)) {
        $error = "Invalid image extension";
        return false;
    }

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0755, true);
    }

    $filename = uniqid('profile_', true) . '.' . $extension;
    $target = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        $error = "Could not save uploaded image";
        return false;
    }

    return $filename;
}
?>
