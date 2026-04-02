<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){ echo "Access Denied!"; exit(); }
include("../../config/database.php");

if(!isset($_GET['id'])){
    echo "Class ID missing!";
    exit();
}

$class_id = $_GET['id'];

// Delete class
mysqli_query($conn, "DELETE FROM classes WHERE class_id='$class_id'");

header("Location: manage_classes.php");
exit();
?>