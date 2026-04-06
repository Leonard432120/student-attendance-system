<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'teacher'){ exit("Access Denied!"); }

include("../../config/database.php");

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

/* TEACHER */
$teacher_query = mysqli_query($conn,"
    SELECT teacher_id FROM teachers WHERE user_id='$user_id'
");
$teacher = mysqli_fetch_assoc($teacher_query);
$teacher_id = $teacher['teacher_id'] ?? 0;

/* GET CLASSES */
$classes = mysqli_query($conn,"
    SELECT * FROM classes WHERE class_teacher='$teacher_id'
");

$class_id = $_GET['class_id'] ?? null;
$date = $_GET['date'] ?? date("Y-m-d");

/* STUDENTS */
$students = [];

if($class_id){

    $query = mysqli_query($conn,"
        SELECT s.student_id, u.name, s.admission_number, a.status
        FROM students s
        JOIN users u ON s.user_id = u.user_id
        LEFT JOIN attendance a 
        ON a.student_id = s.student_id 
        AND a.attendance_date = '$date'
        WHERE s.class_id = '$class_id'
    ");

    while($row = mysqli_fetch_assoc($query)){
        $students[] = $row;
    }
}

/* UPDATE */
if(isset($_POST['update_attendance'])){

    foreach($_POST['attendance'] as $student_id => $status){

        mysqli_query($conn,"
            UPDATE attendance
            SET status='$status'
            WHERE student_id='$student_id'
            AND attendance_date='$date'
        ");
    }

    echo "<script>
        alert('Attendance updated successfully!');
        window.location='update_attendance.php?class_id=$class_id&date=$date';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Attendance</title>

<style>

/* ===== GLOBAL ===== */
body{
    margin:0;
    background:#f5f6fa;
    font-family:Arial, Helvetica, sans-serif;
}

/* ===== TOPBAR ===== */
.topbar{
    background:#2c3e50;
    color:white;
    padding:14px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.profile{
    display:flex;
    align-items:center;
    gap:10px;
}
.profile img{
    width:36px;
    height:36px;
    border-radius:50%;
}

/* ===== LAYOUT ===== */
.layout{
    display:flex;
    min-height:calc(100vh - 60px);
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:240px;
    background:#34495e;
    padding:15px;
    display:flex;
    flex-direction:column;
    gap:6px;
}
.sidebar a{
    color:white;
    text-decoration:none;
    padding:12px;
    border-radius:6px;
    transition:0.2s;
}
.sidebar a:hover,
.sidebar a.active{
    background:rgba(255,255,255,0.15);
}

/* ===== MAIN ===== */
.main{
    flex:1;
    padding:25px;
}

/* ===== SECTION CARD ===== */
.section{
    background:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

/* ===== HEADER ===== */
.section h2{
    margin-bottom:10px;
    color:#2c3e50;
}

/* ===== FILTER FORM ===== */
.filter-box{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    align-items:center;
}

.filter-box select,
.filter-box input{
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
    min-width:180px;
}

/* ===== TABLE ===== */
.table-wrapper{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

thead{
    background: #f4f1f4;
    color: black;
}

th, td{
    padding:12px;
    text-align:left;
}

tbody tr{
    border-bottom:1px solid #eee;
}

tbody tr:hover{
    background:#f4f8fb;
}

/* ===== STATUS SELECT ===== */
.status-select{
    padding:6px 10px;
    border-radius:6px;
    border:1px solid #ccc;
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
.btn-small{
    padding:6px 10px;
    font-size:12px;
    border-radius:1px;
    margin-left:3px;
}

/* ===== ACTION AREA ===== */
.action-area{
    margin-top:15px;
    text-align:right;
}

/* ===== EMPTY ===== */
.empty{
    text-align:center;
    color:#888;
    padding:20px;
}


</style>

</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <div><b>Teacher Portal</b></div>
   <div class="profile" style="display:flex;align-items:center;gap:10px;">
    
    <div style="text-align:right;">
        <div style="font-weight:bold;"><?php echo $teacher_name; ?></div>
       
    </div>

    <img 
        src="../../uploads/profiles/<?php echo $teacher_photo; ?>" 
        style="width:40px;height:40px;border-radius:50%;object-fit:cover;"
        onerror="this.src='../assets/images/default.png';"
    >
</div>
</div>

<div class="layout">

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="../dashboard.php" class="active">Dashboard</a>
    <a href="take_attendance.php">Take Attendance</a>
    <a href="update_attendance.php">Update Attendance</a>
    <a href="view_attendance.php">View Attendance</a>
    <a href="../assessments/enter_marks.php">Enter Marks</a>
    <a href="../assessments/edit_marks.php">Edit Marks</a>
    <a href="../assessments/view_marks.php">View Marks</a>
    <a href="../reports/class_performance.php">Class Performance</a>
    <a href="../students/view_students.php">View Students</a>
    <a href="../profile/my_profile.php">Update Profile</a>
    <a href="../includes/logout.php">Logout</a>
</div>

<div class="main">

<!-- FILTER -->
<div class="section">
    <h2>Update Attendance</h2>

    <form method="GET" class="filter-box">
        <select name="class_id" onchange="this.form.submit()">
            <option value="">Select Class</option>
            <?php while($c = mysqli_fetch_assoc($classes)): ?>
                <option value="<?php echo $c['class_id']; ?>"
                <?php if($class_id == $c['class_id']) echo "selected"; ?>>
                    <?php echo $c['class_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="date" name="date" value="<?php echo $date; ?>" onchange="this.form.submit()">
    </form>
</div>

<!-- TABLE -->
<?php if($class_id && count($students) > 0): ?>

<div class="section">

<form method="POST">

<div class="table-wrapper">

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Admission No</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        <?php $i=1; foreach($students as $s): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $s['name']; ?></td>
            <td><?php echo $s['admission_number']; ?></td>
           <td>

    <label class="status present">
        <input type="radio"
            name="attendance[<?php echo $s['student_id']; ?>]"
            value="Present"
            <?php if($s['status'] == "Present") echo "checked"; ?>
            required>
        Present
    </label>

    <label class="status absent">
        <input type="radio"
            name="attendance[<?php echo $s['student_id']; ?>]"
            value="Absent"
            <?php if($s['status'] == "Absent") echo "checked"; ?>>
        Absent
    </label>

</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>

<div class="action-area">
    <button class="btn" name="update_attendance">Update Attendance</button>
</div>

</form>

</div>

<?php elseif($class_id): ?>

<div class="section empty">
    No students found for this class.
</div>

<?php endif; ?>

</div>
</div>

</body>
</html>