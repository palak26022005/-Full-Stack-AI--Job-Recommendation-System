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

// ✅ Handle skills/interests update
if(isset($_POST['update'])){
    $skills = $_POST['skills'];
    $interests = $_POST['interests'];

    $update = "UPDATE students SET skills='$skills', interests='$interests' WHERE id='$student_id'";
    if(mysqli_query($conn, $update)){
        echo "<div class='notification success'>Skills & Interests updated successfully!</div>";
        $result = mysqli_query($conn, $query);
        $student = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='notification error'>Error updating: " . mysqli_error($conn) . "</div>";
    }
}

// ✅ Call Python AI model
$skills = $student['skills'];
$interests = $student['interests'];

$command = escapeshellcmd("python C:\\xammp\\htdocs\\careerproject\\ai_module\\recommend.py \"$skills\" \"$interests\"");
$output = shell_exec($command);
$suggestion = json_decode($output, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Career Suggestions</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-blue: #1e3c72;
        --secondary-blue: #2a5298;
        --accent-orange: #ff7a00;
        --sidebar-width: 250px;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        background: #f4f6fc;
        color: #333;
        display: flex;
        overflow-x: hidden;
    }

    /* --- Responsive Sidebar --- */
    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(180deg, var(--primary-blue), var(--secondary-blue));
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        padding-top: 30px;
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .sidebar .logo { text-align: center; margin-bottom: 20px; }
    .sidebar .logo img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid #fff;
        object-fit: cover;
    }

    .sidebar h2 {
        text-align: center;
        font-size: 20px;
        margin-top: 10px;
        margin-bottom: 25px;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        color: #fff;
        padding: 15px 25px;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 15px;
    }

    .sidebar a:hover {
        background: rgba(255,255,255,0.15);
        border-left: 4px solid var(--accent-orange);
    }

    .sidebar a span { margin-left: 12px; }

    /* --- Mobile Navigation Header --- */
    .mobile-nav {
        display: none;
        width: 100%;
        background: var(--primary-blue);
        color: white;
        padding: 15px 20px;
        position: fixed;
        top: 0;
        z-index: 1001;
        justify-content: space-between;
        align-items: center;
    }

    .menu-toggle { font-size: 24px; cursor: pointer; }
    .close-sidebar {
        display: none;
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 24px;
        cursor: pointer;
    }

    /* --- Main Content --- */
    .main-content {
        flex: 1;
        margin-left: var(--sidebar-width);
        padding: 30px;
        width: 100%;
        transition: all 0.3s ease;
    }

    .header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .header h2 { margin: 0; font-size: 20px; color: var(--primary-blue); }

    /* Forms */
    .update-form {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    label { font-weight: 600; color: #555; display: block; margin-bottom: 8px; font-size: 14px; }
    
    input[type="text"] {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus { border-color: var(--secondary-blue); }

    .btn {
        background: var(--accent-orange);
        color: #fff;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 15px;
        width: 100%;
        transition: background 0.3s ease;
    }

    .btn:hover { background: #e86a00; }

    /* Career Cards */
    .career-section h2 { font-size: 22px; margin-bottom: 20px; color: #444; }
    
    .career-card {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        border-left: 5px solid var(--secondary-blue);
    }

    .career-card h3 { margin: 0 0 10px 0; color: var(--secondary-blue); font-size: 20px; }
    .career-card p { margin: 10px 0; color: #666; line-height: 1.6; }

    .match-bar {
        height: 10px;
        border-radius: 5px;
        background: #eee;
        margin: 15px 0;
        overflow: hidden;
    }

    .match-fill {
        height: 100%;
        border-radius: 5px;
        background: linear-gradient(90deg, #ff7a00, #ff9d42);
        transition: width 1s ease-in-out;
    }

    /* Notifications */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        z-index: 2000;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        font-weight: 500;
    }
    .success { background: #28a745; }
    .error { background: #dc3545; }

    /* Overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }

    /* --- Media Queries --- */
    @media (max-width: 768px) {
        .sidebar { left: -100%; }
        .sidebar.active { left: 0; width: 280px; }
        .sidebar-overlay.active { display: block; }
        .mobile-nav { display: flex; }
        .close-sidebar { display: block; }
        
        .main-content {
            margin-left: 0;
            padding: 90px 15px 20px 15px;
        }

        .header h2 { font-size: 18px; }
        .career-card { padding: 20px; }
    }
</style>
</head>
<body>

<!-- Overlay for Mobile -->
<div class="sidebar-overlay" id="overlay" onclick="toggleMenu()"></div>

<!-- Mobile Header -->
<div class="mobile-nav">
    <span>CareerPro</span>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
</div>

<div class="sidebar" id="sidebar">
    <div class="close-sidebar" onclick="toggleMenu()">✕</div>
    <div class="logo">
         <img src="carrer.png" alt="CareerPro Logo" class="logo-image">
    </div>
    <h2>Dashboard</h2>
    <a href="dashboard.php">🏠 <span>Home</span></a>
    <a href="profile.php">👤 <span>Profile</span></a>
    <a href="career.php">💼 <span>Career Suggestions</span></a>
    <a href="resume.php">📄 <span>Resume Upload</span></a>
     <a href="student_jobs.php"><i class="fa-solid fa-briefcase"></i> <span>Jobs</span></a>
    <a href="logout.php">🚪 <span>Logout</span></a>
</div>

<div class="main-content">
    <div class="header">
        <h2>Career Suggestions for <?php echo ucfirst($student['name']); ?> 👩‍🎓</h2>
    </div>

    <div class="update-form">
        <form method="POST">
            <label>Update Skills</label>
            <input type="text" name="skills" value="<?php echo htmlspecialchars($student['skills']); ?>" required>

            <label>Update Interests</label>
            <input type="text" name="interests" value="<?php echo htmlspecialchars($student['interests']); ?>" required>

            <button type="submit" name="update" class="btn">Update & Refresh Suggestions</button>
        </form>
    </div>

    <div class="career-section">
        <h2>Suggested Careers</h2>

        <?php if(!empty($suggestion) && empty($suggestion['error'])): ?>
            <!-- Predicted Career -->
            <div class="career-card" style="border-left-color: var(--accent-orange);">
                <h3>✨ Primary Match: <?php echo $suggestion['predicted_career']; ?></h3>
                <p><strong>Match Score:</strong> <?php echo $suggestion['probability']; ?>%</p>
                <div class="match-bar">
                    <div class="match-fill" style="width:<?php echo $suggestion['probability']; ?>%"></div>
                </div>
                <p><strong>Roadmap:</strong> <?php echo $suggestion['predicted_roadmap']; ?></p>
            </div>

            <!-- Top Similar Careers -->
            <?php
            if(!empty($suggestion['jobs'])){
                for($i=0; $i<count($suggestion['jobs']); $i++){
                    echo "<div class='career-card'>
                            <h3>".$suggestion['jobs'][$i]."</h3>
                            <p><strong>Match Score:</strong> ".$suggestion['match_scores'][$i]."%</p>
                            <div class='match-bar'><div class='match-fill' style='width:".$suggestion['match_scores'][$i]."%'></div></div>
                            <p><strong>Roadmap:</strong> ".$suggestion['roadmaps'][$i]."</p>
                          </div>";
                }
            }
            ?>
        <?php else: ?>
            <div class="career-card">
                <p>No career suggestions available yet. Try updating your skills or interests above to see AI recommendations.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleMenu() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('overlay').classList.toggle('active');
    }
</script>

</body>
</html>