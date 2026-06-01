<?php
session_start();
include("include/connection.php");

$error = '';
$success = isset($_GET['registered']) ? "Account created successfully. You can login now." : '';

if (isset($_POST['login'])) {
    $uname = trim($_POST['uname'] ?? '');
    $pass = $_POST['pass'] ?? '';

    if ($uname === '') {
        $error = "Enter username.";
    } elseif ($pass === '') {
        $error = "Enter password.";
    } else {
        $row = db_select_one("SELECT * FROM patient WHERE username = ? LIMIT 1", "s", $uname);

        if ($row && password_matches($pass, $row['password'])) {
            if (password_is_legacy($row['password'])) {
                $hash = hash_user_password($pass);
                db_execute("UPDATE patient SET password = ? WHERE id = ?", "si", $hash, $row['id']);
            }
            session_regenerate_id(true);
            $_SESSION['patient'] = $uname;
            header("Location: patient/index.php");
            exit();
        }

        $error = "Invalid account.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Login</title>
</head>
<body>
<?php include("include/header.php"); ?>
<main class="hms-auth-shell">
    <section class="hms-card hms-auth-card">
        <div class="hms-auth-media" style="background-image: linear-gradient(rgba(17,24,39,.50), rgba(17,24,39,.72)), url('img/Patient.jpg');">
            <p class="eyebrow text-white">Patient Portal</p>
            <h2 class="fw-bold">Book appointments, follow prescriptions, and track invoices.</h2>
        </div>
        <div class="hms-auth-form">
            <h4 class="fw-bold mb-1">Patient Login</h4>
            <p class="text-muted mb-4">Access your appointment and billing dashboard.</p>
            <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>
            <?php if ($success) { ?><div class="hms-alert hms-alert-success"><?php echo e($success); ?></div><?php } ?>
            <form method="post">
                <label>Username</label>
                <input type="text" name="uname" class="form-control" autocomplete="off" placeholder="Enter username" required>

                <label>Password</label>
                <input type="password" name="pass" class="form-control" autocomplete="off" placeholder="Enter password" required>

                <button type="submit" name="login" class="btn btn-primary w-100 mt-3"><i class="fas fa-sign-in-alt me-1"></i>Login</button>
                <p class="mt-3 mb-0">Need a patient account? <a href="account.php">Create one</a></p>
            </form>
        </div>
    </section>
</main>
</body>
</html>
