<?php
session_start();
include("../include/auth.php");
require_login("doctor", "../doctorlogin.php");
include("../include/connection.php");

$id = (int) ($_GET['id'] ?? 0);
$patient = $id > 0 ? db_select_one("SELECT * FROM patient WHERE id = ? LIMIT 1", "i", $id) : null;
$patientProfile = 'patient.jpg.jpg';
if ($patient) {
    $patientProfile = $patient['profile'] ?: 'patient.jpg.jpg';
    if (!is_file(__DIR__ . "/../patient/img/" . $patientProfile)) {
        $patientProfile = 'patient.jpg.jpg';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Details</title>
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
            <h4>Patient Details</h4>
        </div>
        <a href="patient.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back to patients</a>
    </div>

    <?php if (!$patient) { ?>
        <div class="hms-card hms-empty">Patient not found.</div>
    <?php } else { ?>
        <section class="hms-compact-grid">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Photo</p>
                        <h5><?php echo e($patient['firstname'] . ' ' . $patient['surname']); ?></h5>
                    </div>
                </div>
                <img src="../patient/img/<?php echo e($patientProfile); ?>" class="hms-profile-image" alt="Patient photo">
            </div>

            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Profile</p>
                        <h5>Patient Information</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr><th>Firstname</th><td><?php echo e($patient['firstname']); ?></td></tr>
                            <tr><th>Surname</th><td><?php echo e($patient['surname']); ?></td></tr>
                            <tr><th>Username</th><td><?php echo e($patient['username']); ?></td></tr>
                            <tr><th>Email</th><td><?php echo e($patient['email']); ?></td></tr>
                            <tr><th>Phone</th><td><?php echo e($patient['phone']); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo e($patient['gender']); ?></td></tr>
                            <tr><th>Address</th><td><?php echo nl2br(e($patient['address'])); ?></td></tr>
                            <tr><th>Date Registered</th><td><?php echo e($patient['date_reg']); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    <?php } ?>
</main>
</body>
</html>
