<?php

session_start();

if(isset($_SERVER['patient'])){

	unset($_SESSION['patient']);

	header("Location: ../index.php");

}
	
?>