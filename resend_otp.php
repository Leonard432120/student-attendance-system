<?php
session_start();
include("includes/mailer.php");

if(!isset($_SESSION['reset_email'])){
    header("Location: forgot_password.php");
    exit();
}

/* Prevent spam: allow resend after 60 seconds */
if(time() - $_SESSION['resend_time'] < 60){
    header("Location: verify_otp.php");
    exit();
}

/* GENERATE NEW OTP */
$otp = rand(100000,999999);
$_SESSION['reset_otp']=$otp;
$_SESSION['otp_time']=time();
$_SESSION['resend_time']=time();

$email=$_SESSION['reset_email'];

$subject="New OTP Code";
$message="
<h2>Password Reset</h2>
<p>Your NEW OTP Code:</p>
<h1>$otp</h1>
<p>Expires in 10 minutes.</p>";

sendMail($email,$subject,$message);

header("Location: verify_otp.php");
exit();
?>