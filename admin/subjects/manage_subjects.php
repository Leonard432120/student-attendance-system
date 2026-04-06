<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

/* FETCH SUBJECTS WITH CLASS NAME */
$subjects = mysqli_query($conn, "
    SELECT 
        subjects.subject_id,
        subjects.subject_name,
        classes.class_name
    FROM subjects
    LEFT JOIN classes ON subjects.class_id = classes.class_id
    ORDER BY subjects.subject_name ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Subjects</title>
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
        <h2>Manage Subjects</h2>
        <p>Total Subjects: <b><?php echo mysqli_num_rows($subjects); ?></b></p>
    </div>

    <!-- ACTION BAR -->
    <div class="card">
        <a href="add_subject.php" class="btn">+ Add Subject</a>
    </div>

    <!-- TABLE -->
    <div class="card">

    <?php if(mysqli_num_rows($subjects) > 0){ ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Subject Name</th>
                <th>Class</th>
                <th>Actions</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($subjects)){ ?>

            <tr>
                <td><?php echo $row['subject_id']; ?></td>

                <td>
                    <?php echo htmlspecialchars($row['subject_name']); ?>
                </td>

                <td>
                    <?php 
                        echo $row['class_name'] 
                            ? htmlspecialchars($row['class_name']) 
                            : "<span style='color:#e67e22;'>Not Assigned</span>";
                    ?>
                </td>

                <td class="actions">
                    <a href="view_subject.php?id=<?php echo $row['subject_id']; ?>" class="btn_view" style="background-color:#3498db; color:#fff;">View & Manage</a>
                    <a href="edit_subject.php?id=<?php echo $row['subject_id']; ?>" class="btn btn-edit">Edit</a>
                    <a href="#" class="btn btn-delete" onclick="openModal(<?php echo $row['subject_id']; ?>)">Delete</a>
                </td>
            </tr>

            <?php } ?>

        </table>

    <?php } else { ?>

        <p>No subjects found. Click "Add Subject" to create one.</p>

    <?php } ?>

    </div>

</div>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal">

    <div class="modal-content">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this subject?</p>

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
        "delete_subject.php?id=" + id;
}

function closeModal(){
    document.getElementById("deleteModal").style.display = "none";
}
</script>
</body>
</html>