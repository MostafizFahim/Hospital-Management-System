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
    <title>Book Appointment</title>
</head>
<body>
<?php
include("../include/header.php");
include("../include/connection.php");
include("sidenav.php");

$message = '';
$error = '';
$pat = $_SESSION['patient'];
$patient = db_select_one("SELECT * FROM patient WHERE username = ? LIMIT 1", "s", $pat);
$doctors = mysqli_query($connect, "SELECT username, firstname, surname, consultation_fee FROM doctors WHERE status = 'Approved' ORDER BY firstname ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $doctorUsername = trim($_POST['doctor'] ?? '');
    $symptoms = trim($_POST['sym'] ?? '');

    if ($date === '') {
        $error = "Select appointment date.";
    } elseif ($time === '') {
        $error = "Select appointment time.";
    } elseif ($doctorUsername === '') {
        $error = "Select a doctor.";
    } elseif ($symptoms === '') {
        $error = "Enter symptoms.";
    } elseif ($date < date('Y-m-d')) {
        $error = "Appointment date cannot be in the past.";
    } else {
        $doctor = db_select_one("SELECT username FROM doctors WHERE username = ? AND status = 'Approved' LIMIT 1", "s", $doctorUsername);
        $duplicate = db_select_one(
            "SELECT id FROM appointment WHERE patient_username = ? AND doctor_username = ? AND appointment_date = ? AND appointment_status IN ('Pending', 'Approved', 'Rescheduled') LIMIT 1",
            "sss",
            $pat,
            $doctorUsername,
            $date
        );

        if (!$doctor) {
            $error = "Selected doctor is not available.";
        } elseif ($duplicate) {
            $error = "You already have an active appointment with this doctor on that date.";
        } else {
            $created = db_execute(
                "INSERT INTO appointment(patient_username, doctor_username, firstname, surname, gender, phone, appointment_date, appointment_time, symptoms, status, appointment_status, payment_status, prescription_status, date_booked, updated_at) VALUES(?,?,?,?,?,?,?,?,?,'Pending','Pending','Unpaid','Not Created',NOW(),NOW())",
                "sssssssss",
                $patient['username'],
                $doctorUsername,
                $patient['firstname'],
                $patient['surname'],
                $patient['gender'],
                $patient['phone'],
                $date,
                $time,
                $symptoms
            );

            $message = $created ? "Appointment request sent. Admin confirmation is required before the doctor can check it." : "Could not book appointment.";
        }
    }
}

$myAppointmentsStmt = mysqli_prepare($connect, "SELECT * FROM appointment WHERE patient_username = ? ORDER BY appointment_date DESC, date_booked DESC LIMIT 8");
mysqli_stmt_bind_param($myAppointmentsStmt, "s", $pat);
mysqli_stmt_execute($myAppointmentsStmt);
$myAppointments = mysqli_stmt_get_result($myAppointmentsStmt);
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Patient Desk</p>
            <h4>Book Appointment</h4>
        </div>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <section class="hms-section-grid">
        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Request</p>
                    <h5>New Appointment</h5>
                </div>
            </div>
            <form method="post">
                <label>Appointment Date</label>
                <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>

                <label>Appointment Time</label>
                <input type="time" name="time" class="form-control" required>

                <label>Doctor</label>
                <select name="doctor" class="form-select" required>
                    <option value="">Select Doctor</option>
                    <?php while ($doctor = mysqli_fetch_array($doctors)) { ?>
                        <option value="<?php echo e($doctor['username']); ?>">
                            Dr. <?php echo e($doctor['firstname'] . ' ' . $doctor['surname']); ?> - <?php echo hms_money($doctor['consultation_fee']); ?>
                        </option>
                    <?php } ?>
                </select>

                <label>Symptoms</label>
                <textarea name="sym" class="form-control" rows="4" autocomplete="off" placeholder="Describe the main problem, duration, and severity" required></textarea>

                <button type="submit" name="book" class="btn btn-primary mt-3"><i class="fas fa-calendar-plus me-1"></i>Send request</button>
            </form>
        </div>

        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">History</p>
                    <h5>Recent Appointments</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Appointment</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($myAppointments) < 1) { ?>
                        <tr><td colspan="5" class="hms-empty">No appointments yet.</td></tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_array($myAppointments)) { ?>
                        <?php
                        $appointmentStatus = hms_appointment_status($row);
                        $invoice = db_select_one("SELECT id, payment_status FROM income WHERE appointment_id = ? AND patient_username = ? LIMIT 1", "is", $row['id'], $pat);
                        $paymentStatus = $invoice['payment_status'] ?? ($row['payment_status'] ?? 'Unpaid');
                        ?>
                        <tr>
                            <td><?php echo e($row['appointment_date']); ?> <?php echo e($row['appointment_time'] ? substr($row['appointment_time'], 0, 5) : ''); ?></td>
                            <td><?php echo e($row['doctor_username']); ?></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($appointmentStatus); ?>"><?php echo e($appointmentStatus); ?></span></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($paymentStatus); ?>"><?php echo e($paymentStatus); ?></span></td>
                            <td>
                                <?php if ($appointmentStatus === 'Pending') { ?>
                                    <span class="text-muted small">Waiting for approval</span>
                                <?php } elseif ($appointmentStatus === 'Approved') { ?>
                                    <span class="text-muted small">Appointment approved</span>
                                <?php } elseif ($appointmentStatus === 'Completed' && ($row['prescription_status'] ?? '') === 'Created') { ?>
                                    <a href="prescription.php?appointment_id=<?php echo e($row['id']); ?>" class="btn btn-sm btn-primary">View prescription</a>
                                <?php } elseif ($appointmentStatus === 'Rejected') { ?>
                                    <span class="text-muted small">Rejected</span>
                                <?php } else { ?>
                                    <span class="text-muted small"><?php echo e($appointmentStatus); ?></span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
</body>
</html>
