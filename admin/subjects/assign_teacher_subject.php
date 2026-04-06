<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");
include("../../includes/functions.php");

// Ensure class_id and subject_id are provided
if(!isset($_GET['class_id'], $_GET['subject_id'])){
    exit("Class ID or Subject ID missing");
}

$class_id = intval($_GET['class_id']);
$subject_id = intval($_GET['subject_id']);

// Fetch class info
$class = mysqli_query($conn, "SELECT * FROM classes WHERE class_id='$class_id'");
$class_data = mysqli_fetch_assoc($class);
if(!$class_data){
    exit("Class not found");
}

// Fetch subject info
$subject = mysqli_query($conn, "SELECT * FROM subjects WHERE subject_id='$subject_id' AND class_id='$class_id'");
$subject_data = mysqli_fetch_assoc($subject);
if(!$subject_data){
    exit("Subject not found in this class");
}

// Fetch all teachers
$teachers = mysqli_query($conn, "
    SELECT teachers.teacher_id, users.name 
    FROM teachers
    JOIN users ON teachers.user_id = users.user_id
    ORDER BY users.name ASC
");

// Fetch current assigned teacher (if any)
$current_teacher = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT t.teacher_id, u.name
    FROM class_subjects cs
    JOIN teachers t ON t.teacher_id = cs.teacher_id
    JOIN users u ON u.user_id = t.user_id
    WHERE cs.class_id='$class_id' AND cs.subject_id='$subject_id'
"));

// Handle form submission
if(isset($_POST['assign'])) {
    $teacher_id = intval($_POST['teacher_id']);

    // Check if a record exists
    $check = mysqli_query($conn, "
        SELECT * FROM class_subjects
        WHERE class_id='$class_id' AND subject_id='$subject_id'
    ");
    
    if(mysqli_num_rows($check) > 0){
        // Update
        mysqli_query($conn, "
            UPDATE class_subjects 
            SET teacher_id='$teacher_id'
            WHERE class_id='$class_id' AND subject_id='$subject_id'
        ");
    } else {
        // Insert
        mysqli_query($conn, "
            INSERT INTO class_subjects(class_id, subject_id, teacher_id)
            VALUES('$class_id', '$subject_id', '$teacher_id')
        ");
    }

    logAction($conn, $_SESSION['user_id'], "Assigned teacher to subject {$subject_data['subject_name']} in class {$class_data['class_name']}");

    header("Location: view_subject.php?id=$subject_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Teacher - <?php echo htmlspecialchars($subject_data['subject_name']); ?></title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .form-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            max-width: 500px;
            margin: 40px auto;
        }
        .form-card h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-primary { background: #3498db; }
        .btn-danger { background: #7f8c8d; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }
    </style>
</head>

<body>

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

    <div class="content">

        <div class="form-card">
            <h2>Assign Teacher for "<?php echo htmlspecialchars($subject_data['subject_name']); ?>"</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Select Teacher</label>
                    <select name="teacher_id" required>
                        <option value="">-- Choose Teacher --</option>
                        <?php while($t = mysqli_fetch_assoc($teachers)) { ?>
                            <option value="<?php echo $t['teacher_id']; ?>"
                                <?php if($current_teacher && $current_teacher['teacher_id'] == $t['teacher_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($t['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="btn-group">
                    <button type="submit" name="assign" class="btn btn-primary">Assign Teacher</button>
                    <a href="view_class.php?id=<?php echo $class_id; ?>" class="btn btn-danger">Back</a>
                </div>
            </form>
        </div>

    </div>
</div>

</body>
</html>