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
	<title></title>
</head>
<body>
	<?php

	include("../include/header.php");
	include("../include/connection.php");
	?>

	<div class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2" style="margin-left:-30px;">
                    <?php
                    include("sidenav.php");
                    ?>
                </div>
                
            </div>

        </div>

    </div>
    <div class="col-md-10">
                    <h5 class="text-center my-3">Total Income</h5>
                    <?php
                    $query =  "SELECT * FROM income" ;
                    $res = mysqli_query($connect,$query);
                    $output = "";

                    echo "<table class='table table-bordered'>
                            <tr>
                                <td>ID</td>
                                <td>Doctor</td>
                                <td>Patient</td>
                                <td>Date of discharge</td>
                                <td>Amount Paid</td>
                                
                            </tr>";

                    if (mysqli_num_rows($res) < 1) {
                        echo "<tr>
                                <td class='text-center' colspan='10'>No Patient discharge Yet</td>
                            </tr>";
                    }

                    while ($row = mysqli_fetch_array($res)) {
                        echo "<tr>
                                <td>".e($row['id'])."</td>
                                <td>".e($row['doctor'])."</td>
                                <td>".e($row['patient'])."</td>
                                <td>".e($row['date_discharge'])."</td>
                                <td>".e($row['amount_paid'])."</td>
                                
                                
                            </tr>";
                    }

                    echo "</table>";
                    ?>
                </div>


</body>
</html>
