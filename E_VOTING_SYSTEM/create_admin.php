<?php
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$admin_username = "admin";
$plain_password = "admin"; // Raw password

// Check if the admin already exists
$stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Admin already exists!";
} else {
    // If admin does not exist, insert it
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $admin_username, $hashed_password);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully!";
    } else {
        echo "Error inserting admin: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>
