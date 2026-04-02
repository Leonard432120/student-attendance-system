<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

if(!isset($_GET['id'])) exit("Missing ID");

$id = $_GET['id'];

/* GET TEACHER INFO FIRST */
$query = mysqli_query($conn,"
SELECT u.user_id, u.name, u.email
FROM teachers t
JOIN users u ON t.user_id = u.user_id
WHERE t.teacher_id='$id'
");

$data = mysqli_fetch_assoc($query);

if(!$data) exit("Teacher not found");

/* SEND EMAIL FIRST */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'yourgmail@gmail.com';
    $mail->Password = 'your-app-password';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('yourgmail@gmail.com', 'School System');
    $mail->addAddress($data['email'], $data['name']);

    $mail->isHTML(true);
    $mail->Subject = "Account Deactivated";

    $mail->Body = "
        <h3>Hello {$data['name']}</h3>
        <p>Your teacher account has been removed from the system by administration.</p>
        <p>If this is a mistake, please contact the school office.</p>
    ";

    $mail->send();

} catch(Exception $e) {
    // ignore email failure
}

/* DELETE TEACHER */
mysqli_query($conn,"DELETE FROM teachers WHERE teacher_id='$id'");
mysqli_query($conn,"DELETE FROM users WHERE user_id='{$data['user_id']}'");
logAction(
    $conn,
    $_SESSION['user_id'],
    "Deleted teacher: " . $data['name'] . " (" . $data['email'] . ")"
);

header("Location: manage_teachers.php?deleted=1");
exit();
?>