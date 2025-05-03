<?php
// Include database connection
require 'includes/database-connection.php';

// Fetch students from the database
$query_students = "SELECT student_id, first_name, last_name, fsu_email FROM student";
$query_status = "SELECT student_id, app_decision FROM Status"; // Assuming you want the status for each student

// Try to fetch student data
try {
    // Execute the first query to get student details
    $stmt_students = $pdo->query($query_students);
    $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);

    // Execute the second query to get application decision for each student
    $stmt_status = $pdo->query($query_status);
    $statuses = $stmt_status->fetchAll(PDO::FETCH_ASSOC);

    // Create a lookup array to map student_id to app_decision
    $status_lookup = [];
    foreach ($statuses as $status) {
        $status_lookup[$status['student_id']] = $status['app_decision'];
    }

} catch (PDOException $e) {
    die("Error executing query: " . $e->getMessage());
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
    <ul class="nav-links" id="sidebarLinks">
      <li><a href="index.php">Dashboard</a></li>
      <li><a href="applications.php" class="active">Applications</a></li>
      <li><a href="currentStudents.php">Current Students</a></li>
      <li><a href="semesterGradeReport.php">Semester Grade Report</a></li>
      <li><a href="studentEvents.php">Student Events</a></li>
      <li><a href="uploadDataSync.php">Upload/Data Sync</a></li>
    </ul>
  </nav>

  <!-- Top Search Bar -->
  <div class="search-container">
    <input type="text" class="search-bar" id="searchInput" placeholder="Search Ferris Honors Program..." />
    <button class="search-button" onclick="performSearch()">Search</button>
    <i class="fa fa-user-circle signout-icon" aria-hidden="true"></i>
    <i class="fas fa-sign-out-alt signout-icon" onclick="signOut()"></i>
  </div>

  <!-- Main Content -->
  <main class="content">
    <h1>Current Students</h1>

    <!-- Display Students Here -->
    <div id="studentList">
      <?php
      if ($students) {
          foreach ($students as $student) {
              // Get status for each student using the lookup table
              $status = isset($status_lookup[$student['student_id']]) ? $status_lookup[$student['student_id']] : 'Not Available';
              echo "
                <div class='student-entry'>
                  <p><strong>Name:</strong> {$student['first_name']} {$student['last_name']}</p>
                  <p><strong>Student ID:</strong> {$student['student_id']}</p>
                  <p><strong>Email:</strong> {$student['fsu_email']}</p>
                  <p><strong>Status:</strong> {$status}</p>
                  <button class='delete-btn' data-student-id='{$student['student_id']}'>Delete</button>
                  <hr />
                </div>
              ";
          }
      } else {
          echo "<p>No students found in the database.</p>";
      }
      ?>
    </div>

  </main>

  <script>
    // Function to perform search
    function performSearch() {
      const query = document.getElementById("searchInput").value;
      alert(query ? "Searching for: " + query : "Please enter a search query.");
    }

    // Function to handle sign-out
    function signOut() {
      alert("You have signed out.");
    }

    // Handle student entry actions (delete)
    document.addEventListener("DOMContentLoaded", function () {

      // Delete button
      document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function () {
          const studentId = this.getAttribute("data-student-id");
          if (confirm("Are you sure you want to delete student with ID " + studentId + "?")) {
            // Send a request to the backend to delete the student from the database
            fetch(`deleteStudent.php?student_id=${studentId}`, {
              method: 'GET',
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert("Student deleted successfully.");
                location.reload(); // Refresh the page after deletion
              } else {
                alert("Error deleting student.");
              }
            })
            .catch(error => {
              console.error("Error deleting student:", error);
              alert("Error deleting student.");
            });
          }
        });
      });
    });
  </script>

</body>
</html>
