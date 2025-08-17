<?php
session_start();
require_once 'config.php';

// --- AUTH ---
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// --- AJAX endpoint: update negotiated price ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_price') {
    header('Content-Type: application/json; charset=utf-8');

    $booking_id = intval($_POST['booking_id'] ?? 0);
    $neg_price = floatval($_POST['negotiated_price'] ?? 0);

    if ($booking_id <= 0 || $neg_price <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE bookings SET negotiated_price = ? WHERE id = ?");
    $stmt->bind_param("di", $neg_price, $booking_id);
    $ok = $stmt->execute();
    $stmt->close();

    if ($ok) {
        echo json_encode(['status' => 'ok', 'message' => 'Price updated', 'booking_id' => $booking_id, 'negotiated_price' => $neg_price]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB update failed']);
    }
    exit;
}

// --- Handle "mark done" action (non-AJAX) ---
if (isset($_GET['mark_done'])) {
    $order_id = intval($_GET['mark_done']);
    $stmt = $conn->prepare("UPDATE bookings SET status='Done' WHERE id=?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit;
}

// --- Fetch bookings with course info ---
$sql = "
    SELECT b.id, b.course_id, b.customer_name, b.customer_email, b.customer_phone, 
           b.status, b.payment_status, b.original_price, b.negotiated_price,
           c.title, c.base_price
    FROM bookings b
    JOIN courses c ON b.course_id = c.id
    ORDER BY b.id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Admin Dashboard — Bookings</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<style>
    :root{
        --bg1:#f0f6ff; --bg2:#e8f9ff; --card:#ffffff; --accent:#0d6efd;
        --success:#28a745; --danger:#ff4b5c; --muted:#6c757d;
        --glass: rgba(255,255,255,0.6);
    }
    *{box-sizing:border-box}
    body{
        margin:0; font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;
        background: linear-gradient(135deg,var(--bg1),var(--bg2));
        padding:18px;
        color:#222;
    }
    .wrap{max-width:1200px;margin:0 auto;}
    .header{
        display:flex; gap:12px; align-items:center; justify-content:space-between;
        margin-bottom:16px;
    }
    .brand{font-size:1.25rem; font-weight:700; color:var(--accent);}
    .summary{font-size:0.95rem; color:var(--muted);}
    .card{
        background:var(--card); border-radius:12px; padding:14px; box-shadow:0 8px 24px rgba(13,110,253,0.06);
        overflow:auto;
    }
    table{width:100%; border-collapse:collapse; min-width:900px;}
    th,td{padding:10px 12px; text-align:center; border-bottom:1px solid #eef2f8; vertical-align:middle;}
    th{background: linear-gradient(90deg,var(--accent),#2aa7ff); color:#fff; font-weight:600; position:sticky; top:0;}
    td.name{text-align:left;}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;font-weight:600;font-size:0.85rem;}
    .status-pending{color:#b45d00}
    .status-done{color:var(--success)}
    .payment-pending{color:#b45d00}
    .payment-success{color:var(--success)}
    select, input[type="number"]{padding:6px 8px;border-radius:6px;border:1px solid #d6e4ff; min-width:120px;}
    button.btn{padding:8px 10px;border-radius:8px;border:none;cursor:pointer;background:var(--accent);color:#fff;font-weight:700}
    .btn:active{transform:translateY(1px)}
    .btn-logout{background:var(--danger)}
    .flex-row{display:flex;gap:10px;align-items:center;justify-content:center}
    .toast{position:fixed;right:20px;bottom:20px;background:linear-gradient(90deg,#1e7e34,#28a745);color:#fff;padding:12px 16px;border-radius:8px;box-shadow:0 8px 20px rgba(0,0,0,0.12);display:none;}
    @media (max-width:980px){
        table{min-width:760px}
    }
    @media (max-width:680px){
        .header{flex-direction:column;align-items:flex-start;gap:8px}
        table{min-width:680px}
        th:nth-child(1), td:nth-child(1){display:none}
    }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div>
            <div class="brand">Shruti Booking — Admin</div>
            <div class="summary">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> — manage bookings & negotiate prices</div>
        </div>
        <div class="flex-row">
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Course</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Original Price</th>
                    <th>Negotiated Price</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="booking-rows">
            <?php while ($row = $result->fetch_assoc()): 
                // compute prices
                $original = floatval($row['original_price']);
                if ($original <= 0) $original = floatval($row['base_price']); // fallback
                $neg = ($row['negotiated_price'] !== null && $row['negotiated_price'] != 0) ? floatval($row['negotiated_price']) : '';
            ?>
                <tr data-id="<?= (int)$row['id'] ?>">
                    <td><?= (int)$row['id'] ?></td>
                    <td class="name"><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['customer_email']) ?></td>
                    <td><?= htmlspecialchars($row['customer_phone']) ?></td>
                    <td>₹<?= number_format($original,2) ?></td>
                    <td>
                        <div style="display:flex;gap:8px;align-items:center;justify-content:center">
                            <select class="neg-select">
                                <option value="">-- choose --</option>
                                <option value="500">₹500</option>
                                <option value="750">₹750</option>
                                <option value="800">₹800</option>
                                <option value="900">₹900</option>
                                <option value="1000">₹1,000</option>
                                <option value="1200">₹1,200</option>
                            </select>
                            <input type="number" class="neg-custom" placeholder="or custom" min="100" max="2000" step="0.01" style="width:110px;" />
                            <button class="btn save-price">Save</button>
                        </div>
                        <div class="muted" style="margin-top:6px;font-size:0.9rem;color:#666">
                            Current: <strong class="current-neg"><?= $neg !== '' ? '₹'.number_format($neg,2) : '—' ?></strong>
                        </div>
                    </td>
                    <td class="<?= ($row['status'] == 'Done') ? 'status-done' : 'status-pending' ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </td>
                    <td class="<?= ($row['payment_status'] == 'success') ? 'payment-success' : 'payment-pending' ?>">
                        <?= htmlspecialchars(ucfirst($row['payment_status'])) ?>
                    </td>
                    <td>
                        <?php if ($row['status'] != 'Done'): ?>
                            <a class="btn" href="?mark_done=<?= (int)$row['id'] ?>">Mark Done</a>
                        <?php else: ?>
                            ✅
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="toast" class="toast">Saved</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // wire up save buttons
    document.querySelectorAll('.save-price').forEach(function(btn){
        btn.addEventListener('click', async function(e){
            const row = e.target.closest('tr');
            const bookingId = row.getAttribute('data-id');
            const select = row.querySelector('.neg-select');
            const custom = row.querySelector('.neg-custom');
            let price = select.value ? parseFloat(select.value) : (custom.value ? parseFloat(custom.value) : 0);

            if (!price || price < 100 || price > 2000) {
                alert('Please select or enter a price between ₹100 and ₹2000.');
                return;
            }

            // disable while saving
            e.target.disabled = true;
            e.target.textContent = 'Saving...';

            try {
                const fd = new FormData();
                fd.append('action','update_price');
                fd.append('booking_id', bookingId);
                fd.append('negotiated_price', price);

                const res = await fetch('admin_dashboard.php', {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin'
                });
                const data = await res.json();

                if (data.status === 'ok') {
                    // update current-neg text
                    row.querySelector('.current-neg').textContent = '₹' + (parseFloat(price).toFixed(2));
                    showToast('Price updated ✓');
                } else {
                    showToast('Update failed', true);
                    console.error(data);
                }
            } catch (err) {
                console.error(err);
                showToast('Network error', true);
            } finally {
                e.target.disabled = false;
                e.target.textContent = 'Save';
            }
        });
    });
});

function showToast(text, isError=false){
    const t = document.getElementById('toast');
    t.textContent = text;
    t.style.background = isError ? 'linear-gradient(90deg,#cc3d2b,#ff4b5c)' : 'linear-gradient(90deg,#1e7e34,#28a745)';
    t.style.display = 'block';
    setTimeout(()=> t.style.display = 'none', 2800);
}
</script>
</body>
</html>
