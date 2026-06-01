<?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>
<div class="list-group bg-info hms-sidebar">
    <a href="index.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i>Dashboard</a>
    <a href="profile.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>"><i class="fas fa-user-shield"></i>Profile</a>
    <a href="admin.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'admin.php' ? 'active' : ''; ?>"><i class="fas fa-users-cog"></i>Administrators</a>
    <a href="doctor.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'doctor.php' ? 'active' : ''; ?>"><i class="fas fa-user-md"></i>Doctors</a>
    <a href="patient.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'patient.php' ? 'active' : ''; ?>"><i class="fas fa-procedures"></i>Patients</a>
    <a href="appointment.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'appointment.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i>Appointments</a>
    <a href="job_request.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'job_request.php' ? 'active' : ''; ?>"><i class="fas fa-clipboard-list"></i>Job Requests</a>
    <a href="report.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'report.php' ? 'active' : ''; ?>"><i class="fas fa-flag"></i>Reports</a>
    <a href="income.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'income.php' ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i>Billing</a>
</div>
