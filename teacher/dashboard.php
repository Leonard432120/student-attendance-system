<?php
include("../includes/auth.php");
if($_SESSION['role'] != 'teacher'){ exit("Access Denied!"); }

include("../config/database.php");

/* SESSION */
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'] ?? 'Teacher';

/* FETCH PROFILE DATA */
$profile_query = mysqli_query($conn,"
    SELECT name, email, profile_image
    FROM users
    WHERE user_id='$user_id'
");

$profile = mysqli_fetch_assoc($profile_query);

$teacher_name  = $profile['name'] ?? $name;
$teacher_email = $profile['email'] ?? '';
$teacher_photo = $profile['profile_image'] ?? 'default.png';

/* TEACHER ID */
$teacher_query = mysqli_query($conn, "
    SELECT teacher_id 
    FROM teachers 
    WHERE user_id = '$user_id'
");
$teacher_data = mysqli_fetch_assoc($teacher_query);
$teacher_id = $teacher_data['teacher_id'] ?? 0;

/* STATS */
$total_students = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM students"))['total'] ?? 0;

$total_classes = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM classes"))['total'] ?? 0;

$attendance_today = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) as total FROM attendance WHERE attendance_date = CURDATE()
"))['total'] ?? 0;

$pending_marks = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) as total FROM assessments 
    WHERE test_mark = 0 OR assignment_mark = 0 OR exam_mark = 0
"))['total'] ?? 0;

/* CLASSES */
$classes_data = [];

$classes_query = mysqli_query($conn,"
    SELECT * FROM classes WHERE class_teacher = '$teacher_id'
");

while($class = mysqli_fetch_assoc($classes_query)){

    $class_id = $class['class_id'];

    $students_query = mysqli_query($conn,"
        SELECT s.student_id, u.name 
        FROM students s
        JOIN users u ON s.user_id = u.user_id
        WHERE s.class_id = '$class_id'
    ");

    $students = [];
    while($row = mysqli_fetch_assoc($students_query)){
        $students[] = $row;
    }

    $classes_data[] = [
        'class_id' => $class_id,
        'class_name' => $class['class_name'],
        'students' => $students,
        'count' => count($students)
    ];
}

/* PERFORMANCE */
$performance = mysqli_query($conn,"
    SELECT c.class_name,
    AVG(a.test_mark + a.assignment_mark + a.exam_mark) AS avg_score
    FROM assessments a
    JOIN students s ON a.student_id = s.student_id
    JOIN classes c ON s.class_id = c.class_id
    GROUP BY c.class_id
");

/* ACTIVITY */
$recent_activity = mysqli_query($conn,"
    SELECT * FROM attendance
    ORDER BY attendance_date DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Teacher Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.item{
    padding:6px 10px;
    background:#f3f6fa;
    margin:4px 0;
    border-radius:6px;
    font-size:14px;
}

.extra-students{
    display:none;
}

.toggle-btn{
    margin-top:8px;
    padding:6px 10px;
    border:none;
    background:#007bff;
    color:white;
    border-radius:6px;
    cursor:pointer;
}

/* ===== BUTTONS ===== */
.btn {
    background: #3498db;
    color: white;
    padding: 10px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.btn:hover {
    background: #2980b9;
}
/* ACTION BUTTON COLORS */
.btn-edit {
    background: #2ecc71; /* green */
    color: white;
}
.btn_view{
    background: #2980b9; /* green */
    color: white; 
    padding: 10px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}
.btn_view:hover{
    background: #2980b9;
}
.btn-edit:hover {
    background: #27ae60;
}

.btn-delete {
    background: #e74c3c; /* red */
    color: white;
}

.btn-delete:hover {
    background: #c0392b;
}

.btn-small{
    padding:6px 10px;
    font-size:12px;
    border-radius:5px;
    margin-left:3px;
}

.btn-info{
    background:#1abc9c;
    color:#fff;
}

.btn-warning{
    background:#f39c12;
    color:#fff;
}

.btn-info:hover,
.btn-warning:hover{
    opacity:0.8;
}

</style>

</head>

<body>

<div class="topbar">
    <div><b>Teacher Portal</b></div>
    <div class="profile" style="display:flex;align-items:center;gap:10px;">
    
    <div style="text-align:right;">
        <div style="font-weight:bold;"><?php echo $teacher_name; ?></div>
       
    </div>

    <img 
        src="../uploads/profiles/<?php echo $teacher_photo; ?>" 
        style="width:40px;height:40px;border-radius:50%;object-fit:cover;"
        onerror="this.src='../assets/images/default.png';"
    >

</div>
</div>

<div class="layout">

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="attendance/take_attendance.php">Take Attendance</a>
    <a href="attendance/update_attendance.php">Update Attendance</a>
    <a href="attendance/view_attendance.php">View Attendance</a>
    <a href="assessments/enter_marks.php">Enter Marks</a>
    <a href="assessments/edit_marks.php">Edit Marks</a>
    <a href="assessments/view_marks.php">View Marks</a>
    <a href="reports/class_performance.php">Class Performance</a>
    <a href="students/view_students.php">View Students</a>
    <a href="profile/my_profile.php">Update Profile</a>
    <a href="../includes/logout.php">Logout</a>
</div>

<div class="main">

<!-- WELCOME -->
<div class="section">
    <h2>Welcome, <?php echo $name; ?></h2>
    <p>Here is your teaching overview for today.</p>
</div>

<!-- STATS -->
<div class="dashboard-cards">
    <div class="card"><h3><?php echo $total_students; ?></h3><p>Total Students</p></div>
    <div class="card"><h3><?php echo $total_classes; ?></h3><p>Total Classes</p></div>
    <div class="card"><h3><?php echo $attendance_today; ?></h3><p>Attendance Today</p></div>
    <div class="card"><h3><?php echo $pending_marks; ?></h3><p>Pending Marks</p></div>
</div>

<!-- QUICK ACTIONS -->
<div class="section">
    <h3>Quick Actions</h3>
    <a href="attendance/take_attendance.php" class="btn">Take Attendance</a>
    <a href="assessments/enter_marks.php" class="btn">Enter Marks</a>
    <a href="assessments/edit_marks.php" class="btn">Update Marks</a>
</div>

<!-- CLASSES -->
<div class="section">
    <h3>Your Classes</h3>

    <?php foreach($classes_data as $class): ?>

        <div class="today-card">

            <div class="card-top">
                <div class="icon"></div>
                <div>
                    <h4><?php echo $class['class_name']; ?></h4>
                    <div class="count">
                        <?php echo $class['count']; ?> students
                    </div>
                </div>
            </div>

            <!-- SCROLLABLE STUDENT LIST -->
            <div class="card-body student-list">

                <?php if($class['count'] > 0): ?>

                    <?php foreach($class['students'] as $s): ?>
                        <div class="item">
                            <?php echo $s['name']; ?>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="empty">No students found</div>
                <?php endif; ?>

            </div>

        </div>

    <?php endforeach; ?>

</div>
<div class="dashboard-grid">

    <!-- PERFORMANCE CARD -->
    <div class="card">
        <h3>Class Performance</h3>

        <?php while($row = mysqli_fetch_assoc($performance)): ?>
            <div class="perf-item">
                <span class="perf-name">
                    <?php echo $row['class_name']; ?>
                </span>
                <span class="perf-score">
                    <?php echo round($row['avg_score'],2); ?>%
                </span>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- ACTIVITY CARD -->
    <div class="card">
        <h3>Recent Activity</h3>

        <?php while($row = mysqli_fetch_assoc($recent_activity)): ?>
            <div class="activity-item">
                <span class="activity-icon"></span>
                <div>
                    <p>Class ID: <?php echo $row['class_id']; ?></p>
                    <small><?php echo $row['attendance_date']; ?></small>
                </div>
            </div>
        <?php endwhile; ?>

    </div>

</div>
</div>
</div>

<script>
function toggleStudents(id){
    let box = document.getElementById("extra-" + id);

    if(box.style.display === "none" || box.style.display === ""){
        box.style.display = "block";
    } else {
        box.style.display = "none";
    }
}
</script>

</body>
</html>