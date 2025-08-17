<?php
require_once 'config.php';

// Get JSON data from Razorpay webhook
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if (!empty($data['event']) && $data['event'] === 'payment_link.paid') {
    $orderId = $data['payload']['payment_link']['entity']['reference_id'] ?? null;

    if ($orderId) {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'payment_received' WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $stmt->close();
    }
}

http_response_code(200);
echo "Webhook received";
