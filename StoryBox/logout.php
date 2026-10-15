<?php
session_start();

// Hủy toàn bộ session
$_SESSION = [];
session_unset();
session_destroy();

// Chuyển hướng về trang chủ hoặc trang đăng nhập
header("Location: index.php");
exit();
