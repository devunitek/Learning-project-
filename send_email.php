<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config.php';

// Booking ID from URL
$booking_id = intval($_GET['booking_id'] ?? 0);

$message = '';
$status = '';

if ($booking_id > 0) {
    $sql = "SELECT customer_name, customer_email FROM bookings WHERE id = $booking_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row        = $result->fetch_assoc();
        $user_name  = $row['customer_name'];
        $user_email = $row['customer_email'];

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'fk0160242@gmail.com';
            $mail->Password = 'carxriytqohbjpem'; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('fk0160242@gmail.com', 'Unitek.com');
            $mail->addAddress($user_email, $user_name);
            $mail->Subject = 'Booking Confirmation';
            $mail->Body    = "Dear {$user_name},\n\nYour booking (ID: {$booking_id}) is confirmed.\n\nThank you for choosing us!";

            $mail->send();

            $status = 'success';
            $message = "Email sent successfully to {$user_email}";
        } catch (Exception $e) {
            $status = 'error';
            $message = "Email could not be sent. Error: {$mail->ErrorInfo}";
        }
    } else {
        $status = 'error';
        $message = "Booking not found or query failed.";
    }
} else {
    $status = 'error';
    $message = "Invalid booking ID";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            text-align: center;
            animation: slideIn 0.6s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .success {
            color: #28a745;
            font-size: 22px;
            animation: pulse 1.5s infinite;
        }
        .error {
            color: #dc3545;
            font-size: 22px;
            animation: shake 0.4s ease-in-out;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($status === 'success'): ?>
            <div class="success">✅ <?php echo $message; ?></div>
        <?php else: ?>
            <div class="error">❌ <?php echo $message; ?></div>
        <?php endif; ?>
        <a href="index.php" class="btn">Go Back</a>
    </div>
</body>
</html>
