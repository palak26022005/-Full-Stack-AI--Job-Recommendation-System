<?php
session_start();
include("db_connect.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1){
        $_SESSION['admin'] = $email;
        header("Location: admin_dashboard.php");
    } else {
        echo "Invalid login!";
    }
}
?>
<form method="post">
  <input type="text" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>
