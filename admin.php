<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'classes/Database.php';
require_once 'classes/Task.php';

$database = new Database();
$db = $database->getConnection();
$task = new Task($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        $task->title = $_POST['title'];
        $task->description = $_POST['description'];
        $task->user_id = $_POST['user_id'];
        $task->create();
    } elseif ($_POST['action'] == 'update') {
        $task->id = $_POST['task_id'];
        $task->title = $_POST['title'];
        $task->description = $_POST['description'];
        $task->update();
    } elseif ($_POST['action'] == 'delete') {
        $task->id = $_POST['task_id'];
        $task->delete();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
</head>
<body>
    <h2>Admin Panel</h2>
    <a href="logout.php">Logout</a>
    <h3>Create Task</h3>
    <form method="POST" action="">
        <label>Title:</label>
        <input type="text" name="title" required>
        <br>
        <label>Description:</label>
        <textarea name="description"></textarea>
        <br>
        <label>User ID:</label>
        <input type="text" name="user_id" required>
        <br>
        <input type="hidden" name="action" value="create">
        <input type="submit" value="Create Task">
    </form>
    <h3>Existing Tasks</h3>
    <?php
    $stmt = $task->read();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div>";
        echo "<p>ID: " . $row['id'] . " | Title: " . $row['title'] . " | Description: " . $row['description'] . " | User ID: " . $row['user_id'] . "</p>";
        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='task_id' value='" . $row['id'] . "'>";
        echo "<input type='text' name='title' value='" . $row['title'] . "' required>";
        echo "<textarea name='description'>" . $row['description'] . "</textarea>";
        echo "<input type='hidden' name='action' value='update'>";
        echo "<input type='submit' value='Update'>";
        echo "</form>";
        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='task_id' value='" . $row['id'] . "'>";
        echo "<input type='hidden' name='action' value='delete'>";
        echo "<input type='submit' value='Delete'>";
        echo "</form>";
        echo "</div>";
    }
    ?>
</body>
</html>
