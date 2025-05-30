<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferris Honors Program</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <?php include 'includes/database-connection.php'; ?>
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="logo">
                <img src="images/ferris-logo.png" alt="Ferris State University Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.php"  class="active">Dashboard</a></li>
                <li><a href="applications.php">Applications</a></li>
                <li><a href="currentStudents.php">Current Students</a></li>
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

        <!-- Main Content Area -->
        <main class="content">
            <h1>Dashboard</h1>
            <h2>OverView</h2>

                <div class="grid-container">
                <div>New applications<br><br>Inbox<i class="fa fa-inbox" aria-hidden="true"></i></div>
                <div>Student Uploads<br><br>Inbox<i class="fa fa-inbox" aria-hidden="true"></i></div>
                <div>Student Status<br><br>Inbox<i class="fa fa-inbox" aria-hidden="true"></i></div>  
                <div>Students<br><br>
                    <canvas id="myChart2" style="width:100%;max-width:600px"></canvas>

                <script>
                const xValues2 = ["Current", "New",];
                const yValues2 = [55, 15, 0];
                const barColors2 = ["red", "green","blue","orange","brown"];

                new Chart("myChart2", {
                type: "bar",
                data: {
                    labels: xValues2,
                    datasets: [{
                    backgroundColor: barColors2,
                    data: yValues2
                    }]
                },
                options: {
                    legend: {display: false},
                    title: {
                    display: true,
                    text: "Students 2025"
                    }
                }
                });
                </script>
                </div>
                <div>Activity Tracker<br><br>
                    <canvas id="myChart" style="width:100%;max-width:600px"></canvas>

                    <script>
                    const xValues = ["Completed", "In-Progress", "Incomplete"];
                    const yValues = [55, 24, 15, 0];
                    const barColors = ["red", "brown", "Green"];

                    new Chart("myChart", {
                    type: "pie",
                    data: {
                        labels: xValues,
                        datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                        }]
                    },
                    options: {
                        title: {
                        display: true,
                        text: "Student Activity Tracker 2025"
                        }
                    }
                    });
                    </script>
                </div>
                <div>Cultural/Service Events<br><br>  
                <canvas id="myChart3" style="width:100%;max-width:600px"></canvas>

                <script>
                const xValues3 = [100,200,300,400,500,600,700,800,900,1000];

                new Chart("myChart3", {
                type: "line",
                data: {
                    labels: xValues3,
                    datasets: [{ 
                    label: "Event 1",
                    data: [860,940,1060,1160,1270,1510,1830,2010,2330,2478],
                    borderColor: "red",
                    fill: false
                    }, 
                    {
                    label: "Event 2", 
                    data: [1600,1700,1900,2100,2200,2700,3000,4000,5000,6000],
                    borderColor: "green",
                    fill: false
                    }, 
                    {
                    label: "Event 3", 
                    data: [300,700,1000,1200,1400,1700,2000,2200,2500,2800],
                    borderColor: "blue",
                    fill: false
                    }]
                },
                options: {
                    legend: {display: true}
                }
                });
                </script>
                </div>
</div>
        </main>
    </div>

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

<script>
    // Load navigation from JSON file and populate sidebar
    fetch('sidebarNavigationHandler.json')
        .then(response => response.json())
        .then(data => {
            const sidebar = document.getElementById('sidebarLinks');
            let currentPage = window.location.pathname.split('/').pop(); // Get current page name (e.g., 'index.php')
            
            // Check if the currentPage doesn't exist and default to 'index.php'
            if (!currentPage) {
                currentPage = 'index.php';
            }

            data.forEach(item => {
                const li = document.createElement('li');
                const isActive = item.url.includes(currentPage) ? 'active' : ''; // Compare URL with current page
                li.innerHTML = `<a href="${item.url}" class="${isActive}">${item.name}</a>`;
                sidebar.appendChild(li);
            });
        })
        .catch(error => console.error('Error loading navigation:', error));
</script>

</body>
</html>
