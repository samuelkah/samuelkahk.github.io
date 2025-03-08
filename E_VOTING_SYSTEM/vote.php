<?php
session_start();

// Database connection
$host = '127.0.0.1';
$db = 'university_portal';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch campaigns and candidates
$campaigns_stmt = $pdo->query("SELECT * FROM campaigns");
$campaigns = $campaigns_stmt->fetchAll();

$candidates_stmt = $pdo->query("SELECT * FROM candidates");
$candidates = $candidates_stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['campaign_id']) && isset($_POST['candidate_id'])) {
        $campaign_id = $_POST['campaign_id'];
        $candidate_id = $_POST['candidate_id'];

        // Check if the student has already voted in this campaign
        $vote_check_stmt = $pdo->prepare("SELECT * FROM votes WHERE student_id = ? AND campaign_id = ?");
        $vote_check_stmt->execute([$student_id, $campaign_id]);
        if ($vote_check_stmt->fetch()) {
            $error = "You have already voted in this campaign.";
        } else {
            // Insert the vote
            $insert_stmt = $pdo->prepare("INSERT INTO votes (student_id, campaign_id, candidate_id, voted_at) VALUES (?, ?, ?, NOW())");
            $insert_stmt->execute([$student_id, $campaign_id, $candidate_id]);
            $success = "Your vote has been recorded successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote for Leaders</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Grid layout for candidates */
        .candidates-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Styling for each candidate card */
        .candidate-details {
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .candidate-details img {
            max-width: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .candidate-details h3 {
            margin: 10px 0;
            font-size: 1.2em;
            color: #333;
        }

        .candidate-details p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #666;
        }
        h1 {
            text-align: center;
        }
        h2 {
            text-align: center;
        }




        /* Form styling */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            max-width: 400px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            text-align: center;
        }

        form label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        form select, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        form button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }

        form button:hover {
            background-color: #218838;
        }

        /* Error and success messages */
        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }
        .back-to-dashboard {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h1>Vote for Your Leaders</h1>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Display Candidates Details -->
    <h2>Candidates Information</h2>
    <div class="candidates-grid">
        <?php foreach ($candidates as $candidate): ?>
            <div class="candidate-details">
                <?php if (!empty($candidate['photo'])): ?>
                    <img src="<?php echo $candidate['photo']; ?>" alt="<?php echo $candidate['name']; ?>">
                <?php endif; ?>
                <h3><?php echo $candidate['name']; ?></h3>
                <p><strong>Position:</strong> <?php echo $candidate['position']; ?></p>
                <p><strong>Admission Number:</strong> <?php echo $candidate['admission_number']; ?></p>
                <p><strong>Department:</strong> <?php echo $candidate['department']; ?></p>
                <p><strong>Campaign Agendas:</strong> <?php echo $candidate['campaign_agendas']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Voting Form at the Bottom -->
    <h2>Cast Your Vote</h2>
    <form method="POST">
        <label for="campaign">Select Campaign:</label>
        <select name="campaign_id" id="campaign" required>
            <option value="">-- Select Campaign --</option>
            <?php foreach ($campaigns as $campaign): ?>
                <option value="<?php echo $campaign['id']; ?>"><?php echo $campaign['title']; ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="candidate">Select Candidate:</label>
        <select name="candidate_id" id="candidate" required>
            <option value="">-- Select Candidate --</option>
            <?php foreach ($candidates as $candidate): ?>
                <option value="<?php echo $candidate['id']; ?>"><?php echo $candidate['name']; ?> (<?php echo $candidate['position']; ?>)</option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <button type="submit">Submit Vote</button>
    </form>
    <p class="back-to-dashboard"><a href="dashboard.php">🔙 Back to Dashboard</a></p>

</body>
</html>