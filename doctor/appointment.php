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
    <title>Appointments</title>
</head>
<body>
<?php
include("../include/header.php");
include("../include/connection.php");
include("sidenav.php");

$doctor = $_SESSION['doctor'];
$appointmentsStmt = mysqli_prepare($connect, "SELECT * FROM appointment WHERE appointment_status = 'Approved' AND doctor_username = ? ORDER BY appointment_date ASC, appointment_time ASC, date_booked ASC");
mysqli_stmt_bind_param($appointmentsStmt, "s", $doctor);
mysqli_stmt_execute($appointmentsStmt);
$appointments = mysqli_stmt_get_result($appointmentsStmt);

$pendingReview = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE appointment_status = 'Pending' AND doctor_username = ?", "s", $doctor);
$completed = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE appointment_status = 'Completed' AND doctor_username = ?", "s", $doctor);
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Clinical Queue</p>
            <h4>Approved Appointments</h4>
        </div>
        <span class="status-pill status-pending"><?php echo $pendingReview; ?> waiting for admin approval</span>
    </div>

    <section class="hms-stat-grid">
        <div class="hms-card hms-stat">
            <div>
                <p>Ready to check</p>
                <h3><?php echo mysqli_num_rows($appointments); ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-stethoscope"></i></span>
        </div>
        <div class="hms-card hms-stat">
            <div>
                <p>Completed by you</p>
                <h3><?php echo $completed; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-notes-medical"></i></span>
        </div>
    </section>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Appointment</th>
                        <th>Time</th>
                        <th>Symptoms</th>
                        <th>Booked</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($appointments) < 1) { ?>
                        <tr><td class="hms-empty" colspan="9">No approved appointments are ready yet.</td></tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_array($appointments)) { ?>
                        <tr>
                            <td><?php echo e($row['id']); ?></td>
                            <td>
                                <strong><?php echo e($row['firstname'] . ' ' . $row['surname']); ?></strong><br>
                                <span class="text-muted small"><?php echo e($row['patient_username']); ?></span>
                            </td>
                            <td><?php echo e($row['gender']); ?></td>
                            <td><?php echo e($row['phone']); ?></td>
                            <td><?php echo e($row['appointment_date']); ?></td>
                            <td><?php echo e($row['appointment_time'] ? substr($row['appointment_time'], 0, 5) : '-'); ?></td>
                            <td><?php echo e($row['symptoms']); ?></td>
                            <td><?php echo e($row['date_booked']); ?></td>
                            <td>
                                <a href="discharge.php?id=<?php echo e($row['id']); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-notes-medical me-1"></i>Check
                                </a>
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
