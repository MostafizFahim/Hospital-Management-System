<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorId = (int) ($_POST['doctor_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $doctor = db_select_one("SELECT id FROM doctors WHERE id = ? LIMIT 1", "i", $doctorId);

    if (!$doctor) {
        $error = "Doctor not found.";
    } elseif ($action === 'approve') {
        db_execute("UPDATE doctors SET status = 'Approved' WHERE id = ?", "i", $doctorId);
        $message = "Doctor approved.";
    } elseif ($action === 'reject') {
        db_execute("UPDATE doctors SET status = 'Rejected' WHERE id = ?", "i", $doctorId);
        $message = "Doctor rejected.";
    } elseif ($action === 'pending') {
        db_execute("UPDATE doctors SET status = 'Pending' WHERE id = ?", "i", $doctorId);
        $message = "Doctor moved to pending review.";
    } else {
        $error = "Invalid doctor action.";
    }
}

$statusFilter = $_GET['status'] ?? 'Approved';
$allowedStatuses = ['All', 'Pending', 'Approved', 'Rejected'];
if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = 'Approved';
}

$search = trim($_GET['q'] ?? '');
$where = [];
$types = '';
$params = [];

if ($statusFilter !== 'All') {
    $where[] = "status = ?";
    $types .= 's';
    $params[] = $statusFilter;
}

if ($search !== '') {
    $where[] = "(firstname LIKE ? OR surname LIKE ? OR username LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $like = '%' . $search . '%';
    $types .= 'sssss';
    array_push($params, $like, $like, $like, $like, $like);
}

$sql = "SELECT * FROM doctors";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY data_reg DESC, id DESC";

if ($params) {
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $doctors = mysqli_stmt_get_result($stmt);
} else {
    $doctors = mysqli_query($connect, $sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Doctors</title>
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
            <h4>Doctors</h4>
        </div>
        <a href="job_request.php" class="btn btn-outline-primary"><i class="fas fa-clipboard-list me-1"></i>Pending requests</a>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <div class="hms-card mb-3">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label>Search</label>
                <input type="text" name="q" class="form-control" value="<?php echo e($search); ?>" placeholder="Name, username, email, phone">
            </div>
            <div class="col-md-4">
                <label>Status</label>
                <select name="status" class="form-select">
                    <?php foreach ($allowedStatuses as $status) { ?>
                        <option value="<?php echo e($status); ?>" <?php echo $statusFilter === $status ? 'selected' : ''; ?>><?php echo e($status); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" type="submit"><i class="fas fa-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doctor</th>
                        <th>Contact</th>
                        <th>Gender</th>
                        <th>Country</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($doctors) < 1) { ?>
                    <tr><td colspan="9" class="hms-empty">No doctors found.</td></tr>
                <?php } ?>
                <?php while ($row = mysqli_fetch_array($doctors)) { ?>
                    <tr>
                        <td><?php echo e($row['id']); ?></td>
                        <td>
                            <strong>Dr. <?php echo e($row['firstname'] . ' ' . $row['surname']); ?></strong><br>
                            <span class="text-muted small"><?php echo e($row['username']); ?></span>
                        </td>
                        <td>
                            <?php echo e($row['email']); ?><br>
                            <span class="text-muted small"><?php echo e($row['phone']); ?></span>
                        </td>
                        <td><?php echo e($row['gender']); ?></td>
                        <td><?php echo e($row['country']); ?></td>
                        <td><?php echo hms_money($row['salary']); ?></td>
                        <td><span class="status-pill status-<?php echo hms_status_class($row['status']); ?>"><?php echo e($row['status']); ?></span></td>
                        <td><?php echo e($row['data_reg']); ?></td>
                        <td>
                            <div class="hms-actions">
                                <a href="edit.php?id=<?php echo e($row['id']); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Edit</a>
                                <form method="post" class="hms-actions">
                                    <input type="hidden" name="doctor_id" value="<?php echo e($row['id']); ?>">
                                    <?php if ($row['status'] !== 'Approved') { ?>
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                    <?php } ?>
                                    <?php if ($row['status'] !== 'Rejected') { ?>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger">Reject</button>
                                    <?php } ?>
                                    <?php if ($row['status'] !== 'Pending') { ?>
                                        <button type="submit" name="action" value="pending" class="btn btn-sm btn-outline-secondary">Review</button>
                                    <?php } ?>
                                </form>
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
