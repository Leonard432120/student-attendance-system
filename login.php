<?php
session_start();
include("config/database.php");

$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    /* STEP 1: GET USER ONLY BY USERNAME */
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){

        $user = mysqli_fetch_assoc($result);

        /* STEP 2: VERIFY PASSWORD */
        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            /* UPDATE LAST LOGIN */
            mysqli_query($conn,"
                UPDATE users
                SET last_login = NOW()
                WHERE user_id = {$user['user_id']}
            ");

            /* REDIRECT BY ROLE */
            if($user['role'] == "admin"){
                header("Location: admin/dashboard.php");
            } elseif($user['role'] == "teacher"){
                header("Location: teacher/dashboard.php");
            } else {
                header("Location: student/dashboard.php");
            }
            exit();

        } else {
            $error = "Invalid username or password";
        }

    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
        .btn-group button{
            width: 100%;
        }
    </style>
</head>
<body>

<div class="login-container">

    <div class="form-card">

        <h2>Login</h2>

        <?php if($error != ""): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div style="text-align:right; margin-bottom:15px;">
                <a href="forget_password.php">Forgot Password?</a>
            </div>

            <div style="margin-bottom:15px;">
                <input type="checkbox" id="showPassword">
                <label for="showPassword">Show Password</label>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>

        </form>

    </div>

</div>

<script>
    const showPassword = document.getElementById('showPassword');
    const passwordInput = document.getElementById('password');

    showPassword.addEventListener('change', function() {
        passwordInput.type = this.checked ? 'text' : 'password';
    });
</script>

</body>
</html>