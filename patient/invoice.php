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
    <title>My Bills</title>
</head>
<body>
<?php
include("../include/header.php");
include("../include/connection.php");
include("sidenav.php");

$pat = $_SESSION['patient'];
$stmt = mysqli_prepare($connect, "SELECT * FROM income WHERE patient_username = ? ORDER BY date_discharge DESC");
mysqli_stmt_bind_param($stmt, "s", $pat);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$unpaidTotal = db_select_one("SELECT COALESCE(SUM(GREATEST(amount_paid - waived_amount, 0)), 0) AS total FROM income WHERE patient_username = ? AND payment_status = 'Unpaid'", "s", $pat);
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Billing</p>
            <h4>My Bills</h4>
        </div>
        <span class="status-pill status-unpaid">Outstanding <?php echo hms_money($unpaidTotal['total'] ?? 0); ?></span>
    </div>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doctor</th>
                        <th>Created</th>
                        <th>Charge</th>
                        <th>Waived</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($res) < 1) { ?>
                    <tr><td class="hms-empty" colspan="9">No bills yet.</td></tr>
                <?php } ?>
                <?php while ($row = mysqli_fetch_array($res)) { ?>
                    <tr>
                        <td><?php echo e($row['id']); ?></td>
                        <td><?php echo e($row['doctor']); ?></td>
                        <td><?php echo e($row['created_at'] ?: $row['date_discharge']); ?></td>
                        <td><?php echo hms_money($row['amount_paid']); ?></td>
                        <td><?php echo hms_money($row['waived_amount']); ?></td>
                        <td><?php echo hms_money(hms_invoice_due($row)); ?></td>
                        <td><span class="status-pill status-<?php echo hms_status_class($row['payment_status']); ?>"><?php echo e($row['payment_status']); ?></span></td>
                        <td><?php echo e($row['description']); ?></td>
                        <td>
                            <a href="view.php?id=<?php echo e($row['id']); ?>" class="btn btn-sm btn-primary"><i class="fas fa-file-medical-alt me-1"></i>View bill</a>
                            <?php if ($row['payment_status'] === 'Paid') { ?>
                                <a href="receipt.php?id=<?php echo e($row['id']); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-receipt me-1"></i>Receipt</a>
                            <?php } else { ?>
                                <span class="status-pill status-unpaid">Payment pending</span>
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
