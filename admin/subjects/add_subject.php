<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){
    echo "Access Denied!";
    exit();
}

include("../../config/database.php");

$error = "";

/* GET CLASSES */
$classes = mysqli_query($conn, "SELECT * FROM classes");

/* HANDLE FORM */
if($_SERVER['REQUEST_METHOD'] == "POST"){

    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $class_id = $_POST['class_id'];

    if(empty($subject_name) || empty($class_id)){
        $error = "All fields are required!";
    }
    else {

        /* CHECK DUPLICATE */
        $check = mysqli_query($conn,"
            SELECT * FROM subjects 
            WHERE subject_name='$subject_name' 
            AND class_id='$class_id'
        ");

        if(mysqli_num_rows($check) > 0){
            $error = "Subject already exists for this class!";
        }
        else {

            mysqli_query($conn,"
                INSERT INTO subjects(subject_name, class_id)
                VALUES('$subject_name', '$class_id')
            ");

            header("Location: manage_subjects.php?success=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Subject</title>

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
    <a href="manage_subjects.php" class="active">Subjects</a>
    <a href="../reports/attendance_report.php">Attendance Reports</a>
    <a href="../reports/performance_report.php">Performance Reports</a>
    <a href="../settings/system_settings.php">System Settings</a>
</div>

<!-- CONTENT -->
<div class="content">

<div class="page-wrapper">

    <div class="page-header">
        <h2>Add Subject</h2>
    </div>

    <?php if($error != ""){ ?>
        <div class="form-message form-error">
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <div class="form-card">

        <form method="POST">

            <div class="form-grid">

                <div class="form-group full-width">
                    <label>Subject Name</label>
                    <input type="text" name="subject_name" required>
                </div>

                <div class="form-group full-width">
                    <label>Class</label>
                    <select name="class_id" required>
                        <option value="">-- Select Class --</option>

                        <?php while($c = mysqli_fetch_assoc($classes)){ ?>
                            <option value="<?php echo $c['class_id']; ?>">
                                <?php echo $c['class_name']; ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-success">Add Subject</button>
                <a href="manage_subjects.php" class="btn btn-danger">Cancel</a>
            </div>

        </form>

    </div>

</div>

</div>
</div>

</body>
</html>