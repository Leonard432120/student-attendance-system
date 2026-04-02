<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'teacher'){ exit("Access Denied!"); }

include("../../config/database.php");

/* GET STUDENTS FROM USERS + STUDENTS */
$students = mysqli_query($conn, "
    SELECT 
        u.user_id,
        u.name,
        u.email,
        s.student_id,
        s.admission_number,
        c.class_name
    FROM users u
    INNER JOIN students s ON u.user_id = s.user_id
    LEFT JOIN classes c ON s.class_id = c.class_id
    WHERE u.role = 'student'
    ORDER BY c.class_name, u.name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>View Students</title>
<link rel="stylesheet" href="../../assets/css/style.css" />
<style>
/* Style for the table to match your dashboard */
h1 {
    color: #2c3e50;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
thead {
    background: #f4f1f4;
    color: black;
    padding: 14px 12px;
    text-align: left;
    font-weight: 500;
}
thead th {
   background: #f4f1f4;
    color: black;
    padding: 14px 12px;
    text-align: left;
    font-weight: 500;
}
tbody td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
}
tbody tr:hover {
    background-color: #f9f9f9;
}
@media(max-width: 768px){
    table {
        display: block;
        overflow-x: auto;
    }
}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar" style="display:flex; justify-content:space-between; align-items:center; padding:10px 20px; background:#2c3e50; color:#fff; box-shadow:0 2px 4px rgba(0,0,0,0.1); height:60px;">
    <div><b style="color:#ecf0f1;">Teacher Portal</b></div>
    <div class="profile" style="display:flex; align-items:center;">
        <span style="margin-right:10px;"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        <img src="../../assets/images/default.png" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
    </div>
</div>

<div class="layout" style="display:flex; min-height: calc(100vh - 50px);">

<!-- SIDEBAR -->
<div class="sidebar" style="width:220px; background:#2c3e50; color:#ecf0f1; padding:20px;">
    <a href="../dashboard.php" style="display:block; padding:10px; color:#ecf0f1; text-decoration:none;">Dashboard</a>
    <a href="../attendance/take_attendance.php" style="display:block; padding:10px; color:#ecf0f1; text-decoration:none;">Take Attendance</a>
    <a href="../attendance/update_attendance.php" style="display:block; padding:10px; color:#ecf0f1; text-decoration:none;">Update Attendance</a>
    <a href="../attendance/view_attendance.php" style="display:block; padding:10px; color:#ecf0f1; text-decoration:none;">View Attendance</a>
    <a href="../assessments/enter_marks.php" style="display:block; padding:10px; color:#ecf0f1; text-decoration:none;">Enter Marks</a>
    <a href="../assessments/edit_marks.php" style="display:block; padding:10px; color:#ecf0f1; text-decoration:none;">Edit Marks</a>
    <a href="../assessments/view_marks.php" style="display:block; padding:10px; color:#ecf0f1; text-decoration:none;">View Marks</a>
    <a href="view_students.php" class="active" style="display:block; padding:10px; background:#34495e; color:#fff; text-decoration:none;">View Students</a>
    <a href="../../includes/logout.php" style="display:block; padding:10px; color:#ecf0f1; text-decoration:none;">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main" style="flex:1; padding:20px;">
    <h1>Students List</h1>
    <p>All registered students (from users + students table)</p>

    <div class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>Admission No</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                while($row = mysqli_fetch_assoc($students)){
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['admission_number'] ?? 'N/A'); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</body>
</html>