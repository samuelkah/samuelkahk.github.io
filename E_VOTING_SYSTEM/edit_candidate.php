<?php
session_start();
include "db.php";

// Optional: Restrict to admin/sub-admin
// if (!isset($_SESSION["admin_id"]) && !isset($_SESSION["sub_admin_id"])) {
//     header("Location: login.php");
//     exit();
// }

// Check if an ID was provided in the URL
if (!isset($_GET['id'])) {
    die("Error: No candidate ID specified.");
}

$candidate_id = intval($_GET['id']);

// Fetch existing candidate data
$query = "SELECT * FROM candidates WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();
$candidate = $result->fetch_assoc();

if (!$candidate) {
    die("Candidate not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $position = $_POST["position"];
    $admission_number = $_POST["admission_number"];
    $department = $_POST["department"];
    $campaign_agendas = $_POST["campaign_agendas"];

    // Handle photo upload (if new photo is provided)
    $photo_path = $candidate["photo"]; // Keep existing photo if not replaced
    if (!empty($_FILES["photo"]["name"])) {
        $target_dir = "uploads/";
        $filename = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = $target_file;
        } else {
            echo "<p style='color:red;'>Error uploading photo.</p>";
        }
    }

    // Update the record
    $update_query = "
        UPDATE candidates 
        SET 
            name = ?, 
            position = ?, 
            admission_number = ?, 
            department = ?, 
            campaign_agendas = ?, 
            photo = ?
        WHERE id = ?
    ";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param(
        "ssssssi", 
        $name, 
        $position, 
        $admission_number, 
        $department, 
        $campaign_agendas, 
        $photo_path, 
        $candidate_id
    );

    if ($update_stmt->execute()) {
        echo "<p style='color:green;'>Candidate updated successfully!</p>";
        echo "<p><a href='admin_candidates.php'>Back to Candidates</a></p>";
        exit();
    } else {
        echo "<p style='color:red;'>Error updating candidate: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Candidate</title>
    <style>
        body {
            font-family: Arial, sans-serif; 
            margin: 20px;
        }
        .edit-form {
            max-width: 400px;
            margin: auto; 
            padding: 20px; 
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .edit-form label {
            display: block;
            margin-top: 10px;
        }
        .edit-form input[type="text"], .edit-form textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        .edit-form img {
            display: block;
            margin: 10px 0;
            max-width: 200px;
            border-radius: 5px;
        }
        .edit-form button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff; 
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Edit Candidate</h2>
<div class="edit-form">
    <form method="POST" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($candidate['name']); ?>" required>

        <label>Position:</label>
        <input type="text" name="position" value="<?= htmlspecialchars($candidate['position']); ?>" required>

        <label>Admission Number:</label>
        <input type="text" name="admission_number" value="<?= htmlspecialchars($candidate['admission_number'] ?? ''); ?>">

        <label>Department:</label>
        <input type="text" name="department" value="<?= htmlspecialchars($candidate['department'] ?? ''); ?>">

        <label>Campaign Agendas:</label>
        <textarea name="campaign_agendas" rows="3"><?= htmlspecialchars($candidate['campaign_agendas'] ?? ''); ?></textarea>

        <label>Photo:</label>
        <input type="file" name="photo" accept="image/*">
        <?php if (!empty($candidate['photo'])) { ?>
            <img src="<?= htmlspecialchars($candidate['photo']); ?>" alt="Current Photo">
        <?php } ?>

        <button type="submit">Update Candidate</button>
    </form>
</div>

</body>
</html>

<?php $conn->close(); ?>
