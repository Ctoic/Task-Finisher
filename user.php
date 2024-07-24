<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

require_once 'classes/Database.php';
require_once 'classes/Task.php';

$database = new Database();
$db = $database->getConnection();
$task = new Task($db);
$task->user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Panel</title>
</head>
<body>
    <h2>User Panel</h2>
    <a href="logout.php">Logout</a>
    <h3>Your Tasks</h3>
    <?php
    $stmt = $task->read();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div>";
        echo "<p>ID: " . $row['id'] . " | Title: " . $row['title'] . " | Description: " . $row['description'] . " | Created At: " . $row['created_at'] . "</p>";
        echo "</div>";
    }
    ?>
</body>
</html>
