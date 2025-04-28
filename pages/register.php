<?php
include('../includes/db.php');
session_start();

$error_message = '';

if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $error_message = "Email is already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $password, $role]);

        $_SESSION['user_id'] = $conn->lastInsertId();
        header("Location: ../index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - My Store</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f3f6;
        }
        .header {
            background-color: #2874f0;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 16px;
            border: 1px solid white;
            border-radius: 4px;
        }
        .container {
            display: flex;
            height: calc(100vh - 60px);
        }
        .left-section {
            background-color: #2874f0;
            color: white;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        .left-section h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .left-section p {
            font-size: 18px;
            text-align: center;
        }
        .right-section {
            background-color: white;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        .form-box {
            width: 100%;
            max-width: 400px;
        }
        .form-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #2874f0;
            color: white;
            font-size: 16px;
            margin-top: 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0059c1;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            text-decoration: none;
            color: #2874f0;
            font-weight: bold;
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .left-section, .right-section {
                flex: unset;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to MY STORE</h1>
        <a href="login.php">Login</a>
    </div>

    <div class="container">
        <div class="left-section">
            <h2>Looks like you're new here!</h2>
            <p>Sign up to get started and enjoy seamless shopping.</p>
        </div>
        <div class="right-section">
            <div class="form-box">
                <h2>Create My Account</h2>
                <form method="POST">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                    <label>Password:</label>
                    <input type="password" name="password" required>
                    <button type="submit" name="register">Register</button>
                </form>
                <?php if (!empty($error_message)): ?>
                    <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
                <?php endif; ?>
                <div class="login-link">
                    Already have an account? <a href="login.php">Sign in</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
