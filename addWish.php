<?php
session_start();
include "db.php";

if(!isset($_POST['product_id'])){
    echo "error";
    exit;
}

$product_id = intval($_POST['product_id']);

if($product_id <= 0){
    echo "error";
    exit;
}

$user_id = $_SESSION['user_id'] ?? NULL;

if(!isset($_SESSION['session_id'])){
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

/* ================= USER ================= */
if($user_id){

    $check = mysqli_query($conn, "
        SELECT * FROM wishlist 
        WHERE user_id='$user_id' AND product_id='$product_id'
    ");

    if(mysqli_num_rows($check) > 0){

        mysqli_query($conn, "
            DELETE FROM wishlist 
            WHERE user_id='$user_id' AND product_id='$product_id'
        ");

        echo "removed";

    } else {

        mysqli_query($conn, "
            INSERT INTO wishlist (user_id, product_id)
            VALUES ('$user_id', '$product_id')
        ");

        echo "added";
    }

/* ================= GUEST ================= */
}else{

    $check = mysqli_query($conn, "
        SELECT * FROM wishlist 
        WHERE session_id='$session_id' AND product_id='$product_id'
    ");

    if(mysqli_num_rows($check) > 0){

        mysqli_query($conn, "
            DELETE FROM wishlist 
            WHERE session_id='$session_id' AND product_id='$product_id'
        ");

        echo "removed";

    } else {

        mysqli_query($conn, "
            INSERT INTO wishlist (session_id, product_id)
            VALUES ('$session_id', '$product_id')
        ");

        echo "added";
    }
}
?>