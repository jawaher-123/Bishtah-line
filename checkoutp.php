<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'] ?? NULL;

if(!isset($_SESSION['session_id'])){
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

if($user_id){
    $condition = "cart.user_id='$user_id'";
} else {
    $condition = "cart.session_id='$session_id'";
}

$sql = "
SELECT cart.product_id, cart.qty, cart.size,
       abayas.name, abayas.price
FROM cart
INNER JOIN abayas ON cart.product_id = abayas.id
WHERE $condition
";

$result   = mysqli_query($conn, $sql);
$subtotal = 0;
$shipping = 20;
$cart_items = [];

while($row = mysqli_fetch_assoc($result)){
    $subtotal += $row['price'] * $row['qty'];
    $cart_items[] = $row;
}

$total = $subtotal + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout | BISHTAH LINE</title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Alyamama:wght@300;400;500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

</head>

<body class="checkout-page">
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
    <label for="menu-toggle" class="close-btn"><i class="fa-solid fa-xmark"></i></label>
    <div class="profile-section">
            <img src="userr.png" class="profile-img">
            <div class="username">Account</div>
<div class="username"><?php echo $_SESSION['first_name'] ?? 'Account'; ?></div>

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
<main class="checkout-page-container">
<h2 class="checkout-page-title">Checkout</h2>

<section class="checkout-wrap">

<!-- ===== FORM ===== -->
<form class="checkout-form" id="checkoutForm" novalidate>

    <h3>Billing Details</h3>

    <div class="checkout-form-group" id="group-fullname">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" placeholder="e.g. Sara Al-Harbi" minlength="3">
        <i class="fa-solid fa-check checkout-field-check"></i>
        <div class="checkout-error-msg" id="err-fullname">Please enter your full name (min 3 characters)</div>
    </div>

    <div class="checkout-form-group" id="group-email">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="sara@example.com">
        <i class="fa-solid fa-check checkout-field-check"></i>
        <div class="checkout-error-msg" id="err-email">Please enter a valid email address</div>
    </div>

    <div class="checkout-form-group" id="group-phone">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" placeholder="05XXXXXXXX" maxlength="10">
        <i class="fa-solid fa-check checkout-field-check"></i>
        <div class="checkout-error-msg" id="err-phone">Phone must start with 05 and be 10 digits</div>
    </div>

    <div class="checkout-form-group" id="group-city">
        <label for="city">City</label>
        <input type="text" id="city" name="city" placeholder="e.g. Riyadh">
        <i class="fa-solid fa-check checkout-field-check"></i>
        <div class="checkout-error-msg" id="err-city">Please enter your city</div>
    </div>

    <div class="checkout-form-group" id="group-district">
        <label for="district">District</label>
        <input type="text" id="district" name="district" placeholder="e.g. Al-Malaz">
        <i class="fa-solid fa-check checkout-field-check"></i>
        <div class="checkout-error-msg" id="err-district">Please enter your district</div>
    </div>

    <div class="checkout-form-group" id="group-payment">
        <label for="payment">Payment Method</label>
        <div class="checkout-select-wrap">
            <select id="payment" name="payment">
                <option value="">— Select Payment Method —</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
            </select>
        </div>
        <div class="checkout-error-msg" id="err-payment">Please select a payment method</div>
    </div>

    <button class="checkout-btn-confirm" type="button" id="confirmBtn" onclick="confirmOrder()">
        <span class="checkout-btn-spinner"></span>
        <i class="fa-solid fa-check-circle"></i>&nbsp; Confirm Order
    </button>

</form>

<!-- ===== ORDER SUMMARY ===== -->
<aside class="checkout-order-summary">
    <h3><i class="fa-solid fa-receipt"></i> Order Summary</h3>

    <?php if(count($cart_items) > 0){ ?>
        <?php foreach($cart_items as $item){ ?>
        <div class="checkout-sum-row">
            <span><?php echo $item['name']; ?> × <?php echo $item['qty']; ?> <small>(Size <?php echo $item['size']; ?>)</small></span>
            <span><?php echo number_format($item['price'] * $item['qty'], 2); ?> SAR</span>
        </div>
        <?php } ?>

        <div class="checkout-sum-row">
            <span>Shipping</span>
            <span><?php echo $shipping; ?> SAR</span>
        </div>

        <div class="checkout-sum-row checkout-total-row">
            <strong>Total</strong>
            <strong><?php echo number_format($total, 2); ?> SAR</strong>
        </div>

    <?php } else { ?>
        <p style="color:var(--checkout-text-muted); text-align:center; padding:20px 0;">Your cart is empty.</p>
    <?php } ?>

    <a href="cartp.php" class="checkout-btn-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Cart
    </a>
</aside>

</section>
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
<div class="checkout-popup-overlay" id="popupOverlay"></div>
<div class="checkout-popup" id="blPopup">
    <div class="checkout-confetti-wrap" id="confettiWrap"></div>
    <span class="checkout-popup-icon" id="popupIcon">⚠️</span>
    <h3 id="popupTitle">Missing Field</h3>
    <p id="popupMsg">Please fill in all required fields.</p>
    <div class="checkout-popup-actions">
        <button class="checkout-popup-close" id="popupCloseBtn" onclick="closePopup()">OK</button>
    </div>
</div>

<script>
/* ===== POPUP ===== */
function showPopup(icon, title, msg, isSuccess){
    const popup = document.getElementById('blPopup');
    document.getElementById('popupIcon').textContent  = icon;
    document.getElementById('popupTitle').textContent = title;
    document.getElementById('popupMsg').textContent   = msg;
    popup.classList.toggle('checkout-success-popup', !!isSuccess);
    if(isSuccess) spawnConfetti();
    document.getElementById('popupOverlay').classList.add('active');
    popup.classList.add('active');
}

function closePopup(){
    document.getElementById('popupOverlay').classList.remove('active');
    document.getElementById('blPopup').classList.remove('active');
    document.getElementById('confettiWrap').innerHTML = '';
}

document.getElementById('popupOverlay').addEventListener('click', closePopup);

/* ===== CONFETTI ===== */
function spawnConfetti(){
    const wrap = document.getElementById('confettiWrap');
    wrap.innerHTML = '';
    const colors = ['#c9a96e','#e8d5b0','#27ae60','#f1c40f','#3498db','#e74c3c'];
    for(let i = 0; i < 28; i++){
        const dot = document.createElement('div');
        dot.className = 'checkout-confetti-dot';
        dot.style.cssText = `
            left: ${Math.random() * 100}%;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            animation-delay: ${Math.random() * 0.6}s;
            animation-duration: ${0.9 + Math.random() * 0.7}s;
            width: ${5 + Math.random() * 7}px;
            height: ${5 + Math.random() * 7}px;
        `;
        wrap.appendChild(dot);
    }
}

/* ===== VALIDATION ===== */
const rules = {
    fullname: { validate: v => v.trim().length >= 3,                      msg: 'Please enter your full name (min 3 characters)' },
    email:    { validate: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()), msg: 'Please enter a valid email address' },
    phone:    { validate: v => /^05[0-9]{8}$/.test(v.trim()),             msg: 'Phone must start with 05 and be exactly 10 digits' },
    city:     { validate: v => v.trim().length >= 2,                      msg: 'Please enter your city' },
    district: { validate: v => v.trim().length >= 2,                      msg: 'Please enter your district' },
    payment:  { validate: v => v !== '',                                  msg: 'Please select a payment method' }
};

function validateField(id){
    const el    = document.getElementById(id);
    const group = document.getElementById('group-' + id);
    const err   = document.getElementById('err-' + id);
    const rule  = rules[id];
    if(!el || !rule) return true;

    const isValid = rule.validate(el.value);
    el.classList.toggle('checkout-field-error', !isValid);
    el.classList.toggle('checkout-field-valid', isValid);
    if(group) group.classList.toggle('is-valid', isValid);
    if(err){ err.textContent = rule.msg; err.classList.toggle('show', !isValid); }
    return isValid;
}

Object.keys(rules).forEach(id => {
    const el = document.getElementById(id);
    if(!el) return;
    el.addEventListener('blur',  () => validateField(id));
    el.addEventListener('input', () => { if(el.classList.contains('checkout-field-error')) validateField(id); });
});

/* ===== CONFIRM ORDER ===== */
function confirmOrder(){
    const fields = Object.keys(rules);
    let firstInvalid = null;
    let allValid = true;
    let invalidNames = [];

    fields.forEach(id => {
        const ok = validateField(id);
        if(!ok){
            allValid = false;
            if(!firstInvalid) firstInvalid = id;
            const label = document.querySelector(`label[for="${id}"]`);
            if(label) invalidNames.push(label.textContent.trim());
        }
    });

    if(!allValid){
        const el = document.getElementById(firstInvalid);
        if(el){ el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        showPopup('⚠️', 'Missing Information', `Please complete: ${invalidNames.join(', ')}`, false);
        return;
    }

    <?php if(count($cart_items) === 0){ ?>
        showPopup('🛒', 'Empty Cart', 'Your cart is empty. Add items before placing an order.', false);
        return;
    <?php } ?>

    const btn = document.getElementById('confirmBtn');
    btn.classList.add('loading');
    btn.innerHTML = '<span class="checkout-btn-spinner"></span> Processing...';

    fetch("placeOrder.php", { method: "POST", body: new FormData(document.getElementById("checkoutForm")) })
    .then(res => res.text())
    .then(data => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-check-circle"></i>&nbsp; Confirm Order';

        if(data.trim() === "success"){
            showPopup('🎉', 'Order Confirmed!', 'Thank you for your order. We will contact you soon.', true);
            document.getElementById('popupCloseBtn').onclick = function(){
                closePopup();
                window.location.href = "order.php";
            };
        } else if(data.trim() === "empty_cart"){
            showPopup('🛒', 'Empty Cart', 'Your cart is empty!', false);
        } else {
            showPopup('❌', 'Something Went Wrong', 'Error: ' + data, false);
        }
    })
    .catch(() => {
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fa-solid fa-check-circle"></i>&nbsp; Confirm Order';
        showPopup('❌', 'Server Error', 'Could not connect to server. Please try again.', false);
    });
}
</script>

</body>
</html>
