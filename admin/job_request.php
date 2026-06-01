<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorId = (int) ($_POST['doctor_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $doctor = db_select_one("SELECT id, status FROM doctors WHERE id = ? LIMIT 1", "i", $doctorId);

    if (!$doctor) {
        $error = "Doctor application not found.";
    } elseif ($doctor['status'] !== 'Pending') {
        $error = "Only pending applications can be reviewed here.";
    } elseif ($action === 'approve') {
        db_execute("UPDATE doctors SET status = 'Approved' WHERE id = ?", "i", $doctorId);
        $message = "Doctor application approved.";
    } elseif ($action === 'reject') {
        db_execute("UPDATE doctors SET status = 'Rejected' WHERE id = ?", "i", $doctorId);
        $message = "Doctor application rejected.";
    } else {
        $error = "Invalid review action.";
    }
}

$requests = mysqli_query($connect, "SELECT * FROM doctors WHERE status = 'Pending' ORDER BY data_reg ASC, id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Doctor Requests</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Credential Review</p>
            <h4>Doctor Requests</h4>
        </div>
        <a href="doctor.php?status=All" class="btn btn-outline-primary"><i class="fas fa-user-md me-1"></i>All doctors</a>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Contact</th>
                        <th>Gender</th>
                        <th>Country</th>
                        <th>Applied</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($requests) < 1) { ?>
                    <tr><td colspan="7" class="hms-empty">No pending doctor applications.</td></tr>
                <?php } ?>
                <?php while ($row = mysqli_fetch_array($requests)) { ?>
                    <tr>
                        <td><?php echo e($row['id']); ?></td>
                        <td>
                            <strong>Dr. <?php echo e($row['firstname'] . ' ' . $row['surname']); ?></strong><br>
                            <span class="text-muted small"><?php echo e($row['username']); ?></span>
                        </td>
                        <td>
                            <?php echo e($row['email']); ?><br>
                            <span class="text-muted small"><?php echo e($row['phone']); ?></span>
                        </td>
                        <td><?php echo e($row['gender']); ?></td>
                        <td><?php echo e($row['country']); ?></td>
                        <td><?php echo e($row['data_reg']); ?></td>
                        <td>
                            <form method="post" class="hms-actions">
                                <input type="hidden" name="doctor_id" value="<?php echo e($row['id']); ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Approve</button>
                                <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger"><i class="fas fa-times me-1"></i>Reject</button>
                            </form>
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
