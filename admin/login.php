<?php
include '../includes/db.php';
session_start();

$message = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        
        // If "Remember Me" is checked (optional)
        if (!empty($_POST['remember'])) {
            setcookie("admin_email", $email, time() + (86400 * 30)); // 30 days
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Invalid credentials or you are not an admin.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .password-field {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 42%;
            cursor: pointer;
            color: #888;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
        .error-message {
            text-align: center;
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Admin Login</h2>

        <?php if (!empty($message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required value="<?php if(isset($_COOKIE['admin_email'])) echo $_COOKIE['admin_email']; ?>">

            <label for="password">Password</label>
            <div class="password-field">
                <input type="password" name="password" id="password" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

            <div class="remember-me">
                <input type="checkbox" name="remember" id="remember" <?php if(isset($_COOKIE['admin_email'])) echo "checked"; ?>>
                <label for="remember" style="font-weight:normal;">Remember Me</label>
            </div>

            <button type="submit" name="login">Login</button>
        </form>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Admin Panel | Powered by YourWebsite</p>
    </footer>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = "password";
                toggleIcon.textContent = '👁️';
            }
        }
    </script>

</body>
</html>
