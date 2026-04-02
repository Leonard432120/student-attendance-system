<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

/* FILTER */
$class_id = $_GET['class_id'] ?? "";

/* QUERY */
$query = "
SELECT 
    users.name,
    students.admission_number,
    classes.class_name,
    subjects.subject_name,
    (assessments.test_mark + assessments.assignment_mark + assessments.exam_mark) AS total_score
FROM assessments
JOIN students ON assessments.student_id = students.student_id
JOIN users ON students.user_id = users.user_id
JOIN subjects ON assessments.subject_id = subjects.subject_id
LEFT JOIN classes ON students.class_id = classes.class_id
WHERE 1
";

if($class_id != ""){
    $query .= " AND students.class_id='$class_id'";
}

$query .= " ORDER BY users.name ASC";

$result = mysqli_query($conn, $query);

/* CLASSES */
$classes = mysqli_query($conn, "SELECT * FROM classes");

/* PREPARE DATA */
$data = [];
$labels = [];
$scores = [];

/* PIE CHART COUNTS */
$high = 0;
$medium = 0;
$low = 0;

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;

    $labels[] = $row['name'];
    $scores[] = $row['total_score'];

    if($row['total_score'] >= 75){
        $high++;
    } elseif($row['total_score'] >= 50){
        $medium++;
    } else {
        $low++;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Performance Report</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .chart-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
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
    <a href="../dashboard.php">Dashboard</a>
    <a href="../students/manage_students.php">Students</a>
    <a href="../teachers/manage_teachers.php">Teachers</a>
    <a href="../classes/manage_classes.php">Classes</a>
    <a href="../subjects/manage_subjects.php">Subjects</a>
    <a href="attendance_report.php">Attendance Reports</a>
    <a href="performance_report.php" class="active">Performance Reports</a>
</div>

<!-- CONTENT -->
<div class="content">

<!-- FILTER -->
<div class="card">
    <h2>Performance Report</h2>

    <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
        <select name="class_id">
            <option value="">All Classes</option>
            <?php while($c = mysqli_fetch_assoc($classes)){ ?>
                <option value="<?php echo $c['class_id']; ?>"
                    <?php if($class_id == $c['class_id']) echo "selected"; ?>>
                    <?php echo $c['class_name']; ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" class="btn">Filter</button>
    </form>
</div>

<!-- CHARTS -->
<div class="chart-grid">

    <!-- BAR CHART -->
    <div class="card">
        <h3>Student Scores (Bar Graph)</h3>
        <canvas id="barChart"></canvas>
    </div>

    <!-- PIE CHART -->
    <div class="card">
        <h3>Performance Distribution</h3>
        <canvas id="pieChart"></canvas>
    </div>

</div>

<!-- TABLE -->
<div class="card">

<?php if(!empty($data)){ ?>

<table>
<tr>
    <th>Student</th>
    <th>Admission No</th>
    <th>Class</th>
    <th>Subject</th>
    <th>Total Score</th>
</tr>

<?php foreach($data as $row){ ?>

<tr>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo $row['admission_number']; ?></td>
    <td><?php echo $row['class_name']; ?></td>
    <td><?php echo $row['subject_name']; ?></td>
    <td>
        <?php 
            if($row['total_score'] >= 75){
                echo "<span style='color:green;font-weight:bold;'>".$row['total_score']."</span>";
            } elseif($row['total_score'] >= 50){
                echo "<span style='color:orange;font-weight:bold;'>".$row['total_score']."</span>";
            } else {
                echo "<span style='color:red;font-weight:bold;'>".$row['total_score']."</span>";
            }
        ?>
    </td>
</tr>

<?php } ?>

</table>

<?php } else { ?>

<p>No performance records found.</p>

<?php } ?>

</div>

</div>
</div>

<!-- CHART SCRIPTS -->
<script>

// BAR CHART
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Scores',
            data: <?php echo json_encode($scores); ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// PIE CHART
new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: ['High (75+)', 'Medium (50-74)', 'Low (<50)'],
        datasets: [{
            data: [<?php echo $high; ?>, <?php echo $medium; ?>, <?php echo $low; ?>]
        }]
    },
    options: {
        responsive: true
    }
});

</script>

</body>
</html>