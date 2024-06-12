<?php
session_start();


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
                                <td>".$row['id']."</td>
                                <td>".$row['firstname']."</td>
                                <td>".$row['surname']."</td>
                                <td>".$row['username']."</td>
                                <td>".$row['email']."</td>
                                <td>".$row['phone']."</td>
                                <td>".$row['gender']."</td>
                                <td>".$row['country']."</td>
                                <td>".$row['date_reg']."</td>
                                <td>
                                    <a href='view.php?id=".$row['id']."'>
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
