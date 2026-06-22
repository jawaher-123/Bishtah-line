<?php
// 1. إدارة الجلسات (Sessions) - متطلب أساسي للأمان في Milestone 3
session_start();

// تفعيل عرض الأخطاء للتأكد من سير العمليات بشكل صحيح
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. الاتصال الآمن بقاعدة البيانات (Database Connection)
try {
    $conn = new mysqli("localhost", "root", "", "myproject");
    if ($conn->connect_error) {
        throw new Exception("فشل الاتصال بقاعدة البيانات.");
    }
} catch (Exception $e) {
    die("<script>alert('" . $e->getMessage() . "'); window.location.href='order.php';</script>");
}

// 3. استلام البيانات والتحقق من صحتها (Input Validation & Security)
// استلام السعر وID الطلب من الحقول المخفية التي أضفناها في صفحة order.php
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

// التأكد من أن الطلب صالح
if ($order_id <= 0) {
    die("<script>alert('عذراً، الطلب غير موجود.'); window.location.href='order.php';</script>");
}

// 4. التحقق من السعر (Business Logic)
// إذا كان السعر 0، يعني أن الإدارة لم تحدده بعد في قاعدة البيانات
if ($price <= 0) {
    echo "<script>
            alert('نعتذر، لم يتم تحديد السعر من قبل الإدارة بعد. يرجى الانتظار ✨');
            window.location.href = 'order.php';
          </script>";
    exit;
}

// 5. تحديث حالة الطلب باستخدام الاستعلامات المحمية (SQL Security)
// نقوم بتغيير الحالة لـ "Cash on Delivery" لتنشيط الدفع وإخفاء الأزرار
$new_status = 'Cash on Delivery'; 
$sql = "UPDATE orders SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        // 6. تفاعل المستخدم (User Interaction) - رسالة نجاح وتوجيه
        echo "<script>
                alert('تم اختيار الدفع عند الاستلام بنجاح! شكراً لثقتك بـ Bishtah Line ✨');
                window.location.href='order.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('حدث خطأ أثناء معالجة الطلب: " . $stmt->error . "'); window.history.back();</script>";
    }
    $stmt->close();
} else {
    echo "خطأ في تجهيز الاستعلام: " . $conn->error;
}

$conn->close();
?>