<?php
include("../../includes/mailer.php");
include("../../includes/auth.php");
include("../../config/database.php");

if($_SESSION['role'] != 'admin'){
    exit("Access Denied!");
}

/* PASSWORD GENERATOR */
function generatePassword($length = 8){
    return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"),0,$length);
}

$error = "";

if($_SERVER['REQUEST_METHOD']=="POST"){

    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);
    $class_id = mysqli_real_escape_string($conn,$_POST['class_id']);
    $admission_number = mysqli_real_escape_string($conn,$_POST['admission_number']);

    $password_plain = !empty($_POST['password']) ? $_POST['password'] : generatePassword();
    $password_hash = password_hash($password_plain,PASSWORD_DEFAULT);

    // Handle profile image
    $profile_image = 'default.png';
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0){
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext,$allowed)){
            $profile_image = uniqid('student_',true).".".$ext;
            move_uploaded_file($_FILES['profile_image']['tmp_name'], "../../assets/images/".$profile_image);
        }
    }

    // Duplicate checks
    $check_user = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($check_user) > 0){
        $error = "Username '$username' already exists! Please choose another.";
    }

    $check_adm = mysqli_query($conn,"SELECT * FROM students WHERE admission_number='$admission_number'");
    if(mysqli_num_rows($check_adm) > 0){
        $error = "Admission Number '$admission_number' already exists! Please use a unique number.";
    }

    if($error==""){
        mysqli_query($conn,"
            INSERT INTO users(name,username,password,role,email,phone,profile_image)
            VALUES('$name','$username','$password_hash','student','$email','$phone','$profile_image')
        ");
        $user_id = mysqli_insert_id($conn);

        mysqli_query($conn,"
            INSERT INTO students(user_id,class_id,admission_number)
            VALUES('$user_id','$class_id','$admission_number')
        ");

        logAction($conn, $_SESSION['user_id'], "Added student: $name (Admission: $admission_number)");

        $subject = "Student Account Created";
        $message = "
        <h2>Welcome to School System</h2>
        <p>Your student account has been created.</p>
        <h3>Login Details</h3>
        <ul>
            <li><b>Username:</b> $username</li>
            <li><b>Password:</b> $password_plain</li>
            <li><b>Email:</b> $email</li>
            <li><b>Phone:</b> $phone</li>
            <li><b>Class:</b> ".htmlspecialchars($_POST['class_id'])."</li>
            <li><b>Admission Number:</b> $admission_number</li>
        </ul>
        <p>Please login and change your password immediately.</p>
        ";
        sendMail($email,$subject,$message);

        header("Location: manage_students.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Student</title>

<link rel="stylesheet" href="../../assets/css/admin.css">
<link rel="stylesheet" href="../../assets/css/form.css">

<style>
/* Image preview */
#img-preview {
    display: block;
    max-width: 150px;
    max-height: 150px;
    margin-top: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    object-fit: cover;
}
</style>

</head>

<body>

<div class="header">
    <div>Admin Dashboard</div>
    <div>
        <?= htmlspecialchars($_SESSION['name']) ?> |
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

<div class="page-wrapper">

<div class="page-header">
    <h2>Add Student</h2>
</div>

<?php if($error!=""){ ?>
<div class="form-message form-error">
    <?= htmlspecialchars($error) ?>
</div>
<?php } ?>

<div class="form-card">

<form method="POST" enctype="multipart/form-data">

<div class="form-grid">

<div class="form-group">
<label>Full Name</label>
<input type="text" name="name" required>
</div>

<div class="form-group">
<label>Username</label>
<input type="text" name="username" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="form-group">
<label>Phone</label>
<input type="text" name="phone" required>
</div>

<div class="form-group">
<label>Password (optional)</label>
<input type="password" name="password" placeholder="Leave blank for auto password">
</div>

<div class="form-group">
<label>Class</label>
<select name="class_id" required>
    <option value="">-- Select Class --</option>
    <?php
    $classes_query = mysqli_query($conn,"SELECT class_id,class_name FROM classes ORDER BY class_name");
    while($class = mysqli_fetch_assoc($classes_query)){
        echo "<option value='".htmlspecialchars($class['class_id'])."'>".htmlspecialchars($class['class_name'])."</option>";
    }
    ?>
</select>
</div>

<div class="form-group">
<label>Admission Number</label>
<input type="text" name="admission_number" required>
</div>

<div class="form-group">
<label>Profile Image (optional)</label>
<input type="file" name="profile_image" accept="image/*" onchange="previewImage(event)">
<img id="img-preview" src="#" alt="Image Preview" style="display:none;">
</div>

</div>

<div class="btn-group">
<button type="submit" class="btn btn-success">Add Student</button>
<a href="manage_students.php" class="btn btn-danger">Cancel</a>
</div>

</form>

</div>

</div>

</div>
</div>

<script>
// Live image preview
function previewImage(event) {
    const preview = document.getElementById('img-preview');
    const file = event.target.files[0];
    if(file){
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
}
</script>

</body>
</html>