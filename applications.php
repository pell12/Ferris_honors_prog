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
<?php
require 'includes/database-connection.php';

// Fetch students with all relevant fields
$query = "
    SELECT 
        student_id, first_name, last_name, fsu_email,
        term, status_category, student_cat, student_status,
        app_decision, app_date, probation_status, date_resolved, honors_credits
    FROM student
";
$stmt = $pdo->query($query);
$students = $stmt->fetchAll();
?>

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

  <!-- Application Form -->
  <form id="applicationForm" class="application-form" method="POST" action="#">
    <h2>New Application</h2>
    <label for="name">Applicant Name:</label>
    <input type="text" id="name" name="name" required />

    <label for="studentId">Student ID</label>
    <input type="text" id="studentId" name="studentId" required />

    <label for="program">Program Applied For:</label>
    <input type="text" id="program" name="program" required />

    <label for="date">Application Date:</label>
    <input type="date" id="date" name="date" required />

    <label>Status:</label><br />
    <input type="radio" id="approve" name="status" value="Approved" />
    <label for="approve">Approve</label>

    <input type="radio" id="deny" name="status" value="Denied" />
    <label for="deny">Deny</label>

    <input type="radio" id="wait" name="status" value="Waitlisted" />
    <label for="wait">Waitlist</label>

    <br /><br />
    <button type="submit">Submit</button>
  </form>

  <hr />

  <!-- Display Students Here -->
  <div id="studentList">
    <?php
    if ($students) {
        foreach ($students as $student) {
            echo "
              <div class='student-entry'>
                <p><strong>Name:</strong> {$student['first_name']} {$student['last_name']}</p>
                <p><strong>Student ID:</strong> {$student['student_id']}</p>
                <p><strong>Email:</strong> {$student['fsu_email']}</p>
                <p><strong>Term:</strong> {$student['term']}</p>
                <p><strong>Status Category:</strong> {$student['status_category']}</p>
                <p><strong>Student Category:</strong> {$student['student_cat']}</p>
                <p><strong>Student Status:</strong> {$student['student_status']}</p>
                <p><strong>Application Decision:</strong> {$student['app_decision']}</p>
                <p><strong>Application Date:</strong> {$student['app_date']}</p>
                <p><strong>Probation Status:</strong> {$student['probation_status']}</p>
                <p><strong>Date Resolved:</strong> {$student['date_resolved']}</p>
                <p><strong>Honors Credits:</strong> {$student['honors_credits']}</p>
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
  // Perform search
  function performSearch() {
    const query = document.getElementById("searchInput").value;
    alert(query ? "Searching for: " + query : "Please enter a search query.");
  }

  // Sign-out simulation
  function signOut() {
    alert("You have signed out.");
  }

  // Handle delete buttons
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-btn").forEach(button => {
      button.addEventListener("click", function () {
        const studentId = this.getAttribute("data-student-id");
        if (confirm("Are you sure you want to delete student with ID " + studentId + "?")) {
          fetch(`deleteStudent.php?student_id=${studentId}`, {
            method: 'GET',
          })
          .then(response => response.json())
          .then(data => {
            alert("Student deleted successfully.");
            location.reload();
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
