<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

$message = "";

/* HANDLE FORM */
if($_SERVER['REQUEST_METHOD'] == "POST"){

    $system_name = mysqli_real_escape_string($conn, $_POST['system_name']);
    $admin_email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $new_password = $_POST['new_password'];

    /* UPDATE SYSTEM SETTINGS (USING SIMPLE TABLE) */
    mysqli_query($conn, "
        UPDATE users 
        SET email='$admin_email'
        WHERE user_id = '{$_SESSION['user_id']}'
    ");

    /* UPDATE PASSWORD */
    if(!empty($new_password)){
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        mysqli_query($conn, "
            UPDATE users 
            SET password='$hashed'
            WHERE user_id = '{$_SESSION['user_id']}'
        ");
    }

    /* HANDLE LOGO UPLOAD */
    if(isset($_FILES['logo']) && $_FILES['logo']['name'] != ""){

        $target_dir = "../../uploads/";
        if(!is_dir($target_dir)){
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["logo"]["name"]);
        $target_file = $target_dir . $file_name;

        if(move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)){
            
            mysqli_query($conn, "
                UPDATE users 
                SET profile_image='$file_name'
                WHERE user_id = '{$_SESSION['user_id']}'
            ");
        }
    }

    $message = "Settings updated successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>System Settings</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="header-left">
        <span class="dashboard-title">Admin Portal</span>
    </div>

    <div class="header-right">
        <span class="header-name"><?php echo $_SESSION['name']; ?></span>
        <a href="../../includes/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="../dashboard.php">Dashboard</a>
    <a href="../students/manage_students.php">Students</a>
    <a href="../teachers/manage_teachers.php">Teachers</a>
    <a href="../classes/manage_classes.php">Classes</a>
    <a href="../subjects/manage_subjects.php">Subjects</a>
    <a href="../reports/attendance_report.php">Attendance</a>
    <a href="../reports/performance_report.php">Performance</a>
    <a href="system_settings.php" class="active">Settings</a>
</div>

<!-- CONTENT -->
<div class="content">

<div class="card">
    <h2>System Settings</h2>

    <?php if($message != ""){ ?>
        <p style="color:green; font-weight:bold;"><?php echo $message; ?></p>
    <?php } ?>
</div>

<div class="card">

<form method="POST" enctype="multipart/form-data">

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        <!-- SYSTEM NAME -->
        <div>
            <label>System Name</label>
            <input type="text" name="system_name" class="input" placeholder="School System">
        </div>

        <!-- ADMIN EMAIL -->
        <div>
            <label>Admin Email</label>
            <input type="email" name="admin_email" class="input" required>
        </div>

        <!-- NEW PASSWORD -->
        <div>
            <label>New Password</label>
            <input type="password" name="new_password" class="input">
        </div>

        <!-- LOGO -->
        <div>
            <label>Upload Logo</label>
            <input type="file" name="logo" class="input">
        </div>

    </div>

    <br>

    <button type="submit" class="btn btn-edit">Save Settings</button>

</form>

</div>

</div>
</div>

<style>
.input{
    width:100%;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
}
</style>

</body>
</html>