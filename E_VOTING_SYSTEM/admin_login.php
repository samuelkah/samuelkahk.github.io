<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // First, check in the sub_admins table
    $stmt = $conn->prepare("SELECT id, password FROM sub_admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($sub_admin_id, $sub_admin_hashed_password);
        $stmt->fetch();

        if (password_verify($password, $sub_admin_hashed_password)) {
            $_SESSION["sub_admin_id"] = $sub_admin_id;
            $_SESSION["sub_admin_username"] = $username;
            header("Location: sub_admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password for sub-admin.";
        }
    } else {
        // No sub-admin found; check in the admins table.
        $stmt->close();
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($admin_id, $admin_hashed_password);
            $stmt->fetch();

            if (password_verify($password, $admin_hashed_password)) {
                $_SESSION["admin_id"] = $admin_id;
                $_SESSION["admin_username"] = $username;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid password for admin.";
            }
        } else {
            $error = "User not found in sub-admins or admins.";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Unified Login</title>
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
        form input[type="password"] {
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin&Sub-Admin login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <br>
            <input type="password" name="password" placeholder="Password" required>
            <br>
            <button type="submit">Login</button>
        </form>
        <p>Want to go back? <a href="dashboard.php">Click here</a></p>
    </div>
</body>
</html>
