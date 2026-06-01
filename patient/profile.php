<?php
session_start();
include("../include/auth.php");
require_login("patient", "../patientlogin.php");
include("../include/connection.php");

$patient = $_SESSION['patient'];
$error = '';
$success = '';

if (isset($_POST['update_profile'])) {
    $imageError = '';
    $newProfile = save_uploaded_image($_FILES['profile'] ?? null, 'img', $imageError);

    if (!$newProfile) {
        $error = $imageError ?: "Select a profile picture.";
    } else {
        db_execute("UPDATE patient SET profile = ? WHERE username = ?", "ss", $newProfile, $patient);
        $success = "Profile picture updated successfully.";
    }
}

if (isset($_POST['change_username'])) {
    $uname = trim($_POST['uname'] ?? '');

    if ($uname === '') {
        $error = "Enter username.";
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $uname)) {
        $error = "Username must be 3-30 characters and use only letters, numbers, or underscore.";
    } else {
        $exists = db_select_one("SELECT id FROM patient WHERE username = ? AND username != ? LIMIT 1", "ss", $uname, $patient);
        if ($exists) {
            $error = "Username already exists.";
        } else {
            mysqli_begin_transaction($connect);
            $updatedPatient = db_execute("UPDATE patient SET username = ? WHERE username = ?", "ss", $uname, $patient);
            $updatedAppointments = db_execute("UPDATE appointment SET patient_username = ? WHERE patient_username = ?", "ss", $uname, $patient);
            $updatedIncome = db_execute("UPDATE income SET patient_username = ? WHERE patient_username = ?", "ss", $uname, $patient);
            $updatedPrescriptions = db_execute("UPDATE prescriptions SET patient_username = ? WHERE patient_username = ?", "ss", $uname, $patient);
            $updatedReports = db_execute("UPDATE report SET username = ? WHERE username = ?", "ss", $uname, $patient);

            if ($updatedPatient && $updatedAppointments && $updatedIncome && $updatedPrescriptions && $updatedReports) {
                mysqli_commit($connect);
                $_SESSION['patient'] = $uname;
                $patient = $uname;
                $success = "Username updated successfully.";
            } else {
                mysqli_rollback($connect);
                $error = "Could not update username.";
            }
        }
    }
}

if (isset($_POST['update_pass'])) {
    $oldPass = $_POST['old_pass'] ?? '';
    $newPass = $_POST['new_pass'] ?? '';
    $confirmPass = $_POST['con_pass'] ?? '';
    $row = db_select_one("SELECT password FROM patient WHERE username = ? LIMIT 1", "s", $patient);
    $storedPass = $row['password'] ?? '';

    if ($oldPass === '') {
        $error = "Enter old password.";
    } elseif ($newPass === '') {
        $error = "Enter new password.";
    } elseif (strlen($newPass) < 6) {
        $error = "New password must be at least 6 characters.";
    } elseif ($confirmPass === '') {
        $error = "Confirm password.";
    } elseif (!password_matches($oldPass, $storedPass)) {
        $error = "Invalid old password.";
    } elseif ($newPass !== $confirmPass) {
        $error = "Passwords do not match.";
    } else {
        $newHash = hash_user_password($newPass);
        db_execute("UPDATE patient SET password = ? WHERE username = ?", "ss", $newHash, $patient);
        $success = "Password updated successfully.";
    }
}

$row = db_select_one("SELECT username, profile FROM patient WHERE username = ? LIMIT 1", "s", $patient);
$username = $row['username'] ?? $patient;
$profile = $row['profile'] ?? 'patient.jpg.jpg';
if (!is_file(__DIR__ . "/img/" . $profile)) {
    $profile = 'patient.jpg.jpg';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Profile</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Account Settings</p>
            <h4>Patient Profile</h4>
        </div>
    </div>

    <?php if ($success) { ?><div class="hms-alert hms-alert-success"><?php echo e($success); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <section class="hms-compact-grid">
        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Profile</p>
                    <h5><?php echo e($username); ?></h5>
                </div>
            </div>
            <img src="img/<?php echo e($profile); ?>" class="hms-profile-image mb-3" alt="Profile picture">
            <form method="post" enctype="multipart/form-data">
                <label>Profile Picture</label>
                <input type="file" name="profile" class="form-control" accept="image/jpeg,image/png,image/gif">
                <button type="submit" name="update_profile" class="btn btn-primary mt-3"><i class="fas fa-image me-1"></i>Update picture</button>
            </form>
        </div>

        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Settings</p>
                    <h5>Account Security</h5>
                </div>
            </div>
            <div class="hms-form-grid">
                <form method="post">
                    <p class="eyebrow">Identity</p>
                    <label>Username</label>
                    <input type="text" name="uname" class="form-control" autocomplete="off" value="<?php echo e($username); ?>" required>
                    <button type="submit" name="change_username" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Save username</button>
                </form>

                <form method="post">
                    <p class="eyebrow">Password</p>
                    <label>Old Password</label>
                    <input type="password" name="old_pass" class="form-control" autocomplete="current-password" required>

                    <label>New Password</label>
                    <input type="password" name="new_pass" class="form-control" autocomplete="new-password" required>

                    <label>Confirm Password</label>
                    <input type="password" name="con_pass" class="form-control" autocomplete="new-password" required>

                    <button type="submit" name="update_pass" class="btn btn-primary mt-3"><i class="fas fa-key me-1"></i>Update password</button>
                </form>
            </div>
        </div>
    </section>
</main>
</body>
</html>
