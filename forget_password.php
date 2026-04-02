<?php
session_start();
include("config/database.php");
include("includes/mailer.php");

$error="";
$msg="";
$remaining = isset($_SESSION['resend_time']) ? max(0, 60 - (time() - $_SESSION['resend_time'])) : 0;

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $identity = mysqli_real_escape_string($conn, trim($_POST['identity']));

    if(empty($identity)){
        $error="Please enter your Email or Username.";
    }else{

        $query="SELECT * FROM users 
                WHERE email='$identity' OR username='$identity' LIMIT 1";
        $result=mysqli_query($conn,$query);

        if(mysqli_num_rows($result)==1){

            $user=mysqli_fetch_assoc($result);
            $email=$user['email'];

            /* GENERATE OTP */
            $otp = rand(100000,999999);

            $_SESSION['reset_email']=$email;
            $_SESSION['reset_otp']=$otp;
            $_SESSION['otp_time']=time();
            $_SESSION['resend_time']=time(); // for countdown

            /* EMAIL MESSAGE */
            $subject="Password Reset OTP";
            $message="
            <h2>Password Reset</h2>
            <p>Your OTP Code:</p>
            <h1 style='color:#007bff'>$otp</h1>
            <p>This code expires in 10 minutes.</p>";

            if(sendMail($email,$subject,$message)){
                header("Location: verify_otp.php");
                exit();
            }else{
                $error="Failed to send OTP email.";
            }

        }else{
            $error="Account not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<link rel="stylesheet" href="assets/css/form.css">
<style>
body{
    background: #f4f6f9;
    font-family: Arial, sans-serif;
}

.login-container{
    max-width: 400px;
    margin: 80px auto;
}

.form-card h2{
    text-align: center;
    margin-bottom: 20px;
    color: #2c3e50;
}

.error{
    background: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
}

.success{
    background:#d4edda;
    color:#155724;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
}

button{
    width: 100%;
}

/* Resend OTP */
.resend{
    text-align:center;
    margin-top:10px;
    font-size:14px;
}
.resend a{
    color:#007bff;
    text-decoration:none;
    font-weight:bold;
}
.resend a:hover{
    text-decoration:underline;
}
</style>
</head>
<body>

<div class="login-container">
    <div class="form-card">
        <h2>Forgot Password</h2>

        <?php if($error!="") echo "<div class='error'>$error</div>"; ?>
        <?php if($msg!="") echo "<div class='success'>$msg</div>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email or Username</label>
                <input type="text" name="identity" required>
            </div>

            <div class="btn-group">
                <button class="btn btn-primary">Send OTP</button>
            </div>
        </form>

        <div class="resend">
            <?php if($remaining > 0): ?>
                Resend OTP in <span id="count"><?= $remaining ?></span>s
            <?php else: ?>
               
            <?php endif; ?>
        </div>

        <div style="text-align:center; margin-top:10px;">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>

<script>
let time = <?= $remaining ?>;
let count = document.getElementById("count");

if(count){
    let timer=setInterval(()=>{
        time--;
        count.innerText=time;
        if(time<=0) location.reload();
    },1000);
}
</script>

</body>
</html>