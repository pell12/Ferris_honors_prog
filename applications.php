<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ferris Honors Program - Applications</title>
  <link rel="stylesheet" href="styles/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>
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

  <div class="search-container">
    <input type="text" class="search-bar" id="searchInput" placeholder="Search Ferris Honors Program..." />
    <button class="search-button" onclick="performSearch()">Search</button>
    <i class="fa fa-user-circle signout-icon" aria-hidden="true"></i>
    <i class="fas fa-sign-out-alt signout-icon" onclick="signOut()"></i>
  </div>

  <main class="content">
    <h1>Applications</h1>
    <h2>Add New Student</h2>
    <form method="POST" action="currentStudents.php">
      <!-- Grouping form fields together for cleaner layout -->
      <div class="form-group">
        <label for="student_id">Student ID:</label><input type="text" id="student_id" name="student_id" required />
        <label for="first_name">First Name:</label><input type="text" id="first_name" name="first_name" required />
        <label for="middle_name">Middle Name:</label><input type="text" id="middle_name" name="middle_name" />
        <label for="preferred_name">Preferred Name:</label><input type="text" id="preferred_name" name="preferred_name" />
      </div>

      <div class="form-group">
        <label for="last_name">Last Name:</label><input type="text" id="last_name" name="last_name" required />
        <label for="fsu_email">FSU Email:</label><input type="email" id="fsu_email" name="fsu_email" required />
        <label for="personal_email">Personal Email:</label><input type="email" id="personal_email" name="personal_email" />
        <label for="cell_phone_corrected">Cell Phone:</label><input type="text" id="cell_phone_corrected" name="cell_phone_corrected" />
      </div>

      <div class="form-group">
        <label for="current_street_1">Street Address:</label><input type="text" id="current_street_1" name="current_street_1" />
        <label for="current_city">City:</label><input type="text" id="current_city" name="current_city" />
        <label for="current_state">State:</label><input type="text" id="current_state" name="current_state" />
        <label for="current_zip">Zip Code:</label><input type="text" id="current_zip" name="current_zip" />
      </div>

      <div class="form-group">
        <label for="ethnicity_current">Ethnicity:</label><input type="text" id="ethnicity_current" name="ethnicity_current" />
        <label for="gender">Gender:</label><input type="text" id="gender" name="gender" />
        <label for="current_campus">Campus:</label><input type="text" id="current_campus" name="current_campus" />
      </div>

      <div class="form-group">
        <label>Status:</label>
        <input type="radio" id="approved" name="status" value="Approved" /><label for="approved">Approved</label>
        <input type="radio" id="denied" name="status" value="Denied" /><label for="denied">Denied</label>
        <input type="radio" id="waitlisted" name="status" value="Waitlisted" /><label for="waitlisted">Waitlisted</label>
      </div>

      <button type="submit">Add Student</button>
    </form>

    <hr />

    <!-- Display Students -->
    <div id="studentList">
      <?php
      if ($students) {
          foreach ($students as $student) {
              $status = $status_lookup[$student['student_id']] ?? 'Not Available';
              echo "
                <div class='student-entry'>
                  <p><strong>Name:</strong> {$student['first_name']} {$student['middle_name']} {$student['last_name']} (Preferred: {$student['preferred_name']})</p>
                  <p><strong>Student ID:</strong> {$student['student_id']}</p>
                  <p><strong>Email:</strong> {$student['fsu_email']} | Personal: {$student['personal_email']}</p>
                  <p><strong>Phone:</strong> {$student['cell_phone_corrected']}</p>
                  <p><strong>Address:</strong> {$student['current_street_1']}, {$student['current_city']}, {$student['current_state']} {$student['current_zip']}</p>
                  <p><strong>Gender:</strong> {$student['gender']} | <strong>Ethnicity:</strong> {$student['ethnicity_current']}</p>
                  <p><strong>Campus:</strong> {$student['current_campus']}</p>
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
    function performSearch() {
      const query = document.getElementById("searchInput").value;
      alert(query ? "Searching for: " + query : "Please enter a search query.");
    }

    function signOut() {
      alert("You have signed out.");
    }

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
              if (data.success) {
                alert("Student deleted successfully.");
                location.reload();
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
