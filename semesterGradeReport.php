<?php
require 'includes/database-connection.php';

// Insert form data if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "
        INSERT INTO academic_records (
            academic_id, student_id, hs_gpa, act_comp, sat_comp,
            first_semester_as_honors, last_semester_gpa, total_credits_gpa_hours,
            total_credits_attempted, last_semester_at_ferris, total_semester_at_ferris,
            major, college
        ) VALUES (
            :academic_id, :student_id, :hs_gpa, :act_comp, :sat_comp,
            :first_semester_as_honors, :last_semester_gpa, :total_credits_gpa_hours,
            :total_credits_attempted, :last_semester_at_ferris, :total_semester_at_ferris,
            :major, :college
        )
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':academic_id' => $_POST['academic_id'],
        ':student_id' => $_POST['student_id'],
        ':hs_gpa' => $_POST['hs_gpa'],
        ':act_comp' => $_POST['act_comp'],
        ':sat_comp' => $_POST['sat_comp'],
        ':first_semester_as_honors' => $_POST['first_semester_as_honors'],
        ':last_semester_gpa' => $_POST['last_semester_gpa'],
        ':total_credits_gpa_hours' => $_POST['total_credits_gpa_hours'],
        ':total_credits_attempted' => $_POST['total_credits_attempted'],
        ':last_semester_at_ferris' => $_POST['last_semester_at_ferris'],
        ':total_semester_at_ferris' => $_POST['total_semester_at_ferris'],
        ':major' => $_POST['major'],
        ':college' => $_POST['college'],
    ]);

    header("Location: semesterGradeReport.php"); // Redirect to avoid form resubmission
    exit;
}

// Fetch existing records
$query = "SELECT * FROM academic_records";
$records = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Semester Grade Report</title>
  <link rel="stylesheet" href="styles/style.css" />
</head>
<body>

<!-- Sidebar Navigation -->
<nav class="sidebar">
  <!-- sidebar content -->
</nav>

<main class="content">
  <h1>Semester Grade Report</h1>

  <!-- Add Entry Form -->
  <div class="form-container">
    <form method="POST" action="semesterGradeReport.php">
      <input type="text" name="academic_id" placeholder="Academic ID" required />
      <input type="text" name="student_id" placeholder="Student ID" required />
      <input type="text" name="hs_gpa" placeholder="HS GPA" required />
      <input type="text" name="act_comp" placeholder="ACT Comp" required />
      <input type="text" name="sat_comp" placeholder="SAT Comp" required />
      <input type="text" name="first_semester_as_honors" placeholder="First Semester as Honors" required />
      <input type="text" name="last_semester_gpa" placeholder="Last Semester GPA" required />
      <input type="text" name="total_credits_gpa_hours" placeholder="Total Credits GPA Hours" required />
      <input type="text" name="total_credits_attempted" placeholder="Total Credits Attempted"/>
      <input type="text" name="last_semester_at_ferris" placeholder="Last Semester at Ferris"/>
      <input type="text" name="total_semester_at_ferris" placeholder="Total Semester at Ferris"/>
      <input type="text" name="major" placeholder="Major"/>
      <input type="text" name="college" placeholder="College"/>
      <button type="submit">Save Entry</button>
    </form>
  </div>

  <!-- Grade Table -->
  <table>
    <caption>Semester Grade Report</caption>
    <thead>
      <tr>
        <th>Academic ID</th>
        <th>Student ID</th>
        <th>HS GPA</th>
        <th>ACT Comp</th>
        <th>SAT Comp</th>
        <th>First Semester as Honors</th>
        <th>Last Semester GPA</th>
        <th>Total Credits GPA Hours</th>
        <th>Total Credits Attempted</th>
        <th>Last Semester at Ferris</th>
        <th>Total Semester at Ferris</th>
        <th>Major</th>
        <th>College</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($records as $record): ?>
        <tr>
          <td><?= htmlspecialchars($record['academic_id']) ?></td>
          <td><?= htmlspecialchars($record['student_id']) ?></td>
          <td><?= htmlspecialchars($record['hs_gpa']) ?></td>
          <td><?= htmlspecialchars($record['act_comp']) ?></td>
          <td><?= htmlspecialchars($record['sat_comp']) ?></td>
          <td><?= htmlspecialchars($record['first_semester_as_honors']) ?></td>
          <td><?= htmlspecialchars($record['last_semester_gpa']) ?></td>
          <td><?= htmlspecialchars($record['total_credits_gpa_hours']) ?></td>
          <td><?= htmlspecialchars($record['total_credits_attempted']) ?></td>
          <td><?= htmlspecialchars($record['last_semester_at_ferris']) ?></td>
          <td><?= htmlspecialchars($record['total_semester_at_ferris']) ?></td>
          <td><?= htmlspecialchars($record['major']) ?></td>
          <td><?= htmlspecialchars($record['college']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
</body>
</html>
