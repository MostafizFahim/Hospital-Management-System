<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

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
        <a href="doctor.php" class="btn btn-outline-primary"><i class="fas fa-user-md me-1"></i>Approved doctors</a>
    </div>

    <?php if (isset($_GET['reviewed'])) { ?><div class="hms-alert hms-alert-success">Doctor application reviewed.</div><?php } ?>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Contact</th>
                        <th>Verification</th>
                        <th>Applied</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($requests) < 1) { ?>
                    <tr><td colspan="6" class="hms-empty">No pending doctor applications.</td></tr>
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
                        <td>
                            <?php echo e($row['specialization'] ?: 'No specialization'); ?><br>
                            <span class="text-muted small"><?php echo e($row['license_number'] ?: ($row['qualification'] ?: 'Verification details pending')); ?></span>
                        </td>
                        <td><?php echo e($row['data_reg']); ?></td>
                        <td>
                            <a href="review_doctor.php?id=<?php echo e($row['id']); ?>" class="btn btn-sm btn-primary"><i class="fas fa-clipboard-check me-1"></i>Review</a>
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
