<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["candidate_id"])) {
        echo "<p style='color:red;'>Error: No candidate selected.</p>";
        exit();
    }

    $candidate_id = $_POST["candidate_id"];
    $student_id = $_SESSION["student_id"]; // Ensure student is logged in

    // Insert vote
    $stmt = $conn->prepare("INSERT INTO votes (student_id, candidate_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student_id, $candidate_id);
    
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Vote cast successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error casting vote.</p>";
    }
}
?>
