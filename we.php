<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bishtah - Episodes of Your Life</title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Alyamama:wght@300;400;500;600;700;800;900&family=Amiri:wght@400;700&family=Aref+Ruqaa:wght@400;700&display=swap" rel="stylesheet">

</head>

<body>

<?php
session_start();
include "db.php";
$sql = "SELECT * FROM Abayas";
$result = mysqli_query($conn, $sql);
?>

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
<hr><a href="homep.php">Home</a><hr>
<a href="we.php">All Abayas</a><hr>
<a href="customp.php">Custom Order</a><hr>
<a href="order.php">My Order</a><hr>
</div>

<div class="logout-section">
<a href="logout.php" class="logout-btn">Log Out</a></div>

</div>

<label for="menu-toggle" class="overlay"></label>

<!-- ===== PAGE TITLE ===== -->
<section class="product-page-title">
    <h2>All Abayas</h2>
</section>

<!-- ===== SEARCH + FILTER ===== -->
<div class="product-search-wrapper">
    <div class="product-search-section">

        <div class="product-search-container">
            <input type="text" id="searchInput" placeholder="Search Abayas...">
            <div class="product-suggestions-box" id="suggestionsBox"></div>
        </div>

        <select id="colorFilter">
            <option value="none">Filter by Color</option>
            <option value="black">Black</option>
            <option value="navy">Navy</option>
            <option value="brown">Brown</option>
            <option value="light">Light Tones</option>
            <option value="dark">Dark Tones</option>
        </select>

        <select id="priceFilter">
            <option value="none">Filter by Price</option>
            <option value="under300">Under 300</option>
            <option value="between300400">300 - 400</option>
            <option value="over400">Over 400</option>
        </select>

    </div>
</div>

<!-- ===== PRODUCTS ===== -->
<div class="product-grid-container" id="productsContainer">

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<div class="product-card"
     data-name="<?php echo strtolower($row['name']); ?>"
     data-price="<?php echo $row['price']; ?>">

  <div class="product-image-wrap">

    <label class="product-wishlist-btn" onclick="toggleWish(this, <?php echo $row['id']; ?>)">
        <i class="fa-regular fa-heart"></i>
    </label>

    <?php if(!empty($row['tag'])) { ?>
        <span class="product-tag"><?php echo $row['tag']; ?></span>
    <?php } ?>

<img src="<?php echo $row['image']; ?>"
     title="<?php echo htmlspecialchars($row['img_title'] ?? ''); ?>"
     alt="<?php echo htmlspecialchars($row['img_alt'] ?? 'Abaya'); ?>">
    <div class="product-info-overlay">
        <h3 class="product-abaya-name"><?php echo $row['name']; ?></h3>

        <a href="abdet.php?id=<?php echo $row['id']; ?>" class="product-add-btn">
            View Details
        </a>
    </div>

  </div>
</div>

<?php } ?>

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

<a href="#top" class="product-back-to-top">↑</a>

</div>

<!-- ===== JS ===== -->
<script>

const colorFilter = document.getElementById("colorFilter");
const priceFilter = document.getElementById("priceFilter");
const searchInput = document.getElementById("searchInput");
const suggestionsBox = document.getElementById("suggestionsBox");
const products = document.querySelectorAll(".product-card");

function validateSearch() {
    let value = searchInput.value.trim();
    let regex = /^[a-zA-Z0-9\s\u0600-\u06FF]*$/;

    if (!regex.test(value)) {
        searchInput.style.border = "2px solid red";
        return false;
    } else {
        searchInput.style.border = "1px solid #ccc";
        return true;
    }
}

function filterProducts() {

    let selectedColor = colorFilter.value;
    let selectedPrice = priceFilter.value;
    let searchText = searchInput.value.toLowerCase();

    products.forEach(product => {

        let name = product.dataset.name;
        let price = parseFloat(product.dataset.price);
        let show = true;

        if (searchText && !name.includes(searchText)) show = false;

        const colorMap = {
            black: ["royal sado", "najd"],
            navy: ["pearl", "layali"],
            light: ["amira", "pearl", "maha"],
            brown: ["lulu"],
            dark: ["najd", "royal sado", "layali", "lulu"]
        };

        if (selectedColor !== "none") {
            let allowed = colorMap[selectedColor];
            if (!allowed.some(item => name.includes(item))) show = false;
        }

        if (selectedPrice !== "none") {
            if (selectedPrice === "under300" && !(price < 300)) show = false;
            if (selectedPrice === "between300400" && !(price >= 300 && price <= 400)) show = false;
            if (selectedPrice === "over400" && !(price > 400)) show = false;
        }

        product.style.display = show ? "block" : "none";

        if (show) {
            setTimeout(() => product.classList.add("show"), 50);
        } else {
            product.classList.remove("show");
        }
    });
}

function showSuggestions() {

    let text = searchInput.value.toLowerCase();
    suggestionsBox.innerHTML = "";

    if (!text) {
        suggestionsBox.style.display = "none";
        return;
    }

    let matched = [];

    products.forEach(p => {
        let name = p.querySelector(".product-abaya-name").innerText;
        if (name.toLowerCase().includes(text)) matched.push(name);
    });

    if (!matched.length) {
        suggestionsBox.style.display = "none";
        return;
    }

    matched.forEach(name => {
        let div = document.createElement("div");
        div.innerText = name;

        div.onclick = () => {
            searchInput.value = name;
            suggestionsBox.style.display = "none";
            filterProducts();
        };

        suggestionsBox.appendChild(div);
    });

    suggestionsBox.style.display = "block";
}

// ===== EVENTS =====
searchInput.addEventListener("input", () => {
    if (!validateSearch()) return;
    filterProducts();
    showSuggestions();
});

colorFilter.addEventListener("change", filterProducts);
priceFilter.addEventListener("change", filterProducts);

document.addEventListener("click", e => {
    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
        suggestionsBox.style.display = "none";
    }
});

// ===== LOAD ANIMATION =====
window.addEventListener("load", () => {
    products.forEach((p, i) => {
        setTimeout(() => p.classList.add("show"), i * 80);
    });
});

filterProducts();

function toggleWish(el, productId) {

    let icon = el.querySelector("i");

    fetch("addwish.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "product_id=" + productId
    })
    .then(res => res.text())
    .then(data => {

        if (data.trim() === "added") {
            icon.classList.remove("fa-regular");
            icon.classList.add("fa-solid");
            icon.style.color = "red";
        }

        else if (data.trim() === "removed") {
            icon.classList.remove("fa-solid");
            icon.classList.add("fa-regular");
            icon.style.color = "#000";
        }
    });
}
</script>

</body>
</html>
