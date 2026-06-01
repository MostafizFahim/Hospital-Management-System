<?php
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$res = mysqli_query($connect, "SELECT * FROM doctors WHERE status = 'Pending' ORDER BY data_reg ASC");

$output = "";
$output .= "
    <table class='table table-bordered'>
        <tr>
            <th>ID</th>
            <th>Firstname</th>
            <th>Surname</th>
            <th>Username</th>
            <th>Contact</th>
            <th>Verification</th>
            <th>Date Registered</th>
            <th>Action</th>
        </tr>
";

if (mysqli_num_rows($res) < 1) {
    $output .= "<tr><td colspan='9' class='text-center'>No Job Request Yet</td></tr>";
}

while ($row = mysqli_fetch_assoc($res)) {
    $id = e($row['id']);
    $output .= "
        <tr>
            <td>" . e($row['id']) . "</td>
            <td>" . e($row['firstname']) . "</td>
            <td>" . e($row['surname']) . "</td>
            <td>" . e($row['username']) . "</td>
            <td>" . e($row['email']) . "<br><span class='text-muted small'>" . e($row['phone']) . "</span></td>
            <td>" . e($row['specialization'] ?: 'No specialization') . "<br><span class='text-muted small'>" . e($row['license_number'] ?: ($row['qualification'] ?: 'Verification details pending')) . "</span></td>
            <td>" . e($row['data_reg']) . "</td>
            <td>
                <a href='review_doctor.php?id=" . $id . "' class='btn btn-primary btn-sm'>Review</a>
            </td>
        </tr>
    ";
}

$output .= "</table>";
echo $output;
?>
