<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

// Ensure class ID is provided
if(!isset($_GET['id'])){
    exit("Class ID missing");
}

$class_id = intval($_GET['id']);

// Fetch class info
$class = mysqli_query($conn, "SELECT * FROM classes WHERE class_id='$class_id'");
$class_data = mysqli_fetch_assoc($class);
if(!$class_data){
    exit("Class not found");
}

// Fetch students count
$students_count = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM students 
    WHERE class_id='$class_id'
"))['total'];

// Fetch subjects + assigned teachers
$subjects = mysqli_query($conn, "
    SELECT s.subject_id, s.subject_name, u.name AS teacher_name
    FROM subjects s
    LEFT JOIN class_subjects cs
        ON cs.subject_id = s.subject_id AND cs.class_id = s.class_id
    LEFT JOIN teachers t
        ON t.teacher_id = cs.teacher_id
    LEFT JOIN users u
        ON u.user_id = t.user_id
    WHERE s.class_id = '$class_id'
    ORDER BY s.subject_name ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Class - <?php echo htmlspecialchars($class_data['class_name']); ?></title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6fa; margin:0; padding:0; }

        /* ===== HEADER ===== */
        .header {
            background: #2c3e50;
            color: #fff;
            display: flex;
            justify-content: space-between;
            padding: 15px 25px;
            align-items: center;
        }
        .header a { color: #fff; text-decoration: none; margin-left: 15px; }
        .dashboard-title { font-size: 20px; font-weight: bold; }

        /* ===== SIDEBAR ===== */
        .dashboard { display: flex; }
        .sidebar {
            width: 220px;
            background: #34495e;
            min-height: 100vh;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: #ecf0f1;
            padding: 12px 20px;
            text-decoration: none;
            
            transition: 0.2s;
        }
        .sidebar a.active, .sidebar a:hover {  background:#2c3e50; color:#3498db; }

        /* ===== CONTENT ===== */
        .content {
            flex: 1;
            padding: 25px;
        }

        /* ===== HEADER CARD ===== */
        .profile-header {
            background: #fff;
            padding: 20px 25px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .profile-header h2 { margin:0; font-size: 24px; color: #2c3e50; }
        .profile-header div { font-weight: bold; color: #34495e; }

        /* ===== CLASS CARD ===== */
        .profile-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        /* ===== INFO GRID ===== */
        .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-top: 15px; }
        .detail-box {
            background: #f9fbfd;
            padding: 18px;
            border-radius: 10px;
            transition: 0.3s;
        }
        .detail-box:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .detail-box label { font-size: 12px; color: #777; text-transform: uppercase; }
        .detail-box p { margin: 8px 0 0; font-size: 18px; font-weight: bold; color: #2c3e50; }

        /* ===== ACTION BUTTONS ===== */
        .action-bar { margin-top: 25px; display: flex; flex-wrap: wrap; gap: 10px; }
        .action-bar a {
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            color: #fff;
            background-color:#3498db;
            transition: 0.3s;
        }
        .action-bar a:hover { transform: translateY(-2px); opacity: 0.9; }

        /* ===== TABLE ===== */
        table { width: 100%; border-collapse: collapse; margin-top: 25px; background: #fff; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #eee; text-align: left; }
        th {  background: #2c3e50; color: #5e2121; font-weight: bold; }
        td { font-size: 15px; color: #2c3e50; }
        .no-teacher { color: #e74c3c; font-weight: bold; }
        tr:hover { background: #f1f1f1; }

        /* Assign Teacher Button */
.btn-assign {
    display: inline-block;
    padding: 8px 14px;
    background-color: #3498db;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: 0.3s;
    text-align: center;
}

.btn-assign:hover {
    background-color: #2980b9; /* Darker blue on hover */
    transform: translateY(-2px);
    opacity: 0.9;
}

        /* Responsive */
        @media(max-width:768px){
            .profile-header, .profile-card { padding: 15px; }
            .action-bar a { padding: 10px 14px; font-size: 14px; }
            table, th, td { font-size: 13px; }
        }
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

        <!-- HEADER -->
        <div class="profile-header">
            <h2><?php echo htmlspecialchars($class_data['class_name']); ?> Overview</h2>
            <div>Total Students: <strong><?php echo $students_count; ?></strong></div>
        </div>

        <!-- CLASS DETAILS CARD -->
        <div class="profile-card">

            <div class="detail-grid">
                <div class="detail-box">
                    <label>Class ID</label>
                    <p><?php echo $class_data['class_id']; ?></p>
                </div>
                <div class="detail-box">
                    <label>Class Name</label>
                    <p><?php echo htmlspecialchars($class_data['class_name']); ?></p>
                </div>
            </div>

            <!-- ACTIONS -->
            <div class="action-bar">
                <a href="class_students.php?id=<?php echo $class_data['class_id']; ?>">View Students</a>
                <a href="assign_teacher.php?id=<?php echo $class_data['class_id']; ?>">Assign Class Teacher</a>
                <a href="../../admin/subjects/manage_subjects.php?class_id=<?php echo $class_data['class_id']; ?>">Manage Subjects</a>
            </div>

            <!-- SUBJECTS TABLE -->
            <h3 style="margin-top:30px; color:#2c3e50;">Subjects & Assigned Teachers</h3>
            <table>
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Assigned Teacher</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($subjects) > 0): ?>
                        <?php while($sub = mysqli_fetch_assoc($subjects)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sub['subject_name']); ?></td>
                                <td>
                                    <?php 
                                        echo $sub['teacher_name'] 
                                            ? htmlspecialchars($sub['teacher_name']) 
                                            : '<span class="no-teacher">Not Assigned</span>'; 
                                    ?>
                                </td>
                               <td>
    <a href="assign_teacher_subject.php?class_id=<?php echo $class_id; ?>&subject_id=<?php echo $sub['subject_id']; ?>" class="btn-assign">
        Assign Teacher
    </a>
</td>
                            </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No subjects found for this class.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>
</body>
</html>