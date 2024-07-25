<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Task.php';

$database = new Database();
$db = $database->getConnection();
$task = new Task($db);
$user = new User($db);

// Handle task operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_action'])) {
    if ($_POST['task_action'] == 'create') {
        $task->title = $_POST['title'];
        $task->description = $_POST['description'];
        $task->user_id = $_POST['user_id'];
        $task->create();
    } elseif ($_POST['task_action'] == 'update') {
        $task->id = $_POST['task_id'];
        $task->title = $_POST['title'];
        $task->description = $_POST['description'];
        $task->update();
    } elseif ($_POST['task_action'] == 'delete') {
        $task->id = $_POST['task_id'];
        $task->delete();
    }
}

// Handle user operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_action'])) {
    if ($_POST['user_action'] == 'update') {
        $user->id = $_POST['user_id'];
        $user->username = $_POST['username'];
        $user->password = $_POST['password'];
        $user->role = $_POST['role'];
        $task->update();
    } elseif ($_POST['user_action'] == 'delete') {
        $user->id = $_POST['user_id'];
        $task->delete();
    } elseif ($_POST['user_action'] == 'assign_task') {
        $task->title = $_POST['title'];
        $task->description = $_POST['description'];
        $task->user_id = $_POST['user_id'];
        $task->create();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
        }
        .sidebar {
            height: 100vh;
            width: 200px;
            background-color: #333;
            color: white;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 16px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
            width: 100%;
        }
        .hidden {
            display: none;
        }
    </style>
    <script>
        function toggleVisibility(sectionId) {
            var sections = document.querySelectorAll('.section');
            sections.forEach(function(section) {
                section.classList.add('hidden');
            });
            document.getElementById(sectionId).classList.remove('hidden');
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <a href="#" onclick="toggleVisibility('manageUsers')">Manage Users</a>
        <a href="#" onclick="toggleVisibility('addActivity')">Add Activity</a>
        <a href="#" onclick="toggleVisibility('listActivities')">List of Activities</a>
        <a href="#" onclick="toggleVisibility('settings')">Settings</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div id="manageUsers" class="section hidden">
            <h3>Manage Users</h3>
            <form method="POST" action="">
                <label>User ID:</label>
                <input type="text" name="user_id" required>
                <br>
                <label>Username:</label>
                <input type="text" name="username" required>
                <br>
                <label>Password:</label>
                <input type="password" name="password">
                <br>
                <label>Role:</label>
                <select name="role">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <br>
                <input type="hidden" name="user_action" value="update">
                <input type="submit" value="Update User">
            </form>
            <form method="POST" action="">
                <label>User ID:</label>
                <input type="text" name="user_id" required>
                <br>
                <input type="hidden" name="user_action" value="delete">
                <input type="submit" value="Delete User">
            </form>
            <h3>Assign Task to User</h3>
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
                <input type="hidden" name="user_action" value="assign_task">
                <input type="submit" value="Assign Task">
            </form>
        </div>

        <div id="addActivity" class="section hidden">
            <h3>Add Activity</h3>
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
                <input type="hidden" name="task_action" value="create">
                <input type="submit" value="Create Task">
            </form>
        </div>

        <div id="listActivities" class="section hidden">
            <h3>List of Activities</h3>
            <?php
            $stmt = $task->read();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div>";
                echo "<p>ID: " . $row['id'] . " | Title: " . $row['title'] . " | Description: " . $row['description'] . " | User ID: " . $row['user_id'] . "</p>";
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='task_id' value='" . $row['id'] . "'>";
                echo "<input type='text' name='title' value='" . $row['title'] . "' required>";
                echo "<textarea name='description'>" . $row['description'] . "</textarea>";
                echo "<input type='hidden' name='task_action' value='update'>";
                echo "<input type='submit' value='Update'>";
                echo "</form>";
                echo "<form method='POST' action=''>";
                echo "<input type='hidden' name='task_id' value='" . $row['id'] . "'>";
                echo "<input type='hidden' name='task_action' value='delete'>";
                echo "<input type='submit' value='Delete'>";
                echo "</form>";
                echo "</div>";
            }
            ?>
        </div>

        <div id="settings" class="section hidden">
            <h3>Settings</h3>
            <!-- Add any settings you want to manage here -->
        </div>
    </div>
</body>
</html>
