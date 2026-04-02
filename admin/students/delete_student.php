<?php
include("../../includes/mailer.php");
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){ echo "Access Denied!"; exit(); }

include("../../config/database.php");

/* CHECK ID */
if(!isset($_GET['id'])){
    echo "Student ID missing!";
    exit();
}

$student_id = $_GET['id'];

/* FETCH STUDENT DATA FIRST (IMPORTANT) */
$query = mysqli_query($conn, "
    SELECT s.student_id, s.user_id, u.email, u.name
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.student_id = '$student_id'
");

$student = mysqli_fetch_assoc($query);

if(!$student){
    echo "Student not found!";
    exit();
}

$email = $student['email'];
$name  = $student['name'];
$user_id = $student['user_id'];

/* 1. SEND EMAIL BEFORE DELETION */
$subject = "Account Removed";

$message = "
    <h2>Hello $name</h2>

    <p>Your student account has been removed from the system.</p>

    <p>If this was a mistake, please contact administration immediately.</p>

    <br>
    <p><b>School Management System</b></p>
";

sendMail($email, $subject, $message);

/* 2. DELETE RECORDS SAFELY */
mysqli_query($conn, "DELETE FROM students WHERE student_id='$student_id'");
mysqli_query($conn, "DELETE FROM users WHERE user_id='$user_id'");

/* 3. LOG ACTION */
logAction($conn, $_SESSION['user_id'], "Deleted student ID $student_id");

/* 4. REDIRECT */
header("Location: manage_students.php?deleted=1");
exit();
?>