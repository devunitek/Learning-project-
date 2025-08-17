<?php
include 'config.php';

if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    die("Invalid booking ID.");
}

$booking_id = intval($_GET['booking_id']);

// Get booking details
$stmt = $conn->prepare("SELECT amount FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->bind_result($amount);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
</head>
<body>
    <h1>✅ Payment Successful!</h1>
    <p>Your payment for Booking ID <strong><?php echo $booking_id; ?></strong> has been received.</p>
    <p>Amount Paid: ₹<?php echo number_format($amount, 2); ?></p>
    <p><a href="index.php">Back to Home</a></p>
</body>
</html>

