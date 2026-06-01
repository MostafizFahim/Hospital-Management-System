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
	<title>Check Patient Appointment</title>
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
	<h5 class="text-center my-2">Appointment</h5>

	<?php 

		$row = null;
		if(isset($_GET['id'])){
			$id = (int) $_GET['id'];

			$doc = $_SESSION['doctor'];
			$query = "SELECT * FROM appointment WHERE id=$id AND (doctor_username='$doc' OR doctor_username IS NULL OR doctor_username='')";

			$res = mysqli_query($connect,$query);

			$row = mysqli_fetch_array($res);
		}
	?>

	<div class="col-md-12">
		<?php if (empty($row)) { ?>
			<h5 class="text-center alert alert-danger">Appointment not found</h5>
		<?php } else { ?>
		<div class="row">
			<div class="col-md-6">
				<table class="table table-bordered">
					<tr>
						<td colspan="2" class="text-center">Appointment Details</td>
					</tr>
					<tr>
						
						<td>Firstname</td>
						<td><?php echo e($row['firstname']); ?></td>
					</tr>
					<tr>
						
						<td>Surname</td>
						<td><?php echo e($row['surname']); ?></td>
					</tr>
					<tr>
						
						<td>Gender</td>
						<td><?php echo e($row['gender']); ?></td>
					</tr>
					<tr>
						
						<td>Phone No</td>
						<td><?php echo e($row['phone']); ?></td>
					</tr>
					<tr>
						
						<td>Appointment Date</td>
						<td><?php echo e($row['appointment_date']); ?></td>
					</tr>
					<tr>
						
						<td>Symptoms</td>
						<td><?php echo e($row['symptoms']); ?></td>
					</tr>

				</table>
			</div>
			<div class="col-md-6">
				<h5 class="text-center my-2">Invoice</h5>

				<?php 
					if(isset($_POST['send'])){
						$fee = (float) ($_POST['fee'] ?? 0);
						$des = trim($_POST['des'] ?? '');
						$diagnosis = trim($_POST['diagnosis'] ?? '');
						$medicine = trim($_POST['medicine'] ?? '');
						$advice = trim($_POST['advice'] ?? '');
						$follow_up = trim($_POST['follow_up'] ?? '');
						$follow_up = $follow_up === '' ? null : $follow_up;

						if($fee <= 0){
							echo "<script>alert('Fee must be greater than 0')</script>";
						}else if(empty($des)){
							echo "<script>alert('Enter description')</script>";
						}else if(empty($diagnosis)){
							echo "<script>alert('Enter diagnosis')</script>";
						}else if(empty($medicine)){
							echo "<script>alert('Enter prescribed medicine')</script>";
						}else{

							$doc = $_SESSION['doctor'];
							$fname = $row['firstname'];
							$patient_username = $row['patient_username'];
							$query= "INSERT INTO income(doctor,patient_username,patient,appointment_id,date_discharge,amount_paid,description) VALUES(?,?,?,?,NOW(),?,?)";

							$res = db_execute($query, "sssids", $doc, $patient_username, $fname, $id, $fee, $des);

							if($res){
								db_execute("INSERT INTO prescriptions(appointment_id, doctor_username, patient_username, diagnosis, medicine, advice, follow_up_date, created_at) VALUES(?,?,?,?,?,?,?,NOW())", "issssss", $id, $doc, $patient_username, $diagnosis, $medicine, $advice, $follow_up);
								echo "<script>alert('You have sent Invoice')</script>";
								db_execute("UPDATE appointment SET status = 'Discharged', doctor_username = ? WHERE id = ?", "si", $doc, $id);
							}
						}
					}
				?>
				<form method="post">
					
					<label>Fee</label>
					<input type="number" name="fee" class="form-control" autocomplete="off" placeholder="Enter Patient fee">
					<label>Description</label>
					<input type="text" name="des" class="form-control" autocomplete="off" placeholder="Enter Description">
					<label>Diagnosis</label>
					<textarea name="diagnosis" class="form-control" rows="3" placeholder="Enter diagnosis"></textarea>
					<label>Medicine</label>
					<textarea name="medicine" class="form-control" rows="3" placeholder="Enter prescribed medicine"></textarea>
					<label>Advice</label>
					<textarea name="advice" class="form-control" rows="2" placeholder="Enter advice"></textarea>
					<label>Follow Up Date</label>
					<input type="date" name="follow_up" class="form-control">

					<input type="submit"name="send"class="btn btn-info my-2" value="Send">
				</form>

			</div>
		</div>
		<?php } ?>
	</div>
</div>


</body>
</html>
