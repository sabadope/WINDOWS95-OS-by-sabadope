<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            $_SESSION['user'] = [
                'name' => $name,
                'role' => $role
            ];
            header('Location: index.php');
            exit();
        }

        $stmt->close();
    }

    if ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'name' => $user['name'],
                    'role' => $user['role']
                ];
                header('Location: index.php');
                exit();
            }
        }

        $stmt->close();
    }

    $conn->close();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Windows 95 Login/Register</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: "MS Sans Serif", sans-serif; }
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; background: #008080; }
        .container { width: 350px; padding: 20px; background: #C0C0C0; border: 2px solid black; box-shadow: 4px 4px black; text-align: center; position: relative; }
        .title-bar { background: navy; color: white; padding: 5px; text-align: left; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .close-btn { background: red; color: white; border: none; width: 20px; height: 20px; text-align: center; cursor: pointer; font-weight: bold; }
        .form { display: none; padding: 10px; border: 2px inset black; background: #E0E0E0; }
        .form.active { display: block; }
        h2 { margin-bottom: 15px; font-size: 14px; }
        input, select { width: 100%; padding: 5px; margin: 10px 0; border: 2px inset black; background: white; }
        button { width: 100%; padding: 5px; background: #C0C0C0; border: 2px outset black; cursor: pointer; }
        button:active { border: 2px inset black; }
        .toggle-link { margin-top: 10px; display: block; color: black; cursor: pointer; text-decoration: underline; }
        .icon { margin-top: 20px; cursor: pointer; background: #C0C0C0; padding: 10px; border: 2px outset black; display: none; }
        .taskbar { position: absolute; bottom: 0; width: 100%; background: #C0C0C0; border-top: 2px solid black; padding: 5px; display: flex; }
        .taskbar-icon { background: #E0E0E0; padding: 5px 10px; border: 2px outset black; cursor: pointer; margin-right: 5px; display: none; }
    </style>
</head>
<body>
<?php if (isset($_SESSION['user'])): ?>
    <div class="container">
        <div class="title-bar">
            <?php echo ucfirst($_SESSION['user']['role']); ?> Dashboard
            <button class="close-btn" onclick="closeApp()">X</button>
        </div>
        
        <p style="margin-top: 10px;">You are logged in as <strong><?php echo ucfirst($_SESSION['user']['role']); ?></strong>.</p>
    </div>
<?php else: ?>
    <div class="container">
        <div class="title-bar">Login/Register
            <a href="?logout=true"><button class="close-btn">X</button></a>
        </div>

        <div id="loginForm" class="form active">
            <h2>Login</h2>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <span class="toggle-link" onclick="toggleForm()">Don't have an account? Register now!</span>
        </div>
        <div id="registerForm" class="form">
            <h2>Register</h2>
            <form method="POST">
                <input type="hidden" name="action" value="register">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Manager">Manager</option>
                    <option value="User">User</option>
                </select>
                <button type="submit">Register</button>
            </form>
            <span class="toggle-link" onclick="toggleForm()">Already have an account? Login here!</span>
        </div>
    </div>

    <div class="taskbar">
        <div id="taskbarApp" class="taskbar-icon" onclick="openApp()">🖥️ Login/Register</div>
    </div>
<?php endif; ?>
<script>
    function toggleForm() {
        document.getElementById('loginForm').classList.toggle('active');
        document.getElementById('registerForm').classList.toggle('active');
    }

    function closeApp() {
        document.getElementById('appWindow').style.display = 'none';
        document.getElementById('appIcon').style.display = 'block';
        document.getElementById('taskbarApp').style.display = 'none';
    }
    
    function openApp() {
        document.getElementById('appWindow').style.display = 'block';
        document.getElementById('appIcon').style.display = 'none';
        document.getElementById('taskbarApp').style.display = 'block';
    }
</script>
</body>
</html>
