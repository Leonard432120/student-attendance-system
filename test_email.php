<?php

require 'includes/mailer.php';

$to = "Mocconation@gmail.com"; // change this
$subject = "Test Email from School System";

$message = "
<h2>Hello 👋</h2>
<p>This is a test email from your system.</p>
<p>If you received this, SMTP is working correctly 🎉</p>
";

if(sendMail($to, $subject, $message)){
    echo "Email sent successfully ✔";
} else {
    echo "Email failed ❌";
}
?>