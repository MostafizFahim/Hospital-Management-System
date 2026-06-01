<?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>
<div class="list-group bg-info hms-sidebar">
    <a href="index.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i>Dashboard</a>
    <a href="profile.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>"><i class="fas fa-user-injured"></i>Profile</a>
    <a href="appointment.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'appointment.php' ? 'active' : ''; ?>"><i class="fas fa-calendar-plus"></i>Book Appointment</a>
    <a href="invoice.php" class="list-group-item list-group-item-action <?php echo $currentPage === 'invoice.php' || $currentPage === 'view.php' ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i>Invoices</a>
</div>
