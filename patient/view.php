<?php
session_start();
include("../include/auth.php");
require_login("patient", "../patientlogin.php");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>View Invoice</title>
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
		<h5 class="text-center my-2">View Invoice</h5>
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<?php
						if(isset($_GET['id'])){
							$id = (int) $_GET['id'];

							$patient = $_SESSION['patient'];
							$query ="SELECT * FROM income WHERE id=$id AND patient_username='$patient'";

							$res = mysqli_query($connect,$query);

							$row = mysqli_fetch_array($res);
							$prescription = null;
							if ($row) {
								$prescription = db_select_one("SELECT * FROM prescriptions WHERE appointment_id = ? LIMIT 1", "i", $row['appointment_id']);
							}
						}
					?>
					<?php if (!empty($row)) { ?>
					<table class="table table-bordered">
						<tr>
							<td colspan="2" class="text-center">Invoice Details</td>
						</tr>
						<tr>

						<td>Doctor</td>
						<td><?php echo e($row['doctor']);?></td>
					</tr>
					<tr>

						<td>Patient</td>
						<td><?php echo e($row['patient']);?></td>
					</tr>
					<tr>

						<td>Date Discharge</td>
						<td><?php echo e($row['date_discharge']);?></td>
					</tr>
					<tr>

						<td>Amount paid</td>
						<td><?php echo e($row['amount_paid']);?></td>
					</tr>
					<tr>

						<td>Description</td>
						<td><?php echo e($row['description']);?></td>
					</tr>
					<?php if ($prescription) { ?>
					<tr>
						<td>Diagnosis</td>
						<td><?php echo e($prescription['diagnosis']);?></td>
					</tr>
					<tr>
						<td>Medicine</td>
						<td><?php echo e($prescription['medicine']);?></td>
					</tr>
					<tr>
						<td>Advice</td>
						<td><?php echo e($prescription['advice']);?></td>
					</tr>
					<tr>
						<td>Follow Up</td>
						<td><?php echo e($prescription['follow_up_date']);?></td>
					</tr>
					<?php } ?>
					</table>
					<?php } else { ?>
					<h5 class="text-center alert alert-danger">Invoice not found</h5>
					<?php } ?>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
	</div>
</body>
</html>
