<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db_connect.php");
session_start();

// ✅ Session check
if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

// ✅ Fetch student details
$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM students WHERE id='$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Student Dashboard | CareerPro</title>
    <!-- Font Awesome for sleek icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e3c72;
            --secondary: #2a5298;
            --accent: #ff7a00;
            --bg-color: #f4f6fc;
            --text-main: #333;
            --sidebar-width: 250px;
        }

        * { box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: var(--bg-color);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary), var(--secondary));
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding-top: 30px;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            transition: 0.3s;
            z-index: 1000;
        }

        .sidebar .logo { text-align: center; margin-bottom: 20px; }
        .sidebar .logo img {
            width: 75px; 
            height: 75px; 
            border-radius: 50%; 
            border: 3px solid #fff;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .sidebar .logo img:hover { transform: scale(1.1); }

        .sidebar h2 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 25px;
            font-weight: 500;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.8);
            padding: 15px 25px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
        }

        .sidebar a i { font-size: 18px; margin-right: 15px; width: 25px; text-align: center; }

        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left: 5px solid var(--accent);
        }

        /* --- MOBILE HEADER & TOGGLE --- */
        .mobile-header {
            display: none;
            background: var(--primary);
            color: white;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 1100;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .hamburger { font-size: 25px; cursor: pointer; }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            transition: 0.3s;
        }

        .header {
            background: #fff;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h2 { margin: 0; font-size: 22px; color: var(--primary); }

        /* --- BUTTONS --- */
        .btn {
            background: var(--accent);
            color: #fff;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(255, 122, 0, 0.3);
            border: none;
            cursor: pointer;
        }
        .btn:hover { background: #e86a00; transform: translateY(-2px); }
        .btn-logout { background: #ef4444; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3); }
        .btn-logout:hover { background: #dc2626; }

        /* --- PROFILE CARD --- */
        .profile-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 40px;
            margin-top: 25px;
            position: relative;
            overflow: hidden;
        }
        
        /* Decorative Background Circle */
        .profile-card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(30, 60, 114, 0.03);
            border-radius: 50%;
        }

        .profile-card img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .profile-details h3 {
            margin: 0 0 15px 0;
            color: var(--primary);
            font-size: 28px;
            font-weight: 600;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px 30px;
        }

        .info-item { display: flex; align-items: center; gap: 10px; font-size: 15px; color: #555; }
        .info-item i { color: var(--accent); width: 20px; }

        /* --- RESUME VIEWER --- */
        .resume-container {
            margin-top: 30px;
            animation: fadeIn 0.5s ease;
        }
        
        iframe {
            width: 100%;
            height: 700px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- RESPONSIVE DESIGN --- */
        @media (max-width: 992px) {
            .sidebar { left: -250px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; padding: 20px; }
            .mobile-header { display: flex; }
            
            .header { margin-top: 10px; }
            .profile-card { flex-direction: column; text-align: center; padding: 30px; }
            .info-grid { grid-template-columns: 1fr; text-align: left; margin: 0 auto; }
            .profile-card img { margin-bottom: 10px; }
        }

        @media (max-width: 600px) {
            .header h2 { font-size: 18px; }
            .header .btn { display: none; } /* Hide logout in header for mobile to save space */
            .profile-details h3 { font-size: 22px; }
        }
    </style>
</head>
<body>

    <!-- Mobile Header -->
    <div class="mobile-header">
        <span style="font-weight: 600; font-size: 18px; letter-spacing: 1px;">CareerPro</span>
        <div class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('active')">
            <i class="fa-solid fa-bars"></i>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="carrer.png" alt="CareerPro Logo">
        </div>
        <h2>STUDENT PANEL</h2>
        <a href="dashboard.php" style="background: rgba(255,255,255,0.15); border-left: 5px solid var(--accent);">
            <i class="fa-solid fa-house"></i> <span>Home</span>
        </a>
        <a href="profile.php"><i class="fa-solid fa-user-graduate"></i> <span>My Profile</span></a>
        <a href="career.php"><i class="fa-solid fa-lightbulb"></i> <span>Suggestions</span></a>
        <a href="resume.php"><i class="fa-solid fa-file-arrow-up"></i> <span>Upload Resume</span></a>
         <a href="student_jobs.php"><i class="fa-solid fa-briefcase"></i> <span>Jobs</span></a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h2>Welcome back, <?php echo ucfirst($student['name']); ?>! 👋</h2>
            <a href="logout.php" class="btn btn-logout"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>

        <div class="profile-card">
            <?php if(!empty($student['profile_pic'])): ?>
                <img src="<?php echo $student['profile_pic']; ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="default.png" alt="Profile Picture">
            <?php endif; ?>

            <div class="profile-details">
                <h3><?php echo ucfirst($student['name']); ?></h3>
                
                <div class="info-grid">
                    <div class="info-item"><i class="fa-solid fa-graduation-cap"></i> <strong>Course:</strong> <?php echo $student['course']; ?></div>
                    <div class="info-item"><i class="fa-solid fa-envelope"></i> <strong>Email:</strong> <?php echo $student['email']; ?></div>
                    <div class="info-item"><i class="fa-solid fa-code"></i> <strong>Skills:</strong> <?php echo $student['skills']; ?></div>
                    <div class="info-item"><i class="fa-solid fa-heart"></i> <strong>Interests:</strong> <?php echo $student['interests']; ?></div>
                </div>

                <div style="margin-top: 25px;">
                    <?php if(!empty($student['resume_path'])): ?>
                        <a href="?view_resume=true" class="btn">
                            <i class="fa-solid fa-file-pdf"></i> View Saved Resume
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resume Viewer Section -->
        <?php
        if(isset($_GET['view_resume']) && !empty($student['resume_path'])){
            $resume_full_path = $student['resume_path'];
            echo "<div class='resume-container'>";
            if(file_exists($resume_full_path)){
                echo "<div style='display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;'>
                        <h2 style='font-size:20px; color:var(--primary);'><i class='fa-solid fa-eye'></i> Resume Preview</h2>
                        <a href='?view=false' style='color:#666; text-decoration:none;'><i class='fa-solid fa-xmark'></i> Close</a>
                      </div>";
                echo "<iframe src='" . $resume_full_path . "'></iframe>";
            } else {
                echo "<div style='background:#fee2e2; color:#dc2626; padding:15px; border-radius:10px; border-left:5px solid #ef4444;'>
                        <i class='fa-solid fa-circle-exclamation'></i> Resume file not found on server!
                      </div>";
            }
            echo "</div>";
        }
        ?>
    </div>

    <!-- Script to handle sidebar closing on mobile click outside -->
    <script>
        document.addEventListener('click', function(event) {
            var sidebar = document.getElementById('sidebar');
            var hamburger = document.querySelector('.hamburger');
            if (window.innerWidth < 992) {
                if (!sidebar.contains(event.target) && !hamburger.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>

</body>
</html>