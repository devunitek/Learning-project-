<?php
require_once 'config.php';

// Fetch all courses
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Our Courses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
            margin: 0;
        }
        header {
            background: linear-gradient(135deg, #007bff, #00d4ff);
            padding: 20px;
            text-align: center;
            color: white;
        }
        .course-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 30px;
        }
        .course-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 250px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .course-title {
            font-weight: bold;
            font-size: 20px;
            margin: 10px 0;
            color: #333;
        }
        .price {
            color: #28a745;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .details-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .details-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Our Language Courses</h1>
    </header>

    <div class="course-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="course-card">
                <div class="course-title"><?= htmlspecialchars($row['title']) ?></div>
                <div><?= htmlspecialchars($row['short_desc']) ?></div>
                <div class="price">₹<?= number_format($row['base_price'], 2) ?></div>
                <a href="course_details.php?id=<?= $row['id'] ?>" class="details-btn">View Details</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
