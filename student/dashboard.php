<?php
include("../includes/auth.php");
if($_SESSION['role'] != 'student'){ exit("Access Denied!"); }

include("../config/database.php");

$user_id = $_SESSION['user_id'];

/* 1️⃣ Fetch user info first */
$user_query = mysqli_query($conn, "
    SELECT name, profile_image, email
    FROM users
    WHERE user_id = '$user_id'
");

if(mysqli_num_rows($user_query) == 0){
    exit("User not found. Please contact admin.");
}

$user = mysqli_fetch_assoc($user_query);
$name = $user['name'] ?? 'Student';
$profile = $user['profile_image'] ?? 'default.png';
$email = $user['email'] ?? '';

/* 2️⃣ Fetch student record */
$student_query = mysqli_query($conn, "
    SELECT student_id, admission_number, class_id, gender, date_of_birth, phone
    FROM students
    WHERE user_id = '$user_id'
");

if(mysqli_num_rows($student_query) > 0){
    $student = mysqli_fetch_assoc($student_query);
    $student_id = $student['student_id'];
    $admission_number = $student['admission_number'] ?? '';
    $class_id = $student['class_id'] ?? null;
    $gender = $student['gender'] ?? '';
    $date_of_birth = $student['date_of_birth'] ?? '';
    $phone = $student['phone'] ?? '';
} else {
    $student_id = null;
    $admission_number = '';
    $class_id = null;
    $gender = '';
    $date_of_birth = '';
    $phone = '';
}

/* 3️⃣ Fetch class name if assigned */
$class_name = 'Not Assigned';
if($class_id){
    $class_res = mysqli_query($conn, "SELECT class_name FROM classes WHERE class_id = '$class_id'");
    if($class_res && mysqli_num_rows($class_res) > 0){
        $class_name = mysqli_fetch_assoc($class_res)['class_name'];
    }
}

/* 4️⃣ Attendance stats */
$total = $present = $absent = $rate = 0;
if($student_id){
    $att = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT 
            COUNT(*) as total,
            SUM(status='Present') as present,
            SUM(status='Absent') as absent
        FROM attendance
        WHERE student_id='$student_id'
    "));

    $total = $att['total'] ?? 0;
    $present = $att['present'] ?? 0;
    $absent = $att['absent'] ?? 0;
    $rate = ($total > 0) ? round(($present/$total)*100) : 0;
}

/* 5️⃣ Performance stats */
$avg = 0;
if($student_id){
    $res = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT AVG((test_mark + assignment_mark + exam_mark)/3) as avg_score
        FROM assessments
        WHERE student_id='$student_id'
    "));
    $avg = round($res['avg_score'] ?? 0);
}

/* 6️⃣ Subjects enrolled */
$subjects = [];
$total_subjects = 0;
if($class_id){
    $subject_query = "
        SELECT sub.subject_name, u.name AS teacher_name
        FROM subjects sub
        LEFT JOIN class_subjects cs ON cs.subject_id = sub.subject_id AND cs.class_id = '$class_id'
        LEFT JOIN teachers t ON cs.teacher_id = t.teacher_id
        LEFT JOIN users u ON t.user_id = u.user_id
        WHERE sub.class_id = '$class_id'
    ";
    $sub_res = mysqli_query($conn, $subject_query);
    if($sub_res && mysqli_num_rows($sub_res) > 0){
        while($row = mysqli_fetch_assoc($sub_res)){
            $subjects[] = $row;
        }
        $total_subjects = count($subjects);
    }
}

/* 7️⃣ Pending assessments */
$pending = 0;
if($student_id){
    $pending_res = mysqli_query($conn,"SELECT COUNT(*) as pending FROM assessments WHERE student_id='$student_id' AND (test_mark IS NULL OR assignment_mark IS NULL OR exam_mark IS NULL)");
    $pending = mysqli_fetch_assoc($pending_res)['pending'] ?? 0;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body{font-family:Arial,sans-serif; background:#f4f6f9; color:#2c3e50;}
.topbar{display:flex;justify-content:space-between;align-items:center;background:#2c3e50;color:#fff;padding:15px 25px;}
.topbar img.avatar{width:40px;height:40px;border-radius:50%;margin-left:10px;}
.layout{display:flex;min-height:100vh;}
.sidebar{width:220px;background:#34495e;color:#fff;padding:20px 0;display:flex;flex-direction:column;}
.sidebar a{padding:12px 25px;color:#fff;font-weight:bold;transition:0.3s;}
.sidebar a:hover, .sidebar a.active{ background: #2f557b;  color:#3498db;}
.main{flex:1;padding:25px;}
.section h1,h2,h3{margin-top:0;}
.dashboard-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px;margin-bottom:20px;}
.card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);}
.card h3{margin-bottom:10px;}
.card p{margin:0;}
.btn{display:inline-block;padding:8px 15px;border-radius:8px;background:#3498db;color:#fff;text-decoration:none;font-weight:bold;transition:0.3s;}
.btn:hover{opacity:0.9;}
.table{width:100%;border-collapse:collapse;}
.table th,.table td{padding:10px;border-bottom:1px solid #eee;text-align:left;}
.table th{background:#3498db;color:#fff;}
</style>
</head>
<body>

<div class="topbar">
<div><b>Student Portal</b></div>
<div class="profile">
    <span><?php echo $name; ?></span>
    <img src="../uploads/profiles/<?php echo $profile; ?>" class="avatar">
</div>
</div>

<div class="layout">
<div class="sidebar">
<a href="dashboard.php" class="active">Dashboard</a>
<a href="attendance/view_attendance.php">Attendance</a>
<a href="results/view_results.php">Results</a>
<a href="profile/my_profile.php">Update Profile</a>
<a href="../includes/logout.php">Logout</a>
</div>

<div class="main">
<div class="section">
<h2>Welcome back, <?php echo $name; ?></h2>
<p class="subtext"><?php echo $class_name; ?> • Student Dashboard Overview</p>
</div>

<!-- Dashboard Stats -->
<div class="dashboard-cards">
<div class="card"><h3><?php echo $rate; ?>%</h3><p>Attendance Rate</p></div>
<div class="card"><h3><?php echo $present; ?></h3><p>Present Days</p></div>
<div class="card"><h3><?php echo $absent; ?></h3><p>Absent Days</p></div>
<div class="card"><h3><?php echo $avg; ?>%</h3><p>Average Score</p></div>
<div class="card"><h3><?php echo $total_subjects; ?></h3><p>Total Subjects</p></div>
<div class="card"><h3><?php echo $pending; ?></h3><p>Pending Assessments</p></div>
</div>

<!-- Quick Actions -->
<div class="section">
<h3>Quick Actions</h3>
<a href="attendance/view_attendance.php" class="btn">View Attendance</a>
<a href="results/view_results.php" class="btn">View Results</a>
<a href="profile/my_profile.php" class="btn">Update Profile</a>
</div>

<!-- Subjects -->
<div class="section">
<h3>Subjects Enrolled</h3>
<table class="table">
<tr><th>Subject</th><th>Teacher</th></tr>
<?php foreach($subjects as $sub): ?>
<tr>
<td><?php echo $sub['subject_name']; ?></td>
<td><?php echo $sub['teacher_name'] ?? "Not Assigned"; ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<!-- Recent Activity -->
<div class="section">
<h3>Recent Attendance</h3>
<?php 
$recent = mysqli_query($conn,"SELECT attendance_date, status FROM attendance WHERE student_id='$student_id' ORDER BY attendance_date DESC LIMIT 5");
?>
<table class="table">
<tr><th>Date</th><th>Status</th></tr>
<?php while($r = mysqli_fetch_assoc($recent)): ?>
<tr>
<td><?php echo $r['attendance_date']; ?></td>
<td><?php echo $r['status']; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</div>
</body>
</html>