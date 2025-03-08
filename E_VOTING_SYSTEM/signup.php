<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admission_number = strtoupper(trim($_POST["admission_number"]));
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $department = $_POST["department"];

    // Check if the admission number already exists
    $check_stmt = $conn->prepare("SELECT id FROM students WHERE admission_number = ?");
    $check_stmt->bind_param("s", $admission_number);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Admission number already exists.";
    } else {
        // Insert student into database
        $insert_stmt = $conn->prepare("INSERT INTO students (admission_number, password, department) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("sss", $admission_number, $password, $department);

        if ($insert_stmt->execute()) {
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "Error registering student.";
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        form input[type="text"],
        form input[type="password"],
        form select {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            padding: 10px 20px;
            background: #007bff;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        form button:hover {
            background: #0056b3;
        }
        p {
            margin-top: 15px;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Registration</h2>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="admission_number" placeholder="Admission Number (e.g., BCOM/2024/001)" required>
            <br>
            <input type="password" name="password" placeholder="Password" required>
            <br>
            <label>Select Department:</label>
            <br>
            <select name="department" required>
                <option value="BCOM">BCom</option>
                <option value="BBIT">BBIT</option>
                <option value="BAF">BAF</option>
                <!-- Add more departments as needed -->
            </select>
            <br>
            <button type="submit">Register</button>
        </form>
        <p>Want to go Back? <a href="login.php">Click here</a></p>
    </div>
</body>
</html>
