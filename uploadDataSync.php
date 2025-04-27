<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload/Data Sync</title>
  <link rel="stylesheet" href="styles/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <style>
    .error { background-color: #ffcccc; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
      <div class="logo">
        <img src="images/ferris-logo.png" alt="Ferris State University Logo" />
      </div>
      <ul class="nav-links">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="applications.php">Applications</a></li>
                <li><a href="currentStudents.php">Current Students</a></li>
                <li><a href="semesterGradeReport.php">Semester Grade Report</a></li>
                <li><a href="studentEvents.php">Student Events</a></li>
                <li><a href="uploadDataSync.php" class="active">Upload/Data Sync</a></li>
            </ul>
    </nav>

    <!-- Main Content Area -->
    <main class="content">
      <h1>Upload / Data Sync</h1>
      <p>Upload Cultural Events or Service Activities from Excel.</p>

      <!-- Upload Controls -->
      <div class="upload-controls">
        <label for="uploadType">Select Upload Type:</label>
        <select id="uploadType">
          <option value="">-- Select Type --</option>
          <option value="cultural">Cultural Events</option>
          <option value="service">Service Activities</option>
        </select>

        <label for="excelFile">Choose Excel File:</label>
        <input type="file" id="excelFile" accept=".xlsx, .xls" />

        <button onclick="handleFileUpload()">Preview Data</button>
      </div>

      <!-- Preview Table -->
      <div id="previewContainer"></div>
    </main>
  </div>

  <script>
    // Load Sidebar Navigation
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
      });

    function validateStudent(id, email) {
      return /^\d{7,}$/.test(id) && email?.endsWith('@ferris.edu');
    }

    function handleFileUpload() {
      const file = document.getElementById("excelFile").files[0];
      const type = document.getElementById("uploadType").value;
      const previewContainer = document.getElementById("previewContainer");
      previewContainer.innerHTML = "";

      if (!file || !type) {
        alert("Please select both an upload type and a file.");
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: "array" });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        renderPreview(rows, type);
      };
      reader.readAsArrayBuffer(file);
    }

    function renderPreview(rows, type) {
      if (rows.length < 2) return;

      const headers = rows[0];
      const dataRows = rows.slice(1);
      let html = `<h3>Preview: ${type.charAt(0).toUpperCase() + type.slice(1)}</h3><table><thead><tr>`;
      headers.forEach(header => html += `<th>${header}</th>`);
      html += `</tr></thead><tbody>`;

      dataRows.forEach(row => {
        let isValid = true;
        let idIndex = type === "service" ? 1 : 1;
        let emailIndex = type === "cultural" ? 4 : null;

        const id = row[idIndex];
        const email = emailIndex !== null ? row[emailIndex] : "dummy@ferris.edu";

        if (!validateStudent(id, email)) isValid = false;

        html += `<tr${isValid ? "" : ' class="error"'}>`;
        row.forEach(cell => html += `<td>${cell ?? ""}</td>`);
        html += `</tr>`;
      });

      html += `</tbody></table><p style="color:red;">* Rows highlighted in red have invalid student ID or email</p>`;
      document.getElementById("previewContainer").innerHTML = html;
    }
  </script>
</body>
</html>
