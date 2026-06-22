<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "myproject");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// الحصول على بيانات الطلب من الـ POST
$order_id = $_POST['order_id'] ?? '';
$price = $_POST['price'] ?? 0;

// نتأكد إن الـ ID حق الطلب موجود
if (empty($order_id)) {
    die("Invalid request.");
}

// هنا نشيك: هل الأدمن حدد السعر أو لسا؟
if (empty($price) || $price == 0) {
    // إذا السعر صفر، نطلع לו رسالة ونرجعه لصفحة الطلبات
    echo "<script>
            alert('The admin has not set the price yet. Please wait!');
            window.location.href = 'order.php';
          </script>";
    exit;
}

// تحديث حالة الطلب إلى "Cash on Delivery"
$stmt = $conn->prepare("UPDATE orders SET status = 'Cash on Delivery' WHERE id = ?");
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    // إعادة التوجيه إلى صفحة الشكر بعد التحديث
    header("Location: thank_you.php");
    exit;
} else {
    die("Error updating order: " . $stmt->error);
}
?>