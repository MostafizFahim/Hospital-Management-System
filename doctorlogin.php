<?php


include("include/connection.php");

if (isset($_POST['login'])) {
	$uname = $_POST['uname'];
	$password = $_POST['pass'];

	$error = array();

	$q = "SELECT * FROM doctors WHERE username= '$uname' AND password='$password'";
	$qq = mysqli_query($connect,$q);

	$row = mysqli_fetch_array($qq);

	if (empty($uname)) {
		$error['login'] = "Enter Username";
	}elseif (empty($password)) {
		$error['login'] = "Enter Password";
	}elseif ($row['status']=="Pendding") {
		$error['login'] = "Please Wait for the Admin to Confirm";
	}elseif ($row['Pendding']=="Rejected") {
		$error['login'] = "Try again later";
	}

	if(count($error)==0){
		$query = "SELECT * FROM doctors WHERE username='$uname' AND password='$password'";

		$res = mysqli_query($connect,$query);

		if(mysqli_num_rows($res)){
			echo "<script>alert('done')</script>";
			$_SESSION['doctor']=$uname;
			header("Location:doctor/index.php");
		}else{
			echo "<script>alert('Invalid Account')</script>";
		}
	}
}

if (isset($error['login'])) {
	$l = $error['login'];
	$show = "<h5 class='text-center alert alert-danger'>$l</h5>";
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
                            <level>Username</level>
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
                        <p>I dont have an account <a href="apply.php">Apply Now!!</a></p>
                        


                    </form>

                    </div>
                    <div class="col-md-3"></div>

                </div>
            </div>

        </div>


</body>
</html>