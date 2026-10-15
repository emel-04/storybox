<?php
session_start();
require_once 'config/db_connection.php'; // File cấu hình kết nối database

$error = '';

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        // Lấy thông tin user từ database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['password_hash'])) {
                // Kiểm tra tài khoản có bị khóa không
                if (!$user['is_active']) {
                    $error = "Tài khoản của bạn đã bị khóa";
                } else {
                    // Lưu thông tin đăng nhập vào session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['avatar'] = $user['avatar_url'] ?? 'images/default-avatar.png';
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['is_logged_in'] = true;

                    // Cập nhật thời gian đăng nhập cuối
                    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?")
                        ->execute([$user['user_id']]);

                    // Chuyển hướng theo role
                    if ($user['role'] === 'admin') {
                        header("Location: index-admin.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit;
                }
            } else {
                $error = "Email hoặc mật khẩu không đúng";
            }
        } else {
            $error = "Email hoặc mật khẩu không đúng";
        }
    } catch (PDOException $e) {
        $error = "Lỗi kết nối database: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
  <meta charset="utf-8">
  <title>StoryBox - Đăng Nhập</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/auth-style.css" rel="stylesheet">
  <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>

<body>
    <!-- Header -->
    <nav id="main-navbar">
        <div id="navbar-brand">
            <a href="index.php">
                <img src="images/logo.png" alt="StoryBox" id="logo">
            </a>    
        </div>
        <ul id="navbar-links">
            <li><a href="index.php">Trang Chủ</a></li>
            <li><a href="story-list.php">Danh Sách Truyện</a></li>
            <li><a href="ranking.php">Bảng Xếp Hạng</a></li>
        </ul>
        <form id="search-bar" action="search.php" method="get">
            <div id="search-container">
                <input type="search" name="s" placeholder="Tìm kiếm ..." required>
            </div>
        </form>
    </nav>

    <!-- Content -->
    <div id="auth-container">
    <h2 id="auth-title">ĐĂNG NHẬP</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form id="auth-form" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
        
        <div id="auth-links">
            <a class="auth-link" href="register.php">Đăng ký tài khoản</a>
        </div>
    </form>
    </div>

    <!-- Footer -->
    <footer id="main-footer">
        <div id="footer-logo">
        <img src="images/logo.png" alt="StoryBox">
        </div>

        <div id="social-icons">
        <a href="#"><i class="ti-facebook"></i></a>
        <a href="#"><i class="ti-twitter-alt"></i></a>      
        <a href="#"><i class="ti-github"></i></a>
        </div>

        <div id="footer-bottom">
        <p>© 2025 StoryBox. Designed & Developed by <a href="https://www.instagram.com/dyemel.204/">di:emel</a></p>
        </div>
    </footer>

    <script src="plugins/jQuery/jquery.min.js"></script>
    <script src="plugins/clipboard/clipboard.min.js"></script>
    <script src="plugins/match-height/jquery.matchHeight-min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>