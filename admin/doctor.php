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
	<title>Total Doctors</title>
</head>
<body>

	<?php

		include("../include/header.php");

		include("../include/connection.php");

	?>

	<div class="container-fluid">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-2" style="margin-left: -30px;">
					<?php
					include("sidenav.php");

					?>
					
				</div>
				

			</div>
		</div>

	</div>
	<div class="col-md-10">
					<h5 class="text-center">Total Doctors</h5>

					<?php
						$query = "SELECT * FROM doctors WHERE status='Approved' ORDER BY data_reg ASC";
						$res = mysqli_query($connect,$query);

						$output = "";

$output .="
        <table class='table table-bordered'>
            <tr>
                <th>ID</th>
                <th>Firstname</th>
                <th>Surname</th>
                <th>Username</th>
                
                <th>Gender</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Salary</th>
                <th>Data Registered</th>
                <th>Action</th>
            </tr>
        
    ";

if (mysqli_num_rows($res) < 1){
    $output .="
    <tr>
    <td colspan='10' class='text-center'> No Job Request Yet</td>
    </tr>
    ";
}

 while($row = mysqli_fetch_array($res)){
    $output .="
        <tr>
        <td>".e($row['id'])."</td>
        <td>".e($row['firstname'])."</td>
        <td>".e($row['surname'])."</td>
        <td>".e($row['username'])."</td>
        
        <td>".e($row['gender'])."</td>
        <td>".e($row['phone'])."</td>
        <td>".e($row['country'])."</td>
        <td>".e($row['salary'])."</td>
        <td>".e($row['data_reg'])."</td>
        <td>
            <a href='edit.php?id=".e($row['id'])."'>

           <button class='btn btn-info'>Edit</button>

           </a>
        </td>
        
    ";
 }

$output .= "
    </tr>
    </table>
";
echo $output;




					 ?>
					
				</div>
	


</body>
</html>
