<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db_connect.php"); // ✅ correct path

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $course = $_POST['course'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $skills = $_POST['skills'];
    $interests = $_POST['interests'];

    // Profile picture upload (optional)
    $profile_pic = "";
    if(!empty($_FILES['profile_pic']['name'])){
        $profile_pic = "uploads/".time()."_".basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    }

    // Resume upload
    $resume_path = "";
    if(!empty($_FILES['resume']['name'])){
        $resume_path = "uploads/".time()."_".basename($_FILES['resume']['name']);
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    }

    // ✅ Insert into students table
    $query = "INSERT INTO students (name, course, profile_pic, email, password, skills, interests, resume_path) 
              VALUES ('$name','$course','$profile_pic','$email','$password','$skills','$interests','$resume_path')";
    
    if(mysqli_query($conn, $query)){
        // ✅ Redirect to login page after successful registration
        echo "<script>
                alert('Registration successful! Redirecting to login page...');
                window.location.href = 'login.php';
              </script>";
        exit();
    } else {
        echo "<div class='error'>Error: ".mysqli_error($conn)."</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Candidate Registration</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f6f7fb;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding: 15px; /* Mobile par thoda gap dene ke liye */
    }
    .form-container {
        background: #fff;
        width: 100%; /* Full width on mobile */
        max-width: 480px; /* Max width on desktop */
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        box-sizing: border-box; /* Padding handling */
    }
    .form-container h2 {
        text-align: center;
        color: #333;
        margin-bottom: 5px;
    }
    .form-container p {
        text-align: center;
        color: #777;
        margin-bottom: 25px;
        font-size: 14px;
    }
    .section-title {
        font-weight: 600;
        color: #333;
        margin: 20px 0 10px;
        display: flex;
        align-items: center;
    }
    .section-title::before {
        content: "📋";
        margin-right: 8px;
    }
    label {
        font-weight: 500;
        color: #555;
        display: block;
        margin-bottom: 6px;
    }
    input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 14px;
        box-sizing: border-box; /* Extra width fix */
    }
    .upload-box {
        border: 2px dashed #ffa500;
        padding: 10px;
        border-radius: 6px;
        text-align: center;
        margin-bottom: 15px;
        color: #555;
    }
    .btn {
        width: 100%;
        background: #ff7a00;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        font-size: 15px;
    }
    .btn:hover {
        background: #e86a00;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        text-align: center;
    }

    /* ================= Responsive Adjustments ================= */
    @media (max-width: 480px) {
        .form-container {
            padding: 20px 25px; /* Mobile par padding thodi kam */
        }
        .form-container h2 {
            font-size: 1.5rem;
        }
        .btn {
            padding: 14px; /* Mobile par bada button for easy click */
            font-size: 16px;
        }
        input {
            font-size: 16px !important; /* Mobile zoom issue fix */
        }
    }
</style>
</head>
<body>
<div class="form-container">
    <h2>Candidate Registration</h2>
    <p>Join thousands of candidates finding their dream careers</p>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="section-title">Personal Information</div>
        <label>Full Name *</label>
        <input type="text" name="name" placeholder="Enter your full name" required>

        <label>Course *</label>
        <input type="text" name="course" placeholder="Enter your course" required>

        <label>Phone Number *</label>
        <input type="text" name="phone" placeholder="Enter phone number" required>

        <label>Email Address *</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password *</label>
        <input type="password" name="password" placeholder="Create a password" required>

        <div class="section-title">Skills & Interests</div>
        <label>Skills *</label>
        <input type="text" name="skills" placeholder="e.g., Python, Java, SQL" required>

        <label>Interests *</label>
        <input type="text" name="interests" placeholder="e.g., AI, Web Development" required>

        <div class="section-title">Upload Documents</div>
        <div class="upload-box">
            <label>Resume Upload</label>
            <input type="file" name="resume" accept=".pdf,.doc,.docx">
        </div>

        <div class="upload-box">
            <label>Profile Picture (optional)</label>
            <input type="file" name="profile_pic" accept="image/*">
        </div>

        <input type="submit" name="submit" value="Register Now →" class="btn">
    </form>
</div>
</body>
</html>