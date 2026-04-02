<?php
session_start();
include("config/database.php");

if(!isset($_SESSION['reset_email'])){
    header("Location: login.php");
    exit();
}

$error="";
$msg="";
$redirect = false;

if($_SERVER['REQUEST_METHOD']=="POST"){

    $otp_entered = trim($_POST['otp']);
    $new_password = trim($_POST['password']);

    // OTP expires after 10 minutes
    if(time() - $_SESSION['otp_time'] > 600){
        session_destroy();
        $error = "OTP expired. Please restart the reset process.";
    }
    elseif($otp_entered != $_SESSION['reset_otp']){
        $error = "Invalid OTP";
    }
    else{
        $email = $_SESSION['reset_email'];
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        mysqli_query($conn,"UPDATE users SET password='$hashed' WHERE email='$email'");

        session_destroy();
        $msg = "Password reset successful! Redirecting to login...";
        $redirect = true;
    }
}

$remaining = isset($_SESSION['resend_time']) ? max(0, 60 - (time() - $_SESSION['resend_time'])) : 0;
?>
<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
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
        <h2>Reset Password</h2>

        <?php if($error!="") echo "<div class='error'>$error</div>"; ?>
        <?php if($msg!="") echo "<div class='success'>$msg</div>"; ?>

        <?php if(!$redirect): ?>
        <form method="POST">

            <div class="form-group">
                <label>Enter OTP</label>
                <input type="text" name="otp" required>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="btn-group">
                <button class="btn btn-primary">Reset Password</button>
            </div>
        </form>

        <div class="resend">
            <?php if($remaining > 0): ?>
                Resend OTP in <span id="count"><?= $remaining ?></span>s
            <?php else: ?>
                <a href="forgot_password.php">Resend OTP</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div style="text-align:center; margin-top:10px;">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>

<?php if($redirect): ?>
<script>
setTimeout(()=>{
    window.location.href="login.php";
},3000); // Redirect after 3s
</script>
<?php endif; ?>

<script>
let time = <?= $remaining ?>;
let count = document.getElementById("count");

if(count){
    let timer = setInterval(()=>{
        time--;
        count.innerText=time;
        if(time<=0) location.reload();
    },1000);
}
</script>

</body>
</html>