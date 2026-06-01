
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
	<title>Book Appointment</title>
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
		<h5 class="text-center my-2">Book Appointment</h5>
		<?php
			
			
				$pat = $_SESSION['patient'];
				$row = db_select_one("SELECT * FROM patient WHERE username = ? LIMIT 1", "s", $pat);
				
				$firstname = $row['firstname'];
				$surname = $row['surname'];
				$gender = $row['gender'];
				$phone = $row['phone'];
				$patient_username = $row['username'];
				$doctors = mysqli_query($connect, "SELECT username, firstname, surname FROM doctors WHERE status='Approved' ORDER BY firstname ASC");

				
				
					
					

					if(isset($_POST['book'])){
						
						$date = trim($_POST['date'] ?? '');
						$doctor_username = trim($_POST['doctor'] ?? '');
						$sym = trim($_POST['sym'] ?? '');

						if(empty($date)){
							echo "<script>alert('Select appointment date')</script>";
						}else if(empty($doctor_username)){
							echo "<script>alert('Select a doctor')</script>";
						}else if(empty($sym)){
							echo "<script>alert('Enter symptoms')</script>";
						}else if($date < date('Y-m-d')){
							echo "<script>alert('Appointment date cannot be in the past')</script>";
						} else {
							$doctor = db_select_one("SELECT username FROM doctors WHERE username = ? AND status='Approved' LIMIT 1", "s", $doctor_username);
							if (!$doctor) {
								echo "<script>alert('Selected doctor is not available')</script>";
							} else {
								$res = db_execute("INSERT INTO appointment(patient_username, doctor_username, firstname, surname, gender, phone, appointment_date, symptoms, status, date_booked) VALUES(?,?,?,?,?,?,?,?,'Pending',NOW())", "ssssssss", $patient_username, $doctor_username, $firstname, $surname, $gender, $phone, $date, $sym);

								if($res){
									echo "<script>alert('You have booked an appointment')</script>";
								}
							}
						}
					}
				 
				
			
			
		?>
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6 card ">
					<form method="post">
						<label>Appointment Date</label>
						<input type="date" name="date" class="form-control">

						<label>Doctor</label>
						<select name="doctor" class="form-control">
							<option value="">Select Doctor</option>
							<?php while($doctor = mysqli_fetch_array($doctors)){ ?>
								<option value="<?php echo e($doctor['username']); ?>">
									Dr. <?php echo e($doctor['firstname'] . ' ' . $doctor['surname']); ?>
								</option>
							<?php } ?>
						</select>

						<label>Symptoms</label>
						<input type="text" name="sym" class="form-control" autocomplete="off" placeholder="Enter Symptoms">
						<input type="submit" name="book" class="btn btn-info my-2" value="Book Appointment">
					</form>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
	</div>
</body>
</html>
