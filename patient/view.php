<?php
session_start();
include("../include/auth.php");
require_login("patient", "../patientlogin.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Details</title>
</head>
<body>
<?php
include("../include/header.php");
include("../include/connection.php");
include("sidenav.php");

$row = null;
$prescription = null;

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $patient = $_SESSION['patient'];
    $row = db_select_one("SELECT * FROM income WHERE id = ? AND patient_username = ? LIMIT 1", "is", $id, $patient);

    if ($row) {
        $prescription = db_select_one("SELECT * FROM prescriptions WHERE appointment_id = ? AND patient_username = ? LIMIT 1", "is", $row['appointment_id'], $patient);
    }
}
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Billing & Prescription</p>
            <h4>Invoice Details</h4>
        </div>
        <a href="invoice.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back to invoices</a>
    </div>

    <?php if (!$row) { ?>
        <div class="hms-card hms-empty">Invoice not found.</div>
    <?php } else { ?>
        <?php if ($row['payment_status'] !== 'Paid') { ?>
            <div class="hms-alert hms-alert-danger">Bill Status: <?php echo e($row['payment_status']); ?>. Amount Due: <?php echo hms_money(hms_invoice_due($row)); ?>. Prescription access is available, but receipt is locked until payment is marked paid.</div>
        <?php } ?>
        <section class="hms-section-grid">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Invoice</p>
                        <h5>#<?php echo e($row['id']); ?></h5>
                    </div>
                    <span class="status-pill status-<?php echo hms_status_class($row['payment_status']); ?>"><?php echo e($row['payment_status']); ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr><th>Doctor</th><td><?php echo e($row['doctor']); ?></td></tr>
                            <tr><th>Patient</th><td><?php echo e($row['patient']); ?></td></tr>
                            <tr><th>Created</th><td><?php echo e($row['created_at'] ?: $row['date_discharge']); ?></td></tr>
                            <tr><th>Consultation Fee</th><td><?php echo hms_money($row['consultation_fee']); ?></td></tr>
                            <tr><th>Additional Charges</th><td><?php echo hms_money($row['additional_charges']); ?></td></tr>
                            <tr><th>Total Charge</th><td><?php echo hms_money($row['amount_paid']); ?></td></tr>
                            <tr><th>Waived</th><td><?php echo hms_money($row['waived_amount']); ?></td></tr>
                            <tr><th>Due</th><td><?php echo hms_money(hms_invoice_due($row)); ?></td></tr>
                            <tr><th>Paid At</th><td><?php echo e($row['paid_at'] ?: '-'); ?></td></tr>
                            <tr><th>Description</th><td><?php echo e($row['description']); ?></td></tr>
                        </tbody>
                    </table>
                </div>
                <?php if ($row['payment_status'] === 'Paid') { ?>
                    <a href="receipt.php?id=<?php echo e($row['id']); ?>" class="btn btn-primary mt-3"><i class="fas fa-receipt me-1"></i>View receipt</a>
                <?php } ?>
            </div>

            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Clinical Notes</p>
                        <h5>Prescription</h5>
                    </div>
                </div>
                <?php if (!$prescription) { ?>
                    <div class="hms-empty">No prescription attached.</div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <tbody>
                                <tr><th>Symptoms</th><td><?php echo nl2br(e($prescription['symptoms'])); ?></td></tr>
                                <tr><th>Diagnosis</th><td><?php echo e($prescription['diagnosis']); ?></td></tr>
                                <tr><th>Medicine</th><td><?php echo e($prescription['medicine']); ?></td></tr>
                                <tr><th>Dosage</th><td><?php echo nl2br(e($prescription['dosage'])); ?></td></tr>
                                <tr><th>Duration</th><td><?php echo e($prescription['duration']); ?></td></tr>
                                <tr><th>Tests</th><td><?php echo nl2br(e($prescription['tests'])); ?></td></tr>
                                <tr><th>Advice</th><td><?php echo nl2br(e($prescription['advice'])); ?></td></tr>
                                <tr><th>Follow Up</th><td><?php echo e($prescription['follow_up_date'] ?: '-'); ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
</main>
</body>
</html>
