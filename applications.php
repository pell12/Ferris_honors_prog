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

// This block is unnecessary if you're not using $students anywhere below.
// Keeping it commented for now in case you want to reuse later.
/*
$query = "
    SELECT student_id, first_name, last_name, fsu_email
    FROM student
";
$stmt = $pdo->query($query);
$students = $stmt->fetchAll();
*/
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
    <h1>Applications</h1>

    <!-- Application Form -->
    <form id="applicationForm" class="application-form" method="POST" action="#">
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

    <!-- Display Applications Here -->
    <div id="applicationList"></div>
  </main>

  <script>
    let editIndex = null;

    function performSearch() {
      const query = document.getElementById("searchInput").value;
      alert(query ? "Searching for: " + query : "Please enter a search query.");
    }

    function signOut() {
      alert("You have signed out.");
    }

    function displayApplications() {
      const applications = JSON.parse(localStorage.getItem("applications")) || [];
      const container = document.getElementById("applicationList");
      container.innerHTML = "";

      applications.forEach((app, index) => {
        const div = document.createElement("div");
        div.classList.add("application-entry");
        div.innerHTML = `
          <p><strong>Name:</strong> ${app.name}</p>
          <p><strong>Student ID:</strong> ${app.studentId}</p>
          <p><strong>Program:</strong> ${app.program}</p>
          <p><strong>Date:</strong> ${app.date}</p>
          <p><strong>Status:</strong> ${app.status}</p>
          <button class="edit-btn" data-index="${index}">Edit</button>
          <button class="delete-btn" data-index="${index}">Delete</button>
          <button class="wait-btn" data-index="${index}">Mark Waitlisted</button>
          <hr />
        `;
        container.appendChild(div);
      });

      document.querySelectorAll(".delete-btn").forEach((button) => {
        button.addEventListener("click", function () {
          const index = this.getAttribute("data-index");
          const applications = JSON.parse(localStorage.getItem("applications")) || [];
          applications.splice(index, 1);
          localStorage.setItem("applications", JSON.stringify(applications));
          displayApplications();
        });
      });

      document.querySelectorAll(".edit-btn").forEach((button) => {
        button.addEventListener("click", function () {
          editIndex = this.getAttribute("data-index");
          const app = JSON.parse(localStorage.getItem("applications"))[editIndex];

          document.getElementById("name").value = app.name;
          document.getElementById("studentId").value = app.studentId;
          document.getElementById("program").value = app.program;
          document.getElementById("date").value = app.date;

          if (app.status === "Approved") {
            document.getElementById("approve").checked = true;
          } else if (app.status === "Denied") {
            document.getElementById("deny").checked = true;
          } else if (app.status === "Waitlisted") {
            document.getElementById("wait").checked = true;
          }

          window.scrollTo({ top: 0, behavior: "smooth" });
        });
      });

      document.querySelectorAll(".wait-btn").forEach((button) => {
        button.addEventListener("click", function () {
          const index = this.getAttribute("data-index");
          const applications = JSON.parse(localStorage.getItem("applications")) || [];
          applications[index].status = "Waitlisted";
          localStorage.setItem("applications", JSON.stringify(applications));
          displayApplications();
        });
      });
    }

    document.getElementById("applicationForm").addEventListener("submit", function (e) {
      e.preventDefault();

      const name = document.getElementById("name").value;
      const studentId = document.getElementById("studentId").value;
      const program = document.getElementById("program").value;
      const date = document.getElementById("date").value;
      const status = document.querySelector('input[name="status"]:checked')?.value;

      if (!status) {
        alert("Please select a status.");
        return;
      }

      const applications = JSON.parse(localStorage.getItem("applications")) || [];
      const newEntry = { name, studentId, program, date, status };

      if (editIndex === null) {
        applications.push(newEntry);
      } else {
        applications[editIndex] = newEntry;
        editIndex = null;
      }

      localStorage.setItem("applications", JSON.stringify(applications));
      alert("Application saved successfully!");
      displayApplications();
      this.reset();
    });

    window.addEventListener("DOMContentLoaded", () => {
      displayApplications();
    });

    fetch("sidebarNavigationHandler.json")
      .then((response) => response.json())
      .then((data) => {
        const sidebar = document.getElementById("sidebarLinks");
        let currentPage = window.location.pathname.split("/").pop() || "index.php";

        data.forEach((item) => {
          const li = document.createElement("li");
          const isActive = item.url.includes(currentPage) ? "active" : "";
          li.innerHTML = `<a href="${item.url}" class="${isActive}">${item.name}</a>`;
          sidebar.appendChild(li);
        });
      })
      .catch((error) => console.error("Error loading navigation:", error));
  </script>

</body>
</html>
