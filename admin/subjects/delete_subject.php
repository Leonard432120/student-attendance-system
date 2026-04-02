<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){
    echo "Access Denied!";
    exit();
}

include("../../config/database.php");

if(!isset($_GET['id'])){
    echo "Subject ID missing!";
    exit();
}

$subject_id = intval($_GET['id']);

/* CHECK SUBJECT EXISTS */
$query = mysqli_query($conn, "
    SELECT * FROM subjects 
    WHERE subject_id='$subject_id'
");

$subject = mysqli_fetch_assoc($query);

if(!$subject){
    echo "Subject not found!";
    exit();
}

/* DELETE SUBJECT */
mysqli_query($conn, "
    DELETE FROM subjects 
    WHERE subject_id='$subject_id'
");

/* OPTIONAL: LOG ACTION */
mysqli_query($conn, "
    INSERT INTO activity_log(user_id, action)
    VALUES ('{$_SESSION['user_id']}', 'Deleted subject ID $subject_id')
");

header("Location: manage_subjects.php?deleted=1");
exit();
?>