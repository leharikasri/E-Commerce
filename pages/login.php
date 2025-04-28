<?php
include('../includes/db.php');
session_start();

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ../index.php");
        exit();
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | MyStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f3f6;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: white;
            display: flex;
            width: 800px;
            height: 500px;
            border-radius: 10px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .left-panel {
            background-color: #2874f0;
            color: white;
            flex: 1;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .left-panel h2 {
            font-size: 28px;
            margin-bottom: 15px;
        }
        .left-panel p {
            font-size: 16px;
            color: #dfe6f1;
        }
        .right-panel {
            flex: 1;
            padding: 40px 30px;
        }
        .right-panel h2 {
            color: #333;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 15px;
            font-size: 0.95em;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #fb641b;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
        }
        button:hover {
            background-color: #d75a0e;
        }
        .links {
            text-align: center;
            margin-top: 15px;
        }
        .links a {
            text-decoration: none;
            color: #2874f0;
        }
        .top-login {
            position: absolute;
            top: 20px;
            right: 30px;
        }
        .top-login a {
            text-decoration: none;
            background-color: #2874f0;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="top-login">
        <a href="register.php">New user? Register</a>
    </div>
    <div class="container">
        <div class="left-panel">
            <h2>Login</h2>
            <p>Get access to your Orders, Wishlist and Recommendations</p>
        </div>
        <div class="right-panel">
            <h2>Welcome to MyStore</h2>
            <form method="POST">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?= htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <button type="submit" name="login">Sign In</button>
            </form>
            <div class="links">
                <p><a href="#">Forgot Password?</a></p>
                <p>Don't have an account? <a href="register.php">Create One</a></p>
            </div>
        </div>
    </div>
</body>
</html>
