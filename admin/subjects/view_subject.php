<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

if(!isset($_GET['id'])){
    exit("Subject ID missing");
}

$subject_id = intval($_GET['id']);

/* GET SUBJECT + CLASS INFO */
$subject = mysqli_query($conn, "
    SELECT 
        subjects.subject_id,
        subjects.subject_name,
        subjects.class_id,
        classes.class_name
    FROM subjects
    LEFT JOIN classes ON subjects.class_id = classes.class_id
    WHERE subjects.subject_id = '$subject_id'
");

$subject_data = mysqli_fetch_assoc($subject);
if(!$subject_data){
    exit("Subject not found");
}

/* GET ASSIGNED TEACHER */
$teacher = null;
if(!empty($subject_data['class_id'])){
    $teacher_query = mysqli_query($conn, "
        SELECT u.user_id, u.name
        FROM class_subjects cs
        LEFT JOIN teachers t ON cs.teacher_id = t.teacher_id
        LEFT JOIN users u ON t.user_id = u.user_id
        WHERE cs.class_id = '{$subject_data['class_id']}' AND cs.subject_id = '{$subject_data['subject_id']}'
        LIMIT 1
    ");
    $teacher = mysqli_fetch_assoc($teacher_query);
}

/* GET STUDENTS IN CLASS */
$students = null;
if(!empty($subject_data['class_id'])){
    $students = mysqli_query($conn, "
        SELECT students.student_id, users.name, students.admission_number
        FROM students
        JOIN users ON students.user_id = users.user_id
        WHERE students.class_id = '{$subject_data['class_id']}'
        ORDER BY users.name ASC
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Subject - <?php echo htmlspecialchars($subject_data['subject_name']); ?></title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #2c3e50; }
        .dashboard { display: flex; min-height: 100vh; }
        .sidebar { width: 220px; background: #34495e; color: #fff; padding: 20px 0; display: flex; flex-direction: column; }
        .sidebar a { padding: 12px 25px; color: #fff; font-weight: bold; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active {  background: #2c3e50;color:#3498db; }
        .content { flex: 1; padding: 25px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #2c3e50; color: #fff; padding: 15px 25px; }
        .header a { color: #fff; margin-left: 15px; }
        .card { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        h2, h3 { margin-top: 0; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px; }
        .stat-box { background: #f9fbfd; padding: 15px; border-radius: 10px; text-align: center; }
        .stat-box h4 { font-size: 12px; color: #777; margin-bottom: 5px; }
        .stat-box p { font-size: 18px; font-weight: bold; color: #2c3e50; margin: 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; }
        th { background: #3498db; color: #5f3636; text-align: left; }
        td { font-size: 15px; }
        .btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; color: #fff; background: #3498db; transition: 0.3s; }
        .btn:hover { opacity: 0.9; }
        .no-teacher { color: #e74c3c; font-weight: bold; }
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

        <!-- Subject Header Card -->
        <div class="card">
            <h2><?php echo htmlspecialchars($subject_data['subject_name']); ?></h2>
            <p style="color:#777;">Subject Overview & Class Information</p>

            <div class="dashboard-grid">
                <div class="stat-box">
                    <h4>Subject ID</h4>
                    <p><?php echo $subject_data['subject_id']; ?></p>
                </div>

                <div class="stat-box">
                    <h4>Assigned Class</h4>
                    <p><?php echo $subject_data['class_name'] ?? 'Not Assigned'; ?></p>
                </div>

                <div class="stat-box">
                    <h4>Assigned Teacher</h4>
                    <p>
                        <?php 
                        if($teacher){
                            echo htmlspecialchars($teacher['name']);
                        } else {
                            echo '<span class="no-teacher">Not Assigned</span>';
                        }
                        ?>
                    </p>
                    <a href="assign_teacher_subject.php?class_id=<?php echo $subject_data['class_id']; ?>&subject_id=<?php echo $subject_id; ?>" class="btn" style="margin-top:5px; display:inline-block;">
                        <?php echo $teacher ? 'Change Teacher' : 'Assign Teacher'; ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card">
            <h3>Students in This Class</h3>
            <?php if(!empty($subject_data['class_id']) && $students && mysqli_num_rows($students) > 0){ ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Admission Number</th>
                    </tr>
                    <?php while($s = mysqli_fetch_assoc($students)){ ?>
                        <tr>
                            <td><?php echo $s['student_id']; ?></td>
                            <td><?php echo htmlspecialchars($s['name']); ?></td>
                            <td><?php echo htmlspecialchars($s['admission_number']); ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p style="color:#888;">No students found in this class.</p>
            <?php } ?>
        </div>

        

    </div>

</div>

</body>
</html>