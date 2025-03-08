<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM campaigns WHERE id = $id");
    $campaign = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST["id"]);
    $title = $_POST["title"];
    $description = $_POST["description"];

    $stmt = $conn->prepare("UPDATE campaigns SET title=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $description, $id);
    $stmt->execute();

    header("Location: manage_campaigns.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Campaign</title>
</head>
<body>
    <h2>Edit Campaign</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $campaign['id']; ?>">
        <input type="text" name="title" value="<?php echo $campaign['title']; ?>" required>
        <textarea name="description" required><?php echo $campaign['description']; ?></textarea>
        <button type="submit">Update</button>
    </form>
    <p><a href="manage_campaigns.php">Back to Campaigns</a></p>
</body>
</html>
