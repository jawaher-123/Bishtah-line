<?php
session_start();
include "db.php";

if (!isset($_GET['id'])) {
    die("Abaya not found!");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM abayas WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Abaya not found!");
}

$abaya = mysqli_fetch_assoc($result);

$imgs = mysqli_query($conn, "SELECT image FROM abaya_images WHERE abaya_id=$id");
$images = [];

while($img = mysqli_fetch_assoc($imgs)){
    $images[] = $img['image'];
}

if(count($images) == 0){
    $images[] = $abaya['image'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $abaya['name']; ?></title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700;600;800&family=Tajawal:wght@400;500;700;600;800&display=swap" rel="stylesheet">
<style>
/* ===== Help Button ===== */
.help-fab {
    position: fixed;
    bottom: 28px;
    right: 22px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #273c54;
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    z-index: 999;
    transition: background 0.2s, transform 0.15s;
}
.help-fab:hover { background: #444441; transform: scale(1.05); }

/* ===== Help Overlay ===== */
.help-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
    padding: 20px;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s;
}
.help-overlay.visible { opacity: 1; pointer-events: all; }

/* ===== Help Panel ===== */
.help-panel {
    background: linear-gradient(160deg, #f2f4f6 0%, #ccd7df 50%, #f8f9f9 100%);
    border: 1px solid #b0c8d8;
    border-radius: 14px;
    width: 310px;
    max-height: 85vh;
    overflow-y: auto;
    transform: translateY(20px);
    transition: transform 0.3s cubic-bezier(.4,0,.2,1);
    font-family: 'Cormorant Garamond', serif;
}

.help-header {
    display: flex;
    align-items: center;
    justify-content:center;
    padding: 14px 16px 12px;
    border-bottom: 1px solid #b0c8d8;
    background: linear-gradient(135deg, #557594 0%, #a0b8c8 100%);
    border-radius: 14px 14px 0 0;
}
.help-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 20px;
    font-weight: 900;
    color: #ffffff;
}
.help-close {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 22px;
    color: #ffffff;
    line-height: 1;
    position: absolute;
    top: 14px;
    right: 14px;
}
.help-close:hover { color: #2C2C2A; }

.help-section-label {
    padding: 12px 16px 4px;
    font-size: 11px;
    font-weight: 700;
    color: #999;
    letter-spacing: 0.07em;
    text-transform: uppercase;
        font-family:  serif;

}
.help-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 16px;
    border-bottom: 1px solid #f5f5f5;
}
.help-item-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background:none;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 17px;
    color: #031a34;
}
.help-item-label {
    font-size: 16px;
    font-weight: 600;
    color: #25282e;
    margin: 0 0 2px;
}
.help-item-desc {
    font-size: 13px;
    color: #3b4351;
    margin: 0;
    line-height: 1.5;
        font-weight: 520;

}
.help-badge {
    display: inline-block;
    background: #e8f5e9;
    color: #2e7d32;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 99px;
    margin-left: 5px;
    vertical-align: middle;
}
.help-footer {
    padding: 12px 16px 14px;
    font-size: 12px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 7px;
}

/* العناوين - Cormorant Garamond */
.help-header-title,
.help-item-label,
.help-section-label {
    font-family: 'Cormorant Garamond', serif;
}

/* النصوص الوصفية - Tajawal */
.help-item-desc,
.help-footer {
    font-family: 'Tajawal', sans-serif;
}
</style>

</head>

<body class="product-page">

<div id="toast" class="details-toast"></div>

<!-- Zoom Overlay -->
<div class="details-zoom-overlay" id="zoomOverlay">
    <div class="details-zoom-box" id="zoomBox">
        <button class="details-zoom-close" id="zoomClose">&times;</button>
        <button class="details-zoom-arrow prev" id="zoom-prev">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <img id="zoomImg" src="" alt="">
        <div class="details-zoom-label" id="zoom-label"></div>
        <button class="details-zoom-arrow next" id="zoom-next">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
</div>

<input type="checkbox" id="menu-toggle">
<div id="top"></div>

<!-- ===== HEADER ===== -->
<header class="store-header">
    <div class="header-left">
        <a href="cartp.php" class="icon-btn">
            <i class="fa-solid fa-cart-shopping"></i>
        </a>
        <a href="wishListp.php" class="icon-btn">
            <i class="fa-solid fa-heart"></i>
        </a>
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
    <label for="menu-toggle" class="close-btn">
        <i class="fa-solid fa-xmark"></i>
    </label>
    <div class="profile-section">
        <img src="userr.png" class="profile-img">
        <div class="username"><?php echo $_SESSION['first_name'] ?? 'Account'; ?></div>
    </div>
    <div class="sidebar-links">
        <hr>
        <a href="homep.php">Home</a>
        <hr>
        <a href="we.php">All Abayas</a>
        <hr>
        <a href="customp.php">Custom Order</a>
        <hr>
        <a href="order.php">My Order</a>
        <hr>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>
</div>

<label for="menu-toggle" class="overlay"></label>

<div class="container">

    <!-- CAROUSEL -->
    <div class="details-carousel-viewport">
        <div class="details-carousel-track" id="carouselTrack"></div>
    </div>

    <div class="product-info">

        <div class="details-name-row">
            <div class="details-product-name"><?php echo $abaya['name']; ?></div>
            <button class="details-heart-btn" id="wishBtn" onclick="addToWishlist(<?php echo $abaya['id']; ?>)">
                <i class="fa-regular fa-heart" id="heartIcon"></i>
            </button>
        </div>

        <div class="details-price"><?php echo $abaya['price']; ?> SAR</div>
        <div class="tax">Price includes tax</div>

        <div class="rating">
            ⭐⭐⭐⭐⭐
            <span class="rating-count">(125)</span>
        </div>

        <div class="description"><?php echo $abaya['description']; ?></div>

        <form id="cartForm">
            <input type="hidden" name="abaya_id" value="<?php echo $abaya['id']; ?>">

            <div class="uniform-box">
                <select name="size" id="sizeSelect" required>
                    <option value="" disabled selected>Select Size</option>
                    <option value="52">52</option>
                    <option value="54">54</option>
                    <option value="56">56</option>
                    <option value="58">58</option>
                    <option value="60">60</option>
                </select>
            </div>

            <div class="uniform-box quantity-inside">
                <span class="qty-label">Quantity:</span>
                <input type="number" name="quantity" id="qtyInput" value="1" min="1">
            </div>

            <a href="size-guide.html" class="size-link">Size Guide</a>
            <a href="fabricp.php" class="size-link">Fabric Guide</a>

            <div class="policy">
                <p>
                    Return & Exchange Policy:
                    Products can be returned or exchanged within 7 days of receipt,
                    provided they are in their original condition.
                </p>
            </div>

            <div class="cart-container">
                <button type="button" class="cart-btn" onclick="addToCart()"></button>
            </div>
        </form>

    </div>
</div>

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

<!-- ===== HELP BUTTON ===== -->
<button class="help-fab" id="helpFab" aria-label="Help">
    <i class="fa-solid fa-circle-question"></i>
</button>

<!-- ===== HELP OVERLAY ===== -->
<div class="help-overlay" id="helpOverlay">
    <div class="help-panel" role="dialog" aria-modal="true" aria-label="Help Center">

        <div class="help-header">
            <span class="help-header-title">
                <i class="fa-regular fa-star"></i> Help Center
            </span>
            <button class="help-close" id="helpClose">&times;</button>
        </div>

        <p class="help-section-label">Ordering</p>

        <div class="help-item">
            <div class="help-item-icon"><i class="fa-solid fa-ruler"></i></div>
            <div>
                <p class="help-item-label">How to choose your size</p>
                <p class="help-item-desc">Use our size guide to find your perfect fit based on your measurements.</p>
            </div>
        </div>

        <div class="help-item">
            <div class="help-item-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <div>
                <p class="help-item-label">How to place an order</p>
                <p class="help-item-desc">Select a size, set the quantity, then tap Add to Cart to proceed.</p>
            </div>
        </div>

        <div class="help-item">
            <div class="help-item-icon"><i class="fa-regular fa-heart"></i></div>
            <div>
                <p class="help-item-label">Save to wishlist</p>
                <p class="help-item-desc">Tap the heart icon to save items and review them later.</p>
            </div>
        </div>

        <p class="help-section-label">Policies</p>

        <div class="help-item">
            <div class="help-item-icon"><i class="fa-solid fa-rotate-left"></i></div>
            <div>
                <p class="help-item-label">Return & exchange <span class="help-badge">7 days</span></p>
                <p class="help-item-desc">Items in original condition can be returned within 7 days of delivery.</p>
            </div>
        </div>

        <div class="help-item">
            <div class="help-item-icon"><i class="fa-solid fa-shirt"></i></div>
            <div>
                <p class="help-item-label">Fabric guide</p>
                <p class="help-item-desc">Learn about fabric types and care instructions for each abaya.</p>
            </div>
        </div>

        <div class="help-footer">
            <i class="fa-brands fa-instagram"></i>
            Still need help? Contact us on Instagram @bishtah_line
        </div>

    </div>
</div>

<script>

/* ===== Toast ===== */
function showToast(message, type="success"){
    const toast = document.getElementById("toast");
    toast.className = "details-toast " + type;
    toast.innerText = message;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2500);
}

/* ===== Form Validation ===== */
function validateCartForm(){
    const sizeEl = document.getElementById("sizeSelect");
    const qtyEl  = document.getElementById("qtyInput");
    const size   = sizeEl.value;
    const qty    = parseInt(qtyEl.value);
    let valid    = true;

    sizeEl.classList.remove("details-invalid");
    qtyEl.classList.remove("details-invalid");

    if(!size){
        sizeEl.classList.add("details-invalid");
        showToast("Please select a size ⚠️", "error");
        valid = false;
    }

    if(isNaN(qty) || qty < 1){
        qtyEl.classList.add("details-invalid");
        if(valid) showToast("Quantity must be at least 1 ⚠️", "error");
        valid = false;
    }

    setTimeout(() => {
        sizeEl.classList.remove("details-invalid");
        qtyEl.classList.remove("details-invalid");
    }, 1500);

    return valid;
}

/* ===== Add To Cart ===== */
function addToCart(){
    if(!validateCartForm()) return;
    const formData = new FormData(document.getElementById("cartForm"));
    fetch("addCart.php", { method: "POST", body: formData })
    .then(r => r.text())
    .then(data => {
        if(data.trim() === "success")      showToast("Added to cart ✅", "success");
        else if(data.trim() === "missing") showToast("Please select a size ⚠️", "error");
        else                               showToast("Error: " + data, "error");
    })
    .catch(() => showToast("Server Error ❌", "error"));
}

/* ===== Add To Wishlist ===== */
function addToWishlist(productId){
    const btn  = document.getElementById("wishBtn");
    const icon = document.getElementById("heartIcon");

    fetch("addwish.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "product_id=" + productId
    })
    .then(r => r.text())
    .then(data => {
        const res = data.trim();
        if(res === "added"){
            btn.classList.add("active");
            icon.classList.remove("fa-regular");
            icon.classList.add("fa-solid");
            icon.style.color = "#e74c3c";
            showToast("Added to Wishlist ❤️", "success");
        } else if(res === "removed"){
            btn.classList.remove("active");
            icon.classList.remove("fa-solid");
            icon.classList.add("fa-regular");
            icon.style.color = "#333";
            showToast("Removed from Wishlist", "success");
        } else {
            showToast("Error: " + data, "error");
        }
    })
    .catch(() => showToast("Server Error ❌", "error"));
}

/* ===== CAROUSEL + ZOOM ===== */
document.addEventListener("DOMContentLoaded", function(){

    let images = <?php echo json_encode($images); ?>;
    while(images.length < 3) images.push(images[0]);

    const total     = images.length;
    const allImages = [...images, ...images, ...images];
    const allCount  = allImages.length;
    const track     = document.getElementById("carouselTrack");

    track.style.width = (allCount * 33.333) + "%";

    allImages.forEach((src) => {
        const slide = document.createElement("div");
        slide.className   = "details-slide-box";
        slide.style.width = (100 / allCount) + "%";
        slide.innerHTML   = `<img src="${src}" alt=""><div class="details-slide-overlay"></div>`;

        slide.querySelector("img").addEventListener("click", function(){
            openZoom(src);
        });

        track.appendChild(slide);
    });

    let current     = total;
    let isAnimating = false;

    function getOffset(index){
        return -((index - 1) * (100 / allCount));
    }

    function updateCenter(){
        track.querySelectorAll(".details-slide-box").forEach((s, i) => {
            s.classList.toggle("center", i === current);
        });
    }

    function moveTo(index, animate){
        track.style.transition = animate
            ? "transform 0.75s cubic-bezier(0.4,0,0.2,1)"
            : "none";
        track.style.transform = "translateX(" + getOffset(index) + "%)";
        updateCenter();
    }

    moveTo(current, false);

    track.addEventListener("transitionend", function(){
        isAnimating = false;
        if(current >= total * 2){
            current -= total;
            moveTo(current, false);
        } else if(current < total){
            current += total;
            moveTo(current, false);
        }
    });

    function next(){
        if(isAnimating) return;
        isAnimating = true;
        current++;
        moveTo(current, true);
    }

    setInterval(next, 3000);

    /* ===== Zoom Logic ===== */
    let zoomIndex = 0;

    function openZoom(src){
        zoomIndex = images.indexOf(src);
        if(zoomIndex === -1) zoomIndex = 0;
        updateZoom();
        document.getElementById("zoomOverlay").style.display = "flex";
        updateZoomArrows();
    }

    function updateZoom(){
        document.getElementById("zoomImg").src = images[zoomIndex];
        document.getElementById("zoom-label").innerText = "";
    }

    function updateZoomArrows(){
        const hasPrev = document.getElementById("zoom-prev");
        const hasNext = document.getElementById("zoom-next");
        hasPrev.style.display = images.length > 1 ? "flex" : "none";
        hasNext.style.display = images.length > 1 ? "flex" : "none";
    }

    document.getElementById("zoom-prev").addEventListener("click", function(e){
        e.stopPropagation();
        zoomIndex = (zoomIndex - 1 + images.length) % images.length;
        updateZoom();
    });

    document.getElementById("zoom-next").addEventListener("click", function(e){
        e.stopPropagation();
        zoomIndex = (zoomIndex + 1) % images.length;
        updateZoom();
    });

    document.getElementById("zoomClose").addEventListener("click", function(e){
        e.stopPropagation();
        document.getElementById("zoomOverlay").style.display = "none";
    });

    document.getElementById("zoomOverlay").addEventListener("click", function(e){
        if(e.target === this){
            this.style.display = "none";
        }
    });

    document.addEventListener("keydown", function(e){
        if(document.getElementById("zoomOverlay").style.display !== "flex") return;
        if(e.key === "ArrowLeft")  { zoomIndex = (zoomIndex - 1 + images.length) % images.length; updateZoom(); }
        if(e.key === "ArrowRight") { zoomIndex = (zoomIndex + 1) % images.length; updateZoom(); }
        if(e.key === "Escape") {
            document.getElementById("zoomOverlay").style.display = "none";
            document.getElementById("helpOverlay").classList.remove("visible");
        }
    });

});

/* ===== Help Window ===== */
const helpFab     = document.getElementById("helpFab");
const helpOverlay = document.getElementById("helpOverlay");
const helpClose   = document.getElementById("helpClose");

helpFab.addEventListener("click", () => helpOverlay.classList.add("visible"));
helpClose.addEventListener("click", () => helpOverlay.classList.remove("visible"));
helpOverlay.addEventListener("click", function(e){
    if(e.target === this) this.classList.remove("visible");
});
document.addEventListener("keydown", function(e){
    if(e.key === "Escape") helpOverlay.classList.remove("visible");
});

</script>

</body>
</html>