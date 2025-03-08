<?php
session_start();
include "db.php";

// Ensure admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Check if candidate ID is provided
if (!isset($_GET['id'])) {
    die("Candidate ID not provided.");
}

$candidate_id = $_GET['id'];

// Start a transaction to ensure atomicity
$conn->begin_transaction();

try {
    // Step 1: Delete all votes associated with the candidate
    $delete_votes_query = "DELETE FROM votes WHERE candidate_id = ?";
    $stmt_votes = $conn->prepare($delete_votes_query);
    $stmt_votes->bind_param("i", $candidate_id);
    $stmt_votes->execute();

    // Step 2: Delete the candidate
    $delete_candidate_query = "DELETE FROM candidates WHERE id = ?";
    $stmt_candidate = $conn->prepare($delete_candidate_query);
    $stmt_candidate->bind_param("i", $candidate_id);
    $stmt_candidate->execute();

    // Commit the transaction
    $conn->commit();

    // Redirect back to the admin dashboard with a success message
    header("Location: admin_dashboard.php?message=Candidate+and+their+votes+deleted+successfully");
    exit();
} catch (Exception $e) {
    // Rollback the transaction in case of any error
    $conn->rollback();

    // Redirect back to the admin dashboard with an error message
    header("Location: admin_dashboard.php?error=Failed+to+delete+candidate+and+their+votes");
    exit();
}
?>