<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$id = (int) ($_GET['id'] ?? 0);
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_patient') {
    $id = (int) ($_POST['patient_id'] ?? 0);
    $existingPatient = $id > 0 ? db_select_one("SELECT * FROM patient WHERE id = ? LIMIT 1", "i", $id) : null;

    $firstname = trim($_POST['firstname'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    if (!$existingPatient) {
        $error = "Patient not found.";
    } elseif ($firstname === '') {
        $error = "Enter first name.";
    } elseif ($surname === '') {
        $error = "Enter surname.";
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $error = "Username must be 3-30 characters and use only letters, numbers, or underscore.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";
    } elseif (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
        $error = "Enter a valid phone number.";
    } elseif ($address === '') {
        $error = "Enter address.";
    } elseif ($gender === '') {
        $error = "Select gender.";
    } else {
        $duplicate = db_select_one(
            "SELECT id, username, email FROM patient WHERE (username = ? OR email = ?) AND id <> ? LIMIT 1",
            "ssi",
            $username,
            $email,
            $id
        );

        if ($duplicate) {
            $error = strcasecmp($duplicate['username'], $username) === 0 ? "Username already exists." : "Email already exists.";
        } else {
            mysqli_begin_transaction($connect);
            $oldUsername = $existingPatient['username'];
            $fullName = trim($firstname . ' ' . $surname);

            $updatedPatient = db_execute(
                "UPDATE patient SET firstname = ?, surname = ?, username = ?, email = ?, phone = ?, address = ?, gender = ? WHERE id = ?",
                "sssssssi",
                $firstname,
                $surname,
                $username,
                $email,
                $phone,
                $address,
                $gender,
                $id
            );
            $updatedAppointments = db_execute(
                "UPDATE appointment SET patient_username = ?, firstname = ?, surname = ?, gender = ?, phone = ? WHERE patient_username = ?",
                "ssssss",
                $username,
                $firstname,
                $surname,
                $gender,
                $phone,
                $oldUsername
            );
            $updatedIncome = db_execute("UPDATE income SET patient_username = ?, patient = ? WHERE patient_username = ?", "sss", $username, $fullName, $oldUsername);
            $updatedPrescriptions = db_execute("UPDATE prescriptions SET patient_username = ? WHERE patient_username = ?", "ss", $username, $oldUsername);
            $updatedReports = db_execute("UPDATE report SET username = ? WHERE username = ?", "ss", $username, $oldUsername);

            if ($updatedPatient && $updatedAppointments && $updatedIncome && $updatedPrescriptions && $updatedReports) {
                mysqli_commit($connect);
                $success = "Patient profile updated.";
            } else {
                mysqli_rollback($connect);
                $error = "Could not update patient profile.";
            }
        }
    }
}

$patient = $id > 0 ? db_select_one("SELECT * FROM patient WHERE id = ? LIMIT 1", "i", $id) : null;
$appointments = null;
$invoices = null;

if ($patient) {
    $patientProfile = $patient['profile'] ?: 'patient.jpg.jpg';
    if (!is_file(__DIR__ . "/../patient/img/" . $patientProfile)) {
        $patientProfile = 'patient.jpg.jpg';
    }

    $appointmentStmt = mysqli_prepare($connect, "SELECT * FROM appointment WHERE patient_username = ? ORDER BY appointment_date DESC, id DESC LIMIT 8");
    mysqli_stmt_bind_param($appointmentStmt, "s", $patient['username']);
    mysqli_stmt_execute($appointmentStmt);
    $appointments = mysqli_stmt_get_result($appointmentStmt);

    $invoiceStmt = mysqli_prepare($connect, "SELECT * FROM income WHERE patient_username = ? ORDER BY date_discharge DESC, id DESC LIMIT 8");
    mysqli_stmt_bind_param($invoiceStmt, "s", $patient['username']);
    mysqli_stmt_execute($invoiceStmt);
    $invoices = mysqli_stmt_get_result($invoiceStmt);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Details</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Patient Registry</p>
            <h4>Patient Details</h4>
        </div>
        <a href="patient.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Back to patients</a>
    </div>

    <?php if (!$patient) { ?>
        <div class="hms-card hms-empty">Patient not found.</div>
    <?php } else { ?>
        <?php if ($success) { ?><div class="hms-alert hms-alert-success"><?php echo e($success); ?></div><?php } ?>
        <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>
        <section class="hms-section-grid mb-3">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Profile</p>
                        <h5><?php echo e($patient['firstname'] . ' ' . $patient['surname']); ?></h5>
                    </div>
                    <span class="status-pill">Registered</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <tr><th>Username</th><td><?php echo e($patient['username']); ?></td></tr>
                            <tr><th>Email</th><td><?php echo e($patient['email']); ?></td></tr>
                            <tr><th>Phone</th><td><?php echo e($patient['phone']); ?></td></tr>
                            <tr><th>Gender</th><td><?php echo e($patient['gender']); ?></td></tr>
                            <tr><th>Address</th><td><?php echo nl2br(e($patient['address'])); ?></td></tr>
                            <tr><th>Registered</th><td><?php echo e($patient['date_reg']); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hms-card">
                <p class="eyebrow">Photo</p>
                <img src="../patient/img/<?php echo e($patientProfile); ?>" class="hms-profile-image" alt="Patient photo">
            </div>
        </section>

        <div class="hms-card mb-3">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Edit</p>
                    <h5>Patient Information</h5>
                </div>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="update_patient">
                <input type="hidden" name="patient_id" value="<?php echo e($patient['id']); ?>">
                <div class="hms-form-grid">
                    <div>
                        <label>First Name</label>
                        <input type="text" name="firstname" class="form-control" value="<?php echo e($patient['firstname']); ?>" required>
                    </div>
                    <div>
                        <label>Surname</label>
                        <input type="text" name="surname" class="form-control" value="<?php echo e($patient['surname']); ?>" required>
                    </div>
                    <div>
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo e($patient['username']); ?>" required>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e($patient['email']); ?>" required>
                    </div>
                    <div>
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e($patient['phone']); ?>" required>
                    </div>
                    <div>
                        <label>Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="Male" <?php echo $patient['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $patient['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $patient['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3" required><?php echo e($patient['address']); ?></textarea>
                <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i>Save patient</button>
            </form>
        </div>

        <section class="hms-section-grid">
            <div class="hms-card">
                <div class="page-heading">
                    <div>
                        <p class="eyebrow">Clinical Flow</p>
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
                        <?php if (!$appointments || mysqli_num_rows($appointments) < 1) { ?>
                            <tr><td colspan="4" class="hms-empty">No appointments found.</td></tr>
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
                        <p class="eyebrow">Billing</p>
                        <h5>Recent Invoices</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Charge</th>
                                <th>Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$invoices || mysqli_num_rows($invoices) < 1) { ?>
                            <tr><td colspan="4" class="hms-empty">No invoices found.</td></tr>
                        <?php } ?>
                        <?php while ($row = mysqli_fetch_array($invoices)) { ?>
                            <tr>
                                <td><?php echo e($row['date_discharge']); ?></td>
                                <td><?php echo hms_money($row['amount_paid']); ?></td>
                                <td><?php echo hms_money(hms_invoice_due($row)); ?></td>
                                <td><span class="status-pill status-<?php echo hms_status_class($row['payment_status']); ?>"><?php echo e($row['payment_status']); ?></span></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    <?php } ?>
</main>
</body>
</html>
