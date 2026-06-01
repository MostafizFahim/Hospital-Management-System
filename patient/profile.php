<?php
session_start();
include("../include/auth.php");
require_login("patient", "../patientlogin.php");
include("../include/connection.php");

$patient = $_SESSION['patient'];
$error = array();
$success = "";

if (isset($_POST['update'])) {
    $imageError = '';
    $newProfile = save_uploaded_image($_FILES['profile'] ?? null, 'img', $imageError);

    if (!$newProfile) {
        $error['profile'] = $imageError ?: "Add Profile Picture";
    } else {
        db_execute("UPDATE patient SET profile = ? WHERE username = ?", "ss", $newProfile, $patient);
        $success = "Profile picture updated successfully";
    }
}

if (isset($_POST['change'])) {
    $uname = trim($_POST['uname'] ?? '');

    if ($uname === '') {
        $error['u'] = "Enter username";
    } else {
        $exists = db_select_one("SELECT id FROM patient WHERE username = ? AND username != ? LIMIT 1", "ss", $uname, $patient);
        if ($exists) {
            $error['u'] = "Username already exists";
        } else {
            db_execute("UPDATE patient SET username = ? WHERE username = ?", "ss", $uname, $patient);
            $_SESSION['patient'] = $uname;
            $patient = $uname;
            $success = "Username updated successfully";
        }
    }
}

if (isset($_POST['update_pass'])) {
    $old_pass = $_POST['old_pass'] ?? '';
    $new_pass = $_POST['new_pass'] ?? '';
    $con_pass = $_POST['con_pass'] ?? '';

    $row = db_select_one("SELECT password FROM patient WHERE username = ? LIMIT 1", "s", $patient);
    $pass = $row['password'] ?? '';

    if ($old_pass === '') {
        $error['p'] = "Enter old Password";
    } elseif ($new_pass === '') {
        $error['p'] = "Enter New Password";
    } elseif ($con_pass === '') {
        $error['p'] = "Confirm Password";
    } elseif (!password_matches($old_pass, $pass)) {
        $error['p'] = "Invalid old Password";
    } elseif ($new_pass !== $con_pass) {
        $error['p'] = "Both passwords do not match";
    } else {
        $new_hash = hash_user_password($new_pass);
        db_execute("UPDATE patient SET password = ? WHERE username = ?", "ss", $new_hash, $patient);
        $success = "Password updated successfully";
    }
}

$row = db_select_one("SELECT username, profile FROM patient WHERE username = ? LIMIT 1", "s", $patient);
$username = $row['username'] ?? $patient;
$profile = $row['profile'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
</head>
<body>
    <?php include("../include/header.php"); ?>

    <div class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2" style="margin-Left:-30px;">
                    <?php include("sidenav.php"); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-10">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <h4><?php echo e($username); ?> Profile</h4>

                    <?php if ($success !== '') { ?>
                        <h5 class="text-center alert alert-success"><?php echo e($success); ?></h5>
                    <?php } ?>
                    <?php if (isset($error['profile'])) { ?>
                        <h5 class="text-center alert alert-danger"><?php echo e($error['profile']); ?></h5>
                    <?php } ?>

                    <form method="post" enctype="multipart/form-data">
                        <img src="img/<?php echo e($profile); ?>" class="col-md-12" style="height: 400px; object-fit: cover;" alt="Profile Picture">
                        <br><br>
                        <div class="form-group">
                            <label>UPDATE PROFILE</label>
                            <input type="file" name="profile" class="form-control" accept="image/jpeg,image/png,image/gif">
                        </div>
                        <br>
                        <input type="submit" name="update" value="UPDATE" class="btn btn-success">
                    </form>
                </div>

                <div class="col-md-6">
                    <form method="post">
                        <?php if (isset($error['u'])) { ?>
                            <h5 class="text-center alert alert-danger"><?php echo e($error['u']); ?></h5>
                        <?php } ?>
                        <label>Change Username</label>
                        <input type="text" name="uname" class="form-control" autocomplete="off" value="<?php echo e($username); ?>"><br>
                        <input type="submit" name="change" class="btn btn-success" value="Change">
                    </form>

                    <br><br>

                    <form method="post">
                        <h5 class="text-center my-4">Change Password</h5>
                        <?php if (isset($error['p'])) { ?>
                            <h5 class="text-center alert alert-danger"><?php echo e($error['p']); ?></h5>
                        <?php } ?>
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" name="old_pass" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_pass" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="con_pass" class="form-control">
                        </div>
                        <br>
                        <input type="submit" name="update_pass" value="Update Password" class="btn btn-info">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
