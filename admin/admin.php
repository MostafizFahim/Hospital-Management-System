<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$ad = $_SESSION['admin'];
$error = array();
$success = "";

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    db_execute("DELETE FROM admin WHERE id = ? AND username != ?", "is", $id, $ad);
    header("Location: admin.php");
    exit();
}

if (isset($_POST['add'])) {
    $uname = trim($_POST['uname'] ?? '');
    $pass = $_POST['pass'] ?? '';

    if ($uname === '') {
        $error['u'] = "Enter Admin Username";
    } elseif ($pass === '') {
        $error['u'] = "Enter Admin Password";
    } else {
        $exists = db_select_one("SELECT id FROM admin WHERE username = ? LIMIT 1", "s", $uname);
        if ($exists) {
            $error['u'] = "Admin username already exists";
        }
    }

    if (count($error) === 0) {
        $imageError = '';
        $image = save_uploaded_image($_FILES['img'] ?? null, 'img', $imageError);
        if (!$image) {
            $error['u'] = $imageError ?: "Add admin picture";
        }
    }

    if (count($error) === 0) {
        $pass_hash = hash_user_password($pass);
        db_execute("INSERT INTO admin(username, password, profile) VALUES(?,?,?)", "sss", $uname, $pass_hash, $image);
        $success = "New admin added successfully";
    }
}

$res = mysqli_query($connect, "SELECT id, username FROM admin WHERE username != '" . db_escape($ad) . "' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin</title>
</head>
<body>
    <?php include("../include/header.php"); ?>
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2" style="margin-left: -30px;">
                    <?php include("sidenav.php"); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-10">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-center">All Admin</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                        <?php if (mysqli_num_rows($res) < 1) { ?>
                            <tr><td colspan="3" class="text-center">No Other Admin</td></tr>
                        <?php } ?>
                        <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                            <tr>
                                <td><?php echo e($row['id']); ?></td>
                                <td><?php echo e($row['username']); ?></td>
                                <td>
                                    <a href="admin.php?id=<?php echo e($row['id']); ?>" onclick="return confirm('Remove this admin?');">
                                        <button type="button" class="btn btn-danger remove">Remove</button>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5 class="text-center">Add Admin</h5>
                    <?php if ($success !== '') { ?>
                        <h5 class="text-center alert alert-success"><?php echo e($success); ?></h5>
                    <?php } ?>
                    <?php if (isset($error['u'])) { ?>
                        <h5 class="text-center alert alert-danger"><?php echo e($error['u']); ?></h5>
                    <?php } ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="uname" class="form-control" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="pass" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Add admin picture</label>
                            <input type="file" name="img" class="form-control" accept="image/jpeg,image/png,image/gif">
                        </div><br>
                        <input type="submit" name="add" value="Add New Admin" class="btn btn-success">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
