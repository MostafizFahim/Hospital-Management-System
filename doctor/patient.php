<?php
session_start();
include("../include/auth.php");
require_login("doctor", "../doctorlogin.php");
include("../include/connection.php");

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = mysqli_prepare($connect, "SELECT * FROM patient WHERE firstname LIKE ? OR surname LIKE ? OR username LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY date_reg DESC, id DESC");
    mysqli_stmt_bind_param($stmt, "sssss", $like, $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $patients = mysqli_stmt_get_result($stmt);
} else {
    $patients = mysqli_query($connect, "SELECT * FROM patient ORDER BY date_reg DESC, id DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patients</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Patient Registry</p>
            <h4>Patients</h4>
        </div>
    </div>

    <div class="hms-card mb-3">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label>Search Patients</label>
                <input type="text" name="q" class="form-control" value="<?php echo e($search); ?>" placeholder="Name, username, email, phone">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" type="submit"><i class="fas fa-search me-1"></i>Search</button>
            </div>
        </form>
    </div>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($patients) < 1) { ?>
                    <tr><td colspan="6" class="hms-empty">No patients found.</td></tr>
                <?php } ?>
                <?php while ($row = mysqli_fetch_array($patients)) { ?>
                    <tr>
                        <td><?php echo e($row['id']); ?></td>
                        <td>
                            <strong><?php echo e($row['firstname'] . ' ' . $row['surname']); ?></strong><br>
                            <span class="text-muted small"><?php echo e($row['username']); ?></span>
                        </td>
                        <td>
                            <?php echo e($row['email']); ?><br>
                            <span class="text-muted small"><?php echo e($row['phone']); ?></span>
                        </td>
                        <td><?php echo e($row['address'] ?: 'Not provided'); ?></td>
                        <td><?php echo e($row['date_reg']); ?></td>
                        <td>
                            <a href="view.php?id=<?php echo e($row['id']); ?>" class="btn btn-sm btn-primary"><i class="fas fa-folder-open me-1"></i>Open</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
