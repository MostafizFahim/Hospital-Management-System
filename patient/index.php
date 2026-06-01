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
    <title>Patient Dashboard</title>
</head>
<body>
<?php
include("../include/header.php");
include("../include/connection.php");
include("sidenav.php");

$patient = $_SESSION['patient'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['message'] ?? '');

    if ($title === '') {
        $error = "Enter report title.";
    } elseif ($body === '') {
        $error = "Enter report message.";
    } else {
        db_execute("INSERT INTO report(title, message, username, date_send) VALUES(?,?,?,NOW())", "sss", $title, $body, $patient);
        $message = "Your report has been sent to the admin team.";
    }
}

$totalAppointments = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE patient_username = ?", "s", $patient);
$pendingAppointments = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE patient_username = ? AND appointment_status = 'Pending'", "s", $patient);
$approvedAppointments = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE patient_username = ? AND appointment_status = 'Approved'", "s", $patient);
$completedAppointments = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE patient_username = ? AND appointment_status = 'Completed'", "s", $patient);
$unpaidInvoices = hms_count("SELECT COUNT(*) AS total FROM income WHERE patient_username = ? AND payment_status = 'Unpaid'", "s", $patient);
$prescriptionsCount = hms_count("SELECT COUNT(*) AS total FROM prescriptions WHERE patient_username = ?", "s", $patient);

$appointmentsStmt = mysqli_prepare($connect, "SELECT * FROM appointment WHERE patient_username = ? ORDER BY appointment_date DESC, date_booked DESC LIMIT 5");
mysqli_stmt_bind_param($appointmentsStmt, "s", $patient);
mysqli_stmt_execute($appointmentsStmt);
$appointments = mysqli_stmt_get_result($appointmentsStmt);
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Patient Portal</p>
            <h4>Dashboard</h4>
        </div>
        <a href="appointment.php" class="btn btn-primary"><i class="fas fa-calendar-plus me-1"></i>Book appointment</a>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <section class="hms-stat-grid">
        <a href="appointment.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Total appointments</p>
                <h3><?php echo $totalAppointments; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-calendar-check"></i></span>
        </a>
        <a href="appointment.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Pending appointments</p>
                <h3><?php echo $pendingAppointments; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-hourglass-half"></i></span>
        </a>
        <a href="appointment.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Approved appointments</p>
                <h3><?php echo $approvedAppointments; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-calendar-day"></i></span>
        </a>
        <a href="invoice.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Unpaid bills</p>
                <h3><?php echo $unpaidInvoices; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-receipt"></i></span>
        </a>
        <a href="prescription.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Prescriptions</p>
                <h3><?php echo $prescriptionsCount; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-file-prescription"></i></span>
        </a>
        <div class="hms-card hms-stat">
            <div>
                <p>Completed visits</p>
                <h3><?php echo $completedAppointments; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-notes-medical"></i></span>
        </div>
    </section>

    <section class="hms-section-grid">
        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Timeline</p>
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
                    <?php if (mysqli_num_rows($appointments) < 1) { ?>
                        <tr><td colspan="4" class="hms-empty">No appointment history yet.</td></tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_array($appointments)) { ?>
                        <tr>
                            <td><?php echo e($row['appointment_date']); ?></td>
                            <td><?php echo e($row['doctor_username']); ?></td>
                            <td><?php echo e($row['symptoms']); ?></td>
                            <?php $appointmentStatus = hms_appointment_status($row); ?>
                            <td><span class="status-pill status-<?php echo hms_status_class($appointmentStatus); ?>"><?php echo e($appointmentStatus); ?></span></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Support</p>
                    <h5>Send Report</h5>
                </div>
            </div>
            <form method="post">
                <label>Title</label>
                <input type="text" name="title" autocomplete="off" class="form-control" placeholder="Billing, appointment, profile, etc." required>

                <label>Message</label>
                <textarea name="message" autocomplete="off" class="form-control" rows="5" placeholder="Write your concern clearly" required></textarea>

                <button type="submit" name="send" class="btn btn-primary mt-3"><i class="fas fa-paper-plane me-1"></i>Send report</button>
            </form>
        </div>
    </section>
</main>
</body>
</html>
