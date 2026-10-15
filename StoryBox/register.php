<?php
session_start();
require_once 'config/db_connection.php';

// Khởi tạo biến
$errors = [];
$success = false;
$message = '';
$avatar_url = '';

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Lấy và làm sạch dữ liệu
        $username = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm-password'] ?? '';
        $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random"; // Mặc định

        // Xử lý upload ảnh đại diện
        if (isset($_FILES['avatar'])) {
            $avatar = $_FILES['avatar'];
            
            // Kiểm tra lỗi upload
            if ($avatar['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 2 * 1024 * 1024; // 2MB
                
                // Kiểm tra loại file
                if (in_array($avatar['type'], $allowed_types)) {
                    // Kiểm tra kích thước file
                    if ($avatar['size'] <= $max_size) {
                        // Tạo tên file mới
                        $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
                        $filename = uniqid('avatar_') . '.' . $ext;
                        $upload_path = 'images/users/' . $filename;
                        
                        // Di chuyển file vào thư mục đích
                        if (move_uploaded_file($avatar['tmp_name'], $upload_path)) {
                            $avatar_url = $upload_path;
                        } else {
                            $errors['avatar'] = 'Có lỗi khi tải lên ảnh đại diện';
                        }
                    } else {
                        $errors['avatar'] = 'Ảnh đại diện không được vượt quá 2MB';
                    }
                } else {
                    $errors['avatar'] = 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF)';
                }
            } elseif ($avatar['error'] !== UPLOAD_ERR_NO_FILE) {
                $errors['avatar'] = 'Có lỗi khi tải lên ảnh đại diện';
            }
        }

        // Validate dữ liệu
        if (empty($username)) {
            $errors['name'] = 'Vui lòng nhập tên người dùng';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors['name'] = 'Tên người dùng phải từ 3 đến 50 ký tự';
        }

        if (empty($email)) {
            $errors['email'] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        } else {
            // Kiểm tra email tồn tại
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors['email'] = 'Email này đã được sử dụng';
            }
        }

        if (empty($password)) {
            $errors['password'] = 'Vui lòng nhập mật khẩu';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        if ($password !== $confirm_password) {
            $errors['confirm-password'] = 'Mật khẩu nhập lại không khớp';
        }

        // Nếu không có lỗi
        if (empty($errors)) {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Thêm user vào database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, avatar_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash, $avatar_url]);
            
            $success = true;
            $message = 'Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.';
        }
    } catch (PDOException $e) {
        error_log("Database error in register.php: " . $e->getMessage());
        $message = 'Có lỗi hệ thống xảy ra khi đăng ký. Vui lòng thử lại sau.';
    } catch (Exception $e) {
        error_log("General error in register.php: " . $e->getMessage());
        $message = 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại sau.';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <title>StoryBox - Đăng Ký</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/auth-style.css" rel="stylesheet">
  <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <style>
    .alert {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
    }
    .error-message {
      color: #dc3545;
      font-size: 14px;
      margin-top: 5px;
      display: block;
    }
  </style>
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
    <h2 id="auth-title">ĐĂNG KÝ TÀI KHOẢN</h2>
    
    <?php if ($message): ?>
      <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>
    
    <form id="auth-form" method="POST" action="register.php" novalidate enctype="multipart/form-data">
        <div class="form-group">
          <label for="name">Tên người dùng</label>
          <input type="text" id="name" name="name" 
                 value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" 
                 required
                 minlength="3" maxlength="50">
          <?php if (isset($errors['name'])): ?>
            <span class="error-message"><?= htmlspecialchars($errors['name']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" 
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                   required>
            <?php if (isset($errors['email'])): ?>
              <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="password">Mật khẩu (tối thiểu 6 ký tự)</label>
            <input type="password" id="password" name="password" 
                   required minlength="6">
            <?php if (isset($errors['password'])): ?>
              <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="confirm-password">Nhập lại mật khẩu</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
            <?php if (isset($errors['confirm-password'])): ?>
              <span class="error-message"><?= htmlspecialchars($errors['confirm-password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="avatar">Ảnh đại diện</label>
            <input type="file" id="avatar" name="avatar" accept="image/*">            
            <?php if (isset($errors['avatar'])): ?>
                <span class="error-message"><?= htmlspecialchars($errors['avatar']) ?></span>
            <?php endif; ?>
        </div>
        
        <button type="submit" class="btn btn-primary">Đăng ký</button>
        
        <div id="auth-links">
            <span>Đã có tài khoản? <a class="auth-link" href="login.php">Đăng nhập ngay</a></span>
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