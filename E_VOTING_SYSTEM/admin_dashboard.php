<?php
session_start();
include "db.php";

// Ensure admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch total votes dynamically
$total_votes_query = "SELECT COUNT(*) AS total_votes FROM votes";
$total_votes_result = $conn->query($total_votes_query);
$total_votes = ($total_votes_result) ? $total_votes_result->fetch_assoc()["total_votes"] ?? 0 : 0;

// Fetch vote count per candidate, including position
$candidate_votes_query = "
    SELECT 
        candidates.id, 
        candidates.name AS candidate_name, 
        candidates.photo, 
        candidates.position,          -- Fetch the position here
        COUNT(votes.id) AS total_votes
    FROM candidates
    LEFT JOIN votes ON votes.candidate_id = candidates.id
    GROUP BY candidates.id
    ORDER BY total_votes DESC
";
$candidate_votes_result = $conn->query($candidate_votes_query);

if (!$candidate_votes_result) {
    die("Error fetching candidates: " . $conn->error);
}

// Fetch sub-admins
$sub_admins_query = "SELECT id, username FROM sub_admins";
$sub_admins_result = $conn->query($sub_admins_query);

if (!$sub_admins_result) {
    die("Error fetching sub-admins: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        .container { display: flex; gap: 20px; flex-wrap: wrap; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); width: 100%; }
        h2 { text-align: center; }
        .dashboard-links { display: flex; justify-content: space-between; }
        .dashboard-links a { text-decoration: none; color: white; background: #007bff; padding: 8px 12px; border-radius: 4px; }
        .logout { background: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; border-radius: 8px; overflow: hidden; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        img { width: 50px; height: 50px; border-radius: 5px; }
        .delete-btn { color: red; text-decoration: none; }
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="dashboard-links">
        <h2>Admin Dashboard</h2>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>

    <!-- Display success/error messages -->
    <?php if (isset($_GET['message'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <div class="container">
        <div class="box">
            <h3>Total Votes Cast: <?php echo $total_votes; ?></h3>
        </div>
        <div class="box">
            <h3>Sub-Admins</h3>
            <a href="manage_sub_admins.php">➕ Manage Sub-Admins</a>
            <table>
                <tr><th>ID</th><th>Username</th><th>Action</th></tr>
                <?php while ($row = $sub_admins_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><a href="delete_sub_admin.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this sub-admin?')">❌ Delete</a></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <div class="box">
        <a href="admin_candidates.php">➕ Manage Candidates</a>
        <p><a href="manage_campaigns.php">📋 Manage Campaigns</a></p>
        <h3>Candidates & Their Votes</h3>

        <table>
            <tr>
                <th>Photo</th>
                <th>Candidate</th>
                <th>Position</th> <!-- New column for position -->
                <th>Votes</th>
                <th>Action</th>
            </tr>
            <?php if ($candidate_votes_result->num_rows > 0) { 
                while ($row = $candidate_votes_result->fetch_assoc()) { ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($row['photo']) ?>" alt="Candidate Photo"></td>
                        <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['position']); ?></td> <!-- Display position here -->
                        <td><?php echo $row['total_votes']; ?></td>
                        <td>
                            <a href="edit_candidate.php?id=<?= $row['id'] ?>">✏ Edit</a> | 
                            <a href="delete_candidate.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this candidate?')">❌ Delete</a>
                        </td>
                    </tr>
            <?php } 
            } else { ?>
                <tr><td colspan="5">No candidates found</td></tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>

<?php $conn->close(); ?>