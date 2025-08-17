<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logging Out...</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            text-align: center;
            overflow: hidden;
        }

        h1 {
            font-size: 2rem;
            animation: fadeIn 1s ease-in-out forwards;
        }

        p {
            font-size: 1rem;
            opacity: 0;
            animation: fadeIn 1.5s ease-in-out 0.5s forwards;
        }

        .loader {
            margin-top: 20px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top: 5px solid white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        // Redirect after 2 seconds
        setTimeout(function(){
            window.location.href = "admin_login.php";
        }, 2000);
    </script>
</head>
<body>
    <h1>Logging you out...</h1>
    <p>Thank you for using the admin panel</p>
    <div class="loader"></div>
</body>
</html>


