<?php
 include("include/connection.php");

 if(isset($_POST['create'])){

 	$fname = trim($_POST['fname'] ?? '');
 	$sname = trim($_POST['sname'] ?? '');
 	$uname = trim($_POST['uname'] ?? '');
 	$email = trim($_POST['email'] ?? '');
 	$phone = trim($_POST['phone'] ?? '');
 	$gender = trim($_POST['gender'] ?? '');
 	$country = trim($_POST['country'] ?? '');
 	$password = $_POST['pass'] ?? '';
 	$con_pass = $_POST['con_pass'] ?? '';

 	$error = array();

 	if(empty($fname)){
 		$error['ac'] = "Enter Firstname";
 	}else if (empty($sname)) {
 		$error['ac'] = "Enter Surname"; 	
 	}else if(empty($uname)){
 		$error['ac'] = "Enter Username";
 	}else if(empty($email)){
 		$error['ac'] = "Enter Email";
 	}else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
 		$error['ac'] = "Enter a valid Email";
 	}else if(empty($phone)){
 		$error['ac'] = "Enter Phone No";
 	}else if($gender == ""){
 		$error['ac'] = "Select Your Gender";
 	}else if($country == ""){
 		$error['ac'] = "Select Your Country";
 	}else if(empty($password)){
 		$error['ac'] = "Enter Password";
 	}else if($con_pass != $password){
 		$error['ac'] = "Both password do not match";
 	}

 	if(count($error)==0){
		$exists = db_select_one("SELECT id FROM patient WHERE username = ? OR email = ? LIMIT 1", "ss", $uname, $email);
		if ($exists) {
			$error['ac'] = "Username or email already exists";
		}
	}

 	if(count($error)==0){
		$password_hash = hash_user_password($password);
 		$res = db_execute("INSERT INTO patient(firstname, surname, username, email, phone, gender, country, password, date_reg, profile) VALUES(?,?,?,?,?,?,?,?,NOW(),'patient.jpg')", "ssssssss", $fname, $sname, $uname, $email, $phone, $gender, $country, $password_hash);

 		if($res){
 			header("Location:patientlogin.php");
			exit();
 		}else{
 			echo "<script>alert('failed')</script>";
 		}
 	}

 }

 if (isset($error['ac'])) {
 	$show = "<h5 class='text-center alert alert-danger'>" . e($error['ac']) . "</h5>";
 } else {
 	$show = "";
 }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Create Account</title>
</head>
<body style="background-image: url(img/back.jpg); background-repeat: no-repeat; background-size: cover;">

	<?php

	include("include/header.php");
	?>
	<div class="container-fluid">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6 my-2 card">
					<h5 class="text-center text-info my-2">Create Account</h5>
					<?php echo $show; ?>
					<form method="post">
						<div class="form-group">
							<label>Firstname</label>
							<input type="text" name="fname" class="form-control" autocomplete="off" placeholder="Enter Firstname">
						</div>

						<div class="form-group">
							<label>Surname</label>
							<input type="text" name="sname" class="form-control" autocomplete="off" placeholder="Enter Surname">
						</div>
						<div class="form-group">
							<label>Username</label>
							<input type="text" name="uname" class="form-control" autocomplete="off" placeholder="Enter Username">
						</div>
						<div class="form-group">
							<label>Email</label>
							<input type="text" name="email" class="form-control" autocomplete="off" placeholder="Enter Email">
						</div>
						<div class="form-group">
							<label>Phone</label>
							<input type="number" name="phone" class="form-control" autocomplete="off" placeholder="Enter Phone No">
						</div>
						<div class="form-group">
							<label>Gender</label>
							<select name="gender" class="form-control">
								<option value="">Select Your Gender</option>
								<option value="Male">Male</option>
								<option value="Female">Female</option>
							</select>
						</div>
						<div class="form-group">
							<label>Country</label>
							<select name="country" class="form-control">
								<option value="">Select Your Country</option>
								<option value="USA">USA</option>
								<option value="Bangladesh">Bangladesh</option>
							</select>
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="pass" class="form-control" autocomplete="off"placeholder="Enter Password">
							
						</div>

						<div class="form-group">
							<label>Confirm Password</label>
							<input type="password" name="con_pass" class="form-control" autocomplete="off"placeholder="Enter Confirm Password">
							
						</div>
						<input type="submit" name="create" value="Create Account" class="btn btn-info">
						<p>I already have an account <a href="patientlogin.php">Click Here</a></p>
					</form>
					
				</div>
				<div class="col-md-3"></div>

			</div>
			
		</div>
	</div>

</body>
</html>
