<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){
    echo "Access Denied!";
    exit();
}

include("../../config/database.php");

/* SEARCH */
$search = $_GET['search'] ?? "";

/* PAGINATION */
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

/* BASE QUERY */
$query = "
SELECT t.teacher_id, u.name, u.username, u.email, u.phone, t.employee_number
FROM teachers t
JOIN users u ON t.user_id = u.user_id
WHERE 1
";

/* SEARCH FILTER */
if($search != ""){
    $query .= " AND (u.name LIKE '%$search%' OR t.employee_number LIKE '%$search%')";
}

/* COUNT */
$count_query = "
SELECT COUNT(*) as total
FROM teachers t
JOIN users u ON t.user_id = u.user_id
WHERE 1
";

if($search != ""){
    $count_query .= " AND (u.name LIKE '%$search%' OR t.employee_number LIKE '%$search%')";
}

$total_teachers = mysqli_fetch_assoc(mysqli_query($conn, $count_query))['total'];
$total_pages = ceil($total_teachers / $limit);

/* FINAL QUERY */
$query .= " ORDER BY t.teacher_id DESC LIMIT $limit OFFSET $offset";

$teachers = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Teachers</title>
<link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>

<!-- HEADER (SAME AS STUDENTS) -->
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

<!-- SIDEBAR (MATCH STUDENTS EXACTLY) -->
<div class="sidebar">
<a href="../dashboard.php">Dashboard</a>
<a href="../students/manage_students.php">Students</a>
<a href="manage_teachers.php" class="active">Teachers</a>
<a href="../classes/manage_classes.php">Classes</a>
<a href="../subjects/manage_subjects.php">Subjects</a>
<a href="../reports/attendance_report.php">Attendance</a>
<a href="../reports/performance_report.php">Performance</a>
<a href="../settings/system_settings.php">Settings</a>
</div>

<!-- CONTENT -->
<div class="content">

<!-- TITLE CARD -->
<div class="card">
    <h2>Manage Teachers</h2>
    <p>Total Teachers: <b><?php echo $total_teachers; ?></b></p>
</div>

<!-- FILTERS (SAME STYLE AS STUDENTS) -->
<div class="card">
<div class="filters">

<form method="GET" style="display:flex; gap:10px;">

<input type="text" name="search"
placeholder="Search name or employee no..."
value="<?php echo $search; ?>">

<button type="submit" class="btn">Search</button>

<a href="add_teacher.php" class="btn">+ Add Teacher</a>

</form>

</div>
</div>

<!-- TABLE -->
<div class="card">
<h3>Teacher List</h3>

<?php if(mysqli_num_rows($teachers) > 0){ ?>

<table>
<tr>
<th>ID</th>
<th>Username</th>
<th>Phone</th>
<th>Employee No</th>
<th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($teachers)){ ?>

<tr>
<td><?php echo $row['teacher_id']; ?></td>

<td><?php echo $row['username']; ?></td>

<td><?php echo $row['phone']; ?></td>
<td><?php echo $row['employee_number']; ?></td>

<td class="actions">
<a href="view_teacher.php?id=<?php echo $row['teacher_id']; ?>" class="btn_view" style="background-color:#3498db; color:#fff;">View</a>
<a href="edit_teacher.php?id=<?php echo $row['teacher_id']; ?>"
class="btn btn-edit">Edit</a>

<a href="#"
class="btn btn-delete"
onclick="openModal(<?php echo $row['teacher_id']; ?>)">
Delete
</a>

</td>

</tr>

<?php } ?>

</table>

<!-- PAGINATION -->
<div class="pagination">

<?php for($i = 1; $i <= $total_pages; $i++): ?>

<a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"
class="<?php echo ($i == $page) ? 'active' : ''; ?>">
<?php echo $i; ?>
</a>

<?php endfor; ?>

</div>

<?php } else { ?>

<p>No teachers found.</p>

<?php } ?>

</div>

</div>
</div>

<!-- DELETE MODAL (SAME STYLE AS STUDENTS) -->
<div id="deleteModal" class="modal">

<div class="modal-content">
    <h3>Confirm Delete</h3>
    <p>Are you sure you want to delete this teacher?</p>

    <div class="modal-actions">
        <a id="confirmDeleteBtn" href="#" class="btn btn-delete">Yes, Delete</a>
        <button onclick="closeModal()" class="btn">Cancel</button>
    </div>
</div>

</div>

<script>
function openModal(id){
    document.getElementById("deleteModal").style.display = "flex";
    document.getElementById("confirmDeleteBtn").href =
        "delete_teacher.php?id=" + id;
}

function closeModal(){
    document.getElementById("deleteModal").style.display = "none";
}
</script>

</body>
</html>