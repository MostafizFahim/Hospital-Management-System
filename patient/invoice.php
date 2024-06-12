<?php
session_start();
?>
<<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Invoice</title>
</head>
<body>
    <?php
    include("../include/header.php");
    include("../include/connection.php"); 
    ?>
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2" style="margin-Left: -30px;">
                    <?php
                    include("sidenav.php");
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-10">
        <h5 class="text-center my-2">My Invoice</h5>
        <?php
            
            
                $pat = $_SESSION['patient'];
                $query = "SELECT * FROM patient WHERE username = '$pat'";

                $res = mysqli_query($connect,$query);
                $row = mysqli_fetch_array($res);

                $fname = $row['firstname'];
                $querys = mysqli_query($connect,"SELECT * FROM income WHERE patient ='$fname'");
                $output = "";
                echo "<table class='table table-bordered'>
                            <tr>
                                <td>ID</td>
                                <td>Doctor</td>
                                <td>Patient</td>
                                <td>Date of discharge</td>
                                <td>Ammount Paid</td>
                                <td>Description</td>
                                <td>Action</td>
                                
                            </tr>";

                    if (mysqli_num_rows($res) < 1) {
                        echo "<tr>
                                <td class='text-center' colspan='10'>No Invoice Yet</td>
                            </tr>";
                    }

                    while ($row = mysqli_fetch_array($res)) {
                        echo "<tr>
                                <td>".$row['id']."</td>
                                <td>".$row['doctor']."</td>
                                <td>".$row['patient']."</td>
                                <td>".$row['date_discharge']."</td>
                                <td>".$row['amount_paid']."</td>
                                <td>".$row['description']."</td>
                                <td>
                        <a href='check.php?id=".$row['id']."'>
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
