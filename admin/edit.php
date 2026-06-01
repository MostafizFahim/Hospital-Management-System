<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$id = (int) ($_GET['id'] ?? 0);
$message = '';
$error = '';
$allowedStatuses = ['Pending', 'Approved', 'Rejected'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $salary = (float) ($_POST['salary'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($salary < 0) {
        $error = "Salary cannot be negative.";
    } elseif (!in_array($status, $allowedStatuses, true)) {
        $error = "Invalid doctor status.";
    } else {
        db_execute("UPDATE doctors SET salary = ?, status = ? WHERE id = ?", "dsi", $salary, $status, $id);
        $message = "Doctor profile updated.";
    }
}

$doctor = $id > 0 ? db_select_one("SELECT * FROM doctors WHERE id = ? LIMIT 1", "i", $id) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Doctor</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Medical Staff</p>
            <h4>Edit Doctor</h4>
        </div>
        <a href="doctor.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back to doctors</a>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <?php if (!$doctor) { ?>
        <div class="hms-card hms-empty">Doctor not found.</div>
    <?php } else { ?>
        <section class="hms-section-grid">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Profile</p>
                        <h5>Dr. <?php echo e($doctor['firstname'] . ' ' . $doctor['surname']); ?></h5>
                    </div>
                    <span class="status-pill status-<?php echo hms_status_class($doctor['status']); ?>"><?php echo e($doctor['status']); ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr><th>ID</th><td><?php echo e($doctor['id']); ?></td></tr>
                            <tr><th>Username</th><td><?php echo e($doctor['username']); ?></td></tr>
                            <tr><th>Email</th><td><?php echo e($doctor['email']); ?></td></tr>
                            <tr><th>Phone</th><td><?php echo e($doctor['phone']); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo e($doctor['gender']); ?></td></tr>
                            <tr><th>Country</th><td><?php echo e($doctor['country']); ?></td></tr>
                            <tr><th>Registered</th><td><?php echo e($doctor['data_reg']); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Admin Controls</p>
                        <h5>Employment Details</h5>
                    </div>
                </div>
                <form method="post">
                    <label>Salary</label>
                    <input type="number" step="0.01" min="0" name="salary" class="form-control" value="<?php echo e($doctor['salary']); ?>" required>

                    <label>Status</label>
                    <select name="status" class="form-select" required>
                        <?php foreach ($allowedStatuses as $status) { ?>
                            <option value="<?php echo e($status); ?>" <?php echo $doctor['status'] === $status ? 'selected' : ''; ?>><?php echo e($status); ?></option>
                        <?php } ?>
                    </select>

                    <button type="submit" name="update" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Save changes</button>
                </form>
            </div>
        </section>
    <?php } ?>
</main>
</body>
</html>
