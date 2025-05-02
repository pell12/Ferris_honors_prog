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

$query = "
    SELECT s.student_id, s.last_name, s.first_name, s.middle_name, s.preferred_name, s.fsu_email, a.major
    FROM student s
    LEFT JOIN academic_records a ON s.student_id = a.student_id
";
$stmt = $pdo->query($query);
$students = $stmt->fetchAll();
?>
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

  <!-- Main Content -->
  <main class="content">
    <h1>Current Students</h1>

    <!-- Filter -->
    <div class="filter-container">
      <input type="text" id="filterName" placeholder="Filter by Name">
      <input type="text" id="filterMajor" placeholder="Filter by Major">
    </div>

    <!-- Add Student Form -->
    <div class="form-container">
      <form id="studentForm">
        <input type="text" id="name" placeholder="Student Name" required />
        <input type="text" id="studentId" placeholder="Student ID" required />
        <input type="text" id="preferredName" placeholder="Preferred Name" />
        <input type="text" id="major" placeholder="Major" required />
        <input type="text" id="email" placeholder="Email" />
        <button type="submit">Add Student</button>
      </form>
    </div>

    <!-- Student Table -->
    <table>
      <thead>
        <tr>
          <th onclick="sortTable('name')">Student Name ▲▼</th>
          <th>Student ID</th>
          <th>Preferred Name</th>
          <th onclick="sortTable('major')">Major ▲▼</th>
          <th>Email</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="studentTableBody">
      <?php
    // Loop through the students array and populate the table rows
    foreach ($students as $student) {
        // Extract student data from the array
        $studentName = $student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name'];
        $studentId = htmlspecialchars($student['student_id']);
        $preferredName = htmlspecialchars($student['preferred_name']);
        $major = htmlspecialchars($student['major']);
        $email = htmlspecialchars($student['fsu_email']);
        
        echo "
        <tr>
          <td>$studentName</td>
          <td>$studentId</td>
          <td>$preferredName</td>
          <td>$major</td>
          <td><a href='mailto:$email'>$email</a></td>
          <td>
              <button onclick='editRow(this)'>✏️</button>
              <button onclick='deleteRow(this)'>❌</button>
          </td>
        </tr>";
    }
    ?>
      </tbody>
    </table>
  </main>

  <!-- Script for Logic -->
  <script>
    const form = document.getElementById('studentForm');
    const tableBody = document.getElementById('studentTableBody');
    const filterName = document.getElementById('filterName');
    const filterMajor = document.getElementById('filterMajor');

    let students = JSON.parse(localStorage.getItem('students')) || [];

    function updateStorage() {
      localStorage.setItem('students', JSON.stringify(students));
      renderTable();
    }

    /**function renderTable() {
      const nameQuery = filterName.value.toLowerCase();
      const majorQuery = filterMajor.value.toLowerCase();

      tableBody.innerHTML = '';

      students
        .filter(s =>
          s.name.toLowerCase().includes(nameQuery) &&
          s.major.toLowerCase().includes(majorQuery)
        )
        .forEach((student, index) => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${student.name}</td>
            <td>${student.studentId}</td>
            <td>${student.preferredName || ''}</td>
            <td>${student.major}</td>
            <td>${student.email || ''}</td>
            <td><button class="delete-btn" onclick="deleteStudent(${index})">Delete</button></td>
          `;
          tableBody.appendChild(row);
        });
    }**/

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const student = {
        name: document.getElementById('name').value,
        studentId: document.getElementById('studentId').value,
        preferredName: document.getElementById('preferredName').value,
        major: document.getElementById('major').value,
        email: document.getElementById('email').value
      };
      students.push(student);
      updateStorage();
      form.reset();
    });

    function deleteStudent(index) {
      students.splice(index, 1);
      updateStorage();
    }

    function sortTable(key) {
      students.sort((a, b) => a[key].localeCompare(b[key]));
      updateStorage();
    }

    filterName.addEventListener('input', renderTable);
    filterMajor.addEventListener('input', renderTable);

    renderTable();
  </script>
</body>
</html>
