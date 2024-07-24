<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    $user->role = $_POST['role'];

    if ($user->register()) {
        header("Location: login.php");
    } else {
        $error = "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required>
        <br>
        <label>Password:</label>
        <input type="password" name="password" required>
        <br>
        <label>Role:</label>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
        <br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
