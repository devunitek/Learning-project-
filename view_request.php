<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include '../config.php';

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price = $_POST['negotiated_price'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE bookings SET negotiated_price=?, status=? WHERE id=?");
    $stmt->bind_param("dsi", $price, $status, $id);
    $stmt->execute();
    echo "Updated successfully!";
}

$stmt = $conn->prepare("SELECT b.*, c.title FROM bookings b JOIN courses c ON b.course_id = c.id WHERE b.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<title>View Booking</title>
</head>
<body>
<h2>Booking #<?= $data['id'] ?> - <?= $data['title'] ?></h2>
<p>Name: <?= $data['customer_name'] ?></p>
<p>Email: <?= $data['customer_email'] ?></p>
<p>Status: <?= $data['status'] ?></p>

<form method="POST">
    <input type="number" step="0.01" name="negotiated_price" placeholder="Negotiated Price" value="<?= $data['negotiated_price'] ?>">
    <select name="status">
        <option value="pending" <?= $data['status']=="pending"?"selected":"" ?>>Pending</option>
        <option value="negotiating" <?= $data['status']=="negotiating"?"selected":"" ?>>Negotiating</option>
        <option value="approved" <?= $data['status']=="approved"?"selected":"" ?>>Approved</option>
        <option value="cancelled" <?= $data['status']=="cancelled"?"selected":"" ?>>Cancelled</option>
    </select>
    <button type="submit">Update</button>
</form>
</body>
</html>
