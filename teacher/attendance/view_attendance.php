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

/* CLASSES */
$classes = mysqli_query($conn,"
    SELECT * FROM classes WHERE class_teacher='$teacher_id'
");

$class_id = $_GET['class_id'] ?? null;
$date = $_GET['date'] ?? date("Y-m-d");

/* RECORDS */
$records = [];

if($class_id){

    $query = mysqli_query($conn,"
        SELECT u.name, s.admission_number, a.status, a.attendance_date
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN users u ON s.user_id = u.user_id
        WHERE a.class_id='$class_id'
        AND a.attendance_date='$date'
        ORDER BY u.name
    ");

    while($row = mysqli_fetch_assoc($query)){
        $records[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>View Attendance</title>

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
    min-height:100vh;
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
    color:#ecf0f1;
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

/* ===== HEADER CARD ===== */
.welcome{
    background:white;
    padding:18px;
    border-radius:10px;
    margin-bottom:20px;
    
}

/* ===== SECTION ===== */
.section{
    background:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 3px 10px rgba(0,0,0,0.06);
}

/* ===== FILTER ===== */
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

/* ===== STATUS BADGES ===== */
.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
    display:inline-block;
}

.present{
    background: #397d3f;
    color: #c0cfc1;
}

.absent{
    background:#ffebee;
    color:#b71c1c;
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

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="welcome">
        <h2>View Attendance</h2>
        <p>Check attendance records by class and date</p>
    </div>

    <!-- FILTER -->
    <div class="section">

        <form method="GET">
            <div class="filter-box">

                <select name="class_id" onchange="this.form.submit()">
                    <option value="">-- Select Class --</option>

                    <?php while($c = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?php echo $c['class_id']; ?>"
                        <?php if($class_id == $c['class_id']) echo "selected"; ?>>
                            <?php echo $c['class_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <input type="date" name="date"
                    value="<?php echo $date; ?>"
                    onchange="this.form.submit()">

            </div>
        </form>

    </div>

    <!-- TABLE -->
    <?php if($class_id): ?>

    <div class="section">

        <h3>Attendance Records</h3>

        <?php if(count($records) > 0): ?>

        <div class="table-wrapper">
            <table>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Admission No</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1; foreach($records as $r): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $r['name']; ?></td>
                        <td><?php echo $r['admission_number']; ?></td>
                        <td>
                            <span class="status <?php echo strtolower($r['status']); ?>">
                                <?php echo $r['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

        <?php else: ?>
            <div class="empty">No attendance records found for this date.</div>
        <?php endif; ?>

    </div>

    <?php endif; ?>

</div>
</div>

</body>
</html>