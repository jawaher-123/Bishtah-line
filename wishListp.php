<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'] ?? NULL;

if(!isset($_SESSION['session_id'])){
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

/* ===== REMOVE ACTION ===== */
if(isset($_POST['action']) && $_POST['action'] == "remove"){
    $wish_id = intval($_POST['wish_id']);
    if($user_id){
        mysqli_query($conn, "DELETE FROM wishlist WHERE id='$wish_id' AND user_id='$user_id'");
    } else {
        mysqli_query($conn, "DELETE FROM wishlist WHERE id='$wish_id' AND session_id='$session_id'");
    }
    echo "success";
    exit();
}

/* ===== CONDITION ===== */
if($user_id){
    $condition = "wishlist.user_id='$user_id'";
} else {
    $condition = "wishlist.session_id='$session_id'";
}

/* ===== FETCH WISHLIST ===== */
$sql = "
    SELECT wishlist.id AS wish_id, abayas.*
    FROM wishlist
    INNER JOIN abayas ON wishlist.product_id = abayas.id
    WHERE $condition
    ORDER BY wishlist.created_at DESC
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bishtah - Wishlist</title>

<link rel="stylesheet" href="style1.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>

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
<a href="logout.php" class="logout-btn">Log Out</a></div>
    </div>
</div>

<label for="menu-toggle" class="overlay"></label>

<!-- ===== WISHLIST SECTION ===== -->
<section class="wish-section">
    <div class="wish-section-header">
        <h2>Your Wishlist</h2>
        <p>Your favorite pieces in one place.</p>
    </div>

    <div class="wish-product-grid" id="wishlistGrid">

        <?php if(mysqli_num_rows($result) > 0){ ?>
            <?php while($row = mysqli_fetch_assoc($result)){ ?>

            <div class="wish-product-card" id="wish-<?php echo $row['wish_id']; ?>">
                <div class="wish-product-image-wrap">

                    <button class="wish-remove-btn" onclick="removeWish(<?php echo $row['wish_id']; ?>)" title="Remove Item">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <img src="<?php echo $row['image']; ?>" alt="Abaya">

                    <div class="wish-product-info-overlay">
                        <h3><?php echo $row['name']; ?></h3>
                        <a href="abdet.php?id=<?php echo $row['id']; ?>" class="wish-add-btn">View Details</a>
                    </div>

                </div>
            </div>

            <?php } ?>
        <?php } else { ?>

            <div class="wish-empty-state">
                <i class="fa-regular fa-heart"></i>
                <p>Your wishlist is empty 💔</p>
                <a href="we.php" style="color:#c0a080; text-decoration:underline;">Explore Our Collection</a>
            </div>

        <?php } ?>

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

<a href="#top" class="wish-back-to-top">↑</a>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.wish-product-card');
    items.forEach((item, index) => {
        setTimeout(() => item.classList.add('show'), index * 100);
    });
});

function removeWish(wishId){
    const card = document.getElementById("wish-" + wishId);
    card.classList.add('removing');

    setTimeout(() => {
        fetch("wishListp.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "action=remove&wish_id=" + wishId
        })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === "success"){
                card.remove();
                const grid = document.getElementById('wishlistGrid');
                if(grid.querySelectorAll('.wish-product-card').length === 0){
                    grid.innerHTML = `
                    <div class="wish-empty-state">
                        <i class="fa-regular fa-heart"></i>
                        <p>Your wishlist is empty 💔</p>
                        <a href="we.php" style="color:#c0a080; text-decoration:underline;">Explore Our Collection</a>
                    </div>`;
                }
            } else {
                card.classList.remove('removing');
                alert("Error: " + data);
            }
        });
    }, 400);
}
</script>

</body>
</html>
