<?php
session_start();
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportId = (int) ($_POST['report_id'] ?? 0);
    $report = db_select_one("SELECT id FROM report WHERE id = ? LIMIT 1", "i", $reportId);

    if (!$report) {
        $error = "Report not found.";
    } else {
        db_execute("DELETE FROM report WHERE id = ?", "i", $reportId);
        $message = "Report removed.";
    }
}

$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = mysqli_prepare($connect, "SELECT * FROM report WHERE title LIKE ? OR message LIKE ? OR username LIKE ? ORDER BY date_send DESC, id DESC");
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $reports = mysqli_stmt_get_result($stmt);
} else {
    $reports = mysqli_query($connect, "SELECT * FROM report ORDER BY date_send DESC, id DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports</title>
</head>
<body>
<?php
include("../include/header.php");
include("sidenav.php");
?>

<main class="col-md-10">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Patient Support</p>
            <h4>Reports</h4>
        </div>
    </div>

    <?php if ($message) { ?><div class="hms-alert hms-alert-success"><?php echo e($message); ?></div><?php } ?>
    <?php if ($error) { ?><div class="hms-alert hms-alert-danger"><?php echo e($error); ?></div><?php } ?>

    <div class="hms-card mb-3">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label>Search Reports</label>
                <input type="text" name="q" class="form-control" value="<?php echo e($search); ?>" placeholder="Title, message, username">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" type="submit"><i class="fas fa-search me-1"></i>Search</button>
            </div>
        </form>
    </div>

    <div class="hms-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Patient</th>
                        <th>Sent</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($reports) < 1) { ?>
                    <tr><td colspan="6" class="hms-empty">No reports found.</td></tr>
                <?php } ?>
                <?php while ($row = mysqli_fetch_array($reports)) { ?>
                    <tr>
                        <td><?php echo e($row['id']); ?></td>
                        <td><strong><?php echo e($row['title']); ?></strong></td>
                        <td><?php echo e($row['message']); ?></td>
                        <td><?php echo e($row['username']); ?></td>
                        <td><?php echo e($row['date_send']); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="report_id" value="<?php echo e($row['id']); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this handled report?');">
                                    <i class="fas fa-trash me-1"></i>Remove
                                </button>
                            </form>
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
