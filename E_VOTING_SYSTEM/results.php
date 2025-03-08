<?php
include "db.php";

// Fetch campaign results
$sql = "SELECT c.title, COUNT(v.id) AS vote_count 
        FROM campaigns c 
        LEFT JOIN votes v ON c.id = v.campaign_id 
        GROUP BY c.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Voting Results</title>
</head>
<body>
    <h2>Voting Results</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><strong><?php echo $row['title']; ?>:</strong> <?php echo $row['vote_count']; ?> votes</li>
        <?php endwhile; ?>
    </ul>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
