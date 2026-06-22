<?php
session_start();
include "db.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

if(!isset($_SESSION['session_id'])){
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

if($user_id){
    $condition  = "cart.user_id='$user_id'";
    $condition2 = "user_id='$user_id'";
} else {
    $condition  = "cart.session_id='$session_id'";
    $condition2 = "session_id='$session_id'";
}

if(isset($_POST['action'])){
    if($_POST['action'] == "update"){
        $cart_id = intval($_POST['cart_id']);
        $qty = intval($_POST['qty']);
        if($qty < 1) $qty = 1;
        mysqli_query($conn, "UPDATE cart SET qty='$qty' WHERE id='$cart_id' AND $condition2");
        echo "success"; exit();
    }
    if($_POST['action'] == "remove"){
        $cart_id = intval($_POST['cart_id']);
        mysqli_query($conn, "DELETE FROM cart WHERE id='$cart_id' AND $condition2");
        echo "success"; exit();
    }
}

$sql = "
SELECT cart.id AS cart_id, cart.qty, cart.size,
abayas.name, abayas.price, abayas.image, abayas.description
FROM cart
INNER JOIN abayas ON cart.product_id = abayas.id
WHERE $condition
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shopping Cart | BISHTAH LINE</title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Alyamama:wght@300;400;500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

</head>

<body>
<div class="container">

<input type="checkbox" id="menu-toggle">
<div id="top"></div>

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
        <label for="menu-toggle" class="menu-icon"><i class="fa-solid fa-bars"></i></label>
    </div>
</header>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <label for="menu-toggle" class="close-btn">
        <i class="fa-solid fa-xmark"></i>
    </label>

    <div class="profile-section">
         <img src="userr.png" class="profile-img">
<div class="username">
    <?php echo $_SESSION['first_name'] ?? 'Account'; ?>
</div>
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

<!-- ===== MAIN ===== -->
<main class="cart-page-container">
<h2 class="cart-page-title">Shopping Cart</h2>

<table class="cart-table" id="cartTable">
<thead>
<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Size</th>
    <th>Action</th>
</tr>
</thead>
<tbody>

<?php
$grandTotal = 0;
if(mysqli_num_rows($result) > 0){
    $delay = 0;
    while($row = mysqli_fetch_assoc($result)){
        $total = $row['price'] * $row['qty'];
        $grandTotal += $total;
        $delayStyle = "animation-delay:" . ($delay * 0.08) . "s";
        $delay++;
?>

<tr data-cart-id="<?php echo $row['cart_id']; ?>" style="<?php echo $delayStyle; ?>">
    <td>
        <div class="cart-product">
            <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" class="cart-img">
            <div class="cart-info">
                <strong><?php echo $row['name']; ?></strong>
                <small><?php echo $row['description']; ?></small>
            </div>
        </div>
    </td>
    <td class="cart-price" data-price="<?php echo $row['price']; ?>">
        <?php echo $row['price']; ?> SAR
    </td>
    <td>
        <input type="number" value="<?php echo $row['qty']; ?>" min="1" step="1" class="cart-qty-input">
    </td>
    <td><?php echo $row['size']; ?></td>
    <td>
        <button class="cartpage-btn cart-btn-danger remove-btn">
            <i class="fa-solid fa-trash-can"></i> Remove
        </button>
    </td>
</tr>

<?php } } else { ?>

<tr class="cart-empty-row">
    <td colspan="5">
        <i class="fa-solid fa-cart-xmark" style="font-size:2.5rem;color:#555;display:block;margin-bottom:12px;"></i>
        Your cart is empty.
    </td>
</tr>

<?php } ?>

</tbody>
</table>

<div class="cart-total-box">
    Total: &nbsp;<span id="grandTotal"><?php echo number_format($grandTotal,2); ?></span>&nbsp; SAR
</div>

<div class="cart-actions">
    <a href="checkoutp.php" class="cartpage-btn cart-btn-primary" id="checkoutBtn">
        <i class="fa-solid fa-lock"></i> Proceed to Checkout
    </a>
</div>

</main>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-links">
            <a href="https://www.instagram.com/bishtah_line?igsh=YnFubHk4YnQycTdy" target="_blank">
                <i class="fab fa-instagram"></i> Instagram
            </a>
            <a href="contactp.php">Contact us</a>
        </div>
        <p class="copyright">© 2026 All rights reserved</p>
    </div>
</footer>

<a href="#top" class="back-to-top">↑</a>

</div>

<!-- ===== POPUP ===== -->
<div class="cart-popup-overlay" id="popupOverlay"></div>
<div class="cart-popup" id="blPopup">
    <span class="cart-popup-icon" id="popupIcon">🛒</span>
    <h3 id="popupTitle">Updated!</h3>
    <p id="popupMsg">Quantity has been updated successfully.</p>
    <button class="cart-popup-close" onclick="closePopup()">OK</button>
</div>

<!-- ===== TOAST ===== -->
<div class="cart-toast" id="blToast">
    <span class="cart-toast-icon">✅</span>
    <span id="toastMsg">Done!</span>
</div>

<script>
/* ===== POPUP ===== */
function showPopup(icon, title, msg){
    document.getElementById('popupIcon').textContent  = icon;
    document.getElementById('popupTitle').textContent = title;
    document.getElementById('popupMsg').textContent   = msg;
    document.getElementById('popupOverlay').classList.add('active');
    document.getElementById('blPopup').classList.add('active');
}

function closePopup(){
    document.getElementById('popupOverlay').classList.remove('active');
    document.getElementById('blPopup').classList.remove('active');
}

document.getElementById('popupOverlay').addEventListener('click', closePopup);

/* ===== TOAST ===== */
function showToast(msg){
    const t = document.getElementById('blToast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}

/* ===== GRAND TOTAL ===== */
function updateGrandTotal(){
    let rows = document.querySelectorAll("#cartTable tr[data-cart-id]");
    let grandTotal = 0;
    rows.forEach(row => {
        let price = parseFloat(row.querySelector(".cart-price").dataset.price);
        let qty   = parseInt(row.querySelector(".cart-qty-input").value) || 1;
        grandTotal += price * qty;
    });
    const el = document.getElementById("grandTotal");
    el.textContent = grandTotal.toFixed(2);
    el.classList.remove('bump');
    void el.offsetWidth;
    el.classList.add('bump');
}

/* ===== QTY CHANGE ===== */
document.querySelectorAll(".cart-qty-input").forEach(input => {
    input.addEventListener("change", function(){
        let val = parseInt(this.value);
        if(isNaN(val) || val < 1) this.value = 1;
        updateGrandTotal();
        showPopup('🛍️', 'Quantity Updated', 'Cart total has been recalculated.');
    });
});

/* ===== REMOVE ===== */
document.querySelectorAll(".remove-btn").forEach(btn => {
    btn.addEventListener("click", function(){
        let row    = this.closest("tr");
        let cartId = row.dataset.cartId;
        row.classList.add('cart-row-removing');
        setTimeout(() => {
            fetch("cartp.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=remove&cart_id=" + cartId
            })
            .then(res => res.text())
            .then(() => {
                row.remove();
                updateGrandTotal();
                showToast('Item removed from cart');
            });
        }, 380);
    });
});

/* ===== CHECKOUT VALIDATION ===== */
document.getElementById('checkoutBtn').addEventListener('click', function(e){
    let rows = document.querySelectorAll("#cartTable tr[data-cart-id]");
    if(rows.length === 0){
        e.preventDefault();
        showPopup('⚠️', 'Empty Cart', 'Please add items to your cart before proceeding to checkout.');
    }
});
</script>

</body>
</html>
