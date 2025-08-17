<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

function sendBookingEmail($user_email, $user_name, $booking_id) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fk0160242@gmail.com';
        $mail->Password = 'your_app_password_here';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('fk0160242@gmail.com','Unitek.com');
        $mail->addAddress($user_email, $user_name);
        $mail->Subject = 'Booking Confirmation';
        $mail->Body    = "Dear {$user_name},\n\nYour booking (ID: {$booking_id}) is confirmed.\n\nThank you for choosing us!";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}
