<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$id = (int) ($_GET['id'] ?? $_POST['doctor_id'] ?? 0);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $doctor = db_select_one("SELECT id, status FROM doctors WHERE id = ? LIMIT 1", "i", $id);

    if (!$doctor) {
        $error = "Doctor application not found.";
    } elseif ($doctor['status'] !== 'Pending') {
        $error = "Only pending applications can be reviewed here.";
    } elseif ($action === 'approve') {
        db_execute("UPDATE doctors SET status = 'Approved' WHERE id = ?", "i", $id);
        header("Location: job_request.php?reviewed=1");
        exit();
    } elseif ($action === 'reject') {
        db_execute("UPDATE doctors SET status = 'Rejected' WHERE id = ?", "i", $id);
        header("Location: job_request.php?reviewed=1");
        exit();
    } else {
        $error = "Invalid review action.";
    }
}

$doctor = $id > 0 ? db_select_one("SELECT * FROM doctors WHERE id = ? LIMIT 1", "i", $id) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Doctor Application</title>
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
            <h4>Review Doctor Application</h4>
        </div>
        <a href="job_request.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back to requests</a>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <?php if (!$doctor) { ?>
        <div class="hms-card hms-empty">Doctor application not found.</div>
    <?php } else { ?>
        <section class="hms-section-grid">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Applicant</p>
                        <h5>Dr. <?php echo e($doctor['firstname'] . ' ' . $doctor['surname']); ?></h5>
                    </div>
                    <span class="status-pill status-<?php echo hms_status_class($doctor['status']); ?>"><?php echo e($doctor['status']); ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr><th>Username</th><td><?php echo e($doctor['username']); ?></td></tr>
                            <tr><th>Email</th><td><?php echo e($doctor['email']); ?></td></tr>
                            <tr><th>Phone</th><td><?php echo e($doctor['phone']); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo e($doctor['gender']); ?></td></tr>
                            <tr><th>Address</th><td><?php echo e($doctor['address']); ?></td></tr>
                            <tr><th>Applied</th><td><?php echo e($doctor['data_reg']); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Verification</p>
                        <h5>Clinical Credentials</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr><th>Qualification</th><td><?php echo e($doctor['qualification']); ?></td></tr>
                            <tr><th>Specialization</th><td><?php echo e($doctor['specialization']); ?></td></tr>
                            <tr><th>License Number</th><td><?php echo e($doctor['license_number']); ?></td></tr>
                            <tr><th>Consultation Fee</th><td><?php echo hms_money($doctor['consultation_fee']); ?></td></tr>
                            <tr><th>Certification</th><td><?php echo nl2br(e($doctor['certification'])); ?></td></tr>
                            <tr><th>Experience</th><td><?php echo nl2br(e($doctor['experience'])); ?></td></tr>
                        </tbody>
                    </table>
                </div>

                <?php if ($doctor['status'] === 'Pending') { ?>
                    <form method="post" class="hms-actions mt-3">
                        <input type="hidden" name="doctor_id" value="<?php echo e($doctor['id']); ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-success" onclick="return confirm('Approve this doctor application?');">
                            <i class="fas fa-check me-1"></i>Approve
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-outline-danger" onclick="return confirm('Reject this doctor application?');">
                            <i class="fas fa-times me-1"></i>Reject
                        </button>
                    </form>
                <?php } else { ?>
                    <div class="hms-empty">This application has already been reviewed.</div>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
</main>
</body>
</html>
