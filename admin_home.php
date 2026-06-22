<?php
session_start();
include "db.php";
$sql = "SELECT * FROM abayas WHERE id IN (7,6,5)";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bishtah - Episodes of Your Life</title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
<div class="container">

<input type="checkbox" id="menu-toggle" hidden>
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
<div class="username"><?php echo $_SESSION['first_name'] ?? 'Account'; ?></div>
    </div>
    <div class="sidebar-links">
        <hr><a href="homep.php">Home</a><hr>
        <a href="we.php">All Abayas</a><hr>
        <a href="admin_orders.php">Manage Orders</a><hr>
        <a href="Managep.php">Manage Products</a><hr>
    </div>
    <div class="logout-section">
<a href="logout.php" class="logout-btn">Log Out</a></div>
    </div>
</div>

<label for="menu-toggle" class="overlay"></label>

<!-- ===== SLIDER ===== -->
<div class="home-slider-container">
    <div class="home-slider-images">
        <div class="home-slide-wrap"><img src="a7.jpeg"></div>
        <div class="home-slide-wrap"><img src="a6.jpeg"></div>
    </div>
    <div class="home-slider-overlay">
        <h2>Welcome</h2>
        <p>To Episodes of Wonder</p>
        <a href="we.php" class="home-shop-button">Explore Now</a>
    </div>
</div>

<!-- ===== BEST SELLERS ===== -->
<section id="shop" class="home-best-sellers">
    <div class="home-section-header">
        <h2>Best Sellers</h2>
        <p>Our most loved pieces.</p>
    </div>
    <div class="home-product-grid">
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <div class="home-product-card">
            <div class="home-product-image-wrap">

                <div class="home-wishlist-btn" onclick="addToWishlist(<?php echo $row['id']; ?>, this)">
                    <svg viewBox="0 0 24 24" style="fill:none; stroke:currentColor; stroke-width:2;">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>

                <?php if(!empty($row['tag'])) { ?>
                    <span class="home-tag"><?php echo $row['tag']; ?></span>
                <?php } ?>

                <img src="<?php echo $row['image']; ?>">

                <div class="home-product-info-overlay">
                    <h3><?php echo $row['name']; ?></h3>
                    <a class="home-add-btn" href="abdet.php?id=<?php echo $row['id']; ?>">View Details</a>
                </div>

            </div>
        </div>
        <?php } ?>
    </div>
</section>

<!-- ===== ABOUT ===== -->
<section class="home-about-section">
    <div class="home-about-text">
        <h1>Our Story</h1>
        <p>Bishtah redefines daily elegance with high-quality abayas designed for the modern woman.</p>
    </div>
    <div class="home-about-visual">
        <img src="a1.jpeg" id="aboutImg">
    </div>
</section>

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

<a href="#top" class="product-back-to-top">↑</a>

</div>

<script>
/* ===== WISHLIST ===== */
function addToWishlist(productId, element){
    const svg = element.querySelector('svg');
    fetch("addwish.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "product_id=" + productId
    })
    .then(res => res.text())
    .then(data => {
        svg.style.fill   = '#e74c3c';
        svg.style.stroke = '#e74c3c';
        element.style.transform = 'scale(1.2)';
        setTimeout(() => element.style.transform = 'scale(1)', 200);
    })
    .catch(err => console.error("Error:", err));
}

document.addEventListener('DOMContentLoaded', () => {

    /* ===== PRODUCT CARDS ANIMATION ===== */
    const productCards = document.querySelectorAll('.home-product-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if(entry.isIntersecting){
                setTimeout(() => {
                    entry.target.style.opacity   = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 150);
            }
        });
    }, { threshold: 0.1 });
    productCards.forEach(card => observer.observe(card));

    /* ===== PARALLAX ===== */
    const aboutImg = document.getElementById('aboutImg');
    window.addEventListener('scroll', () => {
        const scrollValue = window.scrollY;
        const offset = aboutImg.offsetTop;
        if(scrollValue > offset - window.innerHeight){
            const yPos = (scrollValue - offset) * 0.1;
            aboutImg.style.transform = `translateY(${yPos}px) scale(1.1)`;
        }
    });

    /* ===== BACK TO TOP ===== */
    const backBtn = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        backBtn.classList.toggle('show', window.scrollY > 400);
    });

});
</script>

</body>
</html>
