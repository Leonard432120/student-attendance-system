<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){
    echo "Access Denied!";
    exit();
}

include("../../config/database.php");

if(!isset($_GET['id'])){
    echo "Student ID missing!";
    exit();
}

$student_id = $_GET['id'];

/* FETCH STUDENT */
$query = mysqli_query($conn, "
    SELECT s.student_id, s.admission_number, 
           u.name, u.username, u.email, u.phone,
           c.class_name
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    LEFT JOIN classes c ON s.class_id = c.class_id
    WHERE s.student_id = '$student_id'
");

$student = mysqli_fetch_assoc($query);

if(!$student){
    echo "Student not found!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Student</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <style>
        /* ===== PROFILE HEADER ===== */
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

        .profile-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .profile-sub {
            font-size: 14px;
            opacity: 0.8;
        }

        /* ===== DETAIL GRID ===== */
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
            transition: 0.3s;
        }

        .detail-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .detail-box label {
            font-size: 12px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-box p {
            margin: 8px 0 0;
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* ===== ACTION BUTTONS ===== */
        .action-bar {
            margin-top: 20px;
            display: flex;
            gap: 10px;
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
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="students/manage_students.php">Students</a>
    <a href="teachers/manage_teachers.php">Teachers</a>
    <a href="classes/manage_classes.php">Classes</a>
    <a href="subjects/manage_subjects.php">Subjects</a>
    <a href="reports/attendance_report.php">Attendance Reports</a>
    <a href="reports/performance_report.php">Performance Reports</a>
    <a href="settings/system_settings.php">System Settings</a>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- PROFILE HEADER -->
    <div class="profile-header">
        <div>
          Student Profile Overview
        </div>
        <div>
            <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-edit">Edit Student</a>
        </div>
    </div>

    <!-- DETAILS -->
    <div class="card">

        <div class="detail-grid">

            <div class="detail-box">
                <label>Full Name</label>
                <p><?php echo htmlspecialchars($student['name']); ?></p>
            </div>

            <div class="detail-box">
                <label>Username</label>
                <p><?php echo htmlspecialchars($student['username']); ?></p>
            </div>

            <div class="detail-box">
                <label>Email</label>
                <p><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></p>
            </div>

            <div class="detail-box">
                <label>Phone</label>
                <p><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></p>
            </div>

            <div class="detail-box">
                <label>Admission Number</label>
                <p><?php echo htmlspecialchars($student['admission_number']); ?></p>
            </div>

            <div class="detail-box">
                <label>Class</label>
                <p><?php echo htmlspecialchars($student['class_name'] ?? 'Not Assigned'); ?></p>
            </div>

        </div>

        <!-- ACTIONS -->
        <div class="action-bar">
            <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-edit">
                Edit
            </a>

            <a href="manage_students.php" class="btn">
                Back
            </a>
        </div>

    </div>

</div>

</div>

</body>
</html>