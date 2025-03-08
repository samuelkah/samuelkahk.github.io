<?php
session_start();
include "db.php";

// Ensure sub-admin is logged in
if (!isset($_SESSION["sub_admin_id"])) {
    header("Location: unified_login.php");
    exit();
}

// Fetch candidate vote counts
$candidate_votes_query = "
    SELECT candidates.id, candidates.name AS candidate_name, candidates.photo, COUNT(votes.id) AS total_votes
    FROM candidates
    LEFT JOIN votes ON votes.candidate_id = candidates.id
    GROUP BY candidates.id
    ORDER BY total_votes DESC
";
$candidate_votes_result = $conn->query($candidate_votes_query);
if (!$candidate_votes_result) {
    die("Error fetching candidates: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sub-Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        .container { width: 100%; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        h2 { text-align: center; }
        a.button { text-decoration: none; color: white; background: #007bff; padding: 8px 12px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        img { width: 50px; height: 50px; border-radius: 5px; }
        .delete-btn { color: red; text-decoration: none; }
    </style>
</head>
<body>
    <h2>Sub-Admin Dashboard</h2>
    <div class="container">
        <div class="box">
            <h3>Candidates & Their Votes</h3>
            <p>
                <a href="admin_candidates.php" class="button">Manage Candidates</a>
                &nbsp;
                <a href="manage_campaigns.php" class="button">Manage Campaigns</a>
            </p>
            <table>
                <tr>
                    <th>Photo</th>
                    <th>Candidate</th>
                    <th>Votes</th>
                    <th>Action</th>
                </tr>
                <?php if ($candidate_votes_result->num_rows > 0) { 
                    while ($row = $candidate_votes_result->fetch_assoc()) { ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($row['photo']); ?>" alt="Candidate Photo"></td>
                            <td><?= htmlspecialchars($row['candidate_name']); ?></td>
                            <td><?= $row['total_votes']; ?></td>
                            <td>
                                <a href="edit_candidate.php?id=<?= $row['id']; ?>">✏ Edit</a> | 
                                <a href="delete_candidate.php?id=<?= $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this candidate?')">❌ Delete</a>
                            </td>
                        </tr>
                <?php } } else { ?>
                    <tr><td colspan="4">No candidates found</td></tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
