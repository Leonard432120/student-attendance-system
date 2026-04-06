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

/* TEACHER ID */
$teacher_query = mysqli_query($conn,"
    SELECT teacher_id FROM teachers WHERE user_id='$user_id'
");
$teacher = mysqli_fetch_assoc($teacher_query);
$teacher_id = $teacher['teacher_id'] ?? 0;

/* CLASSES */
$classes = mysqli_query($conn,"
    SELECT * FROM classes WHERE class_teacher='$teacher_id'
");

$class_id = $_GET['class_id'] ?? null;

/* STUDENTS */
$students = [];

if($class_id){
    $students_query = mysqli_query($conn,"
        SELECT s.student_id, s.admission_number, u.name
        FROM students s
        JOIN users u ON s.user_id = u.user_id
        WHERE s.class_id='$class_id'
    ");

    while($row = mysqli_fetch_assoc($students_query)){
        $students[] = $row;
    }
}

/* SAVE ATTENDANCE */
if(isset($_POST['save_attendance'])){

    $date = date("Y-m-d");

    foreach($_POST['attendance'] as $student_id => $status){

        $check = mysqli_query($conn,"
            SELECT * FROM attendance 
            WHERE student_id='$student_id'
            AND attendance_date='$date'
        ");

        if(mysqli_num_rows($check) == 0){
            mysqli_query($conn,"
                INSERT INTO attendance (student_id, class_id, status, attendance_date)
                VALUES ('$student_id', '$class_id', '$status', '$date')
            ");
        }
    }

    echo "<script>
        alert('Attendance saved successfully!');
        window.location='../dashboard.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Take Attendance</title>

<link rel="stylesheet" href="../../assets/css/form.css">

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

/* ===== BUTTON ===== */
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


/* ================= BUTTONS ================= */
.btn-group{
    margin-top:15px;
}

.btn-primary{
    background:#2c3e50;
    color:white;
    padding:10px 15px;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

.btn-primary:hover{
    background:#1f2a36;
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
    <!-- MAIN -->
    <div class="main">

        <div class="welcome">
            <h2>Take Attendance</h2>
            <p>Select a class and mark student attendance</p>
        </div>

        <!-- CLASS SELECT -->
        <div class="section">

            <form method="GET">
                <div class="form-group">
                    <label>Select Class</label>
                    <select name="class_id" onchange="this.form.submit()">
                        <option value="">---Choose Class---</option>

                        <?php while($c = mysqli_fetch_assoc($classes)): ?>
                            <option value="<?php echo $c['class_id']; ?>"
                                <?php if($class_id == $c['class_id']) echo "selected"; ?>>
                                <?php echo $c['class_name']; ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>
            </form>

        </div>

        <!-- ATTENDANCE TABLE -->
        <?php if($class_id && count($students) > 0): ?>

        <div class="section">

            <h3>Student Attendance</h3>

            <form method="POST">

                <div class="table-wrapper">

                    <table class="attendance-table">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Admission No</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $no=1; foreach($students as $s): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $s['name']; ?></td>
                                <td><?php echo $s['admission_number']; ?></td>
                                <td>

                                    <label class="status present">
                                        <input type="radio"
                                            name="attendance[<?php echo $s['student_id']; ?>]"
                                            value="Present" required>
                                        Present
                                    </label>

                                    <label class="status absent">
                                        <input type="radio"
                                            name="attendance[<?php echo $s['student_id']; ?>]"
                                            value="Absent">
                                        Absent
                                    </label>

                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>

                <div class="action-area">
                     <button type="submit" name="save_attendance" class="btn">
                       Save Attendance
                     </button>
                </div>

            </form>

        </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>