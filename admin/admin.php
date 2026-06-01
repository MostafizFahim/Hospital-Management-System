<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$currentAdmin = $_SESSION['admin'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int) ($_POST['admin_id'] ?? 0);
        $target = db_select_one("SELECT username FROM admin WHERE id = ? LIMIT 1", "i", $id);

        if (!$target) {
            $error = "Admin account not found.";
        } elseif ($target['username'] === $currentAdmin) {
            $error = "You cannot remove your own active account.";
        } else {
            db_execute("DELETE FROM admin WHERE id = ?", "i", $id);
            $success = "Admin account removed.";
        }
    }

    if ($action === 'add') {
        $uname = trim($_POST['uname'] ?? '');
        $pass = $_POST['pass'] ?? '';

        if ($uname === '') {
            $error = "Enter admin username.";
        } elseif (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $uname)) {
            $error = "Username must be 3-30 characters and use only letters, numbers, or underscore.";
        } elseif ($pass === '') {
            $error = "Enter admin password.";
        } elseif (strlen($pass) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            $exists = db_select_one("SELECT id FROM admin WHERE username = ? LIMIT 1", "s", $uname);
            if ($exists) {
                $error = "Admin username already exists.";
            }
        }

        $image = 'admin.jpg';
        if ($error === '' && isset($_FILES['img']) && $_FILES['img']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imageError = '';
            $uploaded = save_uploaded_image($_FILES['img'], 'img', $imageError);
            if (!$uploaded) {
                $error = $imageError ?: "Could not save admin picture.";
            } else {
                $image = $uploaded;
            }
        }

        if ($error === '') {
            $passHash = hash_user_password($pass);
            db_execute("INSERT INTO admin(username, password, profile) VALUES(?,?,?)", "sss", $uname, $passHash, $image);
            $success = "New admin added successfully.";
        }
    }
}

$admins = mysqli_query($connect, "SELECT id, username, profile FROM admin ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrators</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Access Control</p>
            <h4>Administrators</h4>
        </div>
    </div>

    <?php if ($success) { ?><div class="hms-alert hms-alert-success"><?php echo e($success); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <section class="hms-section-grid">
        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Users</p>
                    <h5>Admin Accounts</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Admin</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($admins) < 1) { ?>
                        <tr><td colspan="4" class="hms-empty">No admin accounts found.</td></tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_assoc($admins)) { ?>
                        <tr>
                            <td><?php echo e($row['id']); ?></td>
                            <td>
                                <strong><?php echo e($row['username']); ?></strong><br>
                                <span class="text-muted small"><?php echo e($row['profile']); ?></span>
                            </td>
                            <td>
                                <?php if ($row['username'] === $currentAdmin) { ?>
                                    <span class="status-pill status-approved">Current</span>
                                <?php } else { ?>
                                    <span class="status-pill">Active</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($row['username'] !== $currentAdmin) { ?>
                                    <form method="post" class="hms-actions">
                                        <input type="hidden" name="admin_id" value="<?php echo e($row['id']); ?>">
                                        <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this admin account?');">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </button>
                                    </form>
                                <?php } else { ?>
                                    <span class="text-muted small">Protected</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Create</p>
                    <h5>Add Admin</h5>
                </div>
            </div>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <label>Username</label>
                <input type="text" name="uname" class="form-control" autocomplete="off" required>

                <label>Password</label>
                <input type="password" name="pass" class="form-control" autocomplete="new-password" required>

                <label>Profile Picture</label>
                <input type="file" name="img" class="form-control" accept="image/jpeg,image/png,image/gif">

                <button type="submit" name="add" class="btn btn-primary mt-3"><i class="fas fa-user-plus me-1"></i>Add admin</button>
            </form>
        </div>
    </section>
</main>
</body>
</html>
