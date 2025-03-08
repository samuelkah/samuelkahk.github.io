<?php
session_start();
include "db.php";

// Fetch candidates from the database
$query = "SELECT id, name FROM candidates";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vote Now</title>
</head>
<body>
    <h2>Cast Your Vote</h2>
    <form action="vote.php" method="POST">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<input type="radio" name="candidate_id" value="' . $row['id'] . '"> ' . $row['name'] . '<br>';
            }
        } else {
            echo "<p>No candidates available.</p>";
        }
        ?>
        <button type="submit">Vote</button>
    </form>
</body>
</html>
