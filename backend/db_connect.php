<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "carrer_project"; // ✅ spelling match karo

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
