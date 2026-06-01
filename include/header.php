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
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

<style>
    :root {
        --hms-primary: #0f766e;
        --hms-primary-dark: #134e4a;
        --hms-accent: #1d4ed8;
        --hms-success: #15803d;
        --hms-warning: #b45309;
        --hms-danger: #b91c1c;
        --hms-bg: #f5f7fb;
        --hms-surface: #ffffff;
        --hms-soft: #eef6f6;
        --hms-border: #dde6f0;
        --hms-text: #111827;
        --hms-muted: #64748b;
        --hms-sidebar: #111827;
        --hms-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
    }

    * {
        letter-spacing: 0;
    }

    body {
        padding-top: 0;
        background: var(--hms-bg);
        color: var(--hms-text);
        font-family: "Segoe UI", Arial, sans-serif;
    }

    .navbar.hms-topbar {
        position: sticky;
        top: 0;
        z-index: 1030;
        background: var(--hms-surface);
        color: white;
        min-height: 64px;
        border-bottom: 1px solid var(--hms-border);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    }

    .hms-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        margin-bottom: 0;
        color: var(--hms-text) !important;
        font-weight: 800;
    }

    .hms-brand-mark {
        display: inline-grid;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: var(--hms-primary);
        color: #fff;
    }

    .navbar-nav .nav-item {
        margin-left: 0.35rem;
    }

    .navbar-nav .nav-link {
        color: #334155 !important;
        border-radius: 8px;
        font-weight: 650;
        padding: 0.55rem 0.75rem;
    }

    .navbar-nav .nav-link:hover {
        background: var(--hms-soft);
        color: var(--hms-primary-dark) !important;
    }

    .navbar-nav .nav-link.nav-action {
        background: var(--hms-primary);
        color: #fff !important;
    }

    .navbar-nav .nav-link.nav-action:hover {
        background: var(--hms-primary-dark);
        color: #fff !important;
    }

    .hms-user-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--hms-text) !important;
        background: #f8fafc;
        border: 1px solid var(--hms-border);
        border-radius: 999px !important;
    }

    .hms-sidebar {
        position: fixed;
        top: 64px;
        left: 0;
        width: 232px;
        height: calc(100vh - 64px);
        overflow-y: auto;
        z-index: 1020;
        background: var(--hms-sidebar) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1rem 0.8rem;
    }

    body > .col-md-10,
    body > .col-md-6 {
        margin-left: 248px;
        max-width: calc(100% - 264px);
        padding: 1.5rem 1.5rem 2.5rem;
    }

    .hms-sidebar .list-group-item {
        background: transparent !important;
        border: 0;
        border-radius: 8px;
        margin-bottom: 0.3rem;
        text-align: left !important;
        color: #cbd5e1 !important;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.72rem 0.85rem;
    }

    .hms-sidebar .list-group-item i {
        width: 18px;
        color: #7dd3fc;
    }

    .hms-sidebar .list-group-item:hover,
    .hms-sidebar .list-group-item.active {
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
        border-radius: 8px !important;
        box-shadow: var(--hms-shadow);
        padding: 1rem;
    }

    .hms-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .hms-stat {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        min-height: 120px;
    }

    .hms-stat h3 {
        margin: 0;
        font-size: 2rem;
        font-weight: 800;
    }

    .hms-stat p {
        margin: 0;
        color: var(--hms-muted);
        font-weight: 650;
    }

    .hms-stat-icon {
        display: inline-grid;
        place-items: center;
        min-width: 48px;
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: var(--hms-soft);
        color: var(--hms-primary-dark);
        font-size: 1.25rem;
    }

    .hms-section-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.8fr);
        gap: 1rem;
    }

    .hms-compact-grid {
        display: grid;
        grid-template-columns: minmax(280px, 420px) minmax(0, 1fr);
        gap: 1rem;
        align-items: start;
    }

    .hms-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .hms-profile-image {
        width: 100%;
        height: clamp(240px, 36vh, 340px);
        object-fit: contain;
        object-position: center;
        border-radius: 8px;
        border: 1px solid var(--hms-border);
        background: #f8fafc;
    }

    .table {
        background: var(--hms-surface);
        border-color: var(--hms-border);
        margin-bottom: 0;
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
    .status-discharged,
    .status-paid {
        background: #dcfce7;
        color: #166534;
    }

    .status-rejected,
    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-unpaid {
        background: #ffedd5;
        color: #9a3412;
    }

    .status-waived {
        background: #e0e7ff;
        color: #3730a3;
    }

    .hms-alert {
        border-radius: 8px;
        border: 1px solid transparent;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
        font-weight: 650;
    }

    .hms-alert-success {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .hms-alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .hms-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .hms-empty {
        padding: 2.25rem 1rem;
        color: var(--hms-muted);
        text-align: center;
        font-weight: 650;
    }

    .hms-auth-shell {
        min-height: calc(100vh - 64px);
        display: grid;
        place-items: center;
        padding: 2rem 1rem;
    }

    .hms-auth-card {
        width: min(100%, 920px);
        display: grid;
        grid-template-columns: 1fr 1fr;
        overflow: hidden;
        padding: 0;
    }

    .hms-auth-media {
        background: #102a43;
        color: #fff;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        min-height: 420px;
        background-size: cover;
        background-position: center;
    }

    .hms-auth-form {
        padding: 2rem;
    }

    @media (max-width: 768px) {
        .hms-sidebar {
            position: static;
            width: 100%;
            min-height: auto;
            border-right: 0;
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

        .hms-section-grid,
        .hms-compact-grid,
        .hms-form-grid,
        .hms-auth-card {
            grid-template-columns: 1fr;
        }

        .hms-auth-media {
            min-height: 240px;
        }
    }
</style>

<nav class="navbar navbar-expand-lg hms-topbar px-3">
    <a class="navbar-brand hms-brand" href="<?php echo $rootPrefix; ?>index.php">
        <span class="hms-brand-mark"><i class="fas fa-clinic-medical"></i></span>
        <span>Hospital Management System</span>
    </a>
    <div class="ms-auto"></div>
    <ul class="navbar-nav">
        <?php
        if (isset($_SESSION['admin'])) {
            $user = htmlspecialchars($_SESSION['admin']);
            $logout = $isAdminPage ? 'logout.php' : 'admin/logout.php';
            echo '
                <li class="nav-item"><span class="nav-link hms-user-chip"><i class="fas fa-user-shield"></i>'.$user.'</span></li>
                <li class="nav-item"><a href="'.$logout.'" class="nav-link"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
            ';
        } elseif (isset($_SESSION['doctor'])) {
            $user = htmlspecialchars($_SESSION['doctor']);
            $logout = $isDoctorPage ? 'logout.php' : 'doctor/logout.php';
            echo '
                <li class="nav-item"><span class="nav-link hms-user-chip"><i class="fas fa-user-md"></i>'.$user.'</span></li>
                <li class="nav-item"><a href="'.$logout.'" class="nav-link"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
            ';
        } elseif (isset($_SESSION['patient'])) {
            $user = htmlspecialchars($_SESSION['patient']);
            $logout = $isPatientPage ? 'logout.php' : 'patient/logout.php';
            echo '
                <li class="nav-item"><span class="nav-link hms-user-chip"><i class="fas fa-user-injured"></i>'.$user.'</span></li>
                <li class="nav-item"><a href="'.$logout.'" class="nav-link"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
            ';
        } else {
            echo '
                <li class="nav-item"><a href="'.$rootPrefix.'index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="'.$rootPrefix.'index.php#services" class="nav-link">Services</a></li>
                <li class="nav-item"><a href="'.$rootPrefix.'index.php#about" class="nav-link">About</a></li>
                <li class="nav-item"><a href="'.$rootPrefix.'index.php#contact" class="nav-link">Contact</a></li>
                <li class="nav-item"><a href="'.$rootPrefix.'account.php" class="nav-link nav-action">Register</a></li>
            ';
        }
        ?>
    </ul>
</nav>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
