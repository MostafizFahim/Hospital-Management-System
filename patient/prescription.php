<?php
session_start();
include("../include/auth.php");
require_login("patient", "../patientlogin.php");
include("../include/connection.php");

$patient = $_SESSION['patient'];
$appointmentId = (int) ($_GET['appointment_id'] ?? 0);
$detail = null;

if ($appointmentId > 0) {
    $detail = db_select_one(
        "SELECT p.*, a.firstname, a.surname, a.appointment_date, a.appointment_time, a.symptoms AS appointment_symptoms, a.payment_status AS appointment_payment_status, i.id AS invoice_id, i.amount_paid, i.waived_amount, i.payment_status AS invoice_payment_status, i.paid_at
         FROM prescriptions p
         INNER JOIN appointment a ON a.id = p.appointment_id
         LEFT JOIN income i ON i.appointment_id = p.appointment_id
         WHERE p.appointment_id = ? AND p.patient_username = ? AND a.patient_username = ?
         LIMIT 1",
        "iss",
        $appointmentId,
        $patient,
        $patient
    );
}

$stmt = mysqli_prepare(
    $connect,
    "SELECT p.*, a.appointment_date, a.appointment_time, i.id AS invoice_id, i.amount_paid, i.waived_amount, i.payment_status AS invoice_payment_status
     FROM prescriptions p
     INNER JOIN appointment a ON a.id = p.appointment_id
     LEFT JOIN income i ON i.appointment_id = p.appointment_id
     WHERE p.patient_username = ?
     ORDER BY p.created_at DESC, p.id DESC"
);
mysqli_stmt_bind_param($stmt, "s", $patient);
mysqli_stmt_execute($stmt);
$prescriptions = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Prescriptions</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Clinical Records</p>
            <h4>My Prescriptions</h4>
        </div>
        <a href="appointment.php" class="btn btn-outline-primary"><i class="fas fa-calendar-plus me-1"></i>Appointments</a>
    </div>

    <?php if ($appointmentId > 0) { ?>
        <?php if (!$detail) { ?>
            <div class="hms-card hms-empty">Prescription not found for your account.</div>
        <?php } else {
            $paymentStatus = $detail['invoice_payment_status'] ?: $detail['appointment_payment_status'];
            $due = isset($detail['amount_paid']) ? hms_invoice_due($detail) : 0;
        ?>
            <?php if ($paymentStatus !== 'Paid') { ?>
                <div class="hms-alert hms-alert-danger">Bill Status: <?php echo e($paymentStatus ?: 'Unpaid'); ?>. Amount Due: <?php echo hms_money($due); ?>. You can view this prescription now, but receipt is available only after payment.</div>
            <?php } ?>
            <section class="hms-section-grid mb-3">
                <div class="hms-card">
                    <div class="page-heading">
                        <div>
                            <p class="eyebrow">Prescription</p>
                            <h5>Appointment #<?php echo e($detail['appointment_id']); ?></h5>
                        </div>
                        <span class="status-pill status-<?php echo hms_status_class($paymentStatus); ?>">Bill: <?php echo e($paymentStatus ?: 'Unpaid'); ?></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <tbody>
                                <tr><th>Doctor</th><td><?php echo e($detail['doctor_username']); ?></td></tr>
                                <tr><th>Patient</th><td><?php echo e(trim($detail['firstname'] . ' ' . $detail['surname'])); ?></td></tr>
                                <tr><th>Appointment</th><td><?php echo e($detail['appointment_date']); ?> <?php echo e($detail['appointment_time'] ? substr($detail['appointment_time'], 0, 5) : ''); ?></td></tr>
                                <tr><th>Symptoms</th><td><?php echo nl2br(e($detail['symptoms'] ?: $detail['appointment_symptoms'])); ?></td></tr>
                                <tr><th>Diagnosis</th><td><?php echo nl2br(e($detail['diagnosis'])); ?></td></tr>
                                <tr><th>Medicines</th><td><?php echo nl2br(e($detail['medicine'])); ?></td></tr>
                                <tr><th>Dosage</th><td><?php echo nl2br(e($detail['dosage'])); ?></td></tr>
                                <tr><th>Duration</th><td><?php echo e($detail['duration']); ?></td></tr>
                                <tr><th>Tests</th><td><?php echo nl2br(e($detail['tests'])); ?></td></tr>
                                <tr><th>Advice</th><td><?php echo nl2br(e($detail['advice'])); ?></td></tr>
                                <tr><th>Follow Up</th><td><?php echo e($detail['follow_up_date'] ?: '-'); ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="hms-card">
                    <p class="eyebrow">Billing</p>
                    <h5><?php echo hms_money($detail['amount_paid'] ?? 0); ?></h5>
                    <p class="mb-2">Amount due: <?php echo hms_money($due); ?></p>
                    <?php if ($paymentStatus === 'Paid') { ?>
                        <a href="receipt.php?id=<?php echo e($detail['invoice_id']); ?>" class="btn btn-primary"><i class="fas fa-receipt me-1"></i>View receipt</a>
                    <?php } else { ?>
                        <span class="status-pill status-unpaid">Pay at reception</span>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>
    <?php } ?>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Appointment</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th>Bill</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($prescriptions) < 1) { ?>
                        <tr><td colspan="5" class="hms-empty">No prescriptions created yet.</td></tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_array($prescriptions)) {
                        $paymentStatus = $row['invoice_payment_status'] ?: 'Unpaid';
                    ?>
                        <tr>
                            <td><?php echo e($row['appointment_date']); ?> <?php echo e($row['appointment_time'] ? substr($row['appointment_time'], 0, 5) : ''); ?></td>
                            <td><?php echo e($row['doctor_username']); ?></td>
                            <td><?php echo e($row['diagnosis']); ?></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($paymentStatus); ?>"><?php echo e($paymentStatus); ?></span></td>
                            <td>
                                <a href="prescription.php?appointment_id=<?php echo e($row['appointment_id']); ?>" class="btn btn-sm btn-primary">View</a>
                                <?php if ($paymentStatus === 'Paid') { ?>
                                    <a href="receipt.php?id=<?php echo e($row['invoice_id']); ?>" class="btn btn-sm btn-outline-primary">Receipt</a>
                                <?php } ?>
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
