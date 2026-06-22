<?php
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Custom Abaya</title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Alyamama:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
/* ===== CUSTOM PAGE VARS ===== */
:root {
    --fl-brand:      #6F8497;
    --fl-brand-dark: #6F8497;
    --fl-ink:        #1f2937;
    --fl-muted:      #6b7280;
    --fl-bg:         #fbfbfd;
    --fl-card:       #ffffffcc;
    --fl-line:       rgba(31,41,55,0.12);
    --fl-shadow:     0 18px 40px rgba(0,0,0,0.08);
    --fl-radius:     18px;
}

/* ===== LAYOUT ===== */
.fl-container { max-width: 1100px; margin: 22px auto 60px; padding: 0 14px; }
.fl-card { background: var(--fl-card); border: 1px solid var(--fl-line); border-radius: var(--fl-radius); padding: 30px; box-shadow: var(--fl-shadow); margin-bottom: 20px; }
.fl-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }

/* ===== MODELS GRID ===== */
.models-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 30px; }
.model-radio { display: none; }
.model-card { border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden; cursor: pointer; transition: 0.25s; text-align: center; padding-bottom: 10px; }
.model-card img { width: 100%; height: 380px; object-fit: cover; display: block; }
.model-card div { margin-top: 8px; font-size: 0.9rem; color: #374151; }
.model-radio:checked + .model-card { border-color: var(--fl-brand); box-shadow: 0 0 0 4px rgba(154,175,177,0.3); }
.model-card:hover { border-color: var(--fl-brand); transform: translateY(-3px); }

/* ===== FIELD ===== */
.field { display: flex; flex-direction: column; gap: 8px; }
.field label { font-weight: 700; font-size: 14px; color: #374151; }

/* ===== COLOR PICKER ===== */
.color-picker-container { display: flex; align-items: center; gap: 14px; }
.color-picker-container input[type="color"] { width: 50px; height: 50px; border: none; border-radius: 50%; cursor: pointer; padding: 0; background: none; }

/* ===== SIZE SELECTOR ===== */
.size-selector { display: flex; gap: 10px; flex-wrap: wrap; }
.size-radio { display: none; }
.size-label { padding: 10px 20px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; font-weight: 700; transition: 0.2s; }
.size-radio:checked + .size-label { background: var(--fl-brand); color: white; border-color: var(--fl-brand); }
.size-label:hover { border-color: var(--fl-brand); }

/* ===== SUBMIT BUTTON ===== */
.btn-submit { background: linear-gradient(135deg, var(--fl-brand-dark), var(--fl-brand)); color: #fff; border: none; padding: 14px 30px; border-radius: 14px; font-weight: 900; font-size: 1rem; cursor: pointer; width: 100%; margin-top: 20px; transition: 0.3s; box-shadow: 0 4px 16px rgba(110,134,137,0.35); }
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(110,134,137,0.45); }

/* ===== TOAST NOTIFICATION ===== */
.custom-toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%) translateY(-80px);
    background: #c0392b;
    color: white;
    padding: 14px 22px;
    border-radius: 10px;
    font-size: 0.92rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 9999;
    box-shadow: 0 8px 30px rgba(192,57,43,0.4);
    min-width: 280px;
    max-width: 500px;
    opacity: 0;
    transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
    pointer-events: none;
    white-space: nowrap;
}

.custom-toast.show {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
    pointer-events: all;
}

.custom-toast i { font-size: 1rem; flex-shrink: 0; }
.color-picker-container input[type="color"] {
    border-radius: 50%;
    overflow: hidden;
    -webkit-appearance: none;
}

.color-picker-container input[type="color"]::-webkit-color-swatch-wrapper {
    border-radius: 50%;
    padding: 0;
}

.color-picker-container input[type="color"]::-webkit-color-swatch {
    border-radius: 50%;
    border: none;
}
</style>
</head>

<body>

<!-- ===== TOAST ===== -->
<div class="custom-toast" id="customToast">
    <i class="fa-solid fa-circle-exclamation"></i>
    <span id="toastMsg"></span>
</div>

<input type="checkbox" id="menu-toggle">

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
        <hr><a href="order.php">My Order</a><hr>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>
</div>

<label for="menu-toggle" class="overlay"></label>

<!-- ===== MAIN ===== -->
<main class="fl-container">

    <section class="fl-card">
        <h1 style="font-family:'Alyamama'; color:#23384d; margin-bottom:10px;">
            Design Your Custom Abaya ✨
        </h1>
        <p style="color:#666;">
            Choose your preferred style, color, and size to create a piece that fits your episode.
        </p>
    </section>

    <form class="fl-card" id="customForm" action="submit_order.php" method="POST" enctype="multipart/form-data">

        <h2 style="font-size:1.2rem; margin-bottom:25px; color:#6F8497;">
            1. Choose a Style (Required)
        </h2>

        <div class="models-grid">
            <input type="radio" name="style" id="m1" class="model-radio" value="Bisht Style">
            <label class="model-card" for="m1">
                <img src="7.jpg.jpeg" alt="Bisht Style">
                <div style="font-weight:900;">Bisht Style</div>
            </label>

            <input type="radio" name="style" id="m2" class="model-radio" value="Cloché Style">
            <label class="model-card" for="m2">
                <img src="8.jpg.jpeg" alt="Cloché Style">
                <div style="font-weight:900;">Cloché Style</div>
            </label>

            <input type="radio" name="style" id="m3" class="model-radio" value="Quarter Cloché">
            <label class="model-card" for="m3">
                <img src="9.jpg.jpeg" alt="Quarter Cloché">
                <div style="font-weight:900;">Quarter Cloché</div>
            </label>

            <input type="radio" name="style" id="m4" class="model-radio" value="Classic Style">
            <label class="model-card" for="m4">
                <img src="6.jpg.jpeg" alt="Classic Style">
                <div style="font-weight:900;">Classic Style</div>
            </label>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:30px; margin-bottom:24px;">

            <div class="field">
                <label>Pick Your Abaya Color</label>
                <div class="color-picker-container">
                    <input type="color" name="color" value="#000000">
                    <span style="color:#888; font-size:0.9rem;">Click the circle to pick any color</span>
                </div>
            </div>

            <div class="field">
                <label>Select Size (Required)</label>
                <div class="size-selector">
                    <input type="radio" name="size" id="s52" class="size-radio" value="52">
                    <label class="size-label" for="s52">52</label>

                    <input type="radio" name="size" id="s54" class="size-radio" value="54">
                    <label class="size-label" for="s54">54</label>

                    <input type="radio" name="size" id="s56" class="size-radio" value="56">
                    <label class="size-label" for="s56">56</label>

                    <input type="radio" name="size" id="s58" class="size-radio" value="58">
                    <label class="size-label" for="s58">58</label>
                </div>
            </div>

        </div>

        <div class="field">
            <label>Upload Your Sketch (Optional)</label>
            <input type="file" name="image" accept="image/*"
                   style="width:100%; padding:15px; border:1px dashed #9AAFB1; border-radius:12px; background:#fcfcfc;">
        </div>

        <button type="submit" class="btn-submit">Submit Custom Request</button>

    </form>

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

<a href="#top" class="back-to-top">↑</a>

<script>
let toastTimer = null;

function showToast(msg) {
    const toast = document.getElementById('customToast');
    const span  = document.getElementById('toastMsg');
    span.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
}

document.getElementById('customForm').addEventListener('submit', function(e) {

    const styleSelected = document.querySelector('input[name="style"]:checked');
    const sizeSelected  = document.querySelector('input[name="size"]:checked');

    if (!styleSelected) {
        e.preventDefault();
        showToast('Please choose a style first');
        return;
    }

    if (!sizeSelected) {
        e.preventDefault();
        showToast('Please select a size');
        return;
    }

});
</script>

</body>
</html>