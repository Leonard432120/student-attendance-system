<?php
include("../../includes/auth.php");
include("../../includes/mailer.php"); // For sending email notifications
if($_SESSION['role'] != 'teacher'){ exit("Access Denied!"); }

include("../../config/database.php");

$user_id = $_SESSION['user_id'];

/* Fetch user info */
$user_query = mysqli_query($conn, "
    SELECT name, email, profile_image
    FROM users
    WHERE user_id='$user_id'
");
$user = mysqli_fetch_assoc($user_query);
$name = $user['name'] ?? '';
$email = $user['email'] ?? '';
$profile = $user['profile_image'] ?? 'default.png';

/* Fetch student info */
$student_query = mysqli_query($conn, "
    SELECT admission_number, class_id, gender, date_of_birth, phone
    FROM students
    WHERE user_id='$user_id'
");
$student = mysqli_fetch_assoc($student_query);
$admission_number = $student['admission_number'] ?? '';
$class_id = $student['class_id'] ?? null;
$gender = $student['gender'] ?? '';
$date_of_birth = $student['date_of_birth'] ?? '';
$phone = $student['phone'] ?? '';

/* Fetch class name */
$class_name = 'Not Assigned';
if($class_id){
    $cls = mysqli_query($conn, "SELECT class_name FROM classes WHERE class_id='$class_id'");
    if(mysqli_num_rows($cls) > 0){
        $class_name = mysqli_fetch_assoc($cls)['class_name'];
    }
}

/* Handle form submission */
$msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name_new = mysqli_real_escape_string($conn, $_POST['name']);
    $email_new = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_new = mysqli_real_escape_string($conn, $_POST['phone']);
    $password_new = $_POST['password'] ?? '';

    /* Handle image upload */
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0){
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
        $target_dir = "../../uploads/profiles/";
        if(move_uploaded_file($file_tmp, $target_dir . $file_name)){
            $profile = $file_name;
            mysqli_query($conn, "UPDATE users SET profile_image='$file_name' WHERE user_id='$user_id'");
        }
    }

    /* Update user details */
    mysqli_query($conn, "UPDATE users SET name='$name_new', email='$email_new' WHERE user_id='$user_id'");
    mysqli_query($conn, "UPDATE students SET phone='$phone_new' WHERE user_id='$user_id'");

/* Handle password change */
if(!empty($password_new)){
    $hashed_pw = password_hash($password_new, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE users SET password='$hashed_pw' WHERE user_id='$user_id'");

    // Send notification email
    $subject = "Password Changed Successfully";
    $message = "Hello $name_new,\n\nYour password has been updated successfully.\n\nIf you did not make this change, please contact the administrator immediately.\n\nRegards,\nAdmin Team";

    sendMail($email_new, $subject, $message);

    $msg = "Profile updated successfully! Password has been changed and a confirmation email sent.";
} else {
    $msg = "Profile updated successfully!";
}
    // Send email to user
    $name = $name_new;
    $email = $email_new;
    $phone = $phone_new;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<style>
body{font-family:Arial,sans-serif;background:#f4f6f9;color:#2c3e50;margin:0;}
.topbar{display:flex;justify-content:space-between;align-items:center;background:#2c3e50;color:#fff;padding:15px 25px;}
.topbar img.avatar{width:40px;height:40px;border-radius:50%;margin-left:10px;}
.layout{display:flex;min-height:100vh;}
.sidebar{width:220px;background:#34495e;color:#fff;padding:20px 0;display:flex;flex-direction:column;}
.sidebar a{padding:12px 25px;color:#fff;font-weight:bold;transition:0.3s;text-decoration:none;}
.sidebar a:hover, .sidebar a.active{background: #2f557b;  color:#3498db;}
.main{flex:1;padding:25px;}
.section h2{margin-top:0;}
.form{background:#fff;padding:25px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);max-width:600px;}
.form-group{margin-bottom:15px;}
.form-group label{display:block;margin-bottom:5px;font-weight:bold;}
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group input[type="file"]{width:100%;padding:10px;border:1px solid #ccc;border-radius:8px;box-sizing:border-box;}
.btn{padding:10px 20px;background:#3498db;color:#fff;border:none;border-radius:8px;font-weight:bold;cursor:pointer;}
.btn:hover{opacity:0.9;}
.profile-img{width:120px;height:120px;border-radius:50%;object-fit:cover;margin-bottom:10px;}
.msg{color:green;margin-bottom:15px;font-weight:bold;}
</style>
</head>
<body>

<div class="topbar">
<div><b>Student Portal</b></div>
<div class="profile">
    <span><?php echo $_SESSION['name']; ?></span>
    <img src="../../uploads/profiles/<?php echo $profile; ?>" class="avatar">
</div>
</div>

<div class="layout">
<div class="sidebar">
<a href="../dashboard.php">Dashboard</a>
<a href="../attendance/view_attendance.php">Attendance</a>
<a href="../results/view_results.php">Results</a>
<a href="my_profile.php" class="active">My Profile</a>
<a href="../../includes/logout.php">Logout</a>
</div>

<div class="main">
<div class="section">
<h2>Update My Profile</h2>
<?php if($msg): ?><p class="msg"><?php echo $msg; ?></p><?php endif; ?>

<form action="" method="post" enctype="multipart/form-data" class="form">
    <div class="form-group">
        <label>Profile Image</label>
        <img src="../../uploads/profiles/<?php echo $profile; ?>" class="profile-img">
        <input type="file" name="profile_image" accept="image/*">
    </div>

    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo $name; ?>" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo $email; ?>" required>
    </div>

    <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo $phone; ?>">
    </div>

    <div class="form-group">
        <label>Password (leave blank if not changing)</label>
        <input type="password" name="password" placeholder="New Password">
    </div>

    <div class="form-group">
        <label>Admission Number</label>
        <input type="text" value="<?php echo $admission_number; ?>" readonly>
    </div>

    <div class="form-group">
        <label>Class</label>
        <input type="text" value="<?php echo $class_name; ?>" readonly>
    </div>

    <div class="form-group">
        <label>Gender</label>
        <input type="text" value="<?php echo $gender; ?>">
    </div>

    <div class="form-group">
        <label>Date of Birth</label>
        <input type="text" value="<?php echo $date_of_birth; ?>">
    </div>

    <div class="form-group">
        <button type="submit" class="btn">Update Profile</button>
    </div>
</form>
</div>
</div>
</body>
</html>