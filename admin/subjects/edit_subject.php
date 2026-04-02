<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){
    echo "Access Denied!";
    exit();
}

include("../../config/database.php");

if(!isset($_GET['id'])){
    exit("Subject ID missing");
}

$subject_id = intval($_GET['id']);

/* GET SUBJECT */
$subject = mysqli_query($conn,"
    SELECT * FROM subjects WHERE subject_id='$subject_id'
");

$data = mysqli_fetch_assoc($subject);

if(!$data){
    exit("Subject not found");
}

/* GET CLASSES */
$classes = mysqli_query($conn,"SELECT * FROM classes");

/* HANDLE UPDATE */
if($_SERVER['REQUEST_METHOD'] == "POST"){

    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $class_id = $_POST['class_id'];

    mysqli_query($conn,"
        UPDATE subjects 
        SET subject_name='$subject_name',
            class_id='$class_id'
        WHERE subject_id='$subject_id'
    ");

    header("Location: manage_subjects.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>

    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/form.css">
</head>

<body>

<!-- HEADER -->
<div class="header">
    <h3>Admin Dashboard</h3>
    <div>
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
    <a href="manage_subjects.php">Subjects</a>
    <a href="../reports/attendance_report.php">Attendance Reports</a>
    <a href="../reports/performance_report.php">Performance Reports</a>
    <a href="../settings/system_settings.php">System Settings</a>
</div>

<!-- CONTENT -->
<div class="content">

<div class="page-wrapper">

    <div class="page-header">
        <h2>Edit Subject</h2>
    </div>

    <div class="form-card">

        <form method="POST">

            <div class="form-grid">

                <div class="form-group full-width">
                    <label>Subject Name</label>
                    <input type="text" name="subject_name"
                        value="<?php echo $data['subject_name']; ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>Class</label>
                    <select name="class_id" required>

                        <?php while($c = mysqli_fetch_assoc($classes)){ ?>
                            <option value="<?php echo $c['class_id']; ?>"
                                <?php if($data['class_id'] == $c['class_id']) echo "selected"; ?>>
                                <?php echo $c['class_name']; ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-success">Update Subject</button>
                <a href="manage_subjects.php" class="btn btn-danger">Cancel</a>
            </div>

        </form>

    </div>

</div>

</div>
</div>

</body>
</html>