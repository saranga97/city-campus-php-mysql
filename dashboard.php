<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require_once('db.php');

// Function to sanitize input
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update-submit'])) {
        // Retrieve form data
        $student_id = $_POST['student_id'];
        $update_username = sanitize_input($_POST['update-username']);
        $update_nic = sanitize_input($_POST['update-nic']);
        $update_name = sanitize_input($_POST['update-name']);
        $update_address = sanitize_input($_POST['update-address']);
        $update_telephone = sanitize_input($_POST['update-telephone']);
        $update_course = sanitize_input($_POST['update-course']);

        // Update student details in the database
        $sql = "UPDATE students SET username = :username, nic = :nic, name = :name, address = :address, telephone = :telephone, course = :course WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $update_username, PDO::PARAM_STR);
        $stmt->bindParam(':nic', $update_nic, PDO::PARAM_STR);
        $stmt->bindParam(':name', $update_name, PDO::PARAM_STR);
        $stmt->bindParam(':address', $update_address, PDO::PARAM_STR);
        $stmt->bindParam(':telephone', $update_telephone, PDO::PARAM_STR);
        $stmt->bindParam(':course', $update_course, PDO::PARAM_STR);
        $stmt->bindParam(':id', $student_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Update successful
            // Redirect back to dashboard or show success message
            header("location: dashboard.php");
            exit;
        } else {
            // Error occurred during update
            $update_error = "Failed to update student details. Please try again.";
        }
    } elseif (isset($_POST['delete-submit'])) {
        // Retrieve student ID
        $student_id = $_POST['student_id'];

        // Delete student from the database
        $sql = "DELETE FROM students WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $student_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            // Delete successful
            // Redirect back to dashboard or show success message
            header("location: dashboard.php");
            exit;
        } else {
            // Error occurred during delete
            $delete_error = "Failed to delete student. Please try again.";
        }
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register-submit'])) {
        $reg_username = sanitize_input($_POST['reg-username']);
        $reg_password = password_hash($_POST['reg-password'], PASSWORD_DEFAULT); // Hash the password
        $reg_nic = sanitize_input($_POST['reg-nic']);
        $reg_name = sanitize_input($_POST['reg-name']);
        $reg_address = sanitize_input($_POST['reg-address']);
        $reg_telephone = sanitize_input($_POST['reg-telephone']);
        $reg_course = sanitize_input($_POST['reg-course']);

        // Insert new student data into the database
        $sql = "INSERT INTO students (username, password, nic, name, address, telephone, course) 
                VALUES (:username, :password, :nic, :name, :address, :telephone, :course)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $reg_username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $reg_password, PDO::PARAM_STR);
        $stmt->bindParam(':nic', $reg_nic, PDO::PARAM_STR);
        $stmt->bindParam(':name', $reg_name, PDO::PARAM_STR);
        $stmt->bindParam(':address', $reg_address, PDO::PARAM_STR);
        $stmt->bindParam(':telephone', $reg_telephone, PDO::PARAM_STR);
        $stmt->bindParam(':course', $reg_course, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Registration successful
            // Redirect or show success message
            header("location: dashboard.php");
            exit;
        } else {
            // Error occurred during registration
            $reg_error = "Failed to register student. Please try again.";
        }
    }
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM students WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            line-height: 70px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar .user-info {
            position: relative;
            cursor: pointer;
            margin-right: 50px;
        }

        .user-info .dropdown-content {
            display: none;
            position: absolute;
            top: 60px;
            right: 0;
            background-color: #f9f9f9;
            min-width: 150px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 999;
            padding: 5px 5px;
            border-radius: 4px;
            color: black;
        }

        .user-info .dropdown-content a {
            color: #333;
            /* padding: 0px; */
            text-decoration: none;
            display: block;
            text-align: left;
            color: black;
        }

        .user-info .dropdown-content hr {
            margin: 1px 0;
            border: none;
            border-top: 1px solid #ccc;
        }

        .user-info .dropdown-content a:hover {
            background-color: #ddd;
        }

        .user-info:hover .dropdown-content {
            display: block;
        }

        h2 {
            text-align: center;
            margin-top: 100px;
        }

        .container {
            padding: 10px;
            margin-left: 350px;
            width: 70%;
            min-height: 70vh;
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 90px;
            left: 0;
            background-color: #007bff;
            color: #fff;
            overflow-x: hidden;
            padding-top: 20px;
        }

        .sidebar a {
            padding: 20px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #333;
            display: block;
            color: white;
        }

        .sidebar a:hover {
            background-color: #ddd;
            color: black;
        }

        .section {
            display: none;
        }

        /* Form styles */
        form {
            max-width: 400px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .popup {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            z-index: 1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            min-width: 500px;
            margin: auto;
            transition: opacity 0.8s ease-in-out;
            opacity: 0;
            pointer-events: none;
        }

        .popup.active {
            display: block;
            opacity: 1;
            pointer-events: auto;
        }

        .popup h3 {
            margin-top: 0;
            font-size: 18px;
            color: #333;
        }

        .popup p {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .account-details {
            list-style-type: none;
            padding: 0;
            align-items: center;
            padding: 30px;
            margin: auto;

        }

        .account-details li {
            margin-bottom: 15px;
        }

        .account-details li strong {
            display: inline-block;
            width: 150px;
            font-weight: bold;
            color: #333;
            /* Text color for labels */
        }

        .account-details li span {
            display: inline-block;
            width: calc(100% - 150px);
            color: #666;
            /* Text color for values */
        }

        .account-details li strong::after {
            content: ":";
        }

        .account-details li span::first-letter {
            text-transform: uppercase;
            /* Uppercase the first letter of each value */
        }

        .account-details li:hover {
            background-color: #f2f2f2;
            /* Highlight the row on hover */
        }

        /* #my-account {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        } */
    </style>
</head>

<body>
    <div class="navbar">
        <div class="logo">City Campus</div>
        <div class="user-info">
            <?php
            $fullName = $row['name'];
            $firstName = explode(' ', $fullName)[0];
            echo $firstName;
            ?>
            <div class="dropdown-content">
                <div>
                    <?php echo $fullName; ?>
                    <hr>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar">
        <a href="#my-account">My Account</a>
        <a href="#student-details">Student Details</a>
        <a href="#course-details">Course Details</a>
        <a href="#register-student">Register Student</a>
    </div>

    <div class="container">
        <div id="my-account" class="section">
            <h2>My Account</h2>
            <ul class="account-details">
                <li><strong>Username:</strong> <?php echo $row['username']; ?></li>
                <li><strong>Name:</strong> <?php echo $row['name']; ?></li>
                <li><strong>NIC:</strong> <?php echo $row['nic']; ?></li>
                <li><strong>Address:</strong> <?php echo $row['address']; ?></li>
                <li><strong>Telephone Number:</strong> <?php echo $row['telephone']; ?></li>
                <li><strong>Course:</strong> <?php echo $row['course']; ?></li>
            </ul>
        </div>

        <div id="register-student" class="section">
            <h2>Register Student</h2>
            <form method="post">
                <label for="reg-username">Username:</label><br>
                <input type="text" id="reg-username" name="reg-username" required><br>

                <label for="reg-password">Password:</label><br>
                <input type="password" id="reg-password" name="reg-password" required><br>

                <label for="reg-nic">NIC:</label><br>
                <input type="text" id="reg-nic" name="reg-nic" required><br>

                <label for="reg-name">Name:</label><br>
                <input type="text" id="reg-name" name="reg-name" required><br>

                <label for="reg-address">Address:</label><br>
                <input type="text" id="reg-address" name="reg-address" required><br>

                <label for="reg-telephone">Telephone:</label><br>
                <input type="text" id="reg-telephone" name="reg-telephone" required><br>

                <label for="reg-course">Course:</label><br>
                <input type="text" id="reg-course" name="reg-course" required><br>

                <button type="submit" name="register-submit">Register</button>
            </form>
        </div>


        <div id="student-details" class="section">
            <h2>Student Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>NIC</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Telephone</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch student data from the database and iterate over it to generate table rows
                    $sql = "SELECT * FROM students";
                    $stmt = $pdo->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['nic'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['address'] . "</td>";
                        echo "<td>" . $row['telephone'] . "</td>";
                        echo "<td>" . $row['course'] . "</td>";
                        echo "<td>";
                        echo "<a href='javascript:void(0)' onclick='togglePopup(\"view-popup-" . $row['id'] . "\")'>View</a> ";
                        echo "<a href='javascript:void(0)' onclick='togglePopup(\"update-popup-" . $row['id'] . "\")'>Update</a> ";
                        echo "<a href='javascript:void(0)' onclick='togglePopup(\"delete-popup-" . $row['id'] . "\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";

                        // Hidden popups for each row
                        echo "<div id='view-popup-" . $row['id'] . "' class='popup'>";
                        echo "<h3>Student Details</h3>";
                        echo "<p><strong>ID:</strong> " . $row['id'] . "</p>";
                        echo "<p><strong>Username:</strong> " . $row['username'] . "</p>";
                        echo "<p><strong>NIC:</strong> " . $row['nic'] . "</p>";
                        echo "<p><strong>Name:</strong> " . $row['name'] . "</p>";
                        echo "<p><strong>Address:</strong> " . $row['address'] . "</p>";
                        echo "<p><strong>Telephone:</strong> " . $row['telephone'] . "</p>";
                        echo "<p><strong>Course:</strong> " . $row['course'] . "</p>";
                        echo "</div>";


                        echo "<tr>";
                        echo "<td colspan='7'>";
                        echo "<div id='update-popup-" . $row['id'] . "' class='popup'>";
                        echo "<h3>Update Student Details</h3>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='student_id' value='" . $row['id'] . "'>";
                        echo "<label for='update-username'>Username:</label><br>";
                        echo "<input type='text' id='update-username' name='update-username' value='" . $row['username'] . "'><br>";
                        echo "<label for='update-nic'>NIC:</label><br>";
                        echo "<input type='text' id='update-nic' name='update-nic' value='" . $row['nic'] . "'><br>";
                        echo "<label for='update-name'>Name:</label><br>";
                        echo "<input type='text' id='update-name' name='update-name' value='" . $row['name'] . "'><br>";
                        echo "<label for='update-address'>Address:</label><br>";
                        echo "<input type='text' id='update-address' name='update-address' value='" . $row['address'] . "'><br>";
                        echo "<label for='update-telephone'>Telephone:</label><br>";
                        echo "<input type='text' id='update-telephone' name='update-telephone' value='" . $row['telephone'] . "'><br>";
                        echo "<label for='update-course'>Course:</label><br>";
                        echo "<input type='text' id='update-course' name='update-course' value='" . $row['course'] . "'><br>";
                        echo "<button type='submit' name='update-submit'>Update</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";


                        echo "<div id='delete-popup-" . $row['id'] . "' class='popup'>";
                        echo "<h3>Are you sure you want to delete this student?</h3>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='student_id' value='" . $row['id'] . "'>";
                        echo "<button type='submit' name='delete-submit'>Yes</button>";
                        echo "<button type='button' onclick='togglePopup(\"delete-popup-" . $row['id'] . "\")'>No</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                    ?>
                </tbody>


            </table>
        </div>
        <div id="course-details" class="section">
            <h2>Course Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Instructor</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CSE101</td>
                        <td>Introduction to Computer Science</td>
                        <td>Prof. John Doe</td>
                        <td>2024-06-01</td>
                        <td>2024-07-15</td>
                    </tr>
                    <tr>
                        <td>ENG201</td>
                        <td>English Literature</td>
                        <td>Dr. Jane Smith</td>
                        <td>2024-06-10</td>
                        <td>2024-08-20</td>
                    </tr>
                    <!-- Add more dummy course details as needed -->
                </tbody>
            </table>
        </div>

    </div>
    <script>
        function togglePopup(popupId) {
            var popup = document.getElementById(popupId);
            popup.classList.toggle("active");
        }
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarLinks = document.querySelectorAll('.sidebar a');
            const sections = document.querySelectorAll('.section');

            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    sections.forEach(function(section) {
                        if (section.id === targetId) {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>