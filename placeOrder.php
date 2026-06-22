<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'] ?? NULL;

if(!isset($_SESSION['session_id'])){
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

if(!isset($_POST['fullname'], $_POST['email'], $_POST['phone'], $_POST['city'], $_POST['district'], $_POST['payment'])){
    echo "missing_data";
    exit;
}

$fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$city = mysqli_real_escape_string($conn, $_POST['city']);
$district = mysqli_real_escape_string($conn, $_POST['district']);
$payment = mysqli_real_escape_string($conn, $_POST['payment']);

if($user_id){
    $condition = "user_id='$user_id'";
}else{
    $condition = "session_id='$session_id'";
}

// جلب الكارت
$sql_cart = "
SELECT cart.product_id, cart.qty, cart.size, abayas.price
FROM cart
INNER JOIN abayas ON cart.product_id = abayas.id
WHERE $condition
";
$result = mysqli_query($conn, $sql_cart);

if(mysqli_num_rows($result) == 0){
    echo "empty_cart";
    exit;
}

$shipping = 20;
$subtotal = 0;

$items = [];

while($row = mysqli_fetch_assoc($result)){
    $subtotal += $row['price'] * $row['qty'];
    $items[] = $row;
}

$total = $subtotal + $shipping;

// حفظ الطلب
mysqli_query($conn, "
INSERT INTO orders (user_id, session_id, full_name, email, phone, city, district, payment_method, shipping, total)
VALUES ('$user_id', '$session_id', '$fullname', '$email', '$phone', '$city', '$district', '$payment', '$shipping', '$total')
");

$order_id = mysqli_insert_id($conn);

// حفظ المنتجات داخل order_items
foreach($items as $item){

    $product_id = $item['product_id'];
    $qty = $item['qty'];
    $size = $item['size'];
    $price = $item['price'];

    mysqli_query($conn, "
        INSERT INTO order_items (order_id, product_id, qty, size, price)
        VALUES ('$order_id', '$product_id', '$qty', '$size', '$price')
    ");
}

// تفريغ السلة
mysqli_query($conn, "DELETE FROM cart WHERE $condition");

echo "success";
?>