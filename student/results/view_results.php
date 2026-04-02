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

/* Fetch results */
$results_res = mysqli_query($conn, "
    SELECT sub.subject_name, a.test_mark, a.assignment_mark, a.exam_mark,
           ROUND((a.test_mark + a.assignment_mark + a.exam_mark)/3,2) AS avg_mark
    FROM assessments a
    JOIN subjects sub ON a.subject_id = sub.subject_id
    WHERE a.student_id='$student_id'
    ORDER BY sub.subject_name ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>View Results</title>
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
<a href="../attendance/view_attendance.php">Attendance</a>
<a href="view_results.php" class="active">Results</a>
<a href="../profile/my_profile.php">Update Profile</a>
<a href="../../includes/logout.php">Logout</a>
</div>

<div class="main">
<div class="section">
<h2>Assessment Results</h2>
<p class="subtext">Here are your latest scores for each subject</p>

<table class="table">
<tr>
<th>Subject</th>
<th>Test Mark</th>
<th>Assignment Mark</th>
<th>Exam Mark</th>
<th>Average</th>
</tr>
<?php while($res = mysqli_fetch_assoc($results_res)): ?>
<tr>
<td><?php echo $res['subject_name']; ?></td>
<td><?php echo $res['test_mark'] ?? 'N/A'; ?></td>
<td><?php echo $res['assignment_mark'] ?? 'N/A'; ?></td>
<td><?php echo $res['exam_mark'] ?? 'N/A'; ?></td>
<td><?php echo $res['avg_mark'] ?? 'N/A'; ?></td>
</tr>
<?php endwhile; ?>
</table>

</div>
</div>
</div>
</body>
</html>