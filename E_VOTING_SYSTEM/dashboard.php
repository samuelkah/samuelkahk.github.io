<?php
session_start();
include "db.php"; // Ensure database connection is included

// Redirect if user is not logged in
if (!isset($_SESSION["student_id"]) && !isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

$admission_number = $_SESSION["admission_number"] ?? "Guest";
$isAdmin = isset($_SESSION["admin_id"]); // Check if the user is an admin
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }
        h2, h3 {
            margin-bottom: 20px;
        }
        .campaign-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            list-style: none;
            padding: 0;
        }
        .campaign-grid li {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .campaign-grid li strong {
            display: block;
            margin-bottom: 10px;
        }
        .campaign-grid form {
            margin-top: 10px;
        }
        .campaign-grid button {
            padding: 8px 12px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .campaign-grid button:hover {
            background: #0056b3;
        }
        .admin-link, .logout-link {
            text-decoration: none;
            color: white;
            background: #007bff;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 10px;
        }
        .logout-link {
            background: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($admission_number); ?>!</h2>

        <h3>📌 Available Campaigns</h3>
        <ul class="campaign-grid">
            <?php
            $result = $conn->query("SELECT * FROM campaigns"); // Ensure $conn is defined
            while ($row = $result->fetch_assoc()):
            ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                    <span><?php echo htmlspecialchars($row['description']); ?></span>
                    <form action="vote.php" method="post">
                        <input type="hidden" name="campaign_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Vote</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>

        <?php if ($isAdmin): ?>
            <p><a href="admin_login.php" class="admin-link">⚙️ Admin Panel</a></p>
        <?php endif; ?>

        <p><a href="logout.php" class="logout-link">🚪 Logout</a></p>
    </div>
</body>
</html>
<?php $conn->close(); ?>
