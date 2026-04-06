<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $class_name = trim(mysqli_real_escape_string($conn, $_POST['class_name']));

    if(empty($class_name)){
        $error = "Class name cannot be empty.";
    } else {
        // Check if class already exists
        $check = mysqli_query($conn, "SELECT * FROM classes WHERE class_name='$class_name'");
        if(mysqli_num_rows($check) > 0){
            $error = "Class with this name already exists.";
        } else {
            // Insert new class
            $insert = mysqli_query($conn, "INSERT INTO classes (class_name) VALUES ('$class_name')");
            if($insert){
                $success = "Class '$class_name' added successfully.";
                $_POST['class_name'] = '';
            } else {
                $error = "Failed to add class. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Class</title>
    <link rel="stylesheet" href="../../assets/css/form.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #2c3e50; }
        .dashboard { display: flex; min-height: 100vh; }
        .sidebar { width: 220px; background: #34495e; color: #fff; padding: 20px 0; display: flex; flex-direction: column; }
        .sidebar a { padding: 12px 25px; color: #fff; font-weight: bold; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active {  background:#2c3e50; color:#3498db; }
        .content { flex: 1; padding: 25px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #2c3e50; color: #fff; padding: 15px 25px; }
        .header a { color: #fff; margin-left: 15px; }
        .card { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2 { margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; }
        .btn { padding: 10px 18px; border-radius: 8px; font-weight: bold; cursor: pointer; background: #3498db; color: #fff; border: none; transition: 0.3s; }
        .btn:hover { opacity: 0.9; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 8px; }
        .error { background: #e74c3c; color: #fff; }
        .success { background: #1abc9c; color: #fff; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <span class="dashboard-title">Admin Portal</span>
    </div>
    <div class="header-right">
        <span><?php echo $_SESSION['name']; ?></span>
        <a href="../../includes/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">
    <!-- Sidebar -->
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

    <!-- Content -->
    <div class="content">
        <div class="card">
            <h2>Add New Class</h2>

            <?php if($error){ echo "<div class='message error'>$error</div>"; } ?>
            <?php if($success){ echo "<div class='message success'>$success</div>"; } ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="class_name">Class Name</label>
                    <input type="text" id="class_name" name="class_name" value="<?php echo htmlspecialchars($_POST['class_name'] ?? ''); ?>" placeholder="Enter class name">
                </div>
                <button type="submit" class="btn">Add Class</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>