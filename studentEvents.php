<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Events</title>
    <link rel="stylesheet" href="styles/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
<?php
require 'includes/database-connection.php';

$query = "
    SELECT student_id, first_name, last_name, fsu_email
    FROM student
";
$stmt = $pdo->query($query);
$students = $stmt->fetchAll();
?>
    <div class="container">
        <nav class="sidebar">
            <div class="logo">
                <img src="images/ferris-logo.png" alt="Ferris State University Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="applications.php">Applications</a></li>
                <li><a href="currentStudents.php">Current Students</a></li>
                <li><a href="semesterGradeReport.php">Semester Grade Report</a></li>
                <li><a href="studentEvents.php" class="active">Student Events</a></li>
                <li><a href="uploadDataSync.php">Upload/Data Sync</a></li>
            </ul>
        </nav>

        <div class="search-container">
            <input type="text" class="search-bar" id="searchInput" placeholder="Search Ferris Honors Program...">
            <button class="search-button" onclick="performSearch()">Search</button>
            <!-- Sign-out Icon (Font Awesome) -->
            <i class="fa fa-user-circle signout-icon" aria-hidden="true"></i>
            <i class="fas fa-sign-out-alt signout-icon" onclick="signOut()"></i>
        </div>

        <main class="content">
            <h1>Student Events</h1>
            <p>Welcome to the Student Events page. Here you can manage student events.</p>

            <div class="activity-form">
                <h2>Record Student Honors Activity</h2>
                <label for="activityType">Select Activity Type:</label>
                <select id="activityType" onchange="showStudentForm()">
                    <option value="">-- Select an Activity --</option>
                    <option value="honorsClass">Honors Class</option>
                    <option value="honorsContract">Honors Contract</option>
                    <option value="coCurricular">Co-Curricular Project</option>
                    <option value="studyAbroad">Study Abroad</option>
                    <option value="serviceHours">15 Service Hours + 3 Events</option>
                </select>

                <div id="studentForm" style="display: none;">
                    <h3>Enter Student Information</h3>
                    <label for="studentId">Student ID:</label>
                    <input type="text" id="studentId" placeholder="Enter Student ID" required>

                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" placeholder="Enter First Name" required>

                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" placeholder="Enter Last Name" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" placeholder="Enter Email" required>

                    <button onclick="saveStudentActivity()">Save Student Activity</button>
                </div>
            </div>

            <div class="certificate-tracking">
                <h2>Bachelor’s Certificate Requirements</h2>
                <label>
                    <input type="checkbox" id="leadershipRole"> Leadership Role Completed
                </label>
                <label>
                    <input type="checkbox" id="seniorSymposium"> Senior Symposium Attended
                </label>
                <label for="symposiumDate">Symposium Date:</label>
                <input type="date" id="symposiumDate">

                <button onclick="saveCertificateProgress()">Save Progress</button>
            </div>

            <div class="student-list">
                <h2>Recorded Students & Activities</h2>
                <label for="searchStudentId">Search by Student ID:</label>
                <input type="text" id="searchStudentId" placeholder="Enter Student ID" onkeyup="filterByStudentId()">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Activity Type</th>
                            <th>Date Recorded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        <?php
                        foreach ($students as $student) {
                            $studentId = htmlspecialchars($student['student_id']);
                            $firstName = htmlspecialchars($student['first_name']);
                            $lastName = htmlspecialchars($student['last_name']);
                            $email = htmlspecialchars($student['fsu_email']);
                            $dateRecorded = date("m-d-Y");

                            echo "
                            <tr>
                                <td>$studentId</td>
                                <td>$firstName</td>
                                <td>$lastName</td>
                                <td><a href='mailto:$email'>$email</a></td>
                                <td>—</td> <!-- Placeholder for Activity Type -->
                                <td>$dateRecorded</td>
                                <td>
                                    <button onclick='editRow(this)'>✏️</button>
                                    <button onclick='deleteRow(this)'>❌</button>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        fetch('sidebarNavigationHandler.json')
            .then(response => response.json())
            .then(data => {
                const sidebar = document.getElementById('sidebarLinks');
                let currentPage = window.location.pathname.split('/').pop();
                if (!currentPage) currentPage = 'index.php';

                data.forEach(item => {
                    const li = document.createElement('li');
                    const isActive = item.url.includes(currentPage) ? 'active' : '';
                    li.innerHTML = `<a href="${item.url}" class="${isActive}">${item.name}</a>`;
                    sidebar.appendChild(li);
                });
            })
            .catch(error => console.error('Error loading navigation:', error));

        function showStudentForm() {
            const activityType = document.getElementById("activityType").value;
            document.getElementById("studentForm").style.display = activityType ? "block" : "none";
        }

        function saveStudentActivity() {
            const studentId = document.getElementById("studentId").value.trim();
            const firstName = document.getElementById("firstName").value.trim();
            const lastName = document.getElementById("lastName").value.trim();
            const email = document.getElementById("email").value.trim();
            const activityType = document.getElementById("activityType").value;
            const date = new Date().toLocaleDateString();

            if (!studentId || !firstName || !lastName || !email || !activityType) {
                alert("Please fill out all fields.");
                return;
            }

            if (!/^\d{7,}$/.test(studentId)) {
                alert("Student ID must be at least 7 digits.");
                return;
            }

            if (!/^[\w.-]+@ferris\.edu$/.test(email)) {
                alert("Email must be a valid @ferris.edu address.");
                return;
            }

            const table = document.getElementById("studentTableBody");
            const row = table.insertRow();
            row.innerHTML = `
                <td>${studentId}</td>
                <td>${firstName}</td>
                <td>${lastName}</td>
                <td>${email}</td>
                <td>${activityType.replace(/([A-Z])/g, ' $1')}</td>
                <td>${date}</td>
                <td>
                    <button onclick="editRow(this)">✏️</button>
                    <button onclick="deleteRow(this)">❌</button>
                </td>
            `;

            document.getElementById("studentId").value = "";
            document.getElementById("firstName").value = "";
            document.getElementById("lastName").value = "";
            document.getElementById("email").value = "";
            document.getElementById("activityType").value = "";
            document.getElementById("studentForm").style.display = "none";
        }

        function filterByStudentId() {
            const input = document.getElementById("searchStudentId").value.trim().toLowerCase();
            const table = document.getElementById("studentTableBody");
            const rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                const studentIdCell = rows[i].getElementsByTagName("td")[0]; // First cell is Student ID
                if (studentIdCell) {
                    const studentId = studentIdCell.textContent || studentIdCell.innerText;
                    rows[i].style.display = studentId.toLowerCase().includes(input) ? "" : "none";
                }
            }
        }

        function editRow(btn) {
            const row = btn.parentElement.parentElement;
            document.getElementById("studentId").value = row.cells[0].innerText;
            document.getElementById("firstName").value = row.cells[1].innerText;
            document.getElementById("lastName").value = row.cells[2].innerText;
            document.getElementById("email").value = row.cells[3].innerText;
            document.getElementById("activityType").value = row.cells[4].innerText;
            document.getElementById("studentForm").style.display = "block";

            row.remove();
        }

        function deleteRow(btn) {
            const row = btn.parentElement.parentElement;
            const confirmation = confirm("Are you sure you want to delete this record?");
            
            if (confirmation) {
                row.remove();
            }
        }

        function saveCertificateProgress() {
            const leadershipRole = document.getElementById("leadershipRole").checked;
            const seniorSymposium = document.getElementById("seniorSymposium").checked;
            const symposiumDate = document.getElementById("symposiumDate").value;

            alert(`Saved:\nLeadership Role: ${leadershipRole}\nSenior Symposium: ${seniorSymposium}\nSymposium Date: ${symposiumDate || 'N/A'}`);
        }

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
