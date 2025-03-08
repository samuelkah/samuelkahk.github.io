<?php
session_start();
include "db.php";

// Only admins/sub-admins can access
if (!isset($_SESSION["admin_id"]) && !isset($_SESSION["sub_admin_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch all candidates
$result = $conn->query("SELECT * FROM candidates");

// Define available positions (you can also fetch these from the database if they are stored there)
$positions = ["President", "Class Rep", "Library Committee"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Candidates</title>
</head>
<body>
    <h2>Manage Candidates</h2>

    <!-- Add Candidate Form -->
    <form action="add_candidate.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Name" required><br>

        <!-- Dropdown for Position -->
        <label for="position">Position:</label>
        <select name="position" id="position" required>
            <option value="">-- Select Position --</option>
            <?php foreach ($positions as $position): ?>
                <option value="<?= htmlspecialchars($position) ?>"><?= htmlspecialchars($position) ?></option>
            <?php endforeach; ?>
        </select><br>

        <input type="text" name="admission_number" placeholder="Admission Number"><br>
        <input type="text" name="department" placeholder="Department"><br>
        <textarea name="campaign_agendas" placeholder="Campaign Agendas"></textarea><br>
        <label>Photo:</label>
        <input type="file" name="photo" accept="image/*"><br>
        <button type="submit">Add Candidate</button>
    </form>

    <hr>

    <!-- Display Candidates -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Position</th>
            <th>Admission #</th>
            <th>Department</th>
            <th>Campaign Agendas</th>
            <th>Photo</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['position']) ?></td>
            <td><?= htmlspecialchars($row['admission_number']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['campaign_agendas']) ?></td>
            <td><img src="<?= htmlspecialchars($row['photo']) ?>" width="50"></td>
            <td>
                <a href="edit_candidate.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="delete_candidate.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this candidate?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <p><a href="admin_dashboard.php">🔙 Back to Dashboard</a></p>

</body>
</html>