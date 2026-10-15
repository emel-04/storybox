<?php
// Đảm bảo sửa lại thông tin kết nối cho phù hợp
$host = 'localhost';
$dbname = 'story_box';  // Tên database đã đổi từ story_reading_platform -> story_box
$username = 'root';
$password = '697404';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}
?>