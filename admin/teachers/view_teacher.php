<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

if(!isset($_GET['id'])) exit("Teacher ID missing");

$id = $_GET['id'];

$query = mysqli_query($conn,"
SELECT t.teacher_id,t.employee_number,
u.name,u.username,u.email,u.phone
FROM teachers t
JOIN users u ON t.user_id=u.user_id
WHERE t.teacher_id='$id'
");

$teacher = mysqli_fetch_assoc($query);

if(!$teacher) exit("Teacher not found!");
?>

<!DOCTYPE html>
<html>
<head>
<title>View Teacher</title>
<link rel="stylesheet" href="../../assets/css/admin.css">

<style>
.profile-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 25px;
    border-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 15px;
}

.detail-box {
    background: #fff;
    padding: 18px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.detail-box label {
    font-size: 12px;
    color: #777;
}

.detail-box p {
    margin: 8px 0 0;
    font-weight: bold;
    color: #2c3e50;
}

.action-bar {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}
</style>

</head>

<body>

<div class="header">
    <div class="header-left">Admin Portal</div>
    <div class="header-right">
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

<div class="profile-header">

    <div>
        <h3 style="margin:0;">Teacher Profile Overview</h3>
    </div>

    <div>
        <a href="edit_teacher.php?id=<?php echo $teacher['teacher_id']; ?>" class="btn btn-edit">
            Edit Teacher
        </a>
    </div>

</div>

<div class="card">

<div class="detail-grid">

<div class="detail-box">
<label>Name</label>
<p><?php echo $teacher['name']; ?></p>
</div>

<div class="detail-box">
<label>Username</label>
<p><?php echo $teacher['username']; ?></p>
</div>

<div class="detail-box">
<label>Email</label>
<p><?php echo $teacher['email'] ?? 'N/A'; ?></p>
</div>

<div class="detail-box">
<label>Phone</label>
<p><?php echo $teacher['phone'] ?? 'N/A'; ?></p>
</div>

<div class="detail-box">
<label>Employee Number</label>
<p><?php echo $teacher['employee_number']; ?></p>
</div>

</div>

<div class="action-bar">
<a href="edit_teacher.php?id=<?php echo $teacher['teacher_id']; ?>" class="btn btn-edit">Edit</a>
<a href="manage_teachers.php" class="btn">Back</a>
</div>

</div>

</div>

</div>

</body>
</html>