<?php
session_start();
include "db.php";

$order_id = $_POST['order_id'] ?? '';

if (empty($order_id)) {
    die("Invalid order ID.");
}

// جيب مسار الصورة عشان تحذفها
$stmt_img = $conn->prepare("SELECT image FROM custom_orders WHERE id = ?");
$stmt_img->bind_param("i", $order_id);
$stmt_img->execute();
$result = $stmt_img->get_result();

if ($row = $result->fetch_assoc()) {
    $image_path = $row['image'];
    if (!empty($image_path) && $image_path !== 'uploads/default.png' && file_exists($image_path)) {
        unlink($image_path);
    }
}

// احذف الطلب
$stmt = $conn->prepare("DELETE FROM custom_orders WHERE id = ?");
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    header("Location: order.php");
    exit;
} else {
    die("Error deleting order: " . $stmt->error);
}
?>