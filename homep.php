<?php
session_start();
include "db.php";

$sql = "SELECT * FROM abayas WHERE id IN (7,6,5)";
$result = mysqli_query($conn, $sql);

// ===== Past Orders من cookies =====
$past_res = null;

if(isset($_COOKIE['last_user_id'])) {
    $past_uid = intval($_COOKIE['last_user_id']);

    $past_res = mysqli_query($conn, "
    SELECT DISTINCT
        a.id,
        a.name,
        a.image,
        o.created_at
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN abayas a ON a.id = oi.product_id
    WHERE o.user_id = '$past_uid'
    ORDER BY o.created_at DESC
    LIMIT 8
");
}
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

<style>
.past-purchases-section { padding: 60px 5%; background-color: #fff; text-align: center; }
.past-grid { display: flex; justify-content: center; gap: 25px; flex-wrap: wrap; margin-top: 30px; }
.past-card { width: 200px; transition: transform 0.3s ease; }
.past-card img { width: 100%; height: 280px; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.past-card h4 { margin-top: 15px; font-family: 'Poppins', sans-serif; color: #333; }

    :root {
        --bot-blue: #7c90a5;
        --bot-dark: #1a1a1a;
        --bot-bg: #fbfbfb;
    }

    .bishtah-chat-icon {
        position: fixed; bottom: 25px; right: 30px;
        background-color: var(--bot-blue); color: white;
        width: 65px; height: 65px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; cursor: pointer; 
        box-shadow: 0 10px 25px rgba(124, 144, 165, 0.3);
        z-index: 999; transition: all 0.3s ease;
    }
    .bishtah-chat-icon:hover { transform: scale(1.1) rotate(5deg); }

    .bishtah-chat-box {
        position: fixed; bottom: 50%;
    transform: translateY(50%); right: 30px;
        width: 350px; height: 500px; background: white;
        border-radius: 20px; box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        display: none; flex-direction: column; z-index: 999; overflow: hidden;
        font-family: 'Poppins', 'Tajawal', sans-serif;
        border: 1px solid rgba(124, 144, 165, 0.2);
    }

    .chat-header { 
        background: var(--bot-blue); color: white; padding: 20px; 
        text-align: center; position: relative;
    }
    .chat-header h3 { 
        font-family: 'Playfair Display', serif; margin: 0; 
        font-size: 1.5rem; letter-spacing: 2px;
    }
    .chat-header p { font-size: 0.7rem; margin: 5px 0 0; opacity: 0.9; letter-spacing: 1px; }

    #chat-body { 
        flex: 1; padding: 20px; overflow-y: auto; 
        display: flex; flex-direction: column; background: var(--bot-bg);
        gap: 12px;
    }

    .msg { 
        padding: 12px 16px; border-radius: 15px; font-size: 14px; 
        max-width: 85%; line-height: 1.5;
    }
    .bot-msg { 
        background: white; color: var(--bot-dark); align-self: flex-start; 
        border-bottom-left-radius: 2px; box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    }
    .user-msg { 
        background: var(--bot-blue); color: white; align-self: flex-end; 
        border-bottom-right-radius: 2px; text-align: right;
    }

    .quick-replies { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px; }
    .quick-replies button { 
        background: transparent; border: 1.5px solid var(--bot-blue); 
        color: var(--bot-blue); padding: 6px 12px; border-radius: 20px; 
        font-size: 12px; cursor: pointer; transition: 0.3s; font-weight: 600;
    }
    .quick-replies button:hover { background: var(--bot-blue); color: white; }

    .chat-footer { 
        display: flex; padding: 15px; background: white; 
        border-top: 1px solid #eee; align-items: center;
    }
    .chat-footer input { 
        flex: 1; border: 1px solid #ddd; padding: 12px 15px; 
        border-radius: 25px; outline: none; font-size: 14px; transition: 0.3s;
    }
    .chat-footer input:focus { border-color: var(--bot-blue); }
    .chat-footer button { 
        background: none; border: none; color: var(--bot-blue); 
        margin-left: 10px; cursor: pointer; font-size: 20px; 
    }
</style>

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
        <a href="customp.php">Custom Order</a><hr>
        <a href="order.php">My Order</a><hr>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="logout-btn">Log Out</a>
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

<!-- ===== PAST PURCHASES ===== -->
<?php if($past_res && mysqli_num_rows($past_res) > 0): ?>
<section class="past-purchases-section">
    <div class="home-section-header">
        <h2 style="font-family:'Playfair Display';">Welcome Back!</h2>
        <p>Your Past Purchases</p>
    </div>
    <div class="past-grid">
        <?php while($prow = mysqli_fetch_assoc($past_res)): ?>
        <div class="past-card">
            <img src="<?php echo htmlspecialchars($prow['image']); ?>" alt="Abaya">
            <h4><?php echo htmlspecialchars($prow['name']); ?></h4>
            <a href="abdet.php?id=<?php echo $prow['id']; ?>" style="font-size:0.8rem; color:#888;">View Again</a>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<hr style="width:80%; margin:0 auto; border:0.5px solid #eee;">
<?php endif; ?>

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

                <img src="<?php echo $row['image']; ?>"
                     title="<?php echo htmlspecialchars($row['img_title'] ?? ''); ?>"
                     alt="<?php echo htmlspecialchars($row['img_alt'] ?? 'Abaya'); ?>">
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

<!-- ===== CHAT BOT ===== -->
<div class="bishtah-chat-icon" onclick="toggleChat()">
    <i class="fa-solid fa-comments"></i>
</div>

<div class="bishtah-chat-box" id="bishtahChat">
    <div class="chat-header">مساعد Bishtah ✨</div>
    <div id="chat-body">
        <div class="msg bot-msg">هلا بك في متجر Bishtah! كيف أقدر أخدمك اليوم؟</div>
        <div class="quick-replies">
            <button onclick="sendQuick('مقاس')">📏 المقاسات</button>
            <button onclick="sendQuick('قماش')">🧵 الأقمشة</button>
            <button onclick="sendQuick('توصيل')">🚚 التوصيل</button>
        </div>
    </div>
    <div class="chat-footer">
        <input type="text" id="user-input" placeholder="اكتبي سؤالك هنا...">
        <button onclick="handleSend()" style="border:none; background:none; cursor:pointer;">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
/* ===== CHAT BOT ===== */
function toggleChat() {
    const box = document.getElementById('bishtahChat');
    box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'flex' : 'none';
}

function sendQuick(text) {
    document.getElementById('user-input').value = text;
    handleSend();
}

function handleSend() {
    const input   = document.getElementById('user-input');
    const body    = document.getElementById('chat-body');
    const message = input.value.trim();
    if (message === "") return;
    body.innerHTML += `<div class="msg user-msg">${message}</div>`;
    input.value = "";
    fetch('chat_logic.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'text=' + encodeURIComponent(message)
    })
    .then(res => res.text())
    .then(data => {
        setTimeout(() => {
            body.innerHTML += `<div class="msg bot-msg">${data}</div>`;
            body.scrollTop = body.scrollHeight;
        }, 400);
    });
}

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
        if(backBtn) backBtn.classList.toggle('show', window.scrollY > 400);
    });

});
</script>

</body>
</html>