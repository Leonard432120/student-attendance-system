<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'admin') exit("Access Denied!");

include("../../config/database.php");

/* FILTERS */
$class_id = $_GET['class_id'] ?? "";
$date = $_GET['date'] ?? "";

/* QUERY */
$query = "
SELECT 
    attendance.attendance_id,
    attendance.attendance_date,
    attendance.status,
    students.admission_number,
    users.name,
    classes.class_name
FROM attendance
JOIN students ON attendance.student_id = students.student_id
JOIN users ON students.user_id = users.user_id
LEFT JOIN classes ON attendance.class_id = classes.class_id
WHERE 1
";

if($class_id != ""){
    $query .= " AND attendance.class_id='$class_id'";
}

if($date != ""){
    $query .= " AND attendance.attendance_date='$date'";
}

$query .= " ORDER BY attendance.attendance_date DESC";

$result = mysqli_query($conn, $query);

/* CLASSES */
$classes = mysqli_query($conn, "SELECT * FROM classes");

/* PREPARE DATA */
$data = [];
$dates = [];
$present_count = [];
$absent_count = [];

$total_present = 0;
$total_absent = 0;

/* GROUP DATA BY DATE */
$temp = [];

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;

    $d = $row['attendance_date'];

    if(!isset($temp[$d])){
        $temp[$d] = ['present'=>0, 'absent'=>0];
    }

    if($row['status'] == 'Present'){
        $temp[$d]['present']++;
        $total_present++;
    } else {
        $temp[$d]['absent']++;
        $total_absent++;
    }
}

/* PREPARE CHART ARRAYS */
foreach($temp as $d => $counts){
    $dates[] = $d;
    $present_count[] = $counts['present'];
    $absent_count[] = $counts['absent'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">

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

<!-- FILTER -->
<div class="card">
    <h2>Attendance Report</h2>

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

        <input type="date" name="date" value="<?php echo $date; ?>">

        <button type="submit" class="btn">Filter</button>
    </form>
</div>

<!-- CHARTS -->
<div class="chart-grid">

    <!-- BAR CHART -->
    <div class="card">
        <h3>Attendance by Date</h3>
        <canvas id="barChart"></canvas>
    </div>

    <!-- PIE CHART -->
    <div class="card">
        <h3>Overall Attendance</h3>
        <canvas id="pieChart"></canvas>
    </div>

</div>

<!-- TABLE -->
<div class="card">

<?php if(!empty($data)){ ?>

<table>
<tr>
    <th>Date</th>
    <th>Student</th>
    <th>Admission No</th>
    <th>Class</th>
    <th>Status</th>
</tr>

<?php foreach($data as $row){ ?>

<tr>
    <td><?php echo $row['attendance_date']; ?></td>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo $row['admission_number']; ?></td>
    <td><?php echo $row['class_name']; ?></td>
    <td>
        <?php if($row['status'] == 'Present'){ ?>
            <span style="color:green; font-weight:bold;">Present</span>
        <?php } else { ?>
            <span style="color:red; font-weight:bold;">Absent</span>
        <?php } ?>
    </td>
</tr>

<?php } ?>

</table>

<?php } else { ?>

<p>No attendance records found.</p>

<?php } ?>

</div>

</div>
</div>

<!-- CHARTS -->
<script>

// BAR CHART
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [
            {
                label: 'Present',
                data: <?php echo json_encode($present_count); ?>
            },
            {
                label: 'Absent',
                data: <?php echo json_encode($absent_count); ?>
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// PIE CHART
new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: ['Present', 'Absent'],
        datasets: [{
            data: [<?php echo $total_present; ?>, <?php echo $total_absent; ?>]
        }]
    },
    options: {
        responsive: true
    }
});

</script>

</body>
</html>