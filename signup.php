<?php
session_start();
if (isset($_SESSION['username'])) {
    header("location:dashboard.php");
    exit;
}

require_once('db.php'); 
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $username = trim($_POST['username']);
    $nic = trim($_POST['nic']);
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $telephone = trim($_POST['telephone']);
    $course = trim($_POST['course']);
    $password = $_POST['password'];

    // Check if username already exists
    $sql = "SELECT id FROM students WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $errors['username'] = "Username already exists";
    }

    // Check if NIC already exists
    $sql = "SELECT id FROM students WHERE nic = :nic";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nic', $nic, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $errors['nic'] = "NIC already exists";
    }

    // Insert data into database if there are no errors
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO students (username, nic, name, address, telephone, course, password) VALUES (:username, :nic, :name, :address, :telephone, :course, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':nic', $nic, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':telephone', $telephone, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        if ($stmt->execute()) {
            // Redirect to login page after successful signup
            header("location: login.php");
            exit;
        } else {
            $errors['database'] = "Error inserting data into database";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #007bff;
            color: #fff;
            height: 70px;
            line-height: 50px;
            text-align: center;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
        }
        .container {
            margin-top: 150px; /* Adjust top margin to accommodate the navbar */
            padding: 20px;
            width: 400px;
        }
        h2 {
            text-align: center;
        }
        .errors {
            background-color: #ffcccc;
            color: #cc0000;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .errors ul {
            margin: 0;
            padding: 0;
        }
        .errors li {
            list-style: none;
        }
        input[type="text"],
        input[type="password"] {
            width: 378px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1>City Campus</h1>
    </div>
    <div class="container">
        <h2>Signup</h2>
        <?php if (!empty($errors)) : ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="nic" placeholder="NIC" required>
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="telephone" placeholder="Telephone" required>
            <input type="text" name="course" placeholder="Course" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Signup</button>
        </form>
        <p>Already have an account? <a href="login.php">SignIn</a></p>
    </div>
</body>

</html>