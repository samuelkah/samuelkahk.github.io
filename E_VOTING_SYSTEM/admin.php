<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Welcome, Admin!</h2>
        <p>🔹 <a href="view_results.php">View Election Results</a></p>
        <p>🔹 <a href="manage_campaigns.php">Manage Campaigns</a></p>
        <p>🔹 <a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
