<?php

session_start();

include("include/connection.php");

if (isset($_POST['login'])) {
	$uname = trim($_POST['uname'] ?? '');
	$password = $_POST['pass'] ?? '';

	$error = array();

	if (empty($uname)) {
		$error['login'] = "Enter Username";
	}elseif (empty($password)) {
		$error['login'] = "Enter Password";
	}

	if(count($error)==0){
		$row = db_select_one("SELECT * FROM doctors WHERE username = ? LIMIT 1", "s", $uname);

		if($row){
			$status = strtolower($row['status']);

			if (!password_matches($password, $row['password'])) {
				$error['login'] = "Invalid Account";
			}elseif ($status == "approved") {
				if (password_is_legacy($row['password'])) {
					$hash = hash_user_password($password);
					db_execute("UPDATE doctors SET password = ? WHERE id = ?", "si", $hash, $row['id']);
				}
				session_regenerate_id(true);
				$_SESSION['doctor']=$uname;
				header("Location:doctor/index.php");
				exit();
			}elseif ($status == "pending" || $status == "pendding") {
				$error['login'] = "Please Wait for the Admin to Confirm";
			}elseif ($status == "rejected") {
				$error['login'] = "Your application was rejected";
			}else{
				$error['login'] = "Your account is not active";
			}
		}else{
			$error['login'] = "Invalid Account";
		}
	}
}

if (isset($error['login'])) {
	$l = $error['login'];
	$show = "<h5 class='text-center alert alert-danger'>" . e($l) . "</h5>";
}else{
	$show = "";
}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Doctor Login Page</title>
</head>
<body style="background-image:url(img/back.jpg);background-size: cover;background-repeat: no-repeat;">

	<?php
	include("include/header.php");
	?>

	<div class="container-fluid">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6 card my-5">
                        <h2 class="text-center my-5">Doctors Login</h2>
                        <div>
                        	<?php
                        	echo $show;
                        	?>
                        </div>
                    <form method="post" class="my-2">
                        <div >
                            <?php
                            if(isset($error['admin'])){
                                $sh = $error['admin'];

                                $show = "<h4 class='alert alert-danger'>$sh</h4>";



                            }else{
                                $show = "";
                            }
                            echo $show;
                            ?>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="uname" class="form-control"
                            autocomplete="off" placeholder="Enter Username">

                        </div>
                        <br>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="pass"class="form-control"placeholder="Enter Password" >
                        </div>
                        <br>
                        <input type="submit" name="login" class="btn btn-success" value="Login" >
                        <p>I don't have an account <a href="apply.php">Apply Now!!</a></p>
                        


                    </form>

                    </div>
                    <div class="col-md-3"></div>

                </div>
            </div>

        </div>


</body>
</html>
