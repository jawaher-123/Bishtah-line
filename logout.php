<?php
session_start();
session_destroy();
header("Location: role.php");
exit;
?>