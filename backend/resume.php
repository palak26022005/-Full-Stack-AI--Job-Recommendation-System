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

$student_id = $_SESSION['student_id'];

// ✅ Fetch student details
$query = "SELECT * FROM students WHERE id='$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

$suggestion = null;

// ✅ Handle Resume Upload
if(isset($_POST['upload_resume'])){
    $target_dir = "backend/uploads/resumes/";
    if(!is_dir($target_dir)){
        mkdir($target_dir, 0777, true);
    }

    $resume_path = $target_dir . basename($_FILES["resume"]["name"]);
    if(move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_path)){
        // ✅ Call Python resume parser
        $command = "C:\\Users\\lenovo\\AppData\\Local\\Programs\\Python\\Python313\\python.exe C:\\xammp\\htdocs\\careerproject\\ai_module\\resume_parser.py \"$resume_path\"";
        $output = shell_exec($command);

        if($output === null || trim($output) === ""){
            $suggestion = ["error" => "Python parser did not return any output. Check resume_parser.py."];
        } else {
            $parsed = json_decode($output, true);
            if($parsed === null){
                $suggestion = ["error" => "Invalid JSON returned from parser: ".$output];
            } else {
                if(!empty($parsed["extracted_skills"])){
                    $skills_str = implode(" ", $parsed["extracted_skills"]);

                    // ✅ Call recommend.py with extracted skills
                    $command = "C:\\Users\\lenovo\\AppData\\Local\\Programs\\Python\\Python313\\python.exe C:\\xammp\\htdocs\\careerproject\\ai_module\\recommend.py \"$skills_str\" \"\"";
                    $output = shell_exec($command);

                    if($output === null || trim($output) === ""){
                        $suggestion = ["error" => "AI model did not return any output. Check recommend.py."];
                    } else {
                        $suggestion = json_decode($output, true);
                        if($suggestion === null){
                            $suggestion = ["error" => "Invalid JSON returned from AI model: ".$output];
                        }
                    }
                } else {
                    $suggestion = ["error" => "No skills found in resume."];
                }
            }
        }
    } else {
        $suggestion = ["error" => "Resume upload failed."];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Upload & Career Suggestions</title>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e3c72;
            --secondary: #2a5298;
            --accent: #ff7a00;
            --bg-color: #f4f6fc;
            --text-dark: #333;
            --sidebar-width: 250px;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background: var(--bg-color); color: var(--text-dark); overflow-x: hidden; }

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
        .sidebar h2 { text-align: center; font-size: 18px; margin-top: 10px; margin-bottom: 25px; letter-spacing: 1px; }

        .sidebar a {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.8);
            padding: 15px 25px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
            font-weight: 500;
        }
        .sidebar a i { font-size: 18px; margin-right: 15px; width: 25px; text-align: center; }
        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left: 5px solid var(--accent);
        }

        /* --- MOBILE TOGGLE --- */
        .mobile-header {
            display: none;
            background: var(--primary);
            color: white;
            padding: 15px;
            position: sticky;
            top: 0;
            z-index: 1100;
            justify-content: space-between;
            align-items: center;
        }

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
            margin-bottom: 30px;
            border-left: 6px solid var(--accent);
        }
        .header h2 { margin: 0; font-size: 24px; font-weight: 600; color: var(--primary); }

        .upload-form {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        .upload-form label { font-weight: 600; display: block; margin-bottom: 15px; color: #555; }
        
        input[type="file"] {
            width: 100%;
            padding: 12px;
            background: #f8f9fa;
            border: 2px dashed #cbd5e0;
            border-radius: 10px;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .btn {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 122, 0, 0.3);
        }
        .btn:hover { background: #e86a00; transform: translateY(-2px); }

        /* --- CAREER CARDS --- */
        .career-section h2 { font-size: 22px; margin-bottom: 20px; color: var(--primary); display: flex; align-items: center; }
        .career-section h2::after { content: ""; flex: 1; height: 2px; background: #ddd; margin-left: 20px; }

        .career-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            border-top: 4px solid var(--secondary);
            transition: 0.3s;
        }
        .career-card:hover { transform: scale(1.01); }
        .career-card h3 { margin-top: 0; color: var(--secondary); font-size: 20px; }
        
        .match-bar-container { margin: 15px 0; }
        .match-bar { height: 10px; border-radius: 5px; background: #e2e8f0; position: relative; overflow: hidden; }
        .match-fill { height: 100%; border-radius: 5px; background: linear-gradient(90deg, var(--accent), #ff9a44); transition: width 1s ease-in-out; }
        
        .roadmap-box {
            background: #f9fbff;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #cbd5e0;
            margin-top: 15px;
            font-size: 14.5px;
            color: #444;
        }

        .error { background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 10px; border-left: 5px solid #ef4444; margin-bottom: 20px; font-weight: 500; }

        /* --- RESPONSIVENESS --- */
        @media (max-width: 992px) {
            .sidebar { left: -250px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0; padding: 20px; }
            .mobile-header { display: flex; }
            .header h2 { font-size: 18px; }
        }

        @media (max-width: 600px) {
            .upload-form { padding: 20px; }
            .btn { width: 100%; }
            .career-card h3 { font-size: 18px; }
        }
    </style>
</head>
<body>

    <!-- Mobile Navigation Bar -->
    <div class="mobile-header">
        <span style="font-weight: 600; font-size: 18px;">CareerPro</span>
        <i class="fa-solid fa-bars" id="menu-toggle" style="font-size: 24px; cursor: pointer;"></i>
    </div>

    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="carrer.png" alt="Logo">
        </div>
        <h2>DASHBOARD</h2>
        <a href="dashboard.php"><i class="fa-solid fa-house"></i> <span>Home</span></a>
        <a href="profile.php"><i class="fa-solid fa-user-gear"></i> <span>Profile</span></a>
        <a href="career.php"><i class="fa-solid fa-briefcase"></i> <span>Suggestions</span></a>
        <a href="resume.php" style="background: rgba(255,255,255,0.15); border-left: 5px solid var(--accent);"><i class="fa-solid fa-file-arrow-up"></i> <span>Resume Upload</span></a>
         <a href="student_jobs.php"><i class="fa-solid fa-briefcase"></i> <span>Jobs</span></a>
        <a href="logout.php"><i class="fa-solid fa-power-off"></i> <span>Logout</span></a>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="header">
            <h2><i class="fa-solid fa-wand-magic-sparkles" style="color: var(--accent);"></i> Career Suggestions for <?php echo ucfirst($student['name']); ?></h2>
        </div>

        <!-- Upload Form -->
        <div class="upload-form">
            <form method="POST" enctype="multipart/form-data">
                <label><i class="fa-solid fa-cloud-arrow-up"></i> Upload your Resume (PDF or DOC)</label>
                <input type="file" name="resume" required>
                <button type="submit" name="upload_resume" class="btn">Analyze & Generate Suggestions</button>
            </form>
        </div>

        <div class="career-section">
            <h2><i class="fa-solid fa-bolt" style="color: var(--accent); margin-right: 10px;"></i> Suggested Careers</h2>

            <?php if(!empty($suggestion)): ?>
                <?php if(!empty($suggestion['error'])): ?>
                    <div class="error"><i class="fa-solid fa-triangle-exclamation"></i> Error: <?php echo $suggestion['error']; ?></div>
                <?php else: ?>
                    
                    <!-- Predicted Career (Highest Match) -->
                    <div class="career-card" style="border-top-color: var(--accent);">
                        <span style="color: var(--accent); font-weight: bold; text-transform: uppercase; font-size: 12px; display: block; margin-bottom: 5px;">🔥 Top Match</span>
                        <h3><?php echo $suggestion['predicted_career']; ?></h3>
                        <div class="match-bar-container">
                            <span style="font-size: 13px; font-weight: 600;">Match Score: <?php echo $suggestion['probability']; ?>%</span>
                            <div class="match-bar">
                                <div class="match-fill" style="width:<?php echo $suggestion['probability']; ?>%"></div>
                            </div>
                        </div>
                        <div class="roadmap-box">
                            <strong><i class="fa-solid fa-map-location-dot"></i> Success Roadmap:</strong><br>
                            <?php echo $suggestion['predicted_roadmap']; ?>
                        </div>
                    </div>

                    <!-- Top Similar Careers -->
                    <?php
                    if(!empty($suggestion['jobs'])){
                        for($i=0; $i<count($suggestion['jobs']); $i++){
                            echo "<div class='career-card'>
                                    <h3>".$suggestion['jobs'][$i]."</h3>
                                    <div class='match-bar-container'>
                                        <span style='font-size: 13px; font-weight: 600;'>Match Score: ".$suggestion['match_scores'][$i]."%</span>
                                        <div class='match-bar'>
                                            <div class='match-fill' style='width:".$suggestion['match_scores'][$i]."%'></div>
                                        </div>
                                    </div>
                                    <div class='roadmap-box'>
                                        <strong><i class='fa-solid fa-route'></i> Recommended Path:</strong><br>
                                        ".$suggestion['roadmaps'][$i]."
                                    </div>
                                  </div>";
                        }
                    }
                    ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Script to toggle Sidebar on Mobile -->
    <script>
        const toggleBtn = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 992 && !sidebar.contains(e.target) && e.target !== toggleBtn) {
                sidebar.classList.remove('active');
            }
        });
    </script>

</body>
</html>