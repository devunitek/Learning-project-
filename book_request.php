<?php
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id']);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $notes = $_POST['notes'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("
        INSERT INTO bookings 
        (course_id, customer_name, customer_email, customer_phone, notes, date, payment_status, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending', 'pending')
    ");
    $stmt->bind_param("isssss", $course_id, $name, $email, $phone, $notes, $date);
    $stmt->execute();

    $booking_id = $stmt->insert_id;

    // Redirect directly to payment_success.php
header("Location: payment_success.php?booking_id=" . $booking_id);
exit;
}
?>
