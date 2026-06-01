<?php
session_start();
include("../include/auth.php");
require_login("patient", "../patientlogin.php");
include("../include/connection.php");

$id = (int) ($_GET['id'] ?? 0);
$patient = $_SESSION['patient'];
$invoice = $id > 0 ? db_select_one("SELECT * FROM income WHERE id = ? AND patient_username = ? LIMIT 1", "is", $id, $patient) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Billing</p>
            <h4>Receipt</h4>
        </div>
        <a href="invoice.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back to bills</a>
    </div>

    <?php if (!$invoice) { ?>
        <div class="hms-card hms-empty">Receipt not found.</div>
    <?php } elseif ($invoice['payment_status'] !== 'Paid') { ?>
        <div class="hms-card hms-empty">Receipt is available only after the bill is marked paid.</div>
    <?php } else { ?>
        <div class="hms-card" style="max-width:720px;">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Official Receipt</p>
                    <h5>Invoice #<?php echo e($invoice['id']); ?></h5>
                </div>
                <span class="status-pill status-paid">Paid</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <tbody>
                        <tr><th>Patient</th><td><?php echo e($invoice['patient']); ?></td></tr>
                        <tr><th>Doctor</th><td><?php echo e($invoice['doctor']); ?></td></tr>
                        <tr><th>Appointment ID</th><td><?php echo e($invoice['appointment_id']); ?></td></tr>
                        <tr><th>Consultation Fee</th><td><?php echo hms_money($invoice['consultation_fee']); ?></td></tr>
                        <tr><th>Additional Charges</th><td><?php echo hms_money($invoice['additional_charges']); ?></td></tr>
                        <tr><th>Total</th><td><?php echo hms_money($invoice['amount_paid']); ?></td></tr>
                        <tr><th>Waived</th><td><?php echo hms_money($invoice['waived_amount']); ?></td></tr>
                        <tr><th>Paid Amount</th><td><?php echo hms_money(hms_invoice_due($invoice)); ?></td></tr>
                        <tr><th>Paid At</th><td><?php echo e($invoice['paid_at']); ?></td></tr>
                        <tr><th>Description</th><td><?php echo e($invoice['description']); ?></td></tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-primary" onclick="window.print();"><i class="fas fa-print me-1"></i>Print receipt</button>
        </div>
    <?php } ?>
</main>
</body>
</html>
