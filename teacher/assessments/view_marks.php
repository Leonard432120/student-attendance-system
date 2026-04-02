<?php
include("../../includes/auth.php");

if ($_SESSION['role'] !== 'teacher') exit("Access Denied!");

include("../../config/database.php");

$user_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['name'] ?? 'Teacher';

$teacher_query = mysqli_query($conn, "SELECT teacher_id FROM teachers WHERE user_id = '$user_id'");
$teacher = mysqli_fetch_assoc($teacher_query);
$teacher_id = $teacher['teacher_id'] ?? 0;

// Fetch classes
$classes_query = mysqli_query($conn, "SELECT class_id, class_name FROM classes WHERE class_teacher = '$teacher_id' ORDER BY class_name");

$class_id = $_GET['class_id'] ?? null;
$subject_id = $_GET['subject_id'] ?? null;

// Fetch subjects for selected class
$subjects = [];
if ($class_id) {
    $subjects_query = mysqli_query($conn, "SELECT subject_id, subject_name FROM subjects WHERE class_id = '$class_id' ORDER BY subject_name");
    while ($row = mysqli_fetch_assoc($subjects_query)) $subjects[] = $row;
}

// Fetch students and their marks
$students = [];
if ($class_id && $subject_id) {
    $students_query = mysqli_query($conn, "
        SELECT s.student_id, u.name, s.admission_number,
               a.test_mark, a.assignment_mark, a.exam_mark
        FROM students s
        JOIN users u ON s.user_id = u.user_id
        LEFT JOIN assessments a ON s.student_id = a.student_id AND a.subject_id = '$subject_id'
        WHERE s.class_id = '$class_id'
        ORDER BY u.name ASC
    ");
    while ($row = mysqli_fetch_assoc($students_query)) $students[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Marks | Teacher Portal</title>
<style>
/* ===============================
   ROOT VARIABLES
================================*/
:root {
    --primary: #2c3e50;
    --primary-dark: #1f2a36;
}

/* ===============================
   GLOBAL RESET
================================*/
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
    background: #f5f6fa;
    color: #333;
}

/* ===============================
   TOP BAR
================================*/
.topbar {
    background: var(--primary);
    color: #fff;
    padding: 16px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.topbar h1 {
    font-size: 1.4rem;
    font-weight: 600;
}

.profile {
    display: flex;
    align-items: center;
    gap: 12px;
}

.profile img {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.3);
}

/* ===============================
   LAYOUT
================================*/
.layout {
    display: flex;
    min-height: calc(100vh - 70px);
}

.sidebar {
    width: 260px;
    background: #34495e;
    padding: 20px 0;
    color: #fff;
}

.sidebar a {
    display: block;
    padding: 14px 25px;
    color: #fff;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a:hover,
.sidebar a.active {
    background: rgba(255,255,255,0.15);
    padding-left: 30px;
}

.main {
    flex: 1;
    padding: 30px;
}

/* ===============================
   CARDS / SECTIONS
================================*/
.section {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    padding: 25px;
    margin-bottom: 25px;
}

h2 {
    color: var(--primary);
    margin-bottom: 20px;
}

/* ===============================
   TABLE
================================*/
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th {
    background: #f4f1f4;
    color: black;
    padding: 14px 12px;
    text-align: left;
    font-weight: 500;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

/* ===============================
   FORM ELEMENTS
================================*/
select {
    padding: 10px 14px;
    font-size: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fff;
}

/* ===============================
   INFO BOX
================================*/
.info {
    font-size: 0.95rem;
    color: #555;
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 8px;
    border-left: 4px solid rgba(223, 15, 15, 0.07);
}
</style>
</head>
<body>

<div class="topbar">
    <h1>Teacher Portal</h1>
    <div class="profile">
        <span><?= htmlspecialchars($teacher_name) ?></span>
        <img src="../../assets/images/default.png" alt="Profile">
    </div>
</div>

<div class="layout">
    <div class="sidebar">
        <a href="../dashboard.php">Dashboard</a>
        <a href="enter_marks.php">Enter Marks</a>
        <a href="edit_marks.php">Edit Marks</a>
        <a href="view_marks.php" class="active">View Marks</a>
        <a href="../../includes/logout.php">Logout</a>
    </div>

    <div class="main">

        <div class="section">
            <h2>Select Class & Subject</h2>
            <form method="GET">
                <select name="class_id" onchange="this.form.submit()" required>
                    <option value="">-- Select a Class --</option>
                    <?php while ($class = mysqli_fetch_assoc($classes_query)): ?>
                        <option value="<?= $class['class_id'] ?>" <?= ($class_id == $class['class_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class['class_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <?php if ($class_id && count($subjects) > 0): ?>
                    <select name="subject_id" onchange="this.form.submit()" required>
                        <option value="">-- Select a Subject --</option>
                        <?php foreach ($subjects as $sub): ?>
                            <option value="<?= $sub['subject_id'] ?>" <?= ($subject_id == $sub['subject_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sub['subject_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($class_id && $subject_id && count($students) > 0): ?>
            <div class="section">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Admission No.</th>
                            <th>Test (0-20)</th>
                            <th>Assignment (0-20)</th>
                            <th>Exam (0-60)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach ($students as $student): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($student['admission_number']) ?></td>
                            <td><?= $student['test_mark'] ?? '-' ?></td>
                            <td><?= $student['assignment_mark'] ?? '-' ?></td>
                            <td><?= $student['exam_mark'] ?? '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($class_id && $subject_id): ?>
            <div class="section">
                <p class="info">No students or marks found for this class and subject.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>