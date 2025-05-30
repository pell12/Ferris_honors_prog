<?php
require 'includes/database-connection.php';

// Handle Delete
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM student WHERE student_id = ?");
    $stmt->execute([$deleteId]);
    header("Location: currentStudents.php");
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student_id'])) {
    $studentId = $_POST['update_student_id'];
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $lastName = $_POST['last_name'];
    $preferredName = $_POST['preferred_name'];
    $major = $_POST['major'];
    $email = isset($_POST['fsu_email']) ? $_POST['fsu_email'] : '';
    $ethnicity = $_POST['ethnicity_current'];
    $gender = $_POST['gender'];
    $campus = $_POST['current_campus'];

    // Update student table (including new fields)
    $stmt = $pdo->prepare("UPDATE student SET first_name = ?, middle_name = ?, last_name = ?, preferred_name = ?, fsu_email = ?, ethnicity_current = ?, gender = ?, current_campus = ? WHERE student_id = ?");
    $stmt->execute([$firstName, $middleName, $lastName, $preferredName, $email, $ethnicity, $gender, $campus, $studentId]);

    // Update academic_records table for major
    $stmt2 = $pdo->prepare("UPDATE academic_records SET major = ? WHERE student_id = ?");
    $stmt2->execute([$major, $studentId]);

    header("Location: currentStudents.php");
    exit;
}

// Fetch all students
try {
    $query = "
        SELECT s.student_id, s.first_name, s.middle_name, s.last_name, s.preferred_name, s.fsu_email, 
               s.ethnicity_current, s.gender, s.current_campus, a.major
        FROM student s
        LEFT JOIN academic_records a ON s.student_id = a.student_id
    ";
    $stmt = $pdo->query($query);
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
}
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
      <li><a href="currentStudents.php" class="active">Current Students</a></li>
      <li><a href="semesterGradeReport.php">Semester Grade Report</a></li>
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

  <!-- Main Content -->
  <main class="content">
    <h1>Current Students</h1>

    <!-- Add Student Form -->
    <div class="form-container">
      <form id="studentForm" method="POST" action="currentStudents.php">
        <input type="text" id="name" name="name" placeholder="Student Name" required />
        <input type="text" id="studentId" name="studentId" placeholder="Student ID" required />
        <input type="text" id="preferredName" name="preferredName" placeholder="Preferred Name" />
        <input type="text" id="major" name="major" placeholder="Major" required />
        <input type="email" id="email" name="email" placeholder="Email" required />
        <input type="text" id="ethnicity_current" name="ethnicity_current" placeholder="Ethnicity" />
        <input type="text" id="gender" name="gender" placeholder="Gender" />
        <input type="text" id="current_campus" name="current_campus" placeholder="Campus" />
        <button type="submit">Add Student</button>
      </form>
    </div>

    <!-- Student Table -->
    <table>
      <thead>
        <tr>
          <th>Student Name</th>
          <th>Student ID</th>
          <th>Preferred Name</th>
          <th>Major</th>
          <th>Email</th>
          <th>Ethnicity</th>
          <th>Gender</th>
          <th>Campus</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $student): ?>
          <tr>
            <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name']) ?></td>
            <td><?= htmlspecialchars($student['student_id']) ?></td>
            <td><?= htmlspecialchars($student['preferred_name']) ?></td>
            <td><?= htmlspecialchars($student['major']) ?></td>
            <td><a href="mailto:<?= htmlspecialchars($student['fsu_email']) ?>"><?= htmlspecialchars($student['fsu_email']) ?></a></td>
            <td><?= htmlspecialchars($student['ethnicity_current']) ?></td>
            <td><?= htmlspecialchars($student['gender']) ?></td>
            <td><?= htmlspecialchars($student['current_campus']) ?></td>
            <td>
              <a href="javascript:void(0)" onclick="editStudent('<?= $student['student_id'] ?>', '<?= addslashes($student['first_name']) ?>', '<?= addslashes($student['middle_name']) ?>', '<?= addslashes($student['last_name']) ?>', '<?= addslashes($student['preferred_name']) ?>', '<?= addslashes($student['major']) ?>', '<?= addslashes($student['fsu_email']) ?>', '<?= addslashes($student['ethnicity_current']) ?>', '<?= addslashes($student['gender']) ?>', '<?= addslashes($student['current_campus']) ?>')">✏️</a> |
              <a href="currentStudents.php?delete_id=<?= $student['student_id'] ?>" onclick="return confirm('Are you sure you want to delete this student?');">❌</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Edit Student Form -->
    <div id="editFormContainer" style="display: none;">
      <h2>Edit Student</h2>
      <form id="editForm" method="POST" action="currentStudents.php">
        <input type="hidden" name="update_student_id" id="update_student_id" />
        <input type="text" name="first_name" id="edit_first_name" placeholder="First Name" required />
        <input type="text" name="middle_name" id="edit_middle_name" placeholder="Middle Name" />
        <input type="text" name="last_name" id="edit_last_name" placeholder="Last Name" required />
        <input type="text" name="preferred_name" id="edit_preferred_name" placeholder="Preferred Name" />
        <input type="text" name="major" id="edit_major" placeholder="Major" required />
        <input type="email" name="fsu_email" id="edit_fsu_email" placeholder="Email" />
        <input type="text" name="ethnicity_current" id="edit_ethnicity_current" placeholder="Ethnicity" />
        <input type="text" name="gender" id="edit_gender" placeholder="Gender" />
        <input type="text" name="current_campus" id="edit_current_campus" placeholder="Campus" />
        <button type="submit">Update Student</button>
        <button type="button" onclick="cancelEdit()">Cancel</button>
      </form>
    </div>
  </main>

  <script>
    // Function to open the edit form with pre-filled data
    function editStudent(studentId, firstName, middleName, lastName, preferredName, major, fsuEmail, ethnicity, gender, campus) {
      document.getElementById('update_student_id').value = studentId;
      document.getElementById('edit_first_name').value = firstName;
      document.getElementById('edit_middle_name').value = middleName;
      document.getElementById('edit_last_name').value = lastName;
      document.getElementById('edit_preferred_name').value = preferredName;
      document.getElementById('edit_major').value = major;
      document.getElementById('edit_fsu_email').value = fsuEmail;
      document.getElementById('edit_ethnicity_current').value = ethnicity;
      document.getElementById('edit_gender').value = gender;
      document.getElementById('edit_current_campus').value = campus;
      document.getElementById('editFormContainer').style.display = 'block';
    }

    // Function to cancel the edit operation
    function cancelEdit() {
      document.getElementById('editFormContainer').style.display = 'none';
    }
  </script>
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
</body>
</html>
