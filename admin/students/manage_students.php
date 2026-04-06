<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){ echo "Access Denied!"; exit(); }

include("../../config/database.php");

/* SEARCH & FILTERS */
$search = $_GET['search'] ?? "";
$class_filter = $_GET['class'] ?? "";

/* PAGINATION SETTINGS */
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

/* BASE QUERY */
$query = "
SELECT s.student_id, u.name, u.username, s.admission_number, c.class_name
FROM students s
JOIN users u ON s.user_id = u.user_id
LEFT JOIN classes c ON s.class_id = c.class_id
WHERE 1
";

/* APPLY FILTERS */
if($search != ""){
    $query .= " AND (u.name LIKE '%$search%' OR s.admission_number LIKE '%$search%')";
}

if($class_filter != ""){
    $query .= " AND s.class_id='$class_filter'";
}

/* COUNT QUERY (FOR PAGINATION) */
$count_query = "
SELECT COUNT(*) as total
FROM students s
JOIN users u ON s.user_id = u.user_id
WHERE 1
";

if($search != ""){
    $count_query .= " AND (u.name LIKE '%$search%' OR s.admission_number LIKE '%$search%')";
}

if($class_filter != ""){
    $count_query .= " AND s.class_id='$class_filter'";
}

$total_students = mysqli_fetch_assoc(mysqli_query($conn, $count_query))['total'];
$total_pages = ceil($total_students / $limit);

/* FINAL QUERY WITH PAGINATION */
$query .= " ORDER BY s.student_id DESC LIMIT $limit OFFSET $offset";

$students = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Students</title>
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

<!-- TITLE -->
<div class="card">
    <h2>Manage Students</h2>
    <p>Total Students: <b><?php echo $total_students; ?></b></p>
</div>

<!-- FILTERS -->
<div class="card">
<div class="filters">

<form method="GET" style="display:flex; gap:10px;">

<input type="text" name="search" placeholder="Search name or admission..." value="<?php echo $search; ?>">

<select name="class">
<option value="">All Classes</option>

<?php
$classes = mysqli_query($conn,"SELECT * FROM classes");
while($c = mysqli_fetch_assoc($classes)){
    $selected = ($class_filter == $c['class_id']) ? "selected" : "";
    echo "<option value='{$c['class_id']}' $selected>{$c['class_name']}</option>";
}
?>

</select>

<button type="submit" class="btn">Search</button>
<a href="add_student.php" class="btn">+ Add Student</a>

</form>

</div>
</div>

<!-- TABLE -->
<div class="card">
<h3>Student List</h3>

<?php if(mysqli_num_rows($students) > 0){ ?>

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Username</th>
<th>Admission No</th>
<th>Class</th>
<th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($students)){ ?>

<tr>
<td><?php echo $row['student_id']; ?></td>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo $row['username']; ?></td>
<td><?php echo $row['admission_number']; ?></td>
<td><?php echo $row['class_name']; ?></td>

<td class="actions">
<a href="view_student.php?id=<?php echo $row['student_id']; ?>" class="btn_view" style="background-color:#3498db; color:#fff;">View</a>
<a href="edit_student.php?id=<?php echo $row['student_id']; ?>" class="btn btn-edit">Edit</a>
<a href="#" class="btn btn-delete" onclick="openModal(<?php echo $row['student_id']; ?>)">Delete</a>
</td>

</tr>

<?php } ?>

</table>

<!-- PAGINATION -->
<div class="pagination">

<?php for($i = 1; $i <= $total_pages; $i++): ?>

<a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&class=<?php echo $class_filter; ?>"
   class="<?php echo ($i == $page) ? 'active' : ''; ?>">
   <?php echo $i; ?>
</a>

<?php endfor; ?>

</div>

<?php } else { ?>

<p>No students found.</p>

<?php } ?>

</div>

</div>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal">

<div class="modal-content">
    <h3>Confirm Delete</h3>
    <p>Are you sure you want to delete this student?</p>

    <div class="modal-actions">
        <a id="confirmDeleteBtn" href="#" class="btn btn-delete">Yes, Delete</a>
        <button onclick="closeModal()" class="btn">Cancel</button>
    </div>
</div>

</div>

<script>
function openModal(id){
    document.getElementById("deleteModal").style.display = "flex";
    document.getElementById("confirmDeleteBtn").href = "delete_student.php?id=" + id;
}

function closeModal(){
    document.getElementById("deleteModal").style.display = "none";
}
</script>

</body>
</html>