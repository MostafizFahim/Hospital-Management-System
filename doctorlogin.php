<?php
session_start();
include("include/connection.php");

$error = '';
$success = isset($_GET['applied']) ? "Application submitted. Please wait for admin approval before logging in." : '';

if (isset($_POST['login'])) {
    $uname = trim($_POST['uname'] ?? '');
    $password = $_POST['pass'] ?? '';

    if ($uname === '') {
        $error = "Enter username.";
    } elseif ($password === '') {
        $error = "Enter password.";
    } else {
        $row = db_select_one("SELECT * FROM doctors WHERE username = ? LIMIT 1", "s", $uname);

        if ($row) {
            $status = strtolower($row['status']);

            if (!password_matches($password, $row['password'])) {
                $error = "Invalid account.";
            } elseif ($status === "approved") {
                if (password_is_legacy($row['password'])) {
                    $hash = hash_user_password($password);
                    db_execute("UPDATE doctors SET password = ? WHERE id = ?", "si", $hash, $row['id']);
                }
                session_regenerate_id(true);
                $_SESSION['doctor'] = $uname;
                header("Location: doctor/index.php");
                exit();
            } elseif ($status === "pending" || $status === "pendding") {
                $error = "Your application is waiting for admin approval.";
            } elseif ($status === "rejected") {
                $error = "Your application was rejected.";
            } else {
                $error = "Your account is not active.";
            }
        } else {
            $error = "Invalid account.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Doctor Login</title>
</head>
<body>
<?php include("include/header.php"); ?>
<main class="hms-auth-shell">
    <section class="hms-card hms-auth-card">
        <div class="hms-auth-media" style="background-image: linear-gradient(rgba(17,24,39,.55), rgba(17,24,39,.72)), url('img/Doctor.jpg');">
            <p class="eyebrow text-white">Doctor Portal</p>
            <h2 class="fw-bold">Review approved appointments and complete discharge notes.</h2>
        </div>
        <div class="hms-auth-form">
            <h4 class="fw-bold mb-1">Doctor Login</h4>
            <p class="text-muted mb-4">Approved doctors can access the clinical queue.</p>
            <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>
            <?php if ($success) { ?><div class="hms-alert hms-alert-success"><?php echo e($success); ?></div><?php } ?>
            <form method="post">
                <label>Username</label>
                <input type="text" name="uname" class="form-control" autocomplete="off" placeholder="Enter username" required>

                <label>Password</label>
                <input type="password" name="pass" class="form-control" placeholder="Enter password" required>

                <button type="submit" name="login" class="btn btn-primary w-100 mt-3"><i class="fas fa-sign-in-alt me-1"></i>Login</button>
                <p class="mt-3 mb-0">Need a doctor account? <a href="apply.php">Apply now</a></p>
            </form>
        </div>
    </section>
</main>
</body>
</html>
