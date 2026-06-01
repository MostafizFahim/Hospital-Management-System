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

    $appointments = mysqli_query($connect, "SELECT * FROM appointment ORDER BY date_booked DESC");
    ?>

    <main class="col-md-10">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Hospital Operations</p>
                <h4>Appointments</h4>
            </div>
        </div>

        <div class="hms-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Symptoms</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($appointments) < 1) { ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No appointments found</td>
                            </tr>
                        <?php } ?>
                        <?php while ($row = mysqli_fetch_array($appointments)) { ?>
                            <tr>
                                <td><?php echo e($row['id']); ?></td>
                                <td><?php echo e($row['firstname'] . ' ' . $row['surname']); ?></td>
                                <td><?php echo e($row['doctor_username'] ?: 'Unassigned'); ?></td>
                                <td><?php echo e($row['phone']); ?></td>
                                <td><?php echo e($row['appointment_date']); ?></td>
                                <td><?php echo e($row['symptoms']); ?></td>
                                <td><span class="status-pill status-<?php echo strtolower(e($row['status'])); ?>"><?php echo e($row['status']); ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
