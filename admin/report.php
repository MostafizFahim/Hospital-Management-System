<?php
session_start();
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
                    <h5 class="text-center my-3">Total Report</h5>
                    <?php
                    $query =  "SELECT * FROM report" ;
                    $res = mysqli_query($connect,$query);
                    $output = "";

                    echo "<table class='table table-bordered'>
                            <tr>
                                <td>ID</td>
                                <td>Title</td>
                                <td>Message</td>
                                <td>Username</td>
                                <td>Data Send</td>
                                
                            </tr>";

                    if (mysqli_num_rows($res) < 1) {
                        echo "<tr>
                                <td class='text-center' colspan='10'>No Report Yet</td>
                            </tr>";
                    }

                    while ($row = mysqli_fetch_array($res)) {
                        echo "<tr>
                                <td>".$row['id']."</td>
                                <td>".$row['title']."</td>
                                <td>".$row['message']."</td>
                                <td>".$row['username']."</td>
                                <td>".$row['date_send']."</td>
                                
                                
                            </tr>";
                    }

                    echo "</table>";
                    ?>
                </div>

</body>
</html>