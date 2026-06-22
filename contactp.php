<?php
session_start();
require 'db.php';
$show_popup = false;
$popup_name = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $msg   = mysqli_real_escape_string($conn, $_POST['message']);

    $full_phone = "+966" . $phone;

    $sql = "INSERT INTO contacts (name, email, phone, message) VALUES ('$name', '$email', '$full_phone', '$msg')";

    if (mysqli_query($conn, $sql)) {
        $show_popup = true;
        $popup_name = $name;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us | Bishtah</title>

<link rel="stylesheet" href="style1.css">
<link href="https://fonts.googleapis.com/css2?family=Alyamama:wght@300;400;500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
<div class="container">

<input type="checkbox" id="menu-toggle">
<div id="top"></div>

<!-- ===== SUCCESS POPUP ===== -->
<div class="contact-popup-overlay <?php echo $show_popup ? 'show' : ''; ?>" id="successPopup">
    <div class="contact-popup-box">
        <span class="contact-popup-icon">✨</span>
        <h3>Message Sent!</h3>
        <p>Thank you, <strong><?php echo htmlspecialchars($popup_name); ?></strong>!<br>
        Your message has been received. Our team will get back to you shortly.</p>
        <button class="contact-popup-close" onclick="document.getElementById('successPopup').classList.remove('show')">
            Close
        </button>
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
            <img src="userr.png" class="profile-img">
<div class="username"><?php echo $_SESSION['first_name'] ?? 'Account'; ?></div>
    </div>
    <div class="sidebar-links">
        <hr><a href="homep.php">Home</a>
        <hr><a href="we.php">All Abayas</a>
        <hr><a href="customp.php">Custom Order</a>
        <hr><a href="order.php">My Order</a>
        
    </div>
    <div class="logout-section">
<a href="logout.php" class="logout-btn">Log Out</a>
    </div>
</div>

<label for="menu-toggle" class="overlay"></label>

<!-- ===== MAIN ===== -->
<div class="contact-main">

    <div class="contact-section-header">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! Please fill out the form below and our team will get back to you shortly.</p>
    </div>

    <!-- FORM CARD -->
    <div class="contact-card">
        <form id="contactForm" method="POST" action="contactp.php">

            <div class="contact-form-group">
                <label>Full Name</label>
                <input type="text" id="name" name="name" class="contact-simple-input" placeholder="e.g. Sara Al-Harbi">
                <div class="contact-error-text" id="nameError">Please enter your full name.</div>
            </div>

            <div class="contact-form-group">
                <label>Email Address</label>
                <div class="contact-input-group">
                    <input type="text" id="email_username" placeholder="username">
                    <div class="contact-addon" style="border-right:none; border-left:2px solid #e1e8ed;">@</div>
                    <select id="email_domain" class="contact-domain-select">
                        <option value="gmail.com">gmail.com</option>
                        <option value="outlook.com">outlook.com</option>
                        <option value="hotmail.com">hotmail.com</option>
                        <option value="yahoo.com">yahoo.com</option>
                        <option value="icloud.com">icloud.com</option>
                    </select>
                </div>
                <input type="hidden" name="email" id="final_email">
                <div class="contact-error-text" id="emailError">Please enter a valid username.</div>
            </div>

            <div class="contact-form-group">
                <label>Phone Number</label>
                <div class="contact-input-group">
                    <div class="contact-addon">🇸🇦 +966</div>
                    <input type="tel" id="phone" name="phone" placeholder="5XXXXXXXX" maxlength="9">
                </div>
                <div class="contact-error-text" id="phoneError">Number must start with 5 and be exactly 9 digits.</div>
            </div>

            <div class="contact-form-group">
                <label>Message</label>
                <textarea id="message" name="message" class="contact-simple-input" rows="5" placeholder="How can we help you?"></textarea>
                <div class="contact-error-text" id="messageError">Please enter at least 10 characters.</div>
            </div>

            <button type="submit" id="submitBtn" class="contact-submit-btn">Send Message ✉️</button>

        </form>
    </div>

    <!-- MAP CARD -->
    <div class="contact-map-card">
        <div class="contact-map-header">
            <h3><i class="fa-solid fa-location-dot" style="color:#7c90a5; margin-right:8px;"></i> Find Us</h3>
<p>Visit our showroom at Mall of Dhahran, Eastern Province</p>        </div>
        <div class="contact-map-info">
            <div class="contact-map-info-item">
                <i class="fa-solid fa-map-pin"></i>
                <span>Dhahran, Eastern Province, Saudi Arabia</span>
            </div>
            <div class="contact-map-info-item">
                <i class="fa-solid fa-clock"></i>
                <span>Sat – Thu: 10:00 AM – 10:00 PM</span>
            </div>
            <div class="contact-map-info-item">
                <i class="fa-solid fa-phone"></i>
                <span>+966 5X XXX XXXX</span>
            </div>
        </div>
        <!-- Google Maps Embed — استبدلي YOUR_API_KEY بمفتاحك -->
        <iframe
    class="contact-map-frame"
    src="https://maps.google.com/maps?q=Mall+of+Dhahran,+Dhahran,+Saudi+Arabia&t=&z=16&ie=UTF8&iwloc=&output=embed"
    allowfullscreen
    loading="lazy">
</iframe>
    </div>

</div>

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

<a href="#top" class="back-to-top">↑</a>

</div>

<script>
document.getElementById('contactForm').addEventListener('submit', function(event) {
    let isValid = true;

    const name      = document.getElementById('name').value.trim();
    const emailUser = document.getElementById('email_username').value.trim();
    const phone     = document.getElementById('phone').value.trim();
    const message   = document.getElementById('message').value.trim();

    document.getElementById('nameError').style.display    = 'none';
    document.getElementById('emailError').style.display   = 'none';
    document.getElementById('phoneError').style.display   = 'none';
    document.getElementById('messageError').style.display = 'none';

    if (name === '')                                        { document.getElementById('nameError').style.display    = 'block'; isValid = false; }
    if (!emailUser.match(/^[a-zA-Z0-9._-]+$/))             { document.getElementById('emailError').style.display   = 'block'; isValid = false; }
    if (!phone.match(/^5[0-9]{8}$/))                       { document.getElementById('phoneError').style.display   = 'block'; isValid = false; }
    if (message.length < 10)                               { document.getElementById('messageError').style.display = 'block'; isValid = false; }

    if (!isValid) {
        event.preventDefault();
    } else {
        const domain = document.getElementById('email_domain').value;
        document.getElementById('final_email').value = emailUser + '@' + domain;
        const btn = document.getElementById('submitBtn');
        btn.classList.add('loading');
        btn.innerHTML = 'Sending... ⏳';
    }
});
</script>
</body>
</html>
