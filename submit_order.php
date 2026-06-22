<?php
session_start();
include "db.php";

// جلب البيانات من الفورم
$style = $_POST['style'] ?? '';
$size  = $_POST['size']  ?? '';
$color = $_POST['color'] ?? '';

// إعداد متغيرات الصورة
$imageName = $_FILES['image']['name'] ?? '';
$tmp       = $_FILES['image']['tmp_name'] ?? '';
$imagePath = '';

// التحقق إذا اليوزر رفع صورة
if (!empty($imageName) && is_uploaded_file($tmp)) {

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    if (!in_array($_FILES['image']['type'], $allowedTypes)) {
        die("Invalid image type. Please upload a JPEG, PNG, or GIF image.");
    }

    $imagePath = 'uploads/' . time() . "_" . basename($imageName);

    if (!move_uploaded_file($tmp, $imagePath)) {
        die("Error uploading image. تأكد إن مجلد uploads موجود داخل مجلد المشروع!");
    }

} else {
    $imagePath = "uploads/default.png";
}

// حالة الطلب
// حالة الطلب
$status = "In Progress";

// اليوزر والسيشن
$user_id    = $_SESSION['user_id'] ?? null;
$session_id = session_id();

// إدخال البيانات
$stmt = $conn->prepare("INSERT INTO custom_orders (user_id, session_id, style, size, color, status, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $user_id, $session_id, $style, $size, $color, $status, $imagePath);

if (!$stmt->execute()) {
    die("Insert error: " . $stmt->error);
}

header("Location: order.php");
exit;
?>