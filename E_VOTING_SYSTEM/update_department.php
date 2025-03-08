<?php
session_start();
include "db.php";

// Check if admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admission_number = strtoupper(trim($_POST["admission_number"]));
    $new_department = $_POST["new_department"];

    // Check if student exists
    $check_student = $conn->prepare("SELECT id FROM students WHERE admission_number = ?");
    $check_student->bind_param("s", $admission_number);
    $check_student->execute();
    $check_student->store_result();

    if ($check_student->num_rows > 0) {
        // Update department
        $update_stmt = $conn->prepare("UPDATE students SET department = ? WHERE admission_number = ?");
        $update_stmt->bind_param("ss", $new_department, $admission_number);

        if ($update_stmt->execute()) {
            $success = "Department updated successfully!";
        } else {
            $error = "Error updating department.";
        }
        $update_stmt->close();
    } else {
        $error = "Student not found.";
    }
    $check_student->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Student Department</title>
</head>
<body>
    <h2>Update Student Department</h2>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <input type="text" name="admission_number" placeholder="Admission Number (e.g., BCOM/2024/001)" required><br>

        <label>Select New Department:</label>
        <select name="new_department" required>
            <option value="BCOM">BCom</option>
            <option value="BBIT">BBIT</option>
            <option value="BAF">BAF</option>
            <!-- Add more departments as needed -->
        </select><br>

        <button type="submit">Update</button>
    </form>
    <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
