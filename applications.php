<?php
// Include the database connection
require 'includes/database-connection.php';

// Debugging: Check if the connection is successful
if ($pdo) {
    echo "Database connected successfully.";
} else {
    echo "Failed to connect to the database.";
}

// Fetch students from the database
$query = "SELECT student_id, first_name, last_name, fsu_email, status FROM student";
$stmt = $pdo->query($query);
$students = $stmt->fetchAll();
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
              // Display the status of each student
              $statusLabel = "";
              switch ($student['status']) {
                  case 'Approved':
                      $statusLabel = "<span style='color: green;'>Accepted</span>";
                      break;
                  case 'Denied':
                      $statusLabel = "<span style='color: red;'>Denied</span>";
                      break;
                  case 'Waitlisted':
                      $statusLabel = "<span style='color: orange;'>Waitlisted</span>";
                      break;
                  default:
                      $statusLabel = "<span>Unknown Status</span>";
                      break;
              }

              echo "
                  <div class='student-entry'>
                    <p><strong>Name:</strong> {$student['first_name']} {$student['last_name']}</p>
                    <p><strong>Student ID:</strong> {$student['student_id']}</p>
                    <p><strong>Email:</strong> {$student['fsu_email']}</p>
                    <p><strong>Status:</strong> {$statusLabel}</p>
                    <form action='' method='POST'>
                        <input type='hidden' name='student_id' value='{$student['student_id']}' />
                        <select name='status'>
                            <option value='Approved' ".($student['status'] == 'Approved' ? 'selected' : '').">Approved</option>
                            <option value='Denied' ".($student['status'] == 'Denied' ? 'selected' : '').">Denied</option>
                            <option value='Waitlisted' ".($student['status'] == 'Waitlisted' ? 'selected' : '').">Waitlisted</option>
                        </select>
                        <button type='submit' name='update_status'>Update Status</button>
                    </form>
                    <a href='?student_id={$student['student_id']}' class='delete-btn'>Delete</a>
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
  </script>

</body>
</html>
