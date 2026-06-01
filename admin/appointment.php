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
    $appointment = db_select_one("SELECT id, status, appointment_date FROM appointment WHERE id = ? LIMIT 1", "i", $appointmentId);

    if (!$appointment) {
        $error = "Appointment not found.";
    } elseif ($appointment['status'] === 'Discharged') {
        $error = "Discharged appointments cannot be changed.";
    } elseif ($action === 'approve' && $appointment['appointment_date'] < date('Y-m-d')) {
        $error = "Past appointment requests cannot be approved. Ask the patient to book a new date.";
    } elseif ($action === 'approve') {
        db_execute("UPDATE appointment SET status = 'Approved' WHERE id = ?", "i", $appointmentId);
        $message = "Appointment approved and sent to the doctor's queue.";
    } elseif ($action === 'cancel') {
        db_execute("UPDATE appointment SET status = 'Cancelled' WHERE id = ?", "i", $appointmentId);
        $message = "Appointment cancelled.";
    } else {
        $error = "Invalid appointment action.";
    }
}

$statusFilter = $_GET['status'] ?? 'All';
$allowedStatuses = ['All', 'Pending', 'Approved', 'Discharged', 'Cancelled'];
if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = 'All';
}

$pendingCount = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE status = 'Pending'");
$approvedCount = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE status = 'Approved'");
$dischargedCount = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE status = 'Discharged'");

if ($statusFilter === 'All') {
    $appointments = mysqli_query($connect, "SELECT * FROM appointment ORDER BY appointment_date DESC, date_booked DESC");
} else {
    $appointmentsStmt = mysqli_prepare($connect, "SELECT * FROM appointment WHERE status = ? ORDER BY appointment_date DESC, date_booked DESC");
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
                <h3><?php echo $dischargedCount; ?></h3>
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
                        <th>Symptoms</th>
                        <th>Status</th>
                        <th>Booked</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($appointments) < 1) { ?>
                        <tr>
                            <td colspan="9" class="hms-empty">No appointments found for this view.</td>
                        </tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_array($appointments)) { ?>
                        <tr>
                            <td><?php echo e($row['id']); ?></td>
                            <td>
                                <strong><?php echo e($row['firstname'] . ' ' . $row['surname']); ?></strong><br>
                                <span class="text-muted small"><?php echo e($row['patient_username']); ?></span>
                            </td>
                            <td><?php echo e($row['doctor_username'] ?: 'Unassigned'); ?></td>
                            <td><?php echo e($row['phone']); ?></td>
                            <td><?php echo e($row['appointment_date']); ?></td>
                            <td><?php echo e($row['symptoms']); ?></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($row['status']); ?>"><?php echo e($row['status']); ?></span></td>
                            <td><?php echo e($row['date_booked']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'Pending') { ?>
                                    <form method="post" class="hms-actions">
                                        <input type="hidden" name="appointment_id" value="<?php echo e($row['id']); ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Approve</button>
                                        <button type="submit" name="action" value="cancel" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this appointment?');"><i class="fas fa-times me-1"></i>Cancel</button>
                                    </form>
                                <?php } elseif ($row['status'] === 'Approved') { ?>
                                    <form method="post">
                                        <input type="hidden" name="appointment_id" value="<?php echo e($row['id']); ?>">
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
