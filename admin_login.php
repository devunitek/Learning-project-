<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['password']) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = 'Invalid password';
        }
    } else {
        $error = 'Admin not found';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            animation: fadeInBody 1s ease-in-out;
        }
        @keyframes fadeInBody {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 10px 25px rgba(0,0,0,0.15);
            width: 360px;
            text-align: center;
            animation: slideUp 0.6s ease-out forwards;
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        label span {
            color: red;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            transition: 0.3s;
        }
        input:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0,123,255,0.3);
        }
        button {
            padding: 12px;
            width: 100%;
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.2s ease-in-out;
        }
        button:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #0056b3, #0099cc);
        }
        .error {
            color: red;
            background: rgba(255, 0, 0, 0.1);
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 10px;
            animation: shake 0.4s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
        }
        .links {
            margin-top: 15px;
            font-size: 0.9rem;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .google-btn {
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .google-btn img {
            width: 20px;
            margin-right: 10px;
        }
        .google-btn:hover {
            background: #f7f7f7;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <label>Email or Username <span>*</span></label>
            <input type="email" name="email" placeholder="Enter your email" required autofocus>

            <label>Password <span>*</span></label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>

        <div class="google-btn" onclick="window.location.href='google_login.php'">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo">
            Continue with Google
        </div>

        <div class="links">
            <p><a href="forgot_password.php">Forgot password?</a></p>
            <p>Need course? <a href="register.php">Create an account</a></p>
        </div>
    </div>
</body>
</html>
