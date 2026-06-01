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

function db_column_exists($table, $column)
{
    global $connect, $database;

    $row = db_select_one(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1",
        "sss",
        $database,
        $table,
        $column
    );

    return !empty($row);
}

function db_index_exists($table, $index)
{
    global $database;

    $row = db_select_one(
        "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1",
        "sss",
        $database,
        $table,
        $index
    );

    return !empty($row);
}

function hms_add_unique_if_clean($table, $index, $column)
{
    global $connect;

    if (!db_column_exists($table, $column) || db_index_exists($table, $index)) {
        return;
    }

    $duplicate = db_select_one(
        "SELECT `$column`, COUNT(*) AS total FROM `$table` WHERE `$column` IS NOT NULL AND `$column` <> '' GROUP BY `$column` HAVING total > 1 LIMIT 1"
    );

    if (!$duplicate) {
        mysqli_query($connect, "ALTER TABLE `$table` ADD UNIQUE KEY `$index` (`$column`)");
    }
}

function hms_ensure_schema()
{
    global $connect;

    if (!db_column_exists('admin', 'role')) {
        mysqli_query($connect, "ALTER TABLE admin ADD role varchar(30) NOT NULL DEFAULT 'Admin' AFTER profile");
    }

    if (!db_column_exists('admin', 'status')) {
        mysqli_query($connect, "ALTER TABLE admin ADD status varchar(30) NOT NULL DEFAULT 'Active' AFTER role");
    }

    mysqli_query($connect, "UPDATE admin SET role = 'Admin' WHERE role IS NULL OR role = ''");
    mysqli_query($connect, "UPDATE admin SET status = 'Active' WHERE status IS NULL OR status = ''");

    $superAdmin = db_select_one("SELECT id FROM admin WHERE role = 'Super Admin' ORDER BY id ASC LIMIT 1");
    if (!$superAdmin) {
        $superAdmin = db_select_one("SELECT id FROM admin ORDER BY id ASC LIMIT 1");
        if ($superAdmin) {
            db_execute("UPDATE admin SET role = 'Super Admin', status = 'Active' WHERE id = ?", "i", $superAdmin['id']);
        }
    }
    if ($superAdmin) {
        db_execute("UPDATE admin SET status = 'Active' WHERE id = ?", "i", $superAdmin['id']);
        db_execute("UPDATE admin SET role = 'Admin' WHERE role = 'Super Admin' AND id <> ?", "i", $superAdmin['id']);
    }

    if (db_column_exists('patient', 'country')) {
        mysqli_query($connect, "ALTER TABLE patient MODIFY country varchar(100) NULL DEFAULT NULL");
    }

    if (!db_column_exists('patient', 'address')) {
        mysqli_query($connect, "ALTER TABLE patient ADD address text DEFAULT NULL AFTER phone");
    }

    if (db_column_exists('doctors', 'country')) {
        mysqli_query($connect, "ALTER TABLE doctors MODIFY country varchar(100) NULL DEFAULT NULL");
    }

    if (!db_column_exists('doctors', 'address')) {
        mysqli_query($connect, "ALTER TABLE doctors ADD address text DEFAULT NULL AFTER phone");
    }

    if (!db_column_exists('doctors', 'qualification')) {
        mysqli_query($connect, "ALTER TABLE doctors ADD qualification varchar(255) DEFAULT NULL AFTER profile");
    }

    if (!db_column_exists('doctors', 'specialization')) {
        mysqli_query($connect, "ALTER TABLE doctors ADD specialization varchar(255) DEFAULT NULL AFTER qualification");
    }

    if (!db_column_exists('doctors', 'license_number')) {
        mysqli_query($connect, "ALTER TABLE doctors ADD license_number varchar(100) DEFAULT NULL AFTER specialization");
    }

    if (!db_column_exists('doctors', 'certification')) {
        mysqli_query($connect, "ALTER TABLE doctors ADD certification text DEFAULT NULL AFTER license_number");
    }

    if (!db_column_exists('doctors', 'experience')) {
        mysqli_query($connect, "ALTER TABLE doctors ADD experience text DEFAULT NULL AFTER certification");
    }

    if (!db_column_exists('doctors', 'consultation_fee')) {
        mysqli_query($connect, "ALTER TABLE doctors ADD consultation_fee decimal(10,2) NOT NULL DEFAULT 0.00 AFTER salary");
        mysqli_query($connect, "UPDATE doctors SET consultation_fee = CAST(salary AS DECIMAL(10,2)) WHERE consultation_fee = 0 AND salary REGEXP '^[0-9]+(\\.[0-9]+)?$'");
    }

    mysqli_query($connect, "UPDATE doctors SET status = 'Pending' WHERE LOWER(status) = 'pendding'");

    if (!db_column_exists('appointment', 'appointment_time')) {
        mysqli_query($connect, "ALTER TABLE appointment ADD appointment_time time DEFAULT NULL AFTER appointment_date");
    }

    if (!db_column_exists('appointment', 'appointment_status')) {
        mysqli_query($connect, "ALTER TABLE appointment ADD appointment_status varchar(30) NOT NULL DEFAULT 'Pending' AFTER symptoms");
        mysqli_query($connect, "UPDATE appointment SET appointment_status = CASE WHEN status = 'Discharged' THEN 'Completed' ELSE status END");
    }

    if (!db_column_exists('appointment', 'payment_status')) {
        mysqli_query($connect, "ALTER TABLE appointment ADD payment_status varchar(30) NOT NULL DEFAULT 'Unpaid' AFTER appointment_status");
    }

    if (!db_column_exists('appointment', 'prescription_status')) {
        mysqli_query($connect, "ALTER TABLE appointment ADD prescription_status varchar(30) NOT NULL DEFAULT 'Not Created' AFTER payment_status");
    }

    if (!db_column_exists('appointment', 'updated_at')) {
        mysqli_query($connect, "ALTER TABLE appointment ADD updated_at varchar(100) DEFAULT NULL AFTER date_booked");
    }

    mysqli_query($connect, "UPDATE appointment SET status = 'Completed' WHERE status = 'Discharged'");
    mysqli_query($connect, "UPDATE appointment SET appointment_status = 'Completed' WHERE appointment_status = 'Discharged'");
    mysqli_query($connect, "UPDATE appointment SET payment_status = 'Unpaid' WHERE payment_status IS NULL OR payment_status = ''");
    mysqli_query($connect, "UPDATE appointment SET prescription_status = 'Not Created' WHERE prescription_status IS NULL OR prescription_status = ''");

    if (!db_column_exists('income', 'payment_status')) {
        mysqli_query($connect, "ALTER TABLE income ADD payment_status varchar(30) NOT NULL DEFAULT 'Unpaid' AFTER amount_paid");
    }

    if (!db_column_exists('income', 'waived_amount')) {
        mysqli_query($connect, "ALTER TABLE income ADD waived_amount decimal(10,2) NOT NULL DEFAULT 0.00 AFTER amount_paid");
    }

    if (!db_column_exists('income', 'paid_at')) {
        mysqli_query($connect, "ALTER TABLE income ADD paid_at varchar(100) DEFAULT NULL AFTER payment_status");
    }

    if (!db_column_exists('income', 'consultation_fee')) {
        mysqli_query($connect, "ALTER TABLE income ADD consultation_fee decimal(10,2) NOT NULL DEFAULT 0.00 AFTER date_discharge");
        mysqli_query($connect, "UPDATE income SET consultation_fee = amount_paid WHERE consultation_fee = 0");
    }

    if (!db_column_exists('income', 'additional_charges')) {
        mysqli_query($connect, "ALTER TABLE income ADD additional_charges decimal(10,2) NOT NULL DEFAULT 0.00 AFTER consultation_fee");
    }

    if (!db_column_exists('income', 'created_at')) {
        mysqli_query($connect, "ALTER TABLE income ADD created_at varchar(100) DEFAULT NULL AFTER description");
        mysqli_query($connect, "UPDATE income SET created_at = date_discharge WHERE created_at IS NULL");
    }

    if (!db_column_exists('income', 'updated_at')) {
        mysqli_query($connect, "ALTER TABLE income ADD updated_at varchar(100) DEFAULT NULL AFTER created_at");
    }

    if (!db_column_exists('prescriptions', 'symptoms')) {
        mysqli_query($connect, "ALTER TABLE prescriptions ADD symptoms text DEFAULT NULL AFTER patient_username");
    }

    if (!db_column_exists('prescriptions', 'dosage')) {
        mysqli_query($connect, "ALTER TABLE prescriptions ADD dosage text DEFAULT NULL AFTER medicine");
    }

    if (!db_column_exists('prescriptions', 'duration')) {
        mysqli_query($connect, "ALTER TABLE prescriptions ADD duration text DEFAULT NULL AFTER dosage");
    }

    if (!db_column_exists('prescriptions', 'tests')) {
        mysqli_query($connect, "ALTER TABLE prescriptions ADD tests text DEFAULT NULL AFTER duration");
    }

    if (!db_column_exists('prescriptions', 'updated_at')) {
        mysqli_query($connect, "ALTER TABLE prescriptions ADD updated_at varchar(100) DEFAULT NULL AFTER created_at");
    }

    mysqli_query($connect, "UPDATE appointment a INNER JOIN prescriptions p ON p.appointment_id = a.id SET a.prescription_status = 'Created', a.appointment_status = 'Completed', a.status = 'Completed' WHERE a.prescription_status <> 'Created'");
    mysqli_query($connect, "UPDATE appointment a INNER JOIN income i ON i.appointment_id = a.id SET a.payment_status = i.payment_status WHERE i.payment_status IS NOT NULL AND i.payment_status <> ''");

    hms_add_unique_if_clean('admin', 'unique_admin_username', 'username');
    hms_add_unique_if_clean('patient', 'unique_patient_username', 'username');
    hms_add_unique_if_clean('patient', 'unique_patient_email', 'email');
    hms_add_unique_if_clean('doctors', 'unique_doctor_username', 'username');
    hms_add_unique_if_clean('doctors', 'unique_doctor_email', 'email');
}

function hms_count($sql, $types = '', ...$params)
{
    $row = db_select_one($sql, $types, ...$params);
    return (int) ($row['total'] ?? 0);
}

function hms_money($amount)
{
    return 'BDT ' . number_format((float) $amount, 2);
}

function hms_invoice_due($invoice)
{
    $amount = (float) ($invoice['amount_paid'] ?? 0);
    $waived = (float) ($invoice['waived_amount'] ?? 0);
    return max(0, $amount - $waived);
}

function hms_status_class($status)
{
    return strtolower(str_replace(' ', '-', (string) $status));
}

function hms_appointment_status($appointment)
{
    $status = $appointment['appointment_status'] ?? $appointment['status'] ?? 'Pending';
    return $status === 'Discharged' ? 'Completed' : $status;
}

function hms_sync_appointment_status($appointmentId, $status, $paymentStatus = null, $prescriptionStatus = null)
{
    $legacyStatus = $status === 'Completed' ? 'Completed' : $status;

    if ($paymentStatus !== null && $prescriptionStatus !== null) {
        return db_execute(
            "UPDATE appointment SET status = ?, appointment_status = ?, payment_status = ?, prescription_status = ?, updated_at = NOW() WHERE id = ?",
            "ssssi",
            $legacyStatus,
            $status,
            $paymentStatus,
            $prescriptionStatus,
            $appointmentId
        );
    }

    return db_execute(
        "UPDATE appointment SET status = ?, appointment_status = ?, updated_at = NOW() WHERE id = ?",
        "ssi",
        $legacyStatus,
        $status,
        $appointmentId
    );
}

function current_admin_record()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['admin'])) {
        return null;
    }

    return db_select_one("SELECT id, username, role, status FROM admin WHERE username = ? LIMIT 1", "s", $_SESSION['admin']);
}

function current_admin_is_super()
{
    $admin = current_admin_record();
    return $admin
        && strcasecmp((string) $admin['role'], 'Super Admin') === 0
        && strcasecmp((string) $admin['status'], 'Active') === 0;
}

hms_ensure_schema();

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
