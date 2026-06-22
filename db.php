<?php
$conn = mysqli_connect("localhost", "root", "System123", "webp");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>