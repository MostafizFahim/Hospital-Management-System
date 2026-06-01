<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
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

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = (int) ($_POST['appointment_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $appointment = db_select_one("SELECT id, status, appointment_status, appointment_date FROM appointment WHERE id = ? LIMIT 1", "i", $appointmentId);
    $appointmentStatus = $appointment ? hms_appointment_status($appointment) : '';

    if (!$appointment) {
        $error = "Appointment not found.";
    } elseif ($appointmentStatus === 'Completed') {
        $error = "Completed appointments cannot be changed.";
    } elseif ($action === 'approve' && $appointment['appointment_date'] < date('Y-m-d')) {
        $error = "Past appointment requests cannot be approved. Ask the patient to book a new date.";
    } elseif ($action === 'approve') {
        hms_sync_appointment_status($appointmentId, 'Approved');
        $message = "Appointment approved and sent to the doctor's queue.";
    } elseif ($action === 'reject') {
        hms_sync_appointment_status($appointmentId, 'Rejected');
        $message = "Appointment rejected.";
    } elseif ($action === 'reschedule') {
        $newDate = trim($_POST['appointment_date'] ?? '');
        $newTime = trim($_POST['appointment_time'] ?? '');
        if ($newDate === '' || $newDate < date('Y-m-d')) {
            $error = "Select a valid future reschedule date.";
        } elseif ($newTime === '') {
            $error = "Select a reschedule time.";
        } else {
            db_execute(
                "UPDATE appointment SET appointment_date = ?, appointment_time = ?, status = 'Rescheduled', appointment_status = 'Rescheduled', updated_at = NOW() WHERE id = ?",
                "ssi",
                $newDate,
                $newTime,
                $appointmentId
            );
            $message = "Appointment rescheduled. Patient can see the updated date and time.";
        }
    } elseif ($action === 'cancel') {
        hms_sync_appointment_status($appointmentId, 'Cancelled');
        $message = "Appointment cancelled.";
    } else {
        $error = "Invalid appointment action.";
    }
}

$statusFilter = $_GET['status'] ?? 'All';
$allowedStatuses = ['All', 'Pending', 'Approved', 'Rejected', 'Rescheduled', 'Completed', 'Cancelled'];
if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = 'All';
}

$pendingCount = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE appointment_status = 'Pending'");
$approvedCount = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE appointment_status = 'Approved'");
$completedCount = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE appointment_status = 'Completed'");

if ($statusFilter === 'All') {
    $appointments = mysqli_query($connect, "SELECT * FROM appointment ORDER BY appointment_date DESC, date_booked DESC");
} else {
    $appointmentsStmt = mysqli_prepare($connect, "SELECT * FROM appointment WHERE appointment_status = ? ORDER BY appointment_date DESC, date_booked DESC");
    mysqli_stmt_bind_param($appointmentsStmt, "s", $statusFilter);
    mysqli_stmt_execute($appointmentsStmt);
    $appointments = mysqli_stmt_get_result($appointmentsStmt);
}
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Front Desk</p>
            <h4>Appointments</h4>
        </div>
        <div class="hms-actions">
            <?php foreach ($allowedStatuses as $status) { ?>
                <a class="btn btn-sm <?php echo $statusFilter === $status ? 'btn-primary' : 'btn-outline-primary'; ?>" href="appointment.php?status=<?php echo e($status); ?>"><?php echo e($status); ?></a>
            <?php } ?>
        </div>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <section class="hms-stat-grid">
        <div class="hms-card hms-stat">
            <div>
                <p>Pending review</p>
                <h3><?php echo $pendingCount; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-hourglass-half"></i></span>
        </div>
        <div class="hms-card hms-stat">
            <div>
                <p>Approved queue</p>
                <h3><?php echo $approvedCount; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-calendar-check"></i></span>
        </div>
        <div class="hms-card hms-stat">
            <div>
                <p>Completed visits</p>
                <h3><?php echo $completedCount; ?></h3>
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
                        <th>Doctor</th>
                        <th>Phone</th>
                        <th>Appointment Date</th>
                        <th>Time</th>
                        <th>Symptoms</th>
                        <th>Appointment</th>
                        <th>Payment</th>
                        <th>Prescription</th>
                        <th>Booked</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($appointments) < 1) { ?>
                        <tr>
                            <td colspan="12" class="hms-empty">No appointments found for this view.</td>
                        </tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_array($appointments)) { ?>
                        <?php $appointmentStatus = hms_appointment_status($row); ?>
                        <tr>
                            <td><?php echo e($row['id']); ?></td>
                            <td>
                                <strong><?php echo e($row['firstname'] . ' ' . $row['surname']); ?></strong><br>
                                <span class="text-muted small"><?php echo e($row['patient_username']); ?></span>
                            </td>
                            <td><?php echo e($row['doctor_username'] ?: 'Unassigned'); ?></td>
                            <td><?php echo e($row['phone']); ?></td>
                            <td><?php echo e($row['appointment_date']); ?></td>
                            <td><?php echo e($row['appointment_time'] ? substr($row['appointment_time'], 0, 5) : '-'); ?></td>
                            <td><?php echo e($row['symptoms']); ?></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($appointmentStatus); ?>"><?php echo e($appointmentStatus); ?></span></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($row['payment_status']); ?>"><?php echo e($row['payment_status']); ?></span></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($row['prescription_status']); ?>"><?php echo e($row['prescription_status']); ?></span></td>
                            <td><?php echo e($row['date_booked']); ?></td>
                            <td>
                                <?php if ($appointmentStatus === 'Pending' || $appointmentStatus === 'Rescheduled') { ?>
                                    <form method="post" class="hms-actions">
                                        <input type="hidden" name="appointment_id" value="<?php echo e($row['id']); ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Approve</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject this appointment?');"><i class="fas fa-times me-1"></i>Reject</button>
                                    </form>
                                    <form method="post" class="hms-actions mt-2">
                                        <input type="hidden" name="appointment_id" value="<?php echo e($row['id']); ?>">
                                        <input type="date" name="appointment_date" class="form-control form-control-sm" min="<?php echo date('Y-m-d'); ?>" style="width:140px;">
                                        <input type="time" name="appointment_time" class="form-control form-control-sm" style="width:115px;">
                                        <button type="submit" name="action" value="reschedule" class="btn btn-sm btn-outline-primary">Reschedule</button>
                                    </form>
                                <?php } elseif ($appointmentStatus === 'Approved') { ?>
                                    <form method="post" class="hms-actions">
                                        <input type="hidden" name="appointment_id" value="<?php echo e($row['id']); ?>">
                                        <input type="date" name="appointment_date" class="form-control form-control-sm" min="<?php echo date('Y-m-d'); ?>" style="width:140px;">
                                        <input type="time" name="appointment_time" class="form-control form-control-sm" style="width:115px;">
                                        <button type="submit" name="action" value="reschedule" class="btn btn-sm btn-outline-primary">Reschedule</button>
                                        <button type="submit" name="action" value="cancel" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this approved appointment?');"><i class="fas fa-times me-1"></i>Cancel</button>
                                    </form>
                                <?php } else { ?>
                                    <span class="text-muted small">No action</span>
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
