<?php
require 'includes/database-connection.php';

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM academic_records WHERE academic_id = :academic_id");
    $stmt->execute(['academic_id' => $_GET['delete']]);
    header("Location: semesterGradeReport.php");
    exit;
}

// Handle edit form
$editMode = false;
$editData = [];

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM academic_records WHERE academic_id = :academic_id");
    $stmt->execute(['academic_id' => $_GET['edit']]);
    $editData = $stmt->fetch();
    if ($editData) {
        $editMode = true;
    }
}

// Handle insert/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['is_edit']) && $_POST['is_edit'] === '1') {
        // Update
        $sql = "
            UPDATE academic_records SET
                student_id = :student_id,
                hs_gpa = :hs_gpa,
                act_comp = :act_comp,
                sat_comp = :sat_comp,
                first_semester_as_honors = :first_semester_as_honors,
                last_semester_gpa = :last_semester_gpa,
                total_credits_gpa_hours = :total_credits_gpa_hours,
                total_credits_attempted = :total_credits_attempted,
                last_semester_at_ferris = :last_semester_at_ferris,
                total_semesters_at_ferris = :total_semesters_at_ferris,
                major = :major,
                college = :college
            WHERE academic_id = :academic_id
        ";
    } else {
        // Insert
        $sql = "
            INSERT INTO academic_records (
                academic_id, student_id, hs_gpa, act_comp, sat_comp,
                first_semester_as_honors, last_semester_gpa, total_credits_gpa_hours,
                total_credits_attempted, last_semester_at_ferris, total_semesters_at_ferris,
                major, college
            ) VALUES (
                :academic_id, :student_id, :hs_gpa, :act_comp, :sat_comp,
                :first_semester_as_honors, :last_semester_gpa, :total_credits_gpa_hours,
                :total_credits_attempted, :last_semester_at_ferris, :total_semesters_at_ferris,
                :major, :college
            )
        ";
    }

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
        ':total_semesters_at_ferris' => $_POST['total_semesters_at_ferris'],
        ':major' => $_POST['major'],
        ':college' => $_POST['college'],
    ]);

    header("Location: semesterGradeReport.php");
    exit;
}

// Fetch all records
$records = $pdo->query("SELECT * FROM academic_records")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ferris Honors Program - Current Students</title>
  <link rel="stylesheet" href="styles/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
  <!-- Sidebar Navigation -->
  <nav class="sidebar">
    <div class="logo">
      <img src="images/ferris-logo.png" alt="Ferris State University Logo" />
    </div>
    <ul class="nav-links">
      <li><a href="index.php">Dashboard</a></li>
      <li><a href="applications.php">Applications</a></li>
      <li><a href="currentStudents.php">Current Students</a></li>
      <li><a href="semesterGradeReport.php" class="active">Semester Grade Report</a></li>
      <li><a href="studentEvents.php">Student Events</a></li>
      <li><a href="uploadDataSync.php">Upload/Data Sync</a></li>
    </ul>
  </nav>
<!-- Fixed Search Bar at the Top -->
<div class="search-container">
            <input type="text" class="search-bar" id="searchInput" placeholder="Search Ferris Honors Program...">
            <button class="search-button" onclick="performSearch()">Search</button>
            <!-- Sign-out Icon (Font Awesome) -->
            <i class="fa fa-user-circle signout-icon" aria-hidden="true"></i>
            <i class="fas fa-sign-out-alt signout-icon" onclick="signOut()"></i>
        </div>

<main class="content">
  <h1>Semester Grade Report</h1>

  <!-- Entry Form -->
  <div class="form-container">
    <form method="POST" action="semesterGradeReport.php">
      <input type="hidden" name="is_edit" value="<?= $editMode ? '1' : '0' ?>">
      <input type="text" name="academic_id" placeholder="Academic ID" required value="<?= htmlspecialchars($editData['academic_id'] ?? '') ?>" <?= $editMode ? 'readonly' : '' ?> />
      <input type="text" name="student_id" placeholder="Student ID" required value="<?= htmlspecialchars($editData['student_id'] ?? '') ?>" />
      <input type="text" name="hs_gpa" placeholder="HS GPA" value="<?= htmlspecialchars($editData['hs_gpa'] ?? '') ?>" />
      <input type="text" name="act_comp" placeholder="ACT Comp" value="<?= htmlspecialchars($editData['act_comp'] ?? '') ?>" />
      <input type="text" name="sat_comp" placeholder="SAT Comp" value="<?= htmlspecialchars($editData['sat_comp'] ?? '') ?>" />
      <input type="text" name="first_semester_as_honors" placeholder="First Semester as Honors" value="<?= htmlspecialchars($editData['first_semester_as_honors'] ?? '') ?>" />
      <input type="text" name="last_semester_gpa" placeholder="Last Semester GPA" value="<?= htmlspecialchars($editData['last_semester_gpa'] ?? '') ?>" />
      <input type="text" name="total_credits_gpa_hours" placeholder="Total Credits GPA Hours" value="<?= htmlspecialchars($editData['total_credits_gpa_hours'] ?? '') ?>" />
      <input type="text" name="total_credits_attempted" placeholder="Total Credits Attempted" value="<?= htmlspecialchars($editData['total_credits_attempted'] ?? '') ?>" />
      <input type="text" name="last_semester_at_ferris" placeholder="Last Semester at Ferris" value="<?= htmlspecialchars($editData['last_semester_at_ferris'] ?? '') ?>" />
      <input type="text" name="total_semesters_at_ferris" placeholder="Total Semester at Ferris" value="<?= htmlspecialchars($editData['total_semesters_at_ferris'] ?? '') ?>" />
      <input type="text" name="major" placeholder="Major" value="<?= htmlspecialchars($editData['major'] ?? '') ?>" />
      <input type="text" name="college" placeholder="College" value="<?= htmlspecialchars($editData['college'] ?? '') ?>" />
      <button type="submit"><?= $editMode ? 'Update Entry' : 'Save Entry' ?></button>
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
        <th>Total Semesters at Ferris</th>
        <th>Major</th>
        <th>College</th>
        <th>Actions</th>
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
          <td><?= htmlspecialchars($record['total_semesters_at_ferris']) ?></td>
          <td><?= htmlspecialchars($record['major']) ?></td>
          <td><?= htmlspecialchars($record['college']) ?></td>
          <td class="action-btns">
            <a href="?edit=<?= urlencode($record['academic_id']) ?>">✏️</a>
            <a href="?delete=<?= urlencode($record['academic_id']) ?>" onclick="return confirm('Are you sure you want to delete this entry?');">❌</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <script>
        // Function to perform search action
        function performSearch() {
            var query = document.getElementById("searchInput").value;
            if (query) {
                alert("Searching for: " + query);
                // You can integrate this with a search functionality later
            } else {
                alert("Please enter a search query.");
            }
        }

        // Function to handle sign out (example action)
        function signOut() {
            alert("You have signed out.");
            // Add actual sign-out logic here (e.g., redirecting to the login page)
        }
    </script>
</main>
</body>
</html>
