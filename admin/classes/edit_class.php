<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");
include("../../includes/functions.php");

if(!isset($_GET['id'])){
    exit("Class ID missing");
}

$id = intval($_GET['id']);

$class = mysqli_query($conn, "SELECT * FROM classes WHERE class_id='$id'");
$data = mysqli_fetch_assoc($class);

if(!$data){
    exit("Class not found");
}

if(isset($_POST['update'])){

    $class_name = mysqli_real_escape_string($conn, $_POST['class_name']);

    mysqli_query($conn, "
        UPDATE classes SET 
        class_name='$class_name'
        WHERE class_id='$id'
    ");

    // LOG ACTION
    logAction($conn, $_SESSION['user_id'], "Updated class ID: $id");

    header("Location: manage_classes.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Class</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>

<div class="header">
    <h2>Edit Class</h2>
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

<div class="card">

<form method="POST">

    <label>Class Name</label>
    <input type="text" name="class_name" value="<?php echo htmlspecialchars($data['class_name']); ?>" required>

    <br><br>

    <button type="submit" name="update" class="btn">Update Class</button>

</form>

</div>

</div>
</div>

</body>
</html>