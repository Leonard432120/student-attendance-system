<?php
include("../../includes/mailer.php");
include("../../includes/auth.php");
include("../../config/database.php");

if($_SESSION['role']!='admin') exit("Access Denied");

if(!isset($_GET['id'])) exit("Teacher ID missing");

$id = $_GET['id'];

/* FETCH TEACHER */
$query = mysqli_query($conn,"
SELECT t.teacher_id,t.employee_number,t.user_id,
u.name,u.username,u.email,u.phone
FROM teachers t
JOIN users u ON t.user_id=u.user_id
WHERE t.teacher_id='$id'
");

$teacher = mysqli_fetch_assoc($query);

if(!$teacher) exit("Teacher not found!");

if($_SERVER['REQUEST_METHOD']=="POST"){

$name = mysqli_real_escape_string($conn,$_POST['name']);
$username = mysqli_real_escape_string($conn,$_POST['username']);
$email = mysqli_real_escape_string($conn,$_POST['email']);
$phone = mysqli_real_escape_string($conn,$_POST['phone']);
$employee_number = mysqli_real_escape_string($conn,$_POST['employee_number']);
$password = $_POST['password'];

$plain_password = $password;

/* UPDATE USERS */
if($password!=""){

$hash = password_hash($password,PASSWORD_DEFAULT);

mysqli_query($conn,"
UPDATE users SET 
name='$name',
username='$username',
email='$email',
phone='$phone',
password='$hash'
WHERE user_id='{$teacher['user_id']}'
");

} else {

mysqli_query($conn,"
UPDATE users SET 
name='$name',
username='$username',
email='$email',
phone='$phone'
WHERE user_id='{$teacher['user_id']}'
");

}

/* UPDATE TEACHER */
mysqli_query($conn,"
UPDATE teachers SET employee_number='$employee_number'
WHERE teacher_id='$id'
");

logAction(
    $conn,
    $_SESSION['user_id'],
    "Updated teacher: $name ($employee_number)"
);
/* EMAIL */
$subject = "Teacher Account Updated";

$message = "
<h2>Hello $name</h2>

<p>Your teacher account has been updated.</p>

<ul>
<li><b>Name:</b> $name</li>
<li><b>Username:</b> $username</li>
<li><b>Email:</b> $email</li>
<li><b>Phone:</b> $phone</li>
<li><b>Employee Number:</b> $employee_number</li>
</ul>
";

if($password!=""){
$message .= "
<p><b>New Password:</b> $plain_password</p>
<p>Please login and change it immediately.</p>
";
}

$message .= "
<br><p><b>School Management System</b></p>
";

sendMail($email,$subject,$message);

header("Location: manage_teachers.php?updated=1");
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Teacher</title>

<link rel="stylesheet" href="../../assets/css/admin.css">
<link rel="stylesheet" href="../../assets/css/form.css">
</head>

<body>

<div class="header">
    <div>Admin Dashboard</div>
    <div>
        <?php echo $_SESSION['name']; ?> |
        <a href="../../includes/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="../dashboard.php" class="active">Dashboard</a>
    <a href="../students/manage_students.php">Students</a>
    <a href="../teachers/manage_teachers.php">Teachers</a>
    <a href="../classes/manage_classes.php">Classes</a>
    <a href="../subjects/manage_subjects.php">Subjects</a>
    <a href="../reports/attendance_report.php">Attendance Reports</a>
    <a href="../reports/performance_report.php">Performance Reports</a>
    <a href="../settings/system_settings.php">System Settings</a>
</div>

<div class="content">

<div class="page-wrapper">

<div class="page-header">
<h2>Edit Teacher</h2>
</div>

<div class="form-card">

<form method="POST">

<div class="form-grid">

<div class="form-group">
<label>Full Name</label>
<input type="text" name="name" value="<?php echo $teacher['name']; ?>" required>
</div>

<div class="form-group">
<label>Username</label>
<input type="text" name="username" value="<?php echo $teacher['username']; ?>" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" value="<?php echo $teacher['email']; ?>" required>
</div>

<div class="form-group">
<label>Phone</label>
<input type="text" name="phone" value="<?php echo $teacher['phone']; ?>" required>
</div>

<div class="form-group">
<label>Password (leave blank to keep)</label>
<input type="password" name="password">
</div>

<div class="form-group">
<label>Employee Number</label>
<input type="text" name="employee_number" value="<?php echo $teacher['employee_number']; ?>" required>
</div>

</div>

<div class="btn-group">
<button type="submit" class="btn btn-primary">Update Teacher</button>
<a href="manage_teachers.php" class="btn btn-danger">Cancel</a>
</div>

</form>

</div>

</div>

</div>

</div>

</body>
</html>