<?php
session_start();
include("include/connection.php");

if(isset($_POST['login'])){

	$uname = trim($_POST['uname'] ?? '');
	$pass = $_POST['pass'] ?? '';

	if(empty($uname)){
		echo "<script>alert('Enter Username')</script>";

	}else if(empty($pass)){
		echo "<script>alert('Enter Password')</script>";

	}else {
		$row = db_select_one("SELECT * FROM patient WHERE username = ? LIMIT 1", "s", $uname);

		if($row && password_matches($pass, $row['password'])){
			if (password_is_legacy($row['password'])) {
				$hash = hash_user_password($pass);
				db_execute("UPDATE patient SET password = ? WHERE id = ?", "si", $hash, $row['id']);
			}
			session_regenerate_id(true);
			$_SESSION['patient'] = $uname;
			header("Location: patient/index.php");
			exit();

		}else{
			echo "<script>alert('Invalid Account')</script>";
		}


	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Patient Login Page</title>
</head>
<body style="background-image: url(img/back.jpg); background-repeat: no-repeat; background-size: cover;">

	<?php

		include("include/header.php");
	?>

	<div class="container-fluid">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6 my-5 card my-5">
					<h5 class="text-center my-3">Patient Login</h5>

					<form method="post">
						<div class="form-group">
							<label>Username</label>
							<input type="text" name="uname" class="form-control" autocomplete="off" placeholder="Enter Username">
							
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="pass" class="form-control" autocomplete="off" placeholder="Enter Password">
							

						</div>
						<input type="submit" name="login" class=" btn btn-info my-3" value="Login">
						<p>I don't have an account <a href="account.php">Click Here</a></p>
					</form>
					
				</div>
				<div class="col-md-3"></div>
			</div>
			
		</div>
		
	</div>

</body>
</html>
