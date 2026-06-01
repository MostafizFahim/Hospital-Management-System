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
    <title>Admin Dashboard</title>
</head>
<body>
<?php
include("../include/header.php");
include("../include/connection.php");
include("sidenav.php");

$adminCount = hms_count("SELECT COUNT(*) AS total FROM admin");
$doctorCount = hms_count("SELECT COUNT(*) AS total FROM doctors WHERE status = 'Approved'");
$patientCount = hms_count("SELECT COUNT(*) AS total FROM patient");
$pendingAppointments = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE appointment_status = 'Pending'");
$approvedAppointments = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE appointment_status = 'Approved'");
$completedAppointments = hms_count("SELECT COUNT(*) AS total FROM appointment WHERE appointment_status = 'Completed'");
$pendingJobs = hms_count("SELECT COUNT(*) AS total FROM doctors WHERE status = 'Pending'");
$unpaidInvoices = hms_count("SELECT COUNT(*) AS total FROM income WHERE payment_status = 'Unpaid'");
$incomeRow = db_select_one("SELECT COALESCE(SUM(GREATEST(amount_paid - waived_amount, 0)), 0) AS total FROM income WHERE payment_status = 'Paid'");
$paidIncome = (float) ($incomeRow['total'] ?? 0);

$todayAppointments = mysqli_query($connect, "SELECT * FROM appointment WHERE appointment_date = CURDATE() AND appointment_status = 'Approved' ORDER BY appointment_time ASC, date_booked DESC LIMIT 6");
$billingQueue = mysqli_query($connect, "SELECT * FROM income WHERE payment_status = 'Unpaid' ORDER BY date_discharge DESC LIMIT 6");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Control Center</p>
            <h4>Admin Dashboard</h4>
        </div>
        <div class="hms-actions">
            <a href="appointment.php" class="btn btn-primary"><i class="fas fa-calendar-check me-1"></i>Manage appointments</a>
            <a href="income.php" class="btn btn-outline-primary"><i class="fas fa-file-invoice-dollar me-1"></i>Billing</a>
        </div>
    </div>

    <section class="hms-stat-grid">
        <a href="patient.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Total patients</p>
                <h3><?php echo $patientCount; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-procedures"></i></span>
        </a>
        <a href="doctor.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Total doctors</p>
                <h3><?php echo $doctorCount; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-user-md"></i></span>
        </a>
        <a href="job_request.php" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Pending doctor applications</p>
                <h3><?php echo $pendingJobs; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-clipboard-list"></i></span>
        </a>
        <a href="appointment.php?status=Pending" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Pending appointments</p>
                <h3><?php echo $pendingAppointments; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-calendar-plus"></i></span>
        </a>
        <a href="appointment.php?status=Approved" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Approved appointments</p>
                <h3><?php echo $approvedAppointments; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-calendar-day"></i></span>
        </a>
        <a href="appointment.php?status=Completed" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Completed appointments</p>
                <h3><?php echo $completedAppointments; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-notes-medical"></i></span>
        </a>
        <a href="income.php?status=Unpaid" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Unpaid bills</p>
                <h3><?php echo $unpaidInvoices; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-receipt"></i></span>
        </a>
        <a href="income.php?status=Paid" class="hms-card hms-stat text-decoration-none text-reset">
            <div>
                <p>Collected income</p>
                <h3 class="hms-money-value"><?php echo hms_money($paidIncome); ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-money-check-alt"></i></span>
        </a>
    </section>

    <section class="hms-section-grid">
        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Today</p>
                    <h5>Approved Schedule</h5>
                </div>
                <a href="appointment.php" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Phone</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($todayAppointments) < 1) { ?>
                        <tr><td colspan="4" class="hms-empty">No approved appointments scheduled for today.</td></tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_array($todayAppointments)) { ?>
                        <tr>
                            <td><?php echo e($row['firstname'] . ' ' . $row['surname']); ?></td>
                            <td><?php echo e($row['doctor_username'] ?: 'Unassigned'); ?></td>
                            <td><?php echo e($row['phone']); ?></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($row['status']); ?>"><?php echo e($row['status']); ?></span></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="hms-card">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Billing</p>
                    <h5>Payment Queue</h5>
                </div>
                <a href="income.php" class="btn btn-sm btn-outline-primary">Open</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($billingQueue) < 1) { ?>
                        <tr><td colspan="3" class="hms-empty">No unpaid invoices waiting.</td></tr>
                    <?php } ?>
                    <?php while ($row = mysqli_fetch_array($billingQueue)) { ?>
                        <tr>
                            <td><?php echo e($row['patient']); ?></td>
                            <td><?php echo hms_money(hms_invoice_due($row)); ?></td>
                            <td><span class="status-pill status-<?php echo hms_status_class($row['payment_status']); ?>"><?php echo e($row['payment_status']); ?></span></td>
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
