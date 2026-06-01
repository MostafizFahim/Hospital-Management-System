<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hospital Management System</title>
</head>
<body>
<?php
include("include/header.php");
include("include/connection.php");
?>

<main class="container py-5">
    <section class="row align-items-center g-4 mb-5">
        <div class="col-lg-6">
            <p class="eyebrow">Raw PHP Learning Project</p>
            <h1 class="fw-bold mb-3">Hospital operations for patients, doctors, billing, and admin.</h1>
            <p class="text-muted fs-5">Book appointments, approve visits, discharge patients with prescriptions, and track invoice payment status from one simple HMS workspace.</p>
            <div class="hms-actions mt-4">
                <a href="patientlogin.php" class="btn btn-primary"><i class="fas fa-user-injured me-1"></i>Patient login</a>
                <a href="doctorlogin.php" class="btn btn-outline-primary"><i class="fas fa-user-md me-1"></i>Doctor login</a>
                <a href="adminLogin.php" class="btn btn-outline-secondary"><i class="fas fa-user-shield me-1"></i>Admin login</a>
            </div>
        </div>
        <div class="col-lg-6">
            <img src="img/Patient.jpg" class="img-fluid rounded shadow" alt="Hospital care team">
        </div>
    </section>

    <section id="services" class="mb-5">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Services</p>
                <h4>What this HMS manages</h4>
            </div>
        </div>
        <div class="hms-stat-grid">
            <a href="account.php" class="hms-card hms-stat text-decoration-none text-reset">
                <div>
                    <p>Patients</p>
                    <h3>Register</h3>
                </div>
                <span class="hms-stat-icon"><i class="fas fa-user-plus"></i></span>
            </a>
            <a href="apply.php" class="hms-card hms-stat text-decoration-none text-reset">
                <div>
                    <p>Doctors</p>
                    <h3>Apply</h3>
                </div>
                <span class="hms-stat-icon"><i class="fas fa-user-md"></i></span>
            </a>
            <div class="hms-card hms-stat">
                <div>
                    <p>Appointments</p>
                    <h3>Approve</h3>
                </div>
                <span class="hms-stat-icon"><i class="fas fa-calendar-check"></i></span>
            </div>
            <div class="hms-card hms-stat">
                <div>
                    <p>Billing</p>
                    <h3>Track</h3>
                </div>
                <span class="hms-stat-icon"><i class="fas fa-file-invoice-dollar"></i></span>
            </div>
        </div>
    </section>

    <section id="about" class="hms-section-grid mb-5">
        <div class="hms-card">
            <p class="eyebrow">About</p>
            <h4 class="fw-bold">Built for learning real hospital workflows in raw PHP.</h4>
            <p class="text-muted mb-0">This project intentionally stays framework-free so the PHP, MySQL, sessions, validation, and role logic are visible. The current flow covers patient registration, doctor approval, appointment approval, doctor discharge, prescriptions, invoices, and payment status.</p>
        </div>
        <div class="hms-card">
            <p class="eyebrow">Next Scope</p>
            <h5 class="fw-bold">Real-life gaps to add later</h5>
            <p class="text-muted mb-0">Departments, appointment time slots, rooms, admissions, lab tests, pharmacy inventory, SMS/email notifications, and audit logs would make it closer to a production-grade HMS.</p>
        </div>
    </section>

    <section id="contact" class="hms-card">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Contact</p>
                <h4>Need help using the system?</h4>
            </div>
            <div class="hms-actions">
                <a href="account.php" class="btn btn-primary"><i class="fas fa-user-plus me-1"></i>Create patient account</a>
                <a href="apply.php" class="btn btn-outline-primary"><i class="fas fa-briefcase-medical me-1"></i>Apply as doctor</a>
            </div>
        </div>
        <p class="text-muted mb-0">For this learning version, patients can send support reports from the patient dashboard after login.</p>
    </section>
</main>
</body>
</html>
