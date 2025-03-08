<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];

    $stmt = $conn->prepare("INSERT INTO campaigns (title, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $description);
    $stmt->execute();

    header("Location: manage_campaigns.php");
    exit();
}
?>
