<?php
session_start();
include "db.php";

// Ensure only admins can access this page
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// Handle Campaign Deletion
if (isset($_GET['delete'])) {
    $campaign_id = intval($_GET['delete']);
    $conn->query("DELETE FROM campaigns WHERE id = $campaign_id");
    header("Location: manage_campaigns.php");
    exit();
}

// Fetch all campaigns
$result = $conn->query("SELECT * FROM campaigns");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Campaigns</title>
</head>
<body>
    <h2>Campaign Management</h2>

    <!-- Add New Campaign Form -->
    <form action="add_campaign.php" method="POST">
        <input type="text" name="title" placeholder="Campaign Title" required>
        <textarea name="description" placeholder="Campaign Description" required></textarea>
        <button type="submit">Add Campaign</button>
    </form>

    <h3>Existing Campaigns</h3>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td>
                <a href="edit_campaign.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="manage_campaigns.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <p><a href="admin_dashboard.php">🔙 Back to Dashboard</a></p>

</body>
</html>
