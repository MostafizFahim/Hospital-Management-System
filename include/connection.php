<?php
$connect = mysqli_connect("localhost","root","","hmsDB");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
