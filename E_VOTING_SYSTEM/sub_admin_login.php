<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check in sub_admins table
    $stmt = $conn->prepare("SELECT * FROM sub_admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // If using plain text passwords (not recommended):
        if ($password === $row["password"]) {
            // Login successful
            $_SESSION["sub_admin_id"] = $row["id"];
            header("Location: sub_admin_dashboard.php");
            exit();
        } else {
            echo "<p style='color:red;'>Incorrect password.</p>";
        }
    } else {
        echo "<p style='color:red;'>Sub admin not found.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sub Admin Login</title>
</head>
<body>
    <h2>Sub Admin Login</h2>
    <form method="POST">
        <label>Username</label><br>
        <input type="text" name="username" required><br>
        <label>Password</label><br>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
