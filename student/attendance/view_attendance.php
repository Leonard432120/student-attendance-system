<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'student'){ exit("Access Denied!"); }

include("../../config/database.php");

$user_id = $_SESSION['user_id'];

/* Fetch student record */
$student_query = mysqli_query($conn, "
    SELECT student_id, class_id
    FROM students
    WHERE user_id = '$user_id'
");

if(mysqli_num_rows($student_query) == 0){
    exit("Student record not found! Please contact the administrator.");
}

$student = mysqli_fetch_assoc($student_query);
$student_id = $student['student_id'];
$class_id = $student['class_id'] ?? null;

/* Fetch attendance records */
$attendance_res = mysqli_query($conn, "
    SELECT attendance_date, status
    FROM attendance
    WHERE student_id='$student_id'
    ORDER BY attendance_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>View Attendance</title>
<link rel="stylesheet" href="../../assets/css/style.css">
<style>
body{font-family:Arial,sans-serif; background:#f4f6f9; color:#2c3e50;}
.topbar{display:flex;justify-content:space-between;align-items:center;background:#2c3e50;color:#fff;padding:15px 25px;}
.topbar img.avatar{width:40px;height:40px;border-radius:50%;margin-left:10px;}
.layout{display:flex;min-height:100vh;}
.sidebar{width:220px;background:#34495e;color:#fff;padding:20px 0;display:flex;flex-direction:column;}
.sidebar a{padding:12px 25px;color:#fff;font-weight:bold;transition:0.3s;}
.sidebar a:hover, .sidebar a.active{background: #2f557b;  color:#3498db;}
.main{flex:1;padding:25px;}
.section h1,h2,h3{margin-top:0;}
.table{width:100%;border-collapse:collapse;}
.table th,.table td{padding:10px;border-bottom:1px solid #eee;text-align:left;}
.table th{background:#3498db;color:#fff;}
</style>
</head>
<body>

<?php
// Fetch user info for topbar
$user_res = mysqli_query($conn, "SELECT name, profile_image FROM users WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($user_res);
$name = $user['name'] ?? 'Student';
$profile = $user['profile_image'] ?? 'default.png';
?>

<div class="topbar">
<div><b>Student Portal</b></div>
<div class="profile">
    <span><?php echo $name; ?></span>
    <img src="../../uploads/profiles/<?php echo $profile; ?>" class="avatar">
</div>
</div>

<div class="layout">
<div class="sidebar">
<a href="../dashboard.php">Dashboard</a>
<a href="view_attendance.php" class="active">Attendance</a>
<a href="../results/view_results.php">Results</a>
<a href="../profile/update_profile.php">Update Profile</a>
<a href="../../includes/logout.php">Logout</a>
</div>

<div class="main">
<div class="section">
<h2>Attendance Records</h2>
<p class="subtext">Here is your attendance history</p>

<table class="table">
<tr><th>Date</th><th>Status</th></tr>
<?php while($att = mysqli_fetch_assoc($attendance_res)): ?>
<tr>
<td><?php echo $att['attendance_date']; ?></td>
<td><?php echo $att['status']; ?></td>
</tr>
<?php endwhile; ?>
</table>

</div>
</div>
</div>
</body>
</html>