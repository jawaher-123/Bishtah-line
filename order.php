<?php
session_start();
include "db.php";

$user_id    = $_SESSION['user_id'] ?? null;
$session_id = session_id();

if ($user_id) {
    $custom_result = $conn->query("SELECT * FROM custom_orders WHERE user_id = '$user_id' ORDER BY id DESC");
} else {
    $custom_result = $conn->query("SELECT * FROM custom_orders WHERE session_id = '$session_id' ORDER BY id DESC");
}

if ($user_id) {
    $where = "o.user_id = '$user_id'";
} else {
    $where = "o.session_id = '$session_id'";
}

$orders_result = $conn->query("
    SELECT o.*, GROUP_CONCAT(a.name SEPARATOR ', ') AS product_names
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN abayas a ON oi.product_id = a.id
    WHERE $where
    GROUP BY o.id
    ORDER BY o.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders - Bishtah Line</title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Alyamama:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
.delete-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.delete-modal-overlay.show {
    display: flex;
}
.delete-modal-box {
    background: white;
    border-radius: 20px;
    padding: 2rem 1.75rem;
    width: 300px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    animation: popIn 0.2s ease;
}
@keyframes popIn {
    from { transform: scale(0.85); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
.delete-modal-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #ffeaea;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    font-size: 28px;
}
.delete-modal-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 0.5rem;
}
.delete-modal-msg {
    font-size: 13px;
    color: #888;
    margin: 0 0 1.5rem;
    line-height: 1.6;
}
.delete-modal-actions {
    display: flex;
    gap: 10px;
}
.btn-modal-cancel {
    flex: 1;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ddd;
    background: #f5f5f5;
    color: #333;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}
.btn-modal-cancel:hover { background: #eee; }
.btn-modal-confirm {
    flex: 1;
    padding: 10px;
    border-radius: 10px;
    border: none;
    background: #e74c3c;
    color: white;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s;
}
.btn-modal-confirm:hover { background: #c0392b; }
</style>
</head>

<body>
<input type="checkbox" id="menu-toggle">

<!-- ===== DELETE CONFIRM MODAL ===== -->
<div class="delete-modal-overlay" id="deleteModal">
    <div class="delete-modal-box">
        <div class="delete-modal-icon">🗑️</div>
        <div class="delete-modal-title">Delete Order?</div>
        <div class="delete-modal-msg">Are you sure you want to delete this order?<br>This action cannot be undone.</div>
        <div class="delete-modal-actions">
            <button class="btn-modal-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button class="btn-modal-confirm" id="confirmDeleteBtn">Yes, Delete</button>
        </div>
    </div>
</div>

<!-- ===== HEADER ===== -->
<header class="store-header">
    <div class="header-left">
        <a href="cartp.php" class="icon-btn"><i class="fa-solid fa-cart-shopping"></i></a>
        <a href="wishListp.php" class="icon-btn"><i class="fa-solid fa-heart"></i></a>
    </div>
    <div class="header-center">
        <h1>BISHTAH LINE</h1>
        <p>Episodes of Your Life</p>
    </div>
    <div class="header-right">
        <label for="menu-toggle" class="menu-icon">
            <i class="fa-solid fa-bars"></i>
        </label>
    </div>
</header>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <label for="menu-toggle" class="close-btn"><i class="fa-solid fa-xmark"></i></label>
    <div class="profile-section">
        <a href="role.php">
            <img src="userr.png" class="profile-img">
            <div class="username"><?php echo $_SESSION['first_name'] ?? 'Account'; ?></div>
        </a>
    </div>
    <div class="sidebar-links">
        <hr><a href="homep.php">Home</a>
        <hr><a href="we.php">All Abayas</a>
        <hr><a href="customp.php">Custom Order</a>
        <hr><a href="order.php">My Order</a><hr>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>
</div>

<label for="menu-toggle" class="overlay"></label>

<main class="orders-container">

    <!-- ===== HEADER CARD ===== -->
    <div class="orders-card">
        <h1 style="font-family:'Alyamama'; color:#333; margin:0 0 8px 0;">Track Your Abayas ✨</h1>
        <p style="color:#666; margin:0;">Here you can see all your orders, custom designs, and current status.</p>
    </div>

    <!-- ================================================
         SECTION 1: Custom Orders
    ================================================ -->
    <div class="orders-card">
        <h2 style="font-family:'Alyamama'; color:#0c263b; margin:0 0 16px 0; font-size:1.4rem;">
            Custom Orders
        </h2>
        <div style="overflow:auto;">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Preview</th>
                        <th>Design</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($custom_result && $custom_result->num_rows > 0): ?>
                    <?php while($row = $custom_result->fetch_assoc()): ?>
                    <?php $price = $row['price'] ?? ''; ?>
                    <tr class="orders-row">
                        <td style="font-weight:900; color:#7c90a5;">
                            #BISHT-<?php echo 9900 + (int)$row['id']; ?>
                        </td>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image'] ?? ''); ?>"
                                 style="width:50px;height:70px;border-radius:8px;object-fit:cover;"
                                 onerror="this.style.display='none'">
                        </td>
                        <td style="font-weight:700;"><?php echo htmlspecialchars($row['style'] ?? '-'); ?></td>
                        <td><span class="orders-size-badge"><?php echo htmlspecialchars($row['size'] ?? '-'); ?></span></td>
                        <td><?php echo htmlspecialchars($row['color'] ?? '-'); ?></td>
                        <td style="font-weight:bold; color:#2c3e50;">
                            <?php if (!empty($price) && $price != '0'): ?>
                                <?php echo htmlspecialchars($price); ?> SAR
                            <?php else: ?>
                                <span style="color:#e74c3c; font-size:14px;">Pending...</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(($row['status'] ?? '') === "Completed"): ?>
                                <span class="orders-badge orders-badge-completed">Completed</span>
                            <?php elseif(($row['status'] ?? '') === "Cash on Delivery"): ?>
                                <span class="orders-badge orders-badge-cash">Cash Ordered</span>
                            <?php else: ?>
                                <span class="orders-badge orders-badge-progress">In Progress</span>
                            <?php endif; ?>
                        </td>
                        <td style="vertical-align:middle; min-width:120px;">

                            <?php if (!empty($price) && $price != '0'): ?>
                                <!-- زر Pay Now -->
                                <button type="button"
                                        onclick="document.getElementById('pay-c-<?php echo $row['id']; ?>').style.display='flex'"
                                        style="width:100%;padding:8px;background:#333;color:white;border:none;border-radius:5px;cursor:pointer;font-weight:bold;margin-bottom:8px;">
                                    Pay Now
                                </button>
                                <div id="pay-c-<?php echo $row['id']; ?>"
                                     style="display:none;flex-direction:column;gap:5px;margin-bottom:10px;border:1px solid #ddd;padding:8px;border-radius:5px;background:#fafafa;">
                                    <form action="payment.php" method="POST" style="margin:0;">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="price"    value="<?php echo $row['price']; ?>">
                                        <button type="submit" style="width:100%;padding:6px;background:#000;color:white;border:none;border-radius:5px;cursor:pointer;font-size:13px;"> Apple Pay</button>
                                    </form>
                                    <form action="cash_payment.php" method="POST" style="margin:0;">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="price"    value="<?php echo $row['price']; ?>">
                                        <button type="submit" style="width:100%;padding:6px;background:#27ae60;color:white;border:none;border-radius:5px;cursor:pointer;font-size:13px;">💵 Cash</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <!-- زر Waiting for Price -->
                                <button disabled
                                        style="width:100%;padding:8px;background:#e0e0e0;color:#888;border:none;border-radius:5px;cursor:not-allowed;margin-bottom:8px;">
                                    Waiting for Price
                                </button>
                            <?php endif; ?>

                            <!-- زر Delete يفتح المودال -->
                            <button type="button"
                                    onclick="openDeleteModal(<?php echo $row['id']; ?>)"
                                    style="width:100%;padding:6px;background:transparent;color:#e74c3c;border:1px solid #e74c3c;border-radius:5px;cursor:pointer;font-size:13px;"
                                    onmouseover="this.style.background='#ffeaea'"
                                    onmouseout="this.style.background='transparent'">
                                Delete
                            </button>

                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:#7c90a5;padding:30px;">
                            No custom orders yet. <a href="customp.php" class="orders-empty-link">Design your Abaya!</a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================================================
         SECTION 2: Store Orders
    ================================================ -->
    <div class="orders-card">
        <h2 style="font-family:'Alyamama'; color:#7c90a5; margin:0 0 16px 0; font-size:1.4rem;">
            Store Orders
        </h2>
        <div style="overflow:auto;">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Products</th>
                        <th>City</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                    <?php while($row = $orders_result->fetch_assoc()): ?>
                    <tr class="orders-row">
                        <td style="font-weight:900; color:#7c90a5;">
                            #ORD-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td style="font-weight:600; max-width:200px;">
                            <?php echo htmlspecialchars($row['product_names'] ?? '-'); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['city'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method'] ?? '-'); ?></td>
                        <td style="font-weight:bold; color:#2c3e50;">
                            <?php echo number_format($row['total'], 2); ?> SAR
                        </td>
                        <td>
                            <?php $status = $row['status'] ?? 'Processing'; ?>
                            <?php if ($status === 'Completed'): ?>
                                <span class="orders-badge orders-badge-completed">Completed</span>
                            <?php elseif ($status === 'Cancelled'): ?>
                                <span class="orders-badge" style="background:#fee2e2;color:#ef4444;border:1px solid #fecaca;">Cancelled</span>
                            <?php else: ?>
                                <span class="orders-badge orders-badge-progress">Processing</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:#999;padding:30px;">
                            No store orders yet. <a href="we.php" class="orders-empty-link">Shop now!</a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-links">
            <a href="https://www.instagram.com/bishtah_line" target="_blank">
                <i class="fab fa-instagram"></i> Instagram
            </a>
            <a href="contactp.php">Contact us</a>
        </div>
        <p class="copyright">© 2026 All rights reserved</p>
    </div>
</footer>

<script>
/* ===== DELETE MODAL ===== */
let pendingDeleteId = null;

function openDeleteModal(orderId) {
    pendingDeleteId = orderId;
    document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
    pendingDeleteId = null;
    document.getElementById('deleteModal').classList.remove('show');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (!pendingDeleteId) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'delete_Order.php';
    const input = document.createElement('input');
    input.type  = 'hidden';
    input.name  = 'order_id';
    input.value = pendingDeleteId;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
});

// إغلاق بالكليك على الخلفية
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

</body>
</html>