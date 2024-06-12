<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <link rel="stylesheet" type="text/css"href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

        <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <link rel="stylesheet" type="text/css"href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

        <style>
        body {
            padding-top: 0px;
        }

        .navbar {
            background-color: #17a2b8; 
            color: white;
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

        

    </style>
        
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark">
        <h5 class="navbar-brand">Hospital Management System</h5>

        <div class="mr-auto"></div>

        <ul class="navbar-nav">
            <?php
            if(isset($_SESSION['admin'])){
                $user = $_SESSION['admin'];
                echo'
                <li class="nav-item"><a href="#" class="nav-link">'.$user.'</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">LogOut</a></li>
                ';
            }else if(isset($_SESSION['doctor'])){
                $user = $_SESSION['doctor'];
                echo'
                <li class="nav-item"><a href="#" class="nav-link">'.$user.'</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">LogOut</a></li>
                ';

            }else if(isset($_SESSION['patient'])){
                $user = $_SESSION['patient'];
                echo'
                <li class="nav-item"><a href="#" class="nav-link">'.$user.'</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">LogOut</a></li>
                ';

            }else {
                echo'
                <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="adminLogin.php" class="nav-link">Admin</a></li>
                <li class="nav-item"><a href="doctorlogin.php" class="nav-link">Doctor</a></li>
                <li class="nav-item"><a href="patientlogin.php" class="nav-link">Patient</a></li>
                ';
            }
            ?>
        </ul>
    </nav>

    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>




    </body>
</html>
