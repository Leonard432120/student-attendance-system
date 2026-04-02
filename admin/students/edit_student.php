<?php
include("../../includes/mailer.php");
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin'){ echo "Access Denied!"; exit(); }

include("../../config/database.php");

$error = "";

/* CHECK ID */
if(!isset($_GET['id'])){
    echo "Student ID missing!";
    exit();
}

$student_id = $_GET['id'];

/* FETCH CURRENT DATA */
$student_query = mysqli_query($conn, "
    SELECT s.student_id, s.user_id, s.admission_number, s.class_id,
           u.name, u.username, u.email, u.phone, u.password
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.student_id = '$student_id'
");

$student = mysqli_fetch_assoc($student_query);

if(!$student){
    echo "Student not found!";
    exit();
}

/* HANDLE UPDATE */
if($_SERVER['REQUEST_METHOD'] == "POST"){

    /* NEW VALUES */
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $new_password_plain = $password; // SAVE IT BEFORE HASHING
    $admission_number = mysqli_real_escape_string($conn, $_POST['admission_number']);
    $class_id = $_POST['class_id'];

    /* TRACK CHANGES */
    $changes = [];

    if($name != $student['name']) $changes[] = "Name updated";
    if($username != $student['username']) $changes[] = "Username updated";
    if($email != $student['email']) $changes[] = "Email updated";
    if($phone != $student['phone']) $changes[] = "Phone updated";

    /* UPDATE USERS */
    if($password != ""){
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
       $changes[] = "Password updated";

        mysqli_query($conn,"
            UPDATE users 
            SET name='$name', username='$username',
                email='$email', phone='$phone',
                password='$hashed_password'
            WHERE user_id='{$student['user_id']}'
        ");
    } else {
        mysqli_query($conn,"
            UPDATE users 
            SET name='$name', username='$username',
                email='$email', phone='$phone'
            WHERE user_id='{$student['user_id']}'
        ");
    }

    /* UPDATE STUDENTS */
    $update_success = mysqli_query($conn,"
        UPDATE students 
        SET admission_number='$admission_number', class_id='$class_id'
        WHERE student_id='$student_id'
    ");

    if($update_success){

        /* SEND EMAIL ONLY IF SOMETHING CHANGED */
        if(count($changes) > 0){

            $subject = "Your Account Has Been Updated";

            $message = "
                <h2>Hello $name</h2>

                <p>Your student account has been updated by the administration.</p>

                <h3>Updated Information:</h3>
                <ul>
                    <li><b>Name:</b> $name</li>
                    <li><b>Username:</b> $username</li>
                    <li><b>Email:</b> $email</li>
                    <li><b>Phone:</b> $phone</li>
                    <li><b>Password:</b> $new_password_plain</li>

                    <li><b>Admission Number:</b> $admission_number</li>
                </ul>
            ";

            if($password != ""){
                $message .= "<p><b>New Password has been set. Please login using the new password.</b></p>";
            }

            $message .= "
                <br>
                <p>If you did not request this change, please contact the school office immediately.</p>

                <br>
                <p><b>School Management System</b></p>
            ";

            sendMail($email, $subject, $message);
        }
    }

    logAction($conn, $_SESSION['user_id'], "Edited student ID $student_id");

    header("Location: manage_students.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>

    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/form.css">
</head>

<body>

<div class="header">
    <h3>Admin Dashboard</h3>
    <div>
        <?php echo $_SESSION['name']; ?> |
        <a href="../../includes/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

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

<div class="content">

<div class="page-wrapper">

<div class="page-header">
    <h2>Edit Student</h2>
</div>

<div class="form-card">

<form method="POST">

<div class="form-grid">

    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
    </div>

    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" value="<?php echo $student['username']; ?>" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo $student['email']; ?>" required>
    </div>

    <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo $student['phone']; ?>" required>
    </div>

    <div class="form-group">
        <label>Password (leave blank to keep current)</label>
        <input type="password" name="password">
    </div>

    <div class="form-group">
        <label>Admission Number</label>
        <input type="text" name="admission_number" value="<?php echo $student['admission_number']; ?>" required>
    </div>

    <div class="form-group full-width">
        <label>Class</label>
        <select name="class_id" required>

            <?php
            $classes = mysqli_query($conn,"SELECT * FROM classes");
            while($c = mysqli_fetch_assoc($classes)){
                $selected = $c['class_id'] == $student['class_id'] ? "selected" : "";
                echo "<option value='{$c['class_id']}' $selected>{$c['class_name']}</option>";
            }
            ?>

        </select>
    </div>

</div>

<div class="btn-group">

    <button type="submit" class="btn btn-primary">
        Update Student
    </button>

    <a href="manage_students.php" class="btn btn-danger">
        Cancel
    </a>

</div>

</form>

</div>

</div>

</div>

</div>

</body>
</html>