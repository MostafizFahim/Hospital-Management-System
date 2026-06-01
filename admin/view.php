<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$id = (int) ($_GET['id'] ?? 0);
$patient = $id > 0 ? db_select_one("SELECT * FROM patient WHERE id = ? LIMIT 1", "i", $id) : null;
$appointments = null;
$invoices = null;

if ($patient) {
    $patientProfile = $patient['profile'] ?: 'patient.jpg.jpg';
    if (!is_file(__DIR__ . "/../patient/img/" . $patientProfile)) {
        $patientProfile = 'patient.jpg.jpg';
    }

    $appointmentStmt = mysqli_prepare($connect, "SELECT * FROM appointment WHERE patient_username = ? ORDER BY appointment_date DESC, id DESC LIMIT 8");
    mysqli_stmt_bind_param($appointmentStmt, "s", $patient['username']);
    mysqli_stmt_execute($appointmentStmt);
    $appointments = mysqli_stmt_get_result($appointmentStmt);

    $invoiceStmt = mysqli_prepare($connect, "SELECT * FROM income WHERE patient_username = ? ORDER BY date_discharge DESC, id DESC LIMIT 8");
    mysqli_stmt_bind_param($invoiceStmt, "s", $patient['username']);
    mysqli_stmt_execute($invoiceStmt);
    $invoices = mysqli_stmt_get_result($invoiceStmt);
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
        <section class="hms-section-grid mb-3">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Profile</p>
                        <h5><?php echo e($patient['firstname'] . ' ' . $patient['surname']); ?></h5>
                    </div>
                    <span class="status-pill">Registered</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr><th>Username</th><td><?php echo e($patient['username']); ?></td></tr>
                            <tr><th>Email</th><td><?php echo e($patient['email']); ?></td></tr>
                            <tr><th>Phone</th><td><?php echo e($patient['phone']); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo e($patient['gender']); ?></td></tr>
                            <tr><th>Country</th><td><?php echo e($patient['country']); ?></td></tr>
                            <tr><th>Registered</th><td><?php echo e($patient['date_reg']); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hms-card">
                <p class="eyebrow">Photo</p>
                <img src="../patient/img/<?php echo e($patientProfile); ?>" class="hms-profile-image" alt="Patient photo">
            </div>
        </section>

        <section class="hms-section-grid">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Clinical Flow</p>
                        <h5>Recent Appointments</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Doctor</th>
                                <th>Symptoms</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$appointments || mysqli_num_rows($appointments) < 1) { ?>
                            <tr><td colspan="4" class="hms-empty">No appointments found.</td></tr>
                        <?php } ?>
                        <?php while ($row = mysqli_fetch_array($appointments)) { ?>
                            <tr>
                                <td><?php echo e($row['appointment_date']); ?></td>
                                <td><?php echo e($row['doctor_username']); ?></td>
                                <td><?php echo e($row['symptoms']); ?></td>
                                <td><span class="status-pill status-<?php echo hms_status_class($row['status']); ?>"><?php echo e($row['status']); ?></span></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Billing</p>
                        <h5>Recent Invoices</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Charge</th>
                                <th>Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$invoices || mysqli_num_rows($invoices) < 1) { ?>
                            <tr><td colspan="4" class="hms-empty">No invoices found.</td></tr>
                        <?php } ?>
                        <?php while ($row = mysqli_fetch_array($invoices)) { ?>
                            <tr>
                                <td><?php echo e($row['date_discharge']); ?></td>
                                <td><?php echo hms_money($row['amount_paid']); ?></td>
                                <td><?php echo hms_money(hms_invoice_due($row)); ?></td>
                                <td><span class="status-pill status-<?php echo hms_status_class($row['payment_status']); ?>"><?php echo e($row['payment_status']); ?></span></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    <?php } ?>
</main>
</body>
</html>
