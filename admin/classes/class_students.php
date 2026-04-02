<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

if(!isset($_GET['id'])){
    exit("Class ID missing");
}

$class_id = intval($_GET['id']);

/* GET CLASS */
$class = mysqli_query($conn, "
    SELECT * FROM classes 
    WHERE class_id='$class_id'
");

$class_data = mysqli_fetch_assoc($class);

if(!$class_data){
    exit("Class not found");
}

/* GET STUDENTS */
$students = mysqli_query($conn, "
    SELECT 
        students.student_id,
        students.admission_number,
        users.name,
        users.email,
        COALESCE(students.phone, users.phone) AS phone
    FROM students
    JOIN users ON students.user_id = users.user_id
    WHERE students.class_id = '$class_id'
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Class Students</title>
<link rel="stylesheet" href="../../assets/css/admin.css">

<style>
/* HEADER CARD */
.class-header{
    background: linear-gradient(135deg,#3498db,#2c3e50);
    color:white;
    padding:20px;
    border-radius:12px;
}

/* BADGE */
.badge{
    background:#2ecc71;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

/* TABLE IMPROVEMENT */
table tr:hover{
    background:#f9f9f9;
}

/* BACK BUTTON */
.back-btn{
    display:inline-block;
    margin-top:15px;
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
        <a href="../../includes/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="../dashboard.php">Dashboard</a>
    <a href="../students/manage_students.php">Students</a>
    <a href="../teachers/manage_teachers.php">Teachers</a>
    <a href="manage_classes.php" class="active">Classes</a>
    <a href="../subjects/manage_subjects.php">Subjects</a>
    <a href="../reports/attendance_report.php">Attendance</a>
    <a href="../reports/performance_report.php">Performance</a>
    <a href="../settings/system_settings.php">Settings</a>
</div>

<!-- CONTENT -->
<div class="content">

<!-- HEADER CARD -->
<div class="card">
    <h2><?php echo htmlspecialchars($class_data['class_name']); ?></h2>
    <p>
        Total Students:
        <span class=""><?php echo mysqli_num_rows($students); ?></span>
    </p>
</div>

<!-- TABLE -->
<div class="card">

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Admission No</th>
    <th>Phone</th>
</tr>

<?php while($row = mysqli_fetch_assoc($students)){ ?>
<tr>
    <td><?php echo $row['student_id']; ?></td>
    <td><?php echo $row['name'] ?? 'N/A'; ?></td>
    <td><?php echo $row['email'] ?? 'N/A'; ?></td>
    <td><?php echo $row['admission_number']; ?></td>
    <td><?php echo !empty($row['phone']) ? $row['phone'] : 'N/A'; ?></td>
</tr>
<?php } ?>

</table>

<a href="view_class.php?id=<?php echo $class_id; ?>" class="btn back-btn">
    ← Back to Class
</a>

</div>

</div>
</div>

</body>
</html>