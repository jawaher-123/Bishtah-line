<?php
session_start();
include "db.php";

// ===== جلب الكستم أوردر =====
$custom_result = $conn->query("
    SELECT co.*, u.first_name, u.last_name, u.email
    FROM custom_orders co
    LEFT JOIN users u ON co.user_id = u.id
    ORDER BY co.id DESC
");

// ===== جلب طلبات الشيك اوت =====
$orders_result = $conn->query("
    SELECT o.*, u.first_name, u.last_name, u.email,
           GROUP_CONCAT(a.name SEPARATOR ', ') AS product_names
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN abayas a ON oi.product_id = a.id
    GROUP BY o.id
    ORDER BY o.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel - Bishtah Line</title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Alyamama:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
.orders-container {
    max-width: 1200px;
    margin: 22px auto 60px;
    padding: 0 30px;
    box-sizing: border-box;
}

.orders-card {
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(31,41,55,0.12);
    border-radius: 18px;
    padding: 18px 24px;
    box-shadow: 0 18px 40px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: white;
    border-radius: 15px;
    overflow: hidden;
}

.orders-table th {
    background: #f8fafb;
    padding: 15px;
    color: #7c90a5;
    font-weight: 900;
    border-bottom: 2px solid #eee;
    text-align: center;
}

.orders-table td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
}

.orders-badge {
    padding: 6px 15px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-block;
}

.orders-badge-progress {
    background: #e3f2fd;
    color: #2196f3;
    border: 1px solid #bbdefb;
}

.orders-badge-completed {
    background: #e8f5e9;
    color: #4caf50;
    border: 1px solid #c8e6c9;
}

.orders-badge-cash {
    background: #3498db;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
}

.orders-size-badge {
    background: #7c90a5;
    color: white;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 900;
}

.orders-row { transition: 0.3s; }
.orders-row:hover td { background: #f8fafc; }

.admin-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.admin-stat-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    border: 1px solid #eee;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.admin-stat-card .stat-number {
    font-size: 2rem;
    font-weight: 900;
    color: #7c90a5;
    display: block;
}

.admin-stat-card .stat-label {
    font-size: 13px;
    color: #888;
    margin-top: 4px;
}

.status-select {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 13px;
    cursor: pointer;
    background: white;
    color: #333;
}

.status-select:focus { outline: none; border-color: #7c90a5; }

.price-form {
    display: flex;
    gap: 6px;
    align-items: center;
    justify-content: center;
}

.price-input {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 6px 8px;
    width: 80px;
    font-size: 13px;
    text-align: center;
}

.price-input:focus { outline: none; border-color: #7c90a5; }

.btn-save {
    background: #7c90a5;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 12px;
    cursor: pointer;
    font-weight: 700;
    transition: 0.2s;
}

.btn-save:hover { background: #557594; }

.customer-name {
    font-weight: 700;
    color: #2c3e50;
}

.customer-email {
    font-size: 12px;
    color: #888;
    margin-top: 3px;
}

.admin-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    flex-wrap: wrap;
    gap: 10px;
}

.search-input {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 13px;
    width: 220px;
}

.search-input:focus { outline: none; border-color: #7c90a5; }

.toast-admin {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(80px);
    background: #2c3e50;
    color: white;
    padding: 12px 28px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    z-index: 9999;
    transition: transform 0.3s;
    pointer-events: none;
}

.toast-admin.show { transform: translateX(-50%) translateY(0); }
</style>

</head>

<body>
<input type="checkbox" id="menu-toggle">

<!-- ===== HEADER ===== -->
<header class="store-header">
    <div class="header-left">
        <a href="cartp.php" class="icon-btn"><i class="fa-solid fa-cart-shopping"></i></a>
        <a href="wishListp.php" class="icon-btn"><i class="fa-solid fa-heart"></i></a>
    </div>
    <div class="header-center">
        <h1>BISHTAH LINE</h1>
        <p>Admin Panel</p>
    </div>
    <div class="header-right">
        <label for="menu-toggle" class="menu-icon"><i class="fa-solid fa-bars"></i></label>
    </div>
</header>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <label for="menu-toggle" class="close-btn"><i class="fa-solid fa-xmark"></i></label>
    <div class="profile-section">
        <img src="userr.png" class="profile-img">
        <div class="username">Admin</div>
    </div>
    <div class="sidebar-links">
        <hr><a href="homep.php">Home</a>
        <hr><a href="we.php">All Abayas</a>
        <hr><a href="admin_orders.php">Manage Orders</a>
        <hr><a href="managep.php">Manage Products</a><hr>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>
</div>

<label for="menu-toggle" class="overlay"></label>

<!-- ===== TOAST ===== -->
<div class="toast-admin" id="adminToast">✅ Saved!</div>

<main class="orders-container">

    <!-- ===== WELCOME ===== -->
    <div class="orders-card">
        <h1 style="font-family:'Alyamama'; color:#333; margin:0 0 6px 0;">
            <i class="fa-solid fa-shield-halved" style="color:#7c90a5;"></i>
            Admin Dashboard
        </h1>
        <p style="color:#666; margin:0;">Manage all customer orders and custom designs.</p>
    </div>

    <!-- ===== STATS ===== -->
    <div class="admin-stats">
        <div class="admin-stat-card">
            <span class="stat-number"><?php echo $custom_result->num_rows; ?></span>
            <div class="stat-label">Custom Orders</div>
        </div>
        <div class="admin-stat-card">
            <span class="stat-number"><?php echo $orders_result->num_rows; ?></span>
            <div class="stat-label">Store Orders</div>
        </div>
        <div class="admin-stat-card">
            <?php
                $total_rev = $conn->query("SELECT SUM(total) as rev FROM orders")->fetch_assoc()['rev'] ?? 0;
            ?>
            <span class="stat-number"><?php echo number_format($total_rev, 0); ?></span>
            <div class="stat-label">Total Revenue (SAR)</div>
        </div>
        <div class="admin-stat-card">
            <?php
                // ✅ التعديل: يعد In Progress من custom_orders + Processing من orders
                $pending_custom = $conn->query("SELECT COUNT(*) as c FROM custom_orders WHERE status='In Progress'")->fetch_assoc()['c'] ?? 0;
                $pending_store  = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='Processing'")->fetch_assoc()['c'] ?? 0;
                $pending = $pending_custom + $pending_store;
            ?>
            <span class="stat-number"><?php echo $pending; ?></span>
            <div class="stat-label">Pending Orders</div>
        </div>
    </div>

    <!-- ================================================
         SECTION 1: Custom Orders
    ================================================ -->
    <div class="orders-card">
        <div class="admin-header-row">
            <h2 style="font-family:'Alyamama'; color:#0c263b; margin:0; font-size:1.4rem;">
                <i class="fa-solid fa-wand-sparkles" style="color:#7c90a5;"></i> Custom Orders
            </h2>
            <input type="text" class="search-input" placeholder="🔍 Search customer..." oninput="filterTable('customTable', this.value)">
        </div>
        <div style="overflow:auto;">
            <table class="orders-table" id="customTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Preview</th>
                        <th>Design</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Status</th>
                        <!-- ✅ حُذف عامود Action -->
                    </tr>
                </thead>
                <tbody>
                <?php
                $custom_result->data_seek(0);
                if ($custom_result->num_rows > 0):
                    while($row = $custom_result->fetch_assoc()):
                        $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                        if(empty($name)) $name = 'Guest';
                ?>
                    <tr class="orders-row">
                        <td style="font-weight:900; color:#7c90a5;">
                            #BISHT-<?php echo 9900 + (int)$row['id']; ?>
                        </td>
                        <td>
                            <div class="customer-name"><?php echo htmlspecialchars($name); ?></div>
                            <div class="customer-email"><?php echo htmlspecialchars($row['email'] ?? '-'); ?></div>
                        </td>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image'] ?? ''); ?>"
                                 style="width:50px;height:70px;border-radius:8px;object-fit:cover;"
                                 onerror="this.style.display='none'">
                        </td>
                        <td style="font-weight:700;"><?php echo htmlspecialchars($row['style'] ?? '-'); ?></td>
                        <td><span class="orders-size-badge"><?php echo htmlspecialchars($row['size'] ?? '-'); ?></span></td>
                        <td><?php echo htmlspecialchars($row['color'] ?? '-'); ?></td>
                        <td>
                            <form class="price-form" onsubmit="savePrice(event, <?php echo $row['id']; ?>, 'custom')">
                                <input type="number" class="price-input" name="price"
                                       value="<?php echo htmlspecialchars($row['price'] ?? ''); ?>"
                                       placeholder="SAR">
                                <button type="submit" class="btn-save">Save</button>
                            </form>
                        </td>
                        <td>
                            <form onsubmit="saveStatus(event, <?php echo $row['id']; ?>, 'custom')">
                                <select class="status-select" name="status">
                                    <option value="In Progress"      <?php echo ($row['status']??'') === 'In Progress'      ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Completed"        <?php echo ($row['status']??'') === 'Completed'        ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cash on Delivery" <?php echo ($row['status']??'') === 'Cash on Delivery' ? 'selected' : ''; ?>>Cash on Delivery</option>
                                </select>
                                <br><br>
                                <button type="submit" class="btn-save">Update</button>
                            </form>
                        </td>
                        <!-- ✅ حُذف td الحذف بالكامل -->
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:#999;padding:30px;">
                            No custom orders yet.
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
        <div class="admin-header-row">
            <h2 style="font-family:'Alyamama'; color:#04101a; margin:0; font-size:1.4rem;">
                <i class="fa-solid fa-bag-shopping" style="color:#7c90a5;"></i> Store Orders
            </h2>
            <input type="text" class="search-input" placeholder="🔍 Search customer..." oninput="filterTable('storeTable', this.value)">
        </div>
        <div style="overflow:auto;">
            <table class="orders-table" id="storeTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Products</th>
                        <th>City</th>
                        <th>District</th>
                        <th>Phone</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $orders_result->data_seek(0);
                if ($orders_result->num_rows > 0):
                    while($row = $orders_result->fetch_assoc()):
                        $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                        if(empty($name)) $name = 'Guest';
                ?>
                    <tr class="orders-row">
                        <td style="font-weight:900; color:#7c90a5;">
                            #ORD-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td>
                            <div class="customer-name"><?php echo htmlspecialchars($name); ?></div>
                            <div class="customer-email"><?php echo htmlspecialchars($row['email'] ?? '-'); ?></div>
                        </td>
                        <td style="font-weight:600; max-width:180px; font-size:13px;">
                            <?php echo htmlspecialchars($row['product_names'] ?? '-'); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['city'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['district'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method'] ?? '-'); ?></td>
                        <td style="font-weight:bold; color:#2c3e50;">
                            <?php echo number_format($row['total'], 2); ?> SAR
                        </td>
                        <td>
                            <form onsubmit="saveStatus(event, <?php echo $row['id']; ?>, 'store')">
                                <select class="status-select" name="status">
                                    <option value="Processing" <?php echo ($row['status']??'') === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Completed"  <?php echo ($row['status']??'') === 'Completed'  ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled"  <?php echo ($row['status']??'') === 'Cancelled'  ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <br><br>
                                <button type="submit" class="btn-save">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;color:#999;padding:30px;">
                            No store orders yet.
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
/* ===== TOAST ===== */
function showToast(msg){
    const t = document.getElementById('adminToast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

/* ===== SAVE PRICE ===== */
function savePrice(e, id, type){
    e.preventDefault();
    const form  = e.target;
    const price = form.querySelector('[name="price"]').value;
    fetch('admin_update.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `action=update_price&id=${id}&type=${type}&price=${encodeURIComponent(price)}`
    })
    .then(r => r.text())
    .then(() => showToast('✅ Price saved!'))
    .catch(() => showToast('❌ Error saving price'));
}

/* ===== SAVE STATUS ===== */
function saveStatus(e, id, type){
    e.preventDefault();
    const form   = e.target;
    const status = form.querySelector('[name="status"]').value;
    fetch('admin_update.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `action=update_status&id=${id}&type=${type}&status=${encodeURIComponent(status)}`
    })
    .then(r => r.text())
    .then(() => showToast('✅ Status updated!'))
    .catch(() => showToast('❌ Error updating status'));
}

/* ===== SEARCH FILTER ===== */
function filterTable(tableId, query){
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    const q = query.toLowerCase();
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>

</body>
</html>