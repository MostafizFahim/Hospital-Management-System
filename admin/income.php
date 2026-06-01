<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoiceId = (int) ($_POST['invoice_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $invoice = db_select_one("SELECT id, amount_paid, waived_amount, payment_status FROM income WHERE id = ? LIMIT 1", "i", $invoiceId);

    if (!$invoice) {
        $error = "Invoice not found.";
    } elseif ($action === 'paid') {
        if (hms_invoice_due($invoice) <= 0) {
            db_execute("UPDATE income SET payment_status = 'Waived', paid_at = NULL WHERE id = ?", "i", $invoiceId);
            $message = "Invoice is fully waived, so it was marked waived instead of paid.";
        } else {
            db_execute("UPDATE income SET payment_status = 'Paid', paid_at = NOW() WHERE id = ?", "i", $invoiceId);
            $message = "Invoice marked as paid.";
        }
    } elseif ($action === 'apply_waiver') {
        if ($invoice['payment_status'] === 'Paid') {
            $error = "Paid invoices must be moved back to unpaid before changing waiver.";
        } else {
            $waiverType = $_POST['waiver_type'] ?? 'amount';
            $waiverValue = (float) ($_POST['waiver_value'] ?? 0);
            $invoiceTotal = (float) $invoice['amount_paid'];

            if ($waiverValue < 0) {
                $error = "Waiver cannot be negative.";
            } elseif ($waiverType === 'percent' && $waiverValue > 100) {
                $error = "Waiver percent cannot be more than 100.";
            } else {
                $waiverAmount = $waiverType === 'percent' ? ($invoiceTotal * $waiverValue / 100) : $waiverValue;
                $waiverAmount = min($invoiceTotal, round($waiverAmount, 2));
                $newStatus = $waiverAmount >= $invoiceTotal ? 'Waived' : 'Unpaid';

                db_execute("UPDATE income SET waived_amount = ?, payment_status = ?, paid_at = NULL WHERE id = ?", "dsi", $waiverAmount, $newStatus, $invoiceId);
                $message = $newStatus === 'Waived' ? "Invoice fully waived." : "Partial waiver applied. Remaining balance is still unpaid.";
            }
        }
    } elseif ($action === 'clear_waiver') {
        if ($invoice['payment_status'] === 'Paid') {
            $error = "Move the invoice back to unpaid before clearing waiver.";
        } else {
            db_execute("UPDATE income SET waived_amount = 0, payment_status = 'Unpaid', paid_at = NULL WHERE id = ?", "i", $invoiceId);
            $message = "Waiver cleared.";
        }
    } elseif ($action === 'unpaid') {
        db_execute("UPDATE income SET payment_status = 'Unpaid', paid_at = NULL WHERE id = ?", "i", $invoiceId);
        $message = "Invoice moved back to unpaid.";
    } else {
        $error = "Invalid billing action.";
    }
}

$filter = $_GET['status'] ?? 'All';
$allowedStatuses = ['All', 'Unpaid', 'Paid', 'Waived'];
if (!in_array($filter, $allowedStatuses, true)) {
    $filter = 'All';
}

if ($filter === 'All') {
    $res = mysqli_query($connect, "SELECT * FROM income ORDER BY date_discharge DESC, id DESC");
} else {
    $stmt = mysqli_prepare($connect, "SELECT * FROM income WHERE payment_status = ? ORDER BY date_discharge DESC, id DESC");
    mysqli_stmt_bind_param($stmt, "s", $filter);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
}

$paidRow = db_select_one("SELECT COALESCE(SUM(GREATEST(amount_paid - waived_amount, 0)), 0) AS total FROM income WHERE payment_status = 'Paid'");
$unpaidRow = db_select_one("SELECT COALESCE(SUM(GREATEST(amount_paid - waived_amount, 0)), 0) AS total FROM income WHERE payment_status = 'Unpaid'");
$waivedRow = db_select_one("SELECT COALESCE(SUM(waived_amount), 0) AS total FROM income");
$invoiceCount = hms_count("SELECT COUNT(*) AS total FROM income");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Billing</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Revenue Desk</p>
            <h4>Billing</h4>
        </div>
        <div class="hms-actions">
            <?php foreach ($allowedStatuses as $status) { ?>
                <a class="btn btn-sm <?php echo $filter === $status ? 'btn-primary' : 'btn-outline-primary'; ?>" href="income.php?status=<?php echo e($status); ?>"><?php echo e($status); ?></a>
            <?php } ?>
        </div>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <section class="hms-stat-grid">
        <div class="hms-card hms-stat">
            <div>
                <p>Total invoices</p>
                <h3><?php echo $invoiceCount; ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-file-invoice"></i></span>
        </div>
        <div class="hms-card hms-stat">
            <div>
                <p>Collected</p>
                <h3><?php echo hms_money($paidRow['total'] ?? 0); ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-money-check-alt"></i></span>
        </div>
        <div class="hms-card hms-stat">
            <div>
                <p>Outstanding</p>
                <h3><?php echo hms_money($unpaidRow['total'] ?? 0); ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-receipt"></i></span>
        </div>
        <div class="hms-card hms-stat">
            <div>
                <p>Waived</p>
                <h3><?php echo hms_money($waivedRow['total'] ?? 0); ?></h3>
            </div>
            <span class="hms-stat-icon"><i class="fas fa-hand-holding-heart"></i></span>
        </div>
    </section>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Discharged</th>
                        <th>Charge</th>
                        <th>Waived</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Paid At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($res) < 1) { ?>
                    <tr><td class="hms-empty" colspan="9">No invoices found.</td></tr>
                <?php } ?>
                <?php while ($row = mysqli_fetch_array($res)) {
                    $due = hms_invoice_due($row);
                ?>
                    <tr>
                        <td><?php echo e($row['id']); ?></td>
                        <td>
                            <strong><?php echo e($row['patient']); ?></strong><br>
                            <span class="text-muted small">Dr. <?php echo e($row['doctor']); ?></span>
                        </td>
                        <td><?php echo e($row['date_discharge']); ?></td>
                        <td><?php echo hms_money($row['amount_paid']); ?></td>
                        <td><?php echo hms_money($row['waived_amount']); ?></td>
                        <td><?php echo hms_money($due); ?></td>
                        <td><span class="status-pill status-<?php echo hms_status_class($row['payment_status']); ?>"><?php echo e($row['payment_status']); ?></span></td>
                        <td><?php echo $row['payment_status'] === 'Paid' ? e($row['paid_at']) : '-'; ?></td>
                        <td>
                            <div class="hms-actions">
                                <?php if ($row['payment_status'] !== 'Paid' && $due > 0) { ?>
                                    <form method="post">
                                        <input type="hidden" name="invoice_id" value="<?php echo e($row['id']); ?>">
                                        <button type="submit" name="action" value="paid" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Paid</button>
                                    </form>
                                <?php } ?>

                                <?php if ($row['payment_status'] !== 'Paid') { ?>
                                    <form method="post" class="hms-actions">
                                        <input type="hidden" name="invoice_id" value="<?php echo e($row['id']); ?>">
                                        <select name="waiver_type" class="form-select form-select-sm" style="width:90px;">
                                            <option value="amount">BDT</option>
                                            <option value="percent">%</option>
                                        </select>
                                        <input type="number" step="0.01" min="0" name="waiver_value" class="form-control form-control-sm" style="width:105px;" placeholder="Waive">
                                        <button type="submit" name="action" value="apply_waiver" class="btn btn-sm btn-outline-primary"><i class="fas fa-percent me-1"></i>Apply</button>
                                    </form>
                                <?php } ?>

                                <?php if ((float) $row['waived_amount'] > 0 && $row['payment_status'] !== 'Paid') { ?>
                                    <form method="post">
                                        <input type="hidden" name="invoice_id" value="<?php echo e($row['id']); ?>">
                                        <button type="submit" name="action" value="clear_waiver" class="btn btn-sm btn-outline-secondary">Clear waiver</button>
                                    </form>
                                <?php } ?>

                                <?php if ($row['payment_status'] !== 'Unpaid') { ?>
                                    <form method="post">
                                        <input type="hidden" name="invoice_id" value="<?php echo e($row['id']); ?>">
                                        <button type="submit" name="action" value="unpaid" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo me-1"></i>Unpaid</button>
                                    </form>
                                <?php } ?>
                            </div>
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
