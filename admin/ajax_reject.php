<?php
include("../include/auth.php");
require_login("admin", "../adminLogin.php");
include("../include/connection.php");

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    db_execute("UPDATE doctors SET status = 'Rejected' WHERE id = ?", "i", $id);
}
?>
