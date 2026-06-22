<?php
session_start();
include "db.php";
if(!isset($_POST['action'])) exit();

$action = $_POST['action'];
$id     = intval($_POST['id']);
$type   = $_POST['type'];

if($action === 'update_price'){
    $price = floatval($_POST['price']);
    if($type === 'custom'){
        mysqli_query($conn, "UPDATE custom_orders SET price='$price' WHERE id='$id'");
    }
    echo "success";
}

if($action === 'update_status'){
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if($type === 'custom'){
        mysqli_query($conn, "UPDATE custom_orders SET status='$status' WHERE id='$id'");

    } else {
        mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id='$id'");

        if($status === 'Completed'){

            $orderRes = mysqli_query($conn, "SELECT * FROM orders WHERE id='$id'");
            $order    = mysqli_fetch_assoc($orderRes);

            if($order){
                // تحقق إن الطلب ما اتنسخ قبل
                $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM past_orders WHERE order_id='$id'"));
                if(!$check){

                    $payment = mysqli_real_escape_string($conn, $order['payment_method']);

                    mysqli_query($conn, "
                        INSERT INTO past_orders (order_id, user_id, total, shipping, payment_method, status, completed_at)
                        VALUES (
                            '{$order['id']}',
                            '{$order['user_id']}',
                            '{$order['total']}',
                            20.00,
                            '$payment',
                            'Completed',
                            NOW()
                        )
                    ");
                    if(!$insertPast){
    die("ERROR: Past " . mysqli_error($conn));
}

                    $past_order_id = mysqli_insert_id($conn);

                    $items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id='$id'");
                    while($item = mysqli_fetch_assoc($items)){
                        $size  = mysqli_real_escape_string($conn, $item['size']);
                        mysqli_query($conn, "
                            INSERT INTO past_order_items (past_order_id, product_id, qty, size, price)
                            VALUES (
                                '$past_order_id',
                                '{$item['product_id']}',
                                '{$item['qty']}',
                                '$size',
                                '{$item['price']}'
                            )
                        ");
                        if(!$insertItem){
    die("ERROR: " . mysqli_error($conn));
}
                    }
                }
            }
        }
    }

    echo "success";
}
?>