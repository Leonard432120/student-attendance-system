<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");
include("../../includes/functions.php");

if(!isset($_GET['id'])){
    exit("Class ID missing");
}

$class_id = intval($_GET['id']);

/* CLASS INFO */
$class = mysqli_query($conn, "
    SELECT * FROM classes 
    WHERE class_id='$class_id'
");

$class_data = mysqli_fetch_assoc($class);

if(!$class_data){
    exit("Class not found");
}

/* SUBJECT COUNT */
$subject_count = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) as total 
    FROM subjects 
    WHERE class_id='$class_id'
"))['total'];

/* GET TEACHERS */
$teachers = mysqli_query($conn, "
    SELECT teachers.teacher_id, users.name 
    FROM teachers
    JOIN users ON teachers.user_id = users.user_id
    ORDER BY users.name ASC
");

/* CURRENT TEACHER */
$current_teacher_name = "Not assigned";

if(!empty($class_data['class_teacher'])){
    $tq = mysqli_query($conn,"
        SELECT users.name 
        FROM teachers 
        JOIN users ON teachers.user_id = users.user_id
        WHERE teachers.teacher_id='{$class_data['class_teacher']}'
    ");
    $tr = mysqli_fetch_assoc($tq);
    if($tr) $current_teacher_name = $tr['name'];
}

/* ASSIGN */
if(isset($_POST['assign'])){
    $teacher_id = intval($_POST['teacher_id']);

    mysqli_query($conn, "
        UPDATE classes 
        SET class_teacher='$teacher_id'
        WHERE class_id='$class_id'
    ");

    logAction($conn, $_SESSION['user_id'], 
        "Assigned teacher to class {$class_data['class_name']}"
    );

    header("Location: view_class.php?id=$class_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Assign Teacher</title>

<link rel="stylesheet" href="../../assets/css/admin.css">
<link rel="stylesheet" href="../../assets/css/form.css">

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
    <a href="../dashboard.php" class="active">Dashboard</a>
    <a href="../students/manage_students.php">Students</a>
    <a href="../teachers/manage_teachers.php">Teachers</a>
    <a href="../classes/manage_classes.php">Classes</a>
    <a href="../subjects/manage_subjects.php">Subjects</a>
    <a href="../reports/attendance_report.php">Attendance Reports</a>
    <a href="../reports/performance_report.php">Performance Reports</a>
    <a href="../settings/system_settings.php">System Settings</a>
</div>

<!-- CONTENT -->
<div class="content page-wrapper">

    <!-- HEADER -->
    <div class="page-header">
        <h2>Assign Teacher</h2>
    </div>

    <!-- FORM CARD -->
    <div class="form-card">

        <h3 style="margin-top:0;">
            <?php echo htmlspecialchars($class_data['class_name']); ?>
        </h3>

        <div class="form-grid">

            <div class="form-group">
                <label>Number of Subjects</label>
                <input type="text" value="<?php echo $subject_count; ?>" disabled>
            </div>

            <div class="form-group">
                <label>Current Teacher</label>
                <input type="text" value="<?php echo $current_teacher_name; ?>" disabled>
            </div>

        </div>

        <form method="POST">

            <div class="form-group" style="margin-top:15px;">
                <label>Select Teacher</label>
                <select name="teacher_id" required>
                    <option value="">-- Choose Teacher --</option>

                    <?php while($t = mysqli_fetch_assoc($teachers)){ ?>
                        <option value="<?php echo $t['teacher_id']; ?>">
                            <?php echo htmlspecialchars($t['name']); ?>
                        </option>
                    <?php } ?>

                </select>
            </div>

            <div class="btn-group">
                <button type="submit" name="assign" class="btn btn-primary">
                    Assign Teacher
                </button>

                <a href="view_class.php?id=<?php echo $class_id; ?>" class="btn btn-danger">
                    Back
                </a>
            </div>

        </form>

    </div>

</div>
</div>

</body>
</html>