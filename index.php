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
        input { width: 100%; padding: 5px; margin: 10px 0; border: 2px inset black; background: white; }
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
        <div id="loginForm" class="form active">
            <h2>Login</h2>
            <input type="email" placeholder="Email" required>
            <input type="password" placeholder="Password" required>
            <button>Login</button>
            <span class="toggle-link" onclick="toggleForm()">Don't have an account? Register now!</span>
        </div>
        
        <div id="registerForm" class="form">
            <h2>Register</h2>
            <input type="text" placeholder="Full Name" required>
            <input type="email" placeholder="Email" required>
            <input type="password" placeholder="Password" required>
            <button>Register</button>
            <span class="toggle-link" onclick="toggleForm()">Already have an account? Login here!</span>
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
    </script>
</body>
</html>
