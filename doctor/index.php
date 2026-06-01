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
    <title>Doctor Dashboard</title>
</head>
<body>
<?php
include("../include/header.php");
include("../include/connection.php");
include("sidenav.php");

$doctor = $_SESSION['doctor'];
$approvedQueue = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE doctor_username = ? AND appointment_status = 'Approved'", "s", $doctor);
$pendingApproval = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE doctor_username = ? AND appointment_status = 'Pending'", "s", $doctor);
$completedVisits = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE doctor_username = ? AND appointment_status = 'Completed'", "s", $doctor);
$patientCount = hms_count("SELECT COUNT(DISTINCT patient_username) AS total FROM appointment WHERE doctor_username = ?", "s", $doctor);

$todayStmt = mysqli_prepare($connect, "SELECT * FROM appointment WHERE doctor_username = ? AND appointment_date = CURDATE() AND appointment_status = 'Approved' ORDER BY appointment_time ASC, date_booked ASC LIMIT 6");
mysqli_stmt_bind_param($todayStmt, "s", $doctor);
mysqli_stmt_execute($todayStmt);
$todayAppointments = mysqli_stmt_get_result($todayStmt);
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Clinical Workspace</p>
            <h4>Doctor Dashboard</h4>
        </div>
        <a href="appointment.php" class="btn btn-primary"><i class="fas fa-stethoscope me-1"></i>Open queue</a>
    </div>

    <section class="hms-stat-grid">
        <a href="appointment.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Ready to check</p>
                <h3><?php echo $approvedQueue; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-calendar-check"></i></span>
        </a>
        <div class="hms-card hms-stat">
            <div>
                <p>Awaiting admin</p>
                <h3><?php echo $pendingApproval; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-hourglass-half"></i></span>
        </div>
        <div class="hms-card hms-stat">
            <div>
                <p>Completed visits</p>
                <h3><?php echo $completedVisits; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-notes-medical"></i></span>
        </div>
        <a href="patient.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Your patients</p>
                <h3><?php echo $patientCount; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-procedures"></i></span>
        </a>
    </section>

    <div class="hms-card">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Today</p>
                <h5>Appointment Schedule</h5>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>Patient</th>
                    <th>Phone</th>
                    <th>Symptoms</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($todayAppointments) < 1) { ?>
                    <tr><td colspan="5" class="hms-empty">No appointments scheduled for today.</td></tr>
                <?php } ?>
                <?php while ($row = mysqli_fetch_array($todayAppointments)) { ?>
                    <tr>
                        <td><?php echo e($row['firstname'] . ' ' . $row['surname']); ?></td>
                        <td><?php echo e($row['phone']); ?></td>
                        <td><?php echo e($row['symptoms']); ?></td>
                        <?php $appointmentStatus = hms_appointment_status($row); ?>
                        <td><span class="status-pill status-<?php echo hms_status_class($appointmentStatus); ?>"><?php echo e($appointmentStatus); ?></span></td>
                        <td>
                            <?php if ($appointmentStatus === 'Approved') { ?>
                                <a href="discharge.php?id=<?php echo e($row['id']); ?>" class="btn btn-sm btn-primary">Check</a>
                            <?php } else { ?>
                                <span class="text-muted small">Waiting</span>
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
