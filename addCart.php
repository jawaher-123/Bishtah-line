<?php
session_start();
include "db.php";

if(!isset($_POST['abaya_id'], $_POST['quantity'], $_POST['size'])){
    echo "missing";
    exit;
}

$product_id = intval($_POST['abaya_id']);
$qty = intval($_POST['quantity']);
$size = $_POST['size'];

if($product_id <= 0 || $qty <= 0 || empty($size)){
    echo "invalid";
    exit;
}

if(!isset($_SESSION['session_id'])){
    $_SESSION['session_id'] = session_id();
}

$session_id = $_SESSION['session_id'];
$user_id = $_SESSION['user_id'] ?? NULL;

// ===== USER LOGIC =====
if($user_id){

    $check = mysqli_query($conn, "
        SELECT * FROM cart 
        WHERE user_id='$user_id' 
        AND product_id='$product_id' 
        AND size='$size'
    ");

    if(mysqli_num_rows($check) > 0){

        mysqli_query($conn, "
            UPDATE cart 
            SET qty = qty + $qty 
            WHERE user_id='$user_id' 
            AND product_id='$product_id' 
            AND size='$size'
        ");

    }else{

        mysqli_query($conn, "
            INSERT INTO cart (user_id, product_id, qty, size)
            VALUES ('$user_id', '$product_id', '$qty', '$size')
        ");
    }

}else{

    $check = mysqli_query($conn, "
        SELECT * FROM cart 
        WHERE session_id='$session_id' 
        AND product_id='$product_id' 
        AND size='$size'
    ");

    if(mysqli_num_rows($check) > 0){

        mysqli_query($conn, "
            UPDATE cart 
            SET qty = qty + $qty 
            WHERE session_id='$session_id' 
            AND product_id='$product_id' 
            AND size='$size'
        ");

    }else{

        mysqli_query($conn, "
            INSERT INTO cart (session_id, product_id, qty, size)
            VALUES ('$session_id', '$product_id', '$qty', '$size')
        ");
    }
}

echo "success";
?>