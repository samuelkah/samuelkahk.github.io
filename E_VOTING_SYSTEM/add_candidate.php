<?php
session_start();
include "db.php";

// Only admins/sub-admins can access
if (!isset($_SESSION["admin_id"]) && !isset($_SESSION["sub_admin_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $position = $_POST["position"];
    $admission_number = $_POST["admission_number"];
    $department = $_POST["department"];
    $campaign_agendas = $_POST["campaign_agendas"];

    // Handle file upload
    $photo = "";
    if (!empty($_FILES["photo"]["name"])) {
        $target_dir = "uploads/";
        $filename = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo = $target_file;
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO candidates (name, position, admission_number, department, campaign_agendas, photo) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $position, $admission_number, $department, $campaign_agendas, $photo);

    if ($stmt->execute()) {
        echo "Candidate added successfully!";
    } else {
        echo "Error adding candidate: " . $conn->error;
    }
    header("Location: admin_candidates.php");
    exit();
}
?>
