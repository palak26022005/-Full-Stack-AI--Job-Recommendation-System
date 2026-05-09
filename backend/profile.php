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

// ✅ Handle form submission
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $course = $_POST['course'];
    $skills = $_POST['skills'];
    $interests = $_POST['interests'];

    // Profile picture upload
    if(!empty($_FILES['profile_pic']['name'])){
        $profile_pic = "uploads/" . time() . "_" . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    } else {
        $profile_pic = $student['profile_pic'];
    }

    // Resume upload
    if(!empty($_FILES['resume']['name'])){
        $resume_path = "uploads/" . time() . "_" . basename($_FILES['resume']['name']);
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    } else {
        $resume_path = $student['resume_path'];
    }

    // ✅ Update query
    $update = "UPDATE students SET 
                name='$name', 
                course='$course', 
                skills='$skills', 
                interests='$interests', 
                profile_pic='$profile_pic', 
                resume_path='$resume_path' 
               WHERE id='$student_id'";

    if(mysqli_query($conn, $update)){
        echo "<div class='message-overlay'><div class='success'>Profile updated successfully!</div></div>";
        // Refresh data
        $result = mysqli_query($conn, $query);
        $student = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='message-overlay'><div class='error'>Error updating profile: " . mysqli_error($conn) . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile | CareerPro</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-blue: #1e3c72;
        --secondary-blue: #2a5298;
        --accent-orange: #ff7a00;
        --accent-hover: #e86a00;
        --bg-light: #f4f6fc;
        --sidebar-width: 260px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        background: var(--bg-light);
        color: #333;
        display: flex;
        overflow-x: hidden;
    }

    /* --- Sidebar Navigation --- */
    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(180deg, var(--primary-blue), var(--secondary-blue));
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        padding-top: 20px;
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 4px 0 10px rgba(0,0,0,0.1);
    }

    /* Mobile Header / Hamburger */
    .mobile-header {
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
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .hamburger {
        font-size: 24px;
        cursor: pointer;
        display: none;
    }

    .close-btn {
        display: none;
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 24px;
        cursor: pointer;
        color: white;
    }

    .sidebar .logo {
        text-align: center;
        margin-bottom: 10px;
    }

    .sidebar .logo img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.2);
    }

    .sidebar h2 {
        text-align: center;
        font-size: 18px;
        margin-top: 10px;
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
        padding: 14px 25px;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 15px;
        border-left: 4px solid transparent;
    }

    .sidebar a:hover, .sidebar a.active {
        background: rgba(255,255,255,0.1);
        color: #fff;
        border-left: 4px solid var(--accent-orange);
    }

    .sidebar a span {
        margin-left: 12px;
    }

    /* --- Main Content Area --- */
    .main-content {
        flex: 1;
        margin-left: var(--sidebar-width);
        padding: 40px 20px;
        min-height: 100vh;
        transition: all 0.3s ease;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    h2.form-title {
        text-align: center;
        color: var(--primary-blue);
        margin-top: 0;
        margin-bottom: 5px;
        font-weight: 600;
    }

    p.subtitle {
        text-align: center;
        color: #888;
        margin-bottom: 30px;
        font-size: 14px;
    }

    /* --- Form Styling --- */
    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: 500;
        color: #444;
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
    }

    input[type="text"], 
    input[type="file"] {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e1e5ee;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
        background-color: #fafbfc;
    }

    input[type="text"]:focus {
        outline: none;
        border-color: var(--secondary-blue);
        background-color: #fff;
    }

    .btn {
        width: 100%;
        background: var(--accent-orange);
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s, transform 0.2s;
        margin-top: 10px;
    }

    .btn:hover {
        background: var(--accent-hover);
        transform: translateY(-1px);
    }

    /* --- Previews --- */
    .profile-preview {
        text-align: center;
        margin-bottom: 25px;
    }

    .profile-preview img {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .resume-link {
        text-align: center;
        margin-top: 20px;
        padding: 15px;
        background: #f8f9ff;
        border-radius: 8px;
    }

    .resume-link a {
        color: var(--secondary-blue);
        font-weight: 600;
        text-decoration: none;
        font-size: 14px;
    }

    /* Messages */
    .message-overlay {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        min-width: 250px;
    }

    .success, .error {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        animation: slideIn 0.5s ease-out;
    }

    .success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
    .error { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* --- RESPONSIVE DESIGN --- */
    @media (max-width: 992px) {
        :root { --sidebar-width: 230px; }
    }

    @media (max-width: 768px) {
        .mobile-header { display: flex; }
        .hamburger { display: block; }
        
        .sidebar {
            left: -100%; /* Hide sidebar */
            width: 280px;
            padding-top: 60px;
        }

        .sidebar.active {
            left: 0; /* Show sidebar */
        }

        .close-btn { display: block; }

        .main-content {
            margin-left: 0;
            padding-top: 80px; /* Space for mobile header */
        }

        .container {
            padding: 20px;
            width: 95%;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }
    }
</style>
</head>
<body>

<!-- Mobile Top Bar -->
<div class="mobile-header">
    <div style="font-weight:600; font-size:18px;">CareerPro</div>
    <div class="hamburger" onclick="toggleMenu()">☰</div>
</div>

<!-- Overlay for mobile menu -->
<div class="sidebar-overlay" id="overlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">
    <div class="close-btn" onclick="toggleMenu()">✕</div>
    <div class="logo">
         <img src="carrer.png" alt="Logo">
    </div>
    <h2>Dashboard</h2>
    <a href="dashboard.php">🏠 <span>Home</span></a>
    <a href="profile.php" class="active">👤 <span>Profile</span></a>
    <a href="career.php">💼 <span>Career Suggestions</span></a>
    <a href="resume.php">📄 <span>Resume Upload</span></a>
     <a href="student_jobs.php"><i class="fa-solid fa-briefcase"></i> <span>Jobs</span></a>
    <a href="logout.php">🚪 <span>Logout</span></a>
</div>

<div class="main-content">
    <div class="container">
        <h2 class="form-title">Edit Profile</h2>
        <p class="subtitle">Update your personal details, skills, and documents</p>

        <?php if(!empty($student['profile_pic'])): ?>
            <div class="profile-preview">
                <img src="<?php echo $student['profile_pic']; ?>" alt="Profile Picture">
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Course *</label>
                <input type="text" name="course" value="<?php echo htmlspecialchars($student['course']); ?>" required>
            </div>

            <div class="form-group">
                <label>Skills *</label>
                <input type="text" name="skills" value="<?php echo htmlspecialchars($student['skills']); ?>" required>
            </div>

            <div class="form-group">
                <label>Interests *</label>
                <input type="text" name="interests" value="<?php echo htmlspecialchars($student['interests']); ?>" required>
            </div>

            <div class="form-group">
                <label>Update Profile Picture</label>
                <input type="file" name="profile_pic" accept="image/*">
            </div>

            <div class="form-group">
                <label>Update Resume</label>
                <input type="file" name="resume" accept=".pdf,.doc,.docx">
            </div>

            <button type="submit" name="update" class="btn">Save Changes</button>
        </form>

        <?php if(!empty($student['resume_path'])): ?>
            <div class="resume-link">
                <a href="<?php echo $student['resume_path']; ?>" target="_blank">📄 View Current Resume</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
</script>

</body>
</html>