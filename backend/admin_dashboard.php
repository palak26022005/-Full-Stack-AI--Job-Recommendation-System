<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

// Add Job
if(isset($_POST['add'])){
    $title = $_POST['title'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $apply_link = $_POST['apply_link'];

    $sql = "INSERT INTO jobs (title, company, location, description, apply_link) 
            VALUES ('$title','$company','$location','$description','$apply_link')";
    mysqli_query($conn, $sql);
}
?>
<h2>Admin Dashboard</h2>
<form method="post">
  <input type="text" name="title" placeholder="Job Title" required>
  <input type="text" name="company" placeholder="Company" required>
  <input type="text" name="location" placeholder="Location" required>
  <textarea name="description" placeholder="Description"></textarea>
  <input type="text" name="apply_link" placeholder="Apply Link">
  <button type="submit" name="add">Add Job</button>
</form>

<hr>
<h3>Existing Jobs</h3>
<?php
$result = mysqli_query($conn, "SELECT * FROM jobs");
while($row = mysqli_fetch_assoc($result)){
    echo "<p><b>{$row['title']}</b> - {$row['company']} ({$row['location']}) 
          <a href='delete_job.php?id={$row['id']}'>Delete</a></p>";
}
?>
