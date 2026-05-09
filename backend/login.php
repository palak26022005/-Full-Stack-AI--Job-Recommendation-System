<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db_connect.php"); // ✅ correct path

session_start();

$error_message = "";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $query = "SELECT * FROM students WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $_SESSION['student_id'] = $row['id'];
        $_SESSION['student_name'] = $row['name'];
        $_SESSION['student_email'] = $row['email'];

        // ✅ Redirect to dashboard after successful login
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Candidate Login</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f6f7fb;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }
    .form-container {
        background: #fff;
        width: 400px;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        position: relative;
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
    label {
        font-weight: 500;
        color: #555;
        display: block;
        margin-bottom: 6px;
    }
    input[type="email"], input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 14px;
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
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .signup-link {
        text-align: center;
        margin-top: 20px;
    }
    .signup-link a {
        display: inline-block;
        background: #ff7a00;
        color: #fff;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.3s ease;
    }
    .signup-link a:hover {
        background: #e86a00;
    }
</style>
</head>
<body>
<div class="form-container">
    <h2>Candidate Login</h2>
    <p>Welcome back! Please login to continue.</p>

    <?php if(!empty($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Email Address *</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password *</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <input type="submit" name="login" value="Login Now →" class="btn">
    </form>

    <div class="signup-link">
        Don't have an account?
        <a href="signup.php">Create one</a>
    </div>
</div>
</body>
</html>
