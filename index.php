<?php
session_start();
if(isset($_SESSION['username'])){
    header("location:dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Campus</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Welcome to City Campus</h2>
        <p>Please <a href="login.php">login</a> or <a href="signup.php">sign up</a> to access the dashboard.</p>
    </div>
</body>
</html>
