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
            <th>Gender</th>
            <th>Phone</th>
            <th>Country</th>
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
            <td>" . e($row['gender']) . "</td>
            <td>" . e($row['phone']) . "</td>
            <td>" . e($row['country']) . "</td>
            <td>" . e($row['data_reg']) . "</td>
            <td>
                <div class='col-md-12'>
                    <div class='row'>
                        <div class='col-md-6'>
                            <button id='" . $id . "' class='btn btn-success approve'>Approve</button>
                        </div>
                        <div class='col-md-6'>
                            <button id='" . $id . "' class='btn btn-danger reject'>Reject</button>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    ";
}

$output .= "</table>";
echo $output;
?>
