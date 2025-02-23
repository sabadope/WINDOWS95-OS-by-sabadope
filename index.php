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
                $_SESSION['auto_open_dashboard'] = true;
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


// Handle file uploads
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    if (!isset($_SESSION['user'])) {
        die("Unauthorized access");
    }
    
    $role = $_SESSION['user']['role'];
    $uploadDir = "uploads/$role/";
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = basename($_FILES['file']['name']);
    $targetFile = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        echo "File uploaded successfully.";
    } else {
        echo "File upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Windows 98 Login/Register</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: "MS Sans Serif", sans-serif; }
        body { display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100vh; background: #008080; position: relative; overflow: hidden; }
        .desktop-icon { position: absolute; top: 20px; left: 20px; cursor: pointer; text-align: center; color: white; }
        .desktop-icon img { width: 50px; height: 50px; }
        .container { width: 350px; padding: 20px; background: #C0C0C0; border: 2px solid black; box-shadow: 4px 4px black; text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: none; }
        .title-bar { background: navy; color: white; padding: 5px; text-align: left; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .close-btn { background: red; color: white; border: none; width: 20px; height: 20px; text-align: center; cursor: pointer; font-weight: bold; }
        .form { display: none; padding: 10px; border: 2px inset black; background: #E0E0E0; }
        .form.active { display: block; }
        h2 { margin-bottom: 15px; font-size: 14px; }
        input, select { width: 100%; padding: 5px; margin: 10px 0; border: 2px inset black; background: white; }
        button { width: 100%; padding: 5px; background: #C0C0C0; border: 2px outset black; cursor: pointer; }
        button:active { border: 2px inset black; }
        .toggle-link { margin-top: 10px; display: block; color: black; cursor: pointer; text-decoration: underline; }
        .taskbar { width: 100%; height: 30px; background: #C0C0C0; border-top: 2px solid black; display: flex; align-items: center; padding: 5px; position: absolute; bottom: 0; left: 0; }
        .taskbar .task { padding: 5px; background: #E0E0E0; border: 1px solid black; margin-right: 5px; cursor: pointer; }
    </style>
</head>
<body>
<div class="desktop-icon" onclick="openProgram()">
    <img src="icons/computer.png" alt="Program Icon">
    <div>Computer</div>
</div>
<div class="taskbar" id="taskbar"></div>
<?php if (isset($_SESSION['user'])): ?>
    <div class="container" id="programWindow">
        <div class="title-bar">
            <?php echo ucfirst($_SESSION['user']['role']); ?> Dashboard
            <a href="?logout=true"><button class="close-btn">X</button></a>
        </div>
        <h2>Welcome, <?php echo $_SESSION['user']['name']; ?>!</h2>
        <p>You are logged in as <strong><?php echo ucfirst($_SESSION['user']['role']); ?></strong>.</p>
    </div>
<?php else: ?>
    <div class="container" id="programWindow">
        <div class="title-bar">Login/Register <button class="close-btn" onclick="closeProgram()">X</button></div>
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
<?php endif; ?>
<?php if (isset($_SESSION['user'])): ?>
    <h2>Welcome, <?php echo $_SESSION['user']['name']; ?>!</h2>
    <p>You are logged in as <strong><?php echo ucfirst($_SESSION['user']['role']); ?></strong>.</p>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>
    <h3>Your Uploaded Files:</h3>
    <ul>
        <?php
        $role = $_SESSION['user']['role'];
        $uploadDir = "uploads/$role/";
        if (file_exists($uploadDir)) {
            $files = array_diff(scandir($uploadDir), ['.', '..']);
            foreach ($files as $file) {
                echo "<li><a href='$uploadDir$file' target='_blank'>$file</a></li>";
            }
        }
        ?>
    </ul>
    <a href="?logout=true">Logout</a>
<?php else: ?>
    <p>Please <a href="index.php">login</a>.</p>
<?php endif; ?>
<script>
    function toggleForm() {
        document.getElementById('loginForm').classList.toggle('active');
        document.getElementById('registerForm').classList.toggle('active');
    }

    function closeProgram() {
        document.getElementById('programWindow').style.display = 'none';
        let task = document.getElementById('task');
        if (task) task.remove();
    }

    function openProgram() {
        document.getElementById('programWindow').style.display = 'block';
        let taskbar = document.getElementById('taskbar');
        if (!document.getElementById('task')) {
            let task = document.createElement('div');
            task.className = 'task';
            task.id = 'task';
            task.innerText = 'My Program';
            task.onclick = openProgram;
            taskbar.appendChild(task);
        }
    }

    // Automatically open the dashboard if login was successful
    <?php if (isset($_SESSION['auto_open_dashboard']) && $_SESSION['auto_open_dashboard']) : ?>
        window.onload = function() {
            openProgram();
        };
        <?php unset($_SESSION['auto_open_dashboard']); ?>
    <?php endif; ?>
</script>
</body>
</html>
