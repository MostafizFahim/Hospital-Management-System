<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$script = $_SERVER['SCRIPT_NAME'];
$isAdminPage = strpos($script, '/admin/') !== false;
$isDoctorPage = strpos($script, '/doctor/') !== false;
$isPatientPage = strpos($script, '/patient/') !== false;
$rootPrefix = ($isAdminPage || $isDoctorPage || $isPatientPage) ? '../' : '';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

<style>
    :root {
        --hms-primary: #0f766e;
        --hms-primary-dark: #115e59;
        --hms-accent: #2563eb;
        --hms-bg: #f4f7fb;
        --hms-surface: #ffffff;
        --hms-border: #dbe4ef;
        --hms-text: #172033;
        --hms-muted: #64748b;
        --hms-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    body {
        padding-top: 0;
        background: var(--hms-bg);
        color: var(--hms-text);
        font-family: "Segoe UI", Arial, sans-serif;
    }

    .navbar {
        background: linear-gradient(90deg, var(--hms-primary-dark), var(--hms-primary));
        color: white;
        min-height: 62px;
        box-shadow: 0 8px 20px rgba(15, 118, 110, 0.18);
    }

    .navbar h5 {
        margin-bottom: 0;
    }

    .navbar-nav .nav-item {
        margin-right: 10px;
    }

    .navbar-nav .nav-link {
        color: white !important;
    }

    .navbar-nav .nav-link:hover {
        color: #f8f9fa !important;
    }

    .hms-sidebar {
        position: fixed;
        top: 62px;
        left: 0;
        width: 210px;
        min-height: calc(100vh - 62px);
        z-index: 10;
        background: #0f172a !important;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        padding: 0.75rem;
    }

    body > .col-md-10,
    body > .col-md-6 {
        margin-left: 225px;
        max-width: calc(100% - 240px);
        padding: 1.5rem;
    }

    .hms-sidebar .list-group-item {
        background: transparent !important;
        border: 0;
        border-radius: 8px;
        margin-bottom: 0.25rem;
        text-align: left !important;
        color: #dbeafe !important;
        font-weight: 600;
    }

    .hms-sidebar .list-group-item:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }

    .page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .page-heading h4,
    .page-heading h5 {
        margin: 0;
        font-weight: 750;
        color: var(--hms-text);
    }

    .eyebrow {
        margin: 0 0 0.25rem;
        color: var(--hms-muted);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .hms-card,
    .card,
    .jumbotron {
        background: var(--hms-surface) !important;
        border: 1px solid var(--hms-border) !important;
        border-radius: 10px !important;
        box-shadow: var(--hms-shadow);
        padding: 1rem;
    }

    .table {
        background: var(--hms-surface);
        border-color: var(--hms-border);
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table thead th,
    .table tr:first-child td {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
    }

    .form-control,
    .form-select {
        border-color: var(--hms-border);
        border-radius: 8px;
        min-height: 42px;
    }

    label {
        margin-top: 0.75rem;
        margin-bottom: 0.25rem;
        color: #334155;
        font-weight: 650;
    }

    .btn {
        border-radius: 8px;
        font-weight: 650;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.25rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 700;
        background: #e2e8f0;
        color: #334155;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-approved,
    .status-discharged {
        background: #dcfce7;
        color: #166534;
    }

    .status-rejected,
    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .hms-sidebar {
            position: static;
            width: 100%;
            min-height: auto;
        }

        body > .col-md-10,
        body > .col-md-6 {
            margin-left: 0;
            max-width: 100%;
        }

        .page-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark px-3">
    <h5 class="navbar-brand">Hospital Management System</h5>
    <div class="ms-auto"></div>
    <ul class="navbar-nav">
        <?php
        if (isset($_SESSION['admin'])) {
            $user = htmlspecialchars($_SESSION['admin']);
            $logout = $isAdminPage ? 'logout.php' : 'admin/logout.php';
            echo '
                <li class="nav-item"><a href="#" class="nav-link">'.$user.'</a></li>
                <li class="nav-item"><a href="'.$logout.'" class="nav-link">Logout</a></li>
            ';
        } elseif (isset($_SESSION['doctor'])) {
            $user = htmlspecialchars($_SESSION['doctor']);
            $logout = $isDoctorPage ? 'logout.php' : 'doctor/logout.php';
            echo '
                <li class="nav-item"><a href="#" class="nav-link">'.$user.'</a></li>
                <li class="nav-item"><a href="'.$logout.'" class="nav-link">Logout</a></li>
            ';
        } elseif (isset($_SESSION['patient'])) {
            $user = htmlspecialchars($_SESSION['patient']);
            $logout = $isPatientPage ? 'logout.php' : 'patient/logout.php';
            echo '
                <li class="nav-item"><a href="#" class="nav-link">'.$user.'</a></li>
                <li class="nav-item"><a href="'.$logout.'" class="nav-link">Logout</a></li>
            ';
        } else {
            echo '
                <li class="nav-item"><a href="'.$rootPrefix.'index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="'.$rootPrefix.'adminLogin.php" class="nav-link">Admin</a></li>
                <li class="nav-item"><a href="'.$rootPrefix.'doctorlogin.php" class="nav-link">Doctor</a></li>
                <li class="nav-item"><a href="'.$rootPrefix.'patientlogin.php" class="nav-link">Patient</a></li>
            ';
        }
        ?>
    </ul>
</nav>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
