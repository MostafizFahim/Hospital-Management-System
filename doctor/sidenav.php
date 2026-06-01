<?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>
<div class="list-group bg-info hms-sidebar">
    <a href="index.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i>Dashboard</a>
    <a href="profile.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>"><i class="fas fa-user-md"></i>Profile</a>
    <a href="patient.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'patient.php' ? 'active' : ''; ?>"><i class="fas fa-procedures"></i>Patients</a>
    <a href="appointment.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'appointment.php' || $currentPage === 'discharge.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i>Appointments</a>
</div>
