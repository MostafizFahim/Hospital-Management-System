<?php
session_start();
include("../include/auth.php");
require_login("doctor", "../doctorlogin.php");


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Total Patient</title>
</head>
<body>
	<?php

include("../include/header.php");
include("../include/connection.php");
?>
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2">
                    <?php
                    include("sidenav.php");
                    ?>
                </div>
                
            </div>

        </div>

    </div>
    <div class="col-md-10">
                    <h5 class="text-center my-3">Total Patient</h5>
                    <?php
                    $query =  "SELECT * FROM patient" ;
                    $res = mysqli_query($connect,$query);
                    $output = "";

                    echo "<table class='table table-bordered'>
                            <tr>
                                <td>ID</td>
                                <td>Firstname</td>
                                <td>Surename</td>
                                <td>Username</td>
                                <td>Email</td>
                                <td>Phone</td>
                                <td>Gender</td>
                                <td>Country</td>
                                <td>Date Regester</td>
                                <td>Action</td>
                            </tr>";

                    if (mysqli_num_rows($res) < 1) {
                        echo "<tr>
                                <td class='text-center' colspan='10'>No Patient Yet</td>
                            </tr>";
                    }

                    while ($row = mysqli_fetch_array($res)) {
                        echo "<tr>
                                <td>".e($row['id'])."</td>
                                <td>".e($row['firstname'])."</td>
                                <td>".e($row['surname'])."</td>
                                <td>".e($row['username'])."</td>
                                <td>".e($row['email'])."</td>
                                <td>".e($row['phone'])."</td>
                                <td>".e($row['gender'])."</td>
                                <td>".e($row['country'])."</td>
                                <td>".e($row['date_reg'])."</td>
                                <td>
                                    <a href='view.php?id=".e($row['id'])."'>
                                        <button class='btn btn-info'>View</button>
                                    </a>
                                </td>
                            </tr>";
                    }

                    echo "</table>";
                    ?>
                </div>
</body>
</html>
