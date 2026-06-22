<?php
session_start();
require 'db.php';
$sql    = "SELECT * FROM fabrics";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fabric Guide | Bishtah</title>

<link rel="stylesheet" href="style1.css">
<link href="https://fonts.googleapis.com/css2?family=Alyamama:wght@300;400;500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
<div class="fabric-main">

    <div class="fabric-section-header">
        <h1>Fabric Guide</h1>
        <p>Discover our premium materials and find what suits your lifestyle best.</p>
    </div>

    <div class="fabric-filter-container">
        <button class="fabric-filter-btn active" data-filter="all">All Seasons</button>
        <button class="fabric-filter-btn" data-filter="badge-summer">Summer</button>
        <button class="fabric-filter-btn" data-filter="badge-winter">Winter</button>
    </div>

    <div class="fabric-grid">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $season_class = $row['season_class'];

                if ($season_class == 'badge-summer') {
                    $footer_icons = '<span style="font-size:22px;" title="Summer">☀️</span>';
                } elseif ($season_class == 'badge-winter') {
                    $footer_icons = '<span style="font-size:22px;" title="Winter">❄️</span>';
                } else {
                    $footer_icons = '
                        <span style="font-size:18px;" title="Summer">☀️</span>
                        <span style="font-size:18px;" title="Spring">🌸</span>
                        <span style="font-size:18px;" title="Winter">❄️</span>';
                }
        ?>

        <div class="fabric-card <?php echo $season_class; ?>">

            <h3 class="fabric-card-title">
                <?php echo $row['name']; ?>
                <span class="fabric-season-badge"><?php echo $row['season_badge']; ?></span>
            </h3>

            <p class="fabric-desc"><?php echo $row['description']; ?></p>

            <ul class="fabric-usage-list">
                <li><span>✔️</span> Best for: <?php echo $row['best_for']; ?></li>
                <li><span>✔️</span> Durability: <?php echo $row['durability']; ?></li>
            </ul>

            <div class="fabric-icons-footer">
                <span class="fabric-icons-label">Perfect for:</span>
                <?php echo $footer_icons; ?>
            </div>

        </div>

        <?php
            }
        } else {
            echo "<p style='text-align:center; width:100%;'>No fabrics found in the database.</p>";
        }
        ?>
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
document.addEventListener("DOMContentLoaded", function() {
    const filterBtns  = document.querySelectorAll('.fabric-filter-btn');
    const fabricCards = document.querySelectorAll('.fabric-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filterValue = btn.getAttribute('data-filter');

            fabricCards.forEach(card => {
                if (filterValue === 'all') {
                    card.classList.remove('hide');
                    card.style.display = 'flex';
                } else {
                    if (card.classList.contains(filterValue) || card.classList.contains('badge-all')) {
                        card.classList.remove('hide');
                        card.style.display = 'flex';
                    } else {
                        card.classList.add('hide');
                    }
                }
            });
        });
    });
});
</script>

</body>
</html>
