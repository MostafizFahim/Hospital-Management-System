<?php
session_start();
include("include/connection.php");

$error = '';

if (isset($_POST["login"])) {
    $username = trim($_POST["uname"] ?? '');
    $password = $_POST["pass"] ?? '';

    if ($username === '') {
        $error = "Enter username.";
    } elseif ($password === '') {
        $error = "Enter password.";
    } else {
        $row = db_select_one("SELECT * FROM admin WHERE username = ? LIMIT 1", "s", $username);

        if ($row && password_matches($password, $row['password']) && strcasecmp((string) ($row['status'] ?? 'Active'), 'Active') === 0) {
            if (password_is_legacy($row['password'])) {
                $hash = hash_user_password($password);
                db_execute("UPDATE admin SET password = ? WHERE id = ?", "si", $hash, $row['id']);
            }
            session_regenerate_id(true);
            $_SESSION['admin'] = $username;
            header("Location: admin/index.php");
            exit();
        }

        $error = "Invalid username, password, or inactive account.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
</head>
<body>
<?php include("include/header.php"); ?>
<main class="hms-auth-shell">
    <section class="hms-card hms-auth-card">
        <div class="hms-auth-media" style="background-image: linear-gradient(rgba(17,24,39,.55), rgba(17,24,39,.72)), url('img/adminLogin.png');">
            <p class="eyebrow text-white">Admin Workspace</p>
            <h2 class="fw-bold">Manage appointments, staff, reports, and billing.</h2>
        </div>
        <div class="hms-auth-form">
            <h4 class="fw-bold mb-1">Admin Login</h4>
            <p class="text-muted mb-4">Use your administrator account to continue.</p>
            <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>
            <form method="post">
                <label>Username</label>
                <input type="text" name="uname" class="form-control" autocomplete="off" placeholder="Enter username" required>

                <label>Password</label>
                <input type="password" name="pass" class="form-control" placeholder="Enter password" required>

                <button type="submit" name="login" class="btn btn-primary w-100 mt-3"><i class="fas fa-sign-in-alt me-1"></i>Login</button>
            </form>
        </div>
    </section>
</main>
</body>
</html>
