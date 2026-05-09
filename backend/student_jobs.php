<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Dashboard | Job Listings</title>
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --sidebar-width: 260px;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar Styles --- */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links { list-style: none; }
        .nav-item { margin-bottom: 0.5rem; }
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            transition: 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background: #eff6ff;
            color: var(--primary);
        }
        .nav-link i { margin-right: 12px; width: 20px; }

        /* --- Main Content Area --- */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem 3rem;
            max-width: 1400px;
        }

        header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h2 { font-size: 1.875rem; font-weight: 700; }

        /* --- Job Card Grid --- */
        .jobs-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .job-card {
            background: var(--bg-card);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow);
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .job-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .job-meta {
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .job-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .apply-btn {
            margin-top: auto;
            display: inline-block;
            text-align: center;
            background: var(--primary);
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }

        .apply-btn:hover { background: var(--primary-hover); }

        /* --- Mobile Responsiveness --- */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 1.5rem; }
            .mobile-header { display: flex !important; }
        }

        .mobile-header {
            display: none;
            background: white;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            justify-content: space-between;
            align-items: center;
            z-index: 1001;
        }
    </style>
</head>
<body>

    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="logo"><i class="fas fa-graduation-cap"></i> AI Career</div>
        <button onclick="toggleSidebar()" style="background:none; border:none; font-size:1.5rem;"><i class="fas fa-bars"></i></button>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>AI Career</span>
        </div>
        <ul class="nav-links">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="student_profile.php" class="nav-link active"><i class="fas fa-user"></i> Profile</a>
            </li>
            <li class="nav-item">
                <a href="resume_parser.php" class="nav-link"><i class="fas fa-file-alt"></i> Resume Parser</a>
            </li>
            <li class="nav-item">
                <a href="career.php" class="nav-link"><i class="fas fa-route"></i> Career Suggestions</a>
            </li>
            <li class="nav-item" style="margin-top: 2rem;">
                <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <h2>Available Jobs</h2>
            <div class="user-profile" style="color: var(--text-muted);">
                Welcome, <strong>Palak</strong>
            </div>
        </header>

        <div class="jobs-container">
            <?php
            include("db_connect.php");
            $result = mysqli_query($conn, "SELECT * FROM jobs");

            while($job = mysqli_fetch_assoc($result)){
                echo "<div class='job-card'>";
                echo "<h3>" . htmlspecialchars($job['title']) . "</h3>";
                echo "<div class='job-meta'><i class='fas fa-building'></i> " . htmlspecialchars($job['company']) . " | <i class='fas fa-map-marker-alt'></i> " . htmlspecialchars($job['location']) . "</div>";
                echo "<p class='job-desc'>" . htmlspecialchars($job['description']) . "</p>";
                echo "<a href='{$job['apply_link']}' class='apply-btn'>Apply Now</a>";
                echo "</div>";
            }
            ?>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.style.transform = sidebar.style.transform === 'translateX(0px)' ? 'translateX(-100%)' : 'translateX(0px)';
        }
    </script>
</body>
</html>