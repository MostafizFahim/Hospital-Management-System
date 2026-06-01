<?php
session_start();
include("../include/auth.php");
require_login("doctor", "../doctorlogin.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check Appointment</title>
</head>
<body>
<?php
include("../include/header.php");
include("../include/connection.php");
include("sidenav.php");

$id = (int) ($_GET['id'] ?? 0);
$doc = $_SESSION['doctor'];
$message = '';
$error = '';

$row = null;
if ($id > 0) {
    $row = db_select_one("SELECT * FROM appointment WHERE id = ? AND doctor_username = ? LIMIT 1", "is", $id, $doc);
}
$doctorProfile = db_select_one("SELECT consultation_fee FROM doctors WHERE username = ? LIMIT 1", "s", $doc);
$defaultFee = (float) ($doctorProfile['consultation_fee'] ?? 0);

if ($row && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentStatus = hms_appointment_status($row);
    if ($appointmentStatus !== 'Approved') {
        $error = "Only approved appointments can be completed.";
    } else {
        $fee = (float) ($_POST['fee'] ?? $defaultFee);
        $additional = (float) ($_POST['additional_charges'] ?? 0);
        $des = trim($_POST['des'] ?? '');
        $diagnosis = trim($_POST['diagnosis'] ?? '');
        $medicine = trim($_POST['medicine'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $tests = trim($_POST['tests'] ?? '');
        $advice = trim($_POST['advice'] ?? '');
        $followUp = trim($_POST['follow_up'] ?? '');
        $followUp = $followUp === '' ? null : $followUp;
        $total = $fee + $additional;

        if ($fee <= 0) {
            $error = "Fee must be greater than 0.";
        } elseif ($additional < 0) {
            $error = "Additional charges cannot be negative.";
        } elseif ($des === '') {
            $error = "Enter invoice description.";
        } elseif ($diagnosis === '') {
            $error = "Enter diagnosis.";
        } elseif ($medicine === '') {
            $error = "Enter prescribed medicine.";
        } elseif ($dosage === '') {
            $error = "Enter dosage.";
        } elseif ($duration === '') {
            $error = "Enter duration.";
        } elseif ($followUp !== null && $followUp < date('Y-m-d')) {
            $error = "Follow-up date cannot be in the past.";
        } else {
            $existingPrescription = db_select_one("SELECT id FROM prescriptions WHERE appointment_id = ? LIMIT 1", "i", $id);
            $existingInvoice = db_select_one("SELECT id FROM income WHERE appointment_id = ? LIMIT 1", "i", $id);
            if ($existingPrescription || $existingInvoice) {
                $error = "This appointment already has a prescription or bill.";
            } else {
            mysqli_begin_transaction($connect);
            $patientName = trim($row['firstname'] . ' ' . $row['surname']);
            $patientUsername = $row['patient_username'];
            $invoiceCreated = db_execute(
                "INSERT INTO income(doctor, patient_username, patient, appointment_id, date_discharge, consultation_fee, additional_charges, amount_paid, payment_status, paid_at, description, created_at, updated_at) VALUES(?,?,?,?,NOW(),?,?,?,'Unpaid',NULL,?,NOW(),NOW())",
                "sssiddds",
                $doc,
                $patientUsername,
                $patientName,
                $id,
                $fee,
                $additional,
                $total,
                $des
            );

            $prescriptionCreated = db_execute(
                "INSERT INTO prescriptions(appointment_id, doctor_username, patient_username, symptoms, diagnosis, medicine, dosage, duration, tests, advice, follow_up_date, created_at, updated_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())",
                "issssssssss",
                $id,
                $doc,
                $patientUsername,
                $row['symptoms'],
                $diagnosis,
                $medicine,
                $dosage,
                $duration,
                $tests,
                $advice,
                $followUp
            );

            $appointmentUpdated = db_execute(
                "UPDATE appointment SET status = 'Completed', appointment_status = 'Completed', payment_status = 'Unpaid', prescription_status = 'Created', updated_at = NOW() WHERE id = ? AND doctor_username = ? AND appointment_status = 'Approved'",
                "is",
                $id,
                $doc
            );

            if ($invoiceCreated && $prescriptionCreated && $appointmentUpdated) {
                mysqli_commit($connect);
                $message = "Prescription saved and unpaid invoice generated for billing.";
                $row['status'] = 'Completed';
                $row['appointment_status'] = 'Completed';
                $row['payment_status'] = 'Unpaid';
                $row['prescription_status'] = 'Created';
            } else {
                mysqli_rollback($connect);
                $error = "Could not complete consultation. Please try again.";
            }
            }
        }
    }
}
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Clinical Visit</p>
            <h4>Check Appointment</h4>
        </div>
        <a href="appointment.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back to queue</a>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <?php if (empty($row)) { ?>
        <div class="hms-card hms-empty">Appointment not found for your account.</div>
    <?php } else { ?>
        <section class="hms-section-grid">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Patient</p>
                        <h5><?php echo e($row['firstname'] . ' ' . $row['surname']); ?></h5>
                    </div>
                    <?php $appointmentStatus = hms_appointment_status($row); ?>
                    <span class="status-pill status-<?php echo hms_status_class($appointmentStatus); ?>"><?php echo e($appointmentStatus); ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr><th>Username</th><td><?php echo e($row['patient_username']); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo e($row['gender']); ?></td></tr>
                            <tr><th>Phone</th><td><?php echo e($row['phone']); ?></td></tr>
                            <tr><th>Appointment Date</th><td><?php echo e($row['appointment_date']); ?></td></tr>
                            <tr><th>Symptoms</th><td><?php echo e($row['symptoms']); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Consultation</p>
                        <h5>Prescription & Invoice</h5>
                    </div>
                </div>
                <?php if (hms_appointment_status($row) !== 'Approved') { ?>
                    <div class="hms-empty">This appointment is already <?php echo e(strtolower(hms_appointment_status($row))); ?>.</div>
                <?php } else { ?>
                    <form method="post">
                        <label>Consultation Fee</label>
                        <input type="number" step="0.01" min="1" name="fee" class="form-control" autocomplete="off" value="<?php echo e($defaultFee > 0 ? $defaultFee : ''); ?>" placeholder="Enter patient fee" required>

                        <label>Additional Charges</label>
                        <input type="number" step="0.01" min="0" name="additional_charges" class="form-control" autocomplete="off" value="0">

                        <label>Invoice Description</label>
                        <input type="text" name="des" class="form-control" autocomplete="off" placeholder="Consultation, lab test, medicine, etc." required>

                        <label>Diagnosis</label>
                        <textarea name="diagnosis" class="form-control" rows="3" placeholder="Enter diagnosis" required></textarea>

                        <label>Medicine</label>
                        <textarea name="medicine" class="form-control" rows="3" placeholder="Enter prescribed medicine" required></textarea>

                        <label>Dosage</label>
                        <textarea name="dosage" class="form-control" rows="2" placeholder="Example: 1 tablet twice daily" required></textarea>

                        <label>Duration</label>
                        <input type="text" name="duration" class="form-control" placeholder="Example: 7 days" required>

                        <label>Tests</label>
                        <textarea name="tests" class="form-control" rows="2" placeholder="CBC, X-ray, ECG, etc."></textarea>

                        <label>Advice</label>
                        <textarea name="advice" class="form-control" rows="2" placeholder="Diet, rest, tests, warning signs"></textarea>

                        <label>Follow-up Date</label>
                        <input type="date" name="follow_up" class="form-control">

                        <button type="submit" name="send" class="btn btn-primary mt-3"><i class="fas fa-paper-plane me-1"></i>Complete consultation</button>
                    </form>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
</main>
</body>
</html>
