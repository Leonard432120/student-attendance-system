<?php
include("../includes/auth.php");

if($_SESSION['role'] != 'admin'){
    echo "Access Denied!";
    exit();
}

include("../config/database.php");

/* =========================
   STATS
========================= */
/* STUDENTS COUNT */
$student_count = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) as total 
    FROM users 
    WHERE role = 'student'
"))['total'];

/* TEACHERS COUNT */
$teacher_count = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) as total 
    FROM users 
    WHERE role = 'teacher'
"))['total'];
$class_count = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM classes"))['total'];
$subject_count = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM subjects"))['total'];

/* =========================
   RECENT ACTIVITIES
========================= */
$activities = mysqli_query($conn,"
    SELECT 
        a.id,
        a.user_id,
        a.action,
        a.created_at,
        u.name AS user_name
    FROM activity_log a
    LEFT JOIN users u ON a.user_id = u.user_id
    ORDER BY a.created_at DESC
    LIMIT 10
");

/* =========================
   ACTIVE USERS
========================= */
$active_users = mysqli_query($conn,"
    SELECT user_id, name, role, last_login
    FROM users
    WHERE last_login IS NOT NULL
    AND last_login >= NOW() - INTERVAL 15 MINUTE
    ORDER BY last_login DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/admin.css">

<style>

/* GRID */
.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* ACTIVITY FEED */
.activity-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.activity-item b {
    color: #2c3e50;
}

.activity-item small {
    color: #777;
}

/* TABLE */
.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.table th {
    background: #f4f6f9;
}

/* ONLINE BADGE */
.badge-online {
    background: #2ecc71;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
}

/* RESPONSIVE */
@media(max-width:768px){
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

</style>

</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="header-left">
        <span class="dashboard-title">Admin Portal</span>
    </div>

    <div class="header-right">
        <span class="header-name"><?php echo $_SESSION['name']; ?></span>
        <a href="../includes/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="students/manage_students.php">Students</a>
    <a href="teachers/manage_teachers.php">Teachers</a>
    <a href="classes/manage_classes.php">Classes</a>
    <a href="subjects/manage_subjects.php">Subjects</a>
    <a href="reports/attendance_report.php">Attendance Reports</a>
    <a href="reports/performance_report.php">Performance Reports</a>
    <a href="settings/system_settings.php">System Settings</a>
</div>

<!-- CONTENT -->
<div class="content">

<h2>Welcome, <?php echo $_SESSION['name']; ?></h2>

<!-- STATS -->
<div class="stats">

<div class="stat-box">
<h4>Total Students</h4>
<p><?php echo $student_count; ?></p>
</div>

<div class="stat-box">
<h4>Total Teachers</h4>
<p><?php echo $teacher_count; ?></p>
</div>

<div class="stat-box">
<h4>Total Classes</h4>
<p><?php echo $class_count; ?></p>
</div>

<div class="stat-box">
<h4>Total Subjects</h4>
<p><?php echo $subject_count; ?></p>
</div>

</div>

<!-- QUICK ACTIONS -->
<div class="card">
<h3>Quick Actions</h3>

<div class="quick-links">
<a href="students/add_student.php">Add Student</a>
<a href="teachers/add_teacher.php">Add Teacher</a>
<a href="classes/add_class.php">Add Class</a>
<a href="subjects/add_subject.php">Add Subject</a>
</div>

</div>

<!-- SYSTEM INFO -->
<div class="card">
<h3>System Overview</h3>
<p>This system allows administrators to manage students, teachers, classes, subjects, attendance and academic performance efficiently.</p>
</div>

<!-- GRID SECTION -->
<div class="dashboard-grid">

<!-- RECENT ACTIVITIES -->
<div class="card">
<h3>Recent Activities</h3>

<?php if(mysqli_num_rows($activities) > 0){ ?>

<?php while($a = mysqli_fetch_assoc($activities)){ ?>

<div class="activity-item">

<b><?php echo $a['user_name'] ? $a['user_name'] : 'System'; ?></b><br>

<?php echo $a['action']; ?><br>

<small><?php echo date("d M Y, H:i", strtotime($a['created_at'])); ?></small>

</div>

<?php } ?>

<?php } else { ?>
<p>No recent activities.</p>
<?php } ?>

</div>

<!-- ACTIVE USERS -->
<div class="card">
<h3>Active Users</h3>

<?php if(mysqli_num_rows($active_users) > 0){ ?>

<table class="table">
<tr>
<th>Name</th>
<th>Role</th>
<th>Status</th>
</tr>

<?php while($u = mysqli_fetch_assoc($active_users)){ ?>

<tr>
<td><?php echo $u['name']; ?></td>
<td><?php echo ucfirst($u['role']); ?></td>
<td><span class="badge-online">Online</span></td>
</tr>

<?php } ?>

</table>

<?php } else { ?>
<p>No active users right now.</p>
<?php } ?>

</div>

</div>

</div>
</div>

</body>
</html>