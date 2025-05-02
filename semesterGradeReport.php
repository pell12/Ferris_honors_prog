<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Semester Grade Report</title>
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

  <main class="content">
    <h1>Semester Grade Report</h1>

    <!-- Add Entry Form -->
    <div class="form-container">
      <form id="gradeForm">
        <input type="text" id="lastName" placeholder="Last Name" required />
        <input type="text" id="firstName" placeholder="First Name" required />
        <input type="text" id="studentId" placeholder="Student ID" required />
        <input type="text" id="course" placeholder="Course" required />
        <input type="text" id="crn" placeholder="CRN" required />
        <input type="text" id="midTerm" placeholder="Mid-Term Grade" required />
        <input type="text" id="finalGrade" placeholder="Final Grade" required />
        <button type="submit">Save Entry</button>
      </form>
    </div>

    <!-- Grade Table -->
    <table>
      <caption>Semester Grade Report</caption>
      <thead>
        <tr>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Student ID</th>
          <th>Course</th>
          <th>CRN</th>
          <th>Mid-Term Grade</th>
          <th>Final Grade</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="gradeTableBody">
        <!-- Dynamic rows go here -->
      </tbody>
    </table>
  </main>

  <script>
    const form = document.getElementById('gradeForm');
    const tableBody = document.getElementById('gradeTableBody');
    let editIndex = null; // Track if editing

    // Load existing grades on page load
    window.addEventListener('DOMContentLoaded', loadGrades);

    // Handle form submission
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const entry = {
        lastName: document.getElementById('lastName').value,
        firstName: document.getElementById('firstName').value,
        studentId: document.getElementById('studentId').value,
        course: document.getElementById('course').value,
        crn: document.getElementById('crn').value,
        midTerm: document.getElementById('midTerm').value,
        finalGrade: document.getElementById('finalGrade').value
      };

      const grades = JSON.parse(localStorage.getItem('grades')) || [];

      if (editIndex === null) {
        grades.push(entry);
      } else {
        grades[editIndex] = entry;
        editIndex = null;
      }

      localStorage.setItem('grades', JSON.stringify(grades));
      form.reset();
      loadGrades();
    });

    // Load and render grades from localStorage
    function loadGrades() {
      const grades = JSON.parse(localStorage.getItem('grades')) || [];
      tableBody.innerHTML = '';

      grades.forEach((grade, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${grade.lastName}</td>
          <td>${grade.firstName}</td>
          <td>${grade.studentId}</td>
          <td>${grade.course}</td>
          <td>${grade.crn}</td>
          <td>${grade.midTerm}</td>
          <td>${grade.finalGrade}</td>
          <td>
            <button onclick="editGrade(${index})">Edit</button>
            <button class="delete-btn" onclick="deleteGrade(${index})">Delete</button>
          </td>
        `;
        tableBody.appendChild(row);
      });
    }

    // Edit grade
    function editGrade(index) {
      const grades = JSON.parse(localStorage.getItem('grades')) || [];
      const grade = grades[index];

      document.getElementById('lastName').value = grade.lastName;
      document.getElementById('firstName').value = grade.firstName;
      document.getElementById('studentId').value = grade.studentId;
      document.getElementById('course').value = grade.course;
      document.getElementById('crn').value = grade.crn;
      document.getElementById('midTerm').value = grade.midTerm;
      document.getElementById('finalGrade').value = grade.finalGrade;

      editIndex = index;
    }

    // Delete grade by index
    function deleteGrade(index) {
      const grades = JSON.parse(localStorage.getItem('grades')) || [];
      grades.splice(index, 1);
      localStorage.setItem('grades', JSON.stringify(grades));
      loadGrades();
    }
  </script>
</body>
</html>
