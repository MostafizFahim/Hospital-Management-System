<?php
include("include/connection.php");

$error = '';
$old = [
    'fname' => '',
    'sname' => '',
    'uname' => '',
    'email' => '',
    'gender' => '',
    'phone' => '',
    'country' => '',
];

if (isset($_POST['apply'])) {
    $old['fname'] = trim($_POST['fname'] ?? '');
    $old['sname'] = trim($_POST['sname'] ?? '');
    $old['uname'] = trim($_POST['uname'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['gender'] = trim($_POST['gender'] ?? '');
    $old['phone'] = trim($_POST['phone'] ?? '');
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
        $error = "Enter email address.";
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } elseif ($old['gender'] === '') {
        $error = "Select gender.";
    } elseif ($old['phone'] === '') {
        $error = "Enter phone number.";
    } elseif (!preg_match('/^[0-9+\-\s]{7,20}$/', $old['phone'])) {
        $error = "Enter a valid phone number.";
    } elseif ($old['country'] === '') {
        $error = "Select country.";
    } elseif ($password === '') {
        $error = "Enter password.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($confirmPassword !== $password) {
        $error = "Passwords do not match.";
    } else {
        $exists = db_select_one("SELECT id FROM doctors WHERE username = ? OR email = ? LIMIT 1", "ss", $old['uname'], $old['email']);

        if ($exists) {
            $error = "Username or email already exists.";
        } else {
            $passwordHash = hash_user_password($password);
            $result = db_execute(
                "INSERT INTO doctors(firstname, surname, username, email, gender, phone, country, password, salary, data_reg, status, profile) VALUES(?,?,?,?,?,?,?,?,'0',NOW(),'Pending','doctor.jpg')",
                "ssssssss",
                $old['fname'],
                $old['sname'],
                $old['uname'],
                $old['email'],
                $old['gender'],
                $old['phone'],
                $old['country'],
                $passwordHash
            );

            if ($result) {
                header("Location: doctorlogin.php?applied=1");
                exit();
            }

            $error = "Could not submit application. Please try again.";
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
    <title>Doctor Application</title>
</head>
<body>
<?php include("include/header.php"); ?>

<main class="hms-auth-shell">
    <section class="hms-card hms-auth-card">
        <div class="hms-auth-media" style="background-image: linear-gradient(rgba(17,24,39,.55), rgba(17,24,39,.76)), url('img/Doctor.jpg');">
            <p class="eyebrow text-white">Doctor Application</p>
            <h2 class="fw-bold">Apply to join the hospital clinical team.</h2>
            <p class="mb-0">After submission, admin must approve the application before login is allowed.</p>
        </div>
        <div class="hms-auth-form">
            <h4 class="fw-bold mb-1">Apply as Doctor</h4>
            <p class="text-muted mb-4">Use accurate details so the admin can review your profile.</p>

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
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" autocomplete="off" value="<?php echo e($old['phone']); ?>" required>
                    </div>
                </div>

                <label>Country</label>
                <select name="country" class="form-select" required>
                    <option value="">Select Country</option>
                    <option value="Bangladesh" <?php echo selected_option($old['country'], 'Bangladesh'); ?>>Bangladesh</option>
                    <option value="India" <?php echo selected_option($old['country'], 'India'); ?>>India</option>
                    <option value="Pakistan" <?php echo selected_option($old['country'], 'Pakistan'); ?>>Pakistan</option>
                    <option value="USA" <?php echo selected_option($old['country'], 'USA'); ?>>USA</option>
                </select>

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

                <button type="submit" name="apply" class="btn btn-primary w-100 mt-3"><i class="fas fa-paper-plane me-1"></i>Submit application</button>
                <p class="mt-3 mb-0">Already approved? <a href="doctorlogin.php">Login as doctor</a></p>
            </form>
        </div>
    </section>
</main>
</body>
</html>
