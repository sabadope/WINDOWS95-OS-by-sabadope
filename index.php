<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "windows95";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === "register") {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $password, $role);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Registration successful!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
        }
        $stmt->close();
    }

    if ($action === "login") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $full_name, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['role'] = $role;

            echo json_encode([
                "status" => "success",
                "message" => "Login successful!",
                "user" => ["full_name" => $full_name, "role" => $role]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid email or password!"]);
        }
        $stmt->close();
    }
    $conn->close();
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
    <div id="appIcon" class="icon" onclick="openApp()">🖥️ Open Login/Register</div>
    
    <div id="appWindow" class="container">
        <div class="title-bar">
            Login/Register
            <button class="close-btn" onclick="closeApp()">X</button>
        </div>
        <div id="authContainer">
            <div id="loginForm" class="form active">
                <h2>Login</h2>
                <input type="email" id="loginEmail" placeholder="Email" required>
                <input type="password" id="loginPassword" placeholder="Password" required>
                <button onclick="handleLogin()">Login</button>
                <span class="toggle-link" onclick="toggleForm()">Don't have an account? Register now!</span>
            </div>

            <div id="registerForm" class="form">
                <h2>Register</h2>
                <input type="text" id="regName" placeholder="Full Name" required>
                <input type="email" id="regEmail" placeholder="Email" required>
                <input type="password" id="regPassword" placeholder="Password" required>
                <select id="regRole" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Manager">Manager</option>
                    <option value="User">User</option>
                </select>
                <button onclick="handleRegister()">Register</button>
                <span class="toggle-link" onclick="toggleForm()">Already have an account? Login here!</span>
            </div>
        </div>

        <!-- User Dashboard -->
        <div id="userContainer" style="display:none;">
            <h2>Welcome, <span id="userName"></span>!</h2>
            <p>Your role: <span id="userRole"></span></p>
            <button onclick="logout()">Logout</button>
        </div>

    </div>
    
    <div class="taskbar">
        <div id="taskbarApp" class="taskbar-icon" onclick="openApp()">🖥️ Login/Register</div>
    </div>
    
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

        function toggleForm() {
            document.getElementById('loginForm').classList.toggle('active');
            document.getElementById('registerForm').classList.toggle('active');
        }

        function handleRegister() {
            let fullName = document.getElementById('regName').value;
            let email = document.getElementById('regEmail').value;
            let password = document.getElementById('regPassword').value;
            let role = document.getElementById('regRole').value;

            fetch("auth.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=register&full_name=${fullName}&email=${email}&password=${password}&role=${role}`
            })
            .then(response => response.json())
            .then(data => alert(data.message));
        }

        function handleLogin() {
            let email = document.getElementById('loginEmail').value;
            let password = document.getElementById('loginPassword').value;

            fetch("auth.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=login&email=${email}&password=${password}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById("userName").innerText = data.user.full_name;
                    document.getElementById("userRole").innerText = data.user.role;

                    document.getElementById("authContainer").style.display = "none";
                    document.getElementById("userContainer").style.display = "block";
                } else {
                    alert(data.message);
                }
            });
        }

        function logout() {
            fetch("logout.php", { method: "POST" })
            .then(() => {
                document.getElementById("authContainer").style.display = "block";
                document.getElementById("userContainer").style.display = "none";
            });
        }
    </script>
</body>
</html>
