<?php
require_once 'config.php';

// Get course ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid course ID.");
}

$course_id = intval($_GET['id']);

// Fetch course details
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    die("Course not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($course['title']) ?> - Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
            margin: 0;
            padding: 0;
        }
        header {
            background: linear-gradient(135deg, #007bff, #00d4ff);
            padding: 20px;
            color: white;
            text-align: center;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
        }
        .price {
            font-size: 20px;
            color: #28a745;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .booking-form {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .booking-form input, .booking-form textarea, .booking-form button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .booking-form button {
            background: #007bff;
            color: white;
            border: none;
            transition: background 0.3s ease;
        }
        .booking-form button:hover {
            background: #0056b3;
        }
        a.back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #007bff;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($course['title']) ?></h1>
    </header>

    <div class="container">
        <a href="index.php" class="back-link">← Back to Courses</a>
        <h2>About this course</h2>
        <p><?= nl2br(htmlspecialchars($course['full_desc'])) ?></p>

        <div class="price">Price: ₹<?= number_format($course['base_price'], 2) ?></div>

        <div class="booking-form">
            <h3>Book Appointment</h3>
            <form action="book_request.php" method="post">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <input type="text" name="phone" placeholder="Phone Number">
                <textarea name="notes" placeholder="Additional Notes"></textarea>
                <button type="submit">Confirm Booking</button>
            </form>
        </div>
    </div>
</body>
</html>
