<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

/* FETCH CLASSES */
$classes = mysqli_query($conn, "
SELECT * FROM classes ORDER BY class_name ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
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
<div class="content">

<!-- TITLE CARD -->
<div class="card">
    <h2>Manage Classes</h2>
    <p>Total Classes: <b><?php echo mysqli_num_rows($classes); ?></b></p>
</div>

<!-- ACTION BAR -->
<div class="card">
    <a href="add_class.php" class="btn">+ Add Class</a>
</div>

<!-- TABLE -->
<div class="card">

<?php if(mysqli_num_rows($classes) > 0){ ?>

<table>
<tr>
    <th>ID</th>
    <th>Class Name</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($classes)){ ?>

<tr>
    <td><?php echo $row['class_id']; ?></td>
    <td><?php echo htmlspecialchars($row['class_name']); ?></td>

 <td class="actions">

    <!-- EXISTING GOOD BUTTONS -->
    <a href="view_class.php?id=<?php echo $row['class_id']; ?>" class="btn_view" style="background-color:#3498db; color:#fff;">
        View & Manage
    </a>

    <a href="edit_class.php?id=<?php echo $row['class_id']; ?>" class="btn btn-edit">
        Edit
    </a>

    <a href="delete_class.php?id=<?php echo $row['class_id']; ?>" class="btn btn-delete"
       onclick="return confirm('Are you sure?')">
        Delete
    </a>

</td>
    </td>
</tr>

<?php } ?>

</table>

<?php } else { ?>

<p>No classes found.</p>

<?php } ?>

</div>

</div>
</div>

</body>
</html>