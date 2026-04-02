<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

/* =========================
   SEND EMAIL FUNCTION
========================= */
function sendMail($to, $subject, $message)
{
    $mail = new PHPMailer(true);

    try {

        // =========================
        // SMTP CONFIG (GMAIL)
        // =========================
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // 🔐 YOUR GMAIL DETAILS
        $mail->Username = 'leonardmlungupro@gmail.com';
        $mail->Password = 'pzza mjot khbl ojya'; // no spaces

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // =========================
        // EMAIL SETUP
        // =========================
        $mail->setFrom('leonardmlungupro@gmail.com', 'School System');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
?>