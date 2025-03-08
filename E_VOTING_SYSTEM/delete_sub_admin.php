<?php
session_start();
include "db.php";

// Ensure admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$sub_admin_id = intval($_GET['id']);

// Delete sub-admin query
$delete_query = "DELETE FROM sub_admins WHERE id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $sub_admin_id);

if ($stmt->execute()) {
    echo "<script>alert('Sub-admin deleted successfully!'); window.location='admin_dashboard.php';</script>";
} else {
    echo "Error deleting sub-admin: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
