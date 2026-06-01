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
	<title>Total Appointment</title>
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
		<h5 class="text-center my-2">Total Appointment</h5>
		<?php

		$doctor = $_SESSION['doctor'];
		$query = "SELECT * FROM appointment WHERE status='Pending' AND (doctor_username='$doctor' OR doctor_username IS NULL OR doctor_username='') ORDER BY appointment_date ASC";

		$res = mysqli_query($connect,$query);

		$output = "";

		$output .="
		<table class='table table-bordered'>
		<tr>
			<td>ID</td>
			<td>Firstname</td>
			<td>Surname</td>
			<td>Gender</td>
			<td>Phone</td>
			<td>Doctor</td>
			<td>Appointment</td>
			<td>Symptoms</td>
			<td>Date Booked</td>
			<td>Action</td>


		</tr>

		";

		if(mysqli_num_rows($res) < 1){
			$output .="

				<tr>
					<td class='text-center' colspan='8'>No Appointment yet</td>
				</tr>

			";
		}

		while($row=mysqli_fetch_array($res)){

			$output .="

				<tr>

					<td>".e($row['id'])."</td>
					<td>".e($row['firstname'])."</td>
					<td>".e($row['surname'])."</td>
					<td>".e($row['gender'])."</td>
					<td>".e($row['phone'])."</td>
					<td>".e($row['doctor_username'] ?: 'Unassigned')."</td>
					<td>".e($row['appointment_date'])."</td>
					<td>".e($row['symptoms'])."</td>
					<td>".e($row['date_booked'])."</td>
					<td>
						<a href='discharge.php?id=".e($row['id'])."'>
						<button class='btn btn-info'>Check</button>

						</a>

					</td>

				</tr>
			";

		}
		$output .="</tr></table>";
		echo $output;
		?>
	</div>
</body>
</html>
