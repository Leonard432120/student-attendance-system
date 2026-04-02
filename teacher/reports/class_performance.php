<?php
include("../../includes/auth.php");
if($_SESSION['role'] != 'teacher'){ exit("Access Denied!"); }

include("../../config/database.php");

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'] ?? 'Teacher';

/* GET TEACHER ID */
$tq = mysqli_query($conn,"SELECT teacher_id FROM teachers WHERE user_id='$user_id'");
$teacher = mysqli_fetch_assoc($tq);
$teacher_id = $teacher['teacher_id'] ?? 0;

/* CLASSES */
$classes = mysqli_query($conn,"
    SELECT * FROM classes WHERE class_teacher='$teacher_id'
");

$class_id = $_GET['class_id'] ?? null;

/* DATA */
$data = [];

if($class_id){
   $query = mysqli_query($conn, "
    SELECT 
        u.name,
        s.admission_number,
        COALESCE(SUM(a.test_mark + a.assignment_mark + a.exam_mark), 0) AS total
    FROM assessments a
    JOIN students s ON a.student_id = s.student_id
    JOIN users u ON s.user_id = u.user_id
    WHERE s.class_id='$class_id'
    GROUP BY a.student_id
    ORDER BY total DESC
");
    while($row = mysqli_fetch_assoc($query)){
        $data[] = $row;
    }
}

/* STATS */
$totals = array_column($data,'total');
$average = !empty($totals) ? round(array_sum($totals)/count($totals),1) : 0;
$highest = !empty($totals) ? max($totals) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Class Performance</title>
<link rel="stylesheet" href="../../assets/css/main.css" />

<style>
/* Reset and Variables */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

:root {
  --primary: #2c3e50;
  --primary-dark: #1f2a36;
  --background: #f5f6fa;
  --card-bg: #ffffff;
  --border-color: #e0e0e0;
  --hover-shadow: rgba(0,0,0,0.12);
  --font-primary: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  --accent-gold: #ffd700;
  --accent-silver: #c0c0c0;
  --accent-bronze: #cd7f32;
}

/* Body */
body {
  background: var(--background);
  color: #1f2d3d;
  font-family: var(--font-primary);
  line-height: 1.6;
}

/* Topbar */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 30px;
  background-color: var(--primary);
  color: #fff;
  box-shadow: 0 2px 8px var(--hover-shadow);
  position: sticky;
  top: 0;
  z-index: 1000;
}
.header h1 {
  font-size: 1.4rem;
  font-weight: 600;
}
.profile span {
  font-weight: 600;
}

/* Layout */
.layout {
  display: flex;
  min-height: calc(100vh - 70px);
}

/* Sidebar */
.sidebar {
  width: 240px;
  background: #34495e;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.sidebar a {
  padding: 14px 20px;
  color: #ecf0f1;
  text-decoration: none;
  border-radius: 8px;
  transition: background 0.2s, transform 0.2s;
}
.sidebar a:hover,
.sidebar a.active {
  background: rgba(255,255,255,0.15);
  transform: translateX(2px);
}

/* Main Content */
.main {
  flex: 1;
  padding: 30px;
}

/* Section Container */
.section {
  background: var(--card-bg);
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  transition: box-shadow 0.3s;
}
.section:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}
h2 {
  margin-bottom: 15px;
  color: var(--primary);
  font-size: 1.8rem;
  font-weight: 600;
}

/* Performance Table */
.table-wrapper {
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 8px;
  font-size: 0.95rem;
  border-radius: 8px;
  overflow: hidden;
}
thead {
  background: #f4f1f4;
    color: black;
    padding: 14px 12px;
    text-align: left;
    font-weight: 500;
}
thead th {
  padding: 14px;
  text-align: left;
  font-weight: 600;
  font-size: 1rem;
}
tbody {
  background-color: #fff;
}
tbody tr {
  transition: background-color 0.2s, transform 0.2s;
  cursor: default;
}
tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}
tbody tr:hover {
  background-color: #f1f1f1;
  transform: translateY(-2px);
}
td {
  padding: 14px;
  border-bottom: 1px solid #e0e0e0;
}
td.bold {
  font-weight: 600;
}
td.rank {
  font-weight: bold;
  font-size: 1.1em;
}
.rank-top1 {
  color: var(--accent-gold);
}
.rank-top2 {
  color: silver;
}
.rank-top3 {
  color: #cd7f32;
}

/* Stats Section */
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 15px;
  margin-top: 20px;
}
.stat {
  background: #fff;
  padding: 18px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  text-align: center;
  transition: box-shadow 0.3s;
}
.stat:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}
.stat h4 {
  font-size: 1rem;
  margin-bottom: 8px;
  color: #7f8c8d;
}
.stat p {
  font-size: 1.4rem;
  font-weight: bold;
  color: #2c3e50;
}

/* Empty State */
.empty {
  text-align: center;
  padding: 30px;
  color: #7f8c8d;
  font-size: 1.2rem;
}
.empty span {
  font-size: 40px;
  display: block;
  margin-bottom: 10px;
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div><b>Teacher Portal</b></div>
  <div class="profile">
    <span><?php echo htmlspecialchars($name); ?></span>
  </div>
</div>

<div class="layout">

<!-- SIDEBAR -->
<div class="sidebar">
  <a href="../dashboard.php">Dashboard</a>
  <a href="../attendance/take_attendance.php">Attendance</a>
  <a href="../assessments/enter_marks.php">Enter Marks</a>
  <a href="../assessments/edit_marks.php">Edit Marks</a>
  <a href="../assessments/view_marks.php" class="active">View Marks</a>
  <a href="class_performance.php">Class Performance</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">

<div class="section">
  <div class="header-bar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
    <h2>Class Performance Report</h2>
    <?php if($class_id): ?>
      <div class="badge"><?php echo count($data); ?> Students</div>
    <?php endif; ?>
  </div>

  <form method="GET" style="max-width:300px;">
    <select name="class_id" onchange="this.form.submit()" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
      <option value="">-- Select Class --</option>
      <?php while($c = mysqli_fetch_assoc($classes)): ?>
        <option value="<?php echo $c['class_id']; ?>" <?php if($class_id==$c['class_id']) echo "selected"; ?>>
          <?php echo htmlspecialchars($c['class_name']); ?>
        </option>
      <?php endwhile; ?>
    </select>
  </form>
</div>

<?php if($class_id): ?>

<div class="section">
  <h3>Performance Table</h3>
  <?php if(count($data) > 0): ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Rank</th>
          <th>Name</th>
          <th>Admission</th>
          <th>Total Score</th>
        </tr>
      </thead>
      <tbody>
        <?php $rank=1; foreach($data as $d): ?>
        <tr>
          <td>
            <span class="rank <?php echo $rank==1?'rank-top1':($rank==2?'rank-top2':($rank==3?'rank-top3':'')); ?>">
              #<?php echo $rank++; ?>
            </span>
          </td>
          <td><b><?php echo htmlspecialchars($d['name']); ?></b></td>
          <td><?php echo htmlspecialchars($d['admission_number']); ?></td>
          <td><b><?php echo $d['total']; ?></b></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="empty">
    <span>📊</span>
    No performance data available
  </div>
  <?php endif; ?>
</div>

<!-- STATS -->
<?php if(count($data) > 0): ?>
<div class="stats">
  <div class="stat">
    <h4>Average Score</h4>
    <p><?php echo $average; ?></p>
  </div>
  <div class="stat">
    <h4>Highest Score</h4>
    <p><?php echo $highest; ?></p>
  </div>
  <div class="stat">
    <h4>Total Students</h4>
    <p><?php echo count($data); ?></p>
  </div>
</div>
<?php endif; ?>

<?php endif; ?>

</div>
</div>

</body>
</html>