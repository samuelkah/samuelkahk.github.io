<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password, role) VALUES (?, ?, 'sub_admin')");
    $stmt->bind_param("ss", $username, $password);
    if ($stmt->execute()) {
        echo "Sub-Admin added successfully!";
    } else {
        echo "Error adding sub-admin.";
    }
    $stmt->close();
    $conn->close();
}
?>
