<?php
session_start();
include "db.php";

// Check if admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch sub-admins
$sub_admins_query = "SELECT id, username FROM sub_admins";
$sub_admins_result = $conn->query($sub_admins_query);

// Add sub-admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_sub_admin"])) {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO sub_admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Sub-admin added successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error adding sub-admin.</p>";
    }
}

// Delete sub-admin
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $stmt = $conn->prepare("DELETE FROM sub_admins WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<p style='color:red;'>Sub-admin deleted!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Sub-Admins</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin-bottom: 20px; }
        input, button { padding: 10px; margin: 5px; }
        .delete { color: red; text-decoration: none; }
    </style>
</head>
<body>
    <h2>Manage Sub-Admins</h2>

    <h3>Add Sub-Admin</h3>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="add_sub_admin">Add Sub-Admin</button>
    </form>

    <h3>Sub-Admin List</h3>
    <table>
        <tr><th>ID</th><th>Username</th><th>Action</th></tr>
        <?php while ($row = $sub_admins_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><a href="?delete_id=<?php echo $row['id']; ?>" class="delete">Delete</a></td>
            </tr>
        <?php } ?>
    </table>

    <p><a href="admin_dashboard.php">🔙 Back to Dashboard</a></p>
</body>
</html>

<?php $conn->close(); ?>
