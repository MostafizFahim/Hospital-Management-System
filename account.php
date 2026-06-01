<?php
include("include/connection.php");

$error = '';
$old = [
    'fname' => '',
    'sname' => '',
    'uname' => '',
    'email' => '',
    'phone' => '',
    'gender' => '',
    'country' => '',
];

if (isset($_POST['create'])) {
    $old['fname'] = trim($_POST['fname'] ?? '');
    $old['sname'] = trim($_POST['sname'] ?? '');
    $old['uname'] = trim($_POST['uname'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['phone'] = trim($_POST['phone'] ?? '');
    $old['gender'] = trim($_POST['gender'] ?? '');
    $old['country'] = trim($_POST['country'] ?? '');
    $password = $_POST['pass'] ?? '';
    $confirmPassword = $_POST['con_pass'] ?? '';

    if ($old['fname'] === '') {
        $error = "Enter first name.";
    } elseif ($old['sname'] === '') {
        $error = "Enter surname.";
    } elseif ($old['uname'] === '') {
        $error = "Enter username.";
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $old['uname'])) {
        $error = "Username must be 3-30 characters and use only letters, numbers, or underscore.";
    } elseif ($old['email'] === '') {
        $error = "Enter email.";
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";
    } elseif ($old['phone'] === '') {
        $error = "Enter phone number.";
    } elseif (!preg_match('/^[0-9+\-\s]{7,20}$/', $old['phone'])) {
        $error = "Enter a valid phone number.";
    } elseif ($old['gender'] === '') {
        $error = "Select gender.";
    } elseif ($old['country'] === '') {
        $error = "Select country.";
    } elseif ($password === '') {
        $error = "Enter password.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($confirmPassword !== $password) {
        $error = "Passwords do not match.";
    } else {
        $exists = db_select_one("SELECT id FROM patient WHERE username = ? OR email = ? LIMIT 1", "ss", $old['uname'], $old['email']);

        if ($exists) {
            $error = "Username or email already exists.";
        } else {
            $passwordHash = hash_user_password($password);
            $created = db_execute(
                "INSERT INTO patient(firstname, surname, username, email, phone, gender, country, password, date_reg, profile) VALUES(?,?,?,?,?,?,?,?,NOW(),'patient.jpg.jpg')",
                "ssssssss",
                $old['fname'],
                $old['sname'],
                $old['uname'],
                $old['email'],
                $old['phone'],
                $old['gender'],
                $old['country'],
                $passwordHash
            );

            if ($created) {
                header("Location: patientlogin.php?registered=1");
                exit();
            }

            $error = "Could not create account. Please try again.";
        }
    }
}

function selected_option($current, $value)
{
    return $current === $value ? 'selected' : '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Patient Account</title>
</head>
<body>
<?php include("include/header.php"); ?>

<main class="hms-auth-shell">
    <section class="hms-card hms-auth-card">
        <div class="hms-auth-media" style="background-image: linear-gradient(rgba(17,24,39,.50), rgba(17,24,39,.76)), url('img/Patient.jpg');">
            <p class="eyebrow text-white">Patient Registration</p>
            <h2 class="fw-bold">Create your account before booking appointments.</h2>
            <p class="mb-0">After registration, you can request appointments and view invoices or prescriptions.</p>
        </div>
        <div class="hms-auth-form">
            <h4 class="fw-bold mb-1">Create Patient Account</h4>
            <p class="text-muted mb-4">Use a valid email and phone number for hospital communication.</p>

            <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

            <form method="post">
                <div class="row">
                    <div class="col-md-6">
                        <label>First Name</label>
                        <input type="text" name="fname" class="form-control" autocomplete="off" value="<?php echo e($old['fname']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label>Surname</label>
                        <input type="text" name="sname" class="form-control" autocomplete="off" value="<?php echo e($old['sname']); ?>" required>
                    </div>
                </div>

                <label>Username</label>
                <input type="text" name="uname" class="form-control" autocomplete="off" value="<?php echo e($old['uname']); ?>" required>

                <label>Email</label>
                <input type="email" name="email" class="form-control" autocomplete="off" value="<?php echo e($old['email']); ?>" required>

                <label>Phone</label>
                <input type="text" name="phone" class="form-control" autocomplete="off" value="<?php echo e($old['phone']); ?>" required>

                <div class="row">
                    <div class="col-md-6">
                        <label>Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo selected_option($old['gender'], 'Male'); ?>>Male</option>
                            <option value="Female" <?php echo selected_option($old['gender'], 'Female'); ?>>Female</option>
                            <option value="Other" <?php echo selected_option($old['gender'], 'Other'); ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Country</label>
                        <select name="country" class="form-select" required>
                            <option value="">Select Country</option>
                            <option value="Bangladesh" <?php echo selected_option($old['country'], 'Bangladesh'); ?>>Bangladesh</option>
                            <option value="India" <?php echo selected_option($old['country'], 'India'); ?>>India</option>
                            <option value="Pakistan" <?php echo selected_option($old['country'], 'Pakistan'); ?>>Pakistan</option>
                            <option value="USA" <?php echo selected_option($old['country'], 'USA'); ?>>USA</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Password</label>
                        <input type="password" name="pass" class="form-control" autocomplete="new-password" required>
                    </div>
                    <div class="col-md-6">
                        <label>Confirm Password</label>
                        <input type="password" name="con_pass" class="form-control" autocomplete="new-password" required>
                    </div>
                </div>

                <button type="submit" name="create" class="btn btn-primary w-100 mt-3"><i class="fas fa-user-plus me-1"></i>Create account</button>
                <p class="mt-3 mb-0">Already registered? <a href="patientlogin.php">Login as patient</a></p>
            </form>
        </div>
    </section>
</main>
</body>
</html>
