<?php
session_start();
include("include/connection.php");

$error = array();

if (isset($_POST["login"])) {
    $username = trim($_POST["uname"] ?? '');
    $password = $_POST["pass"] ?? '';

    if ($username === '') {
        $error['admin'] = "Enter Username";
    } elseif ($password === '') {
        $error["admin"] = "Enter Password";
    }

    if (count($error) === 0) {
        $row = db_select_one("SELECT * FROM admin WHERE username = ? LIMIT 1", "s", $username);

        if ($row && password_matches($password, $row['password'])) {
            if (password_is_legacy($row['password'])) {
                $hash = hash_user_password($password);
                db_execute("UPDATE admin SET password = ? WHERE id = ?", "si", $hash, $row['id']);
            }
            session_regenerate_id(true);
            $_SESSION['admin'] = $username;
            header("Location: admin/index.php");
            exit();
        }

        $error['admin'] = "Invalid Username or Password";
    }
}

$show = isset($error['admin']) ? "<h4 class='alert alert-danger'>" . e($error['admin']) . "</h4>" : "";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body style="background-image:url(img/back.jpg);background-repeat: no-repeat;background-size:cover;background-opacity:0;">
    <?php include("include/header.php"); ?>
    <div style="margin-top: 60px;"></div>
    <div class="container">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6 jumbotron">
                    <img src="img/adminLogin.png" style="width:100%" class="col-mg-6">
                    <form method="post" class="my-2">
                        <div><?php echo $show; ?></div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="uname" class="form-control" autocomplete="off" placeholder="Enter Username">
                        </div>
                        <br>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="pass" class="form-control" placeholder="Enter Password">
                        </div>
                        <br>
                        <input type="submit" name="login" class="btn btn-success" value="Login">
                    </form>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </div>
</body>
</html>
