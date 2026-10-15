<?php
// Kết nối cơ sở dữ liệu và kiểm tra quyền admin
session_start();
require_once 'config/db_connection.php'; // File cấu hình kết nối database

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: index.php');
  exit;
}

// Khởi tạo biến
$error = '';
$success = '';

try {
    // Lấy danh sách thể loại
    $genres = $pdo->query("SELECT * FROM genres ORDER BY genre_name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Xử lý form khi submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title']);
        $author_name = trim($_POST['author_name']);
        $status = $_POST['status'];
        $description = trim($_POST['description']);
        $selected_genres = isset($_POST['genres']) ? $_POST['genres'] : [];
        
        // Validate dữ liệu
        if (empty($title)) {
            $error = "Vui lòng nhập tên truyện";
        } elseif (empty($author_name)) {
            $error = "Vui lòng nhập tên tác giả";
        } else {
          // Xử lý upload ảnh bìa
          $cover_image_url = 'images/default-cover.jpg'; // Ảnh mặc định
          
          if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
              $upload_dir = 'images/story/';
              if (!is_dir($upload_dir)) {
                  mkdir($upload_dir, 0755, true);
              }
              
              $file_ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
              $file_name = 'cover_' . time() . '.' . $file_ext;
              $file_path = $upload_dir . $file_name;
              
              if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $file_path)) {
                  $cover_image_url = $file_path;
              }
          }
          
          // Thêm truyện mới vào database
          $stmt = $pdo->prepare("INSERT INTO stories (title, author_name, status, description, cover_image_url, created_at, updated_at) 
                           VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
          $stmt->execute([$title, $author_name, $status, $description, $cover_image_url]);
          $story_id = $pdo->lastInsertId();
          
          // Thêm thể loại cho truyện
          if (!empty($selected_genres)) {
              $stmt = $pdo->prepare("INSERT INTO story_genres (story_id, genre_id) VALUES (?, ?)");
              foreach ($selected_genres as $genre_id) {
                  $stmt->execute([$story_id, $genre_id]);
              }
          }
          
          // Ghi log hoạt động
          $admin_id = $_SESSION['user_id'];
          $stmt = $pdo->prepare("INSERT INTO admin_activities 
                                  (admin_id, activity_type, action, target_id, description, ip_address) 
                                  VALUES (?, 'story_management', 'create', ?, 'Thêm truyện mới', ?)");
          $stmt->execute([$admin_id, $story_id, $_SERVER['REMOTE_ADDR']]);
          
          $success = "Thêm truyện mới thành công!";
          
          // Reset form sau khi thêm thành công
          if ($success) {
              $_POST = array();
          }
        }
    }
    
} catch (PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Lấy thông tin admin
$admin_name = $_SESSION['username'] ?? 'Admin';
$admin_avatar = $_SESSION['avatar'] ?? 'images/avatar.png';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StoryBox - Thêm truyện mới</title>
  <link rel="stylesheet" href="css/style-admin.css">
  <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
</head>

<body>
  <div id="admin-container">
    <!-- Sidebar -->
    <aside id="admin-sidebar"> 
      <div id="sidebar-header">
        <img id="admin-logo" src="images/logo.png" alt="StoryBox">
      </div>      
      <nav id="sidebar-nav">
        <ul>
          <li id="nav-dashboard"><a href="index-admin.php"><i class="ti-dashboard"></i>Dashboard</a></li>
          <li id="nav-users"><a href="user-management.php"><i class="ti-user"></i> Quản lý User</a></li>
          <li id="nav-stories" class="active"><a href="story-management.php"><i class="ti-book"></i> Quản lý Truyện</a></li>
          <li id="nav-analytics"><a href="statistics.php"><i class="ti-stats-up"></i> Thống kê</a></li>
        </ul>
      </nav>
      
      <div id="sidebar-footer">
        <a href="logout.php" id="logout-btn"><i class="ti-power-off"></i> Đăng xuất</a>
      </div>
    </aside>

    <!-- Header -->
    <main id="admin-main">
      <header id="admin-header">
        <div id="header-left">
          <h1>Quản Lý Truyện</h1>
        </div>
        <div id="header-right">          
          <div id="admin-profile">
            <img id="profile-img" src="<?= htmlspecialchars($admin_avatar) ?>" alt="Admin">
            <span id="profile-name"><?= htmlspecialchars($admin_name) ?></span>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div id="admin-content"> 
        <!-- Thông báo -->       
        <?php if ($error): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>        
        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <div class="content-card">
          <div class="card-header">
            <h3>Thêm truyện mới</h3>
          </div>
          <div class="card-body">    
            <!-- Biểu mẫu thêm truyện -->        
            <form class="story-form-container" method="POST" enctype="multipart/form-data">
              <div class="story-cover-form">
                <div class="cover-preview">
                  <img id="cover-preview-img" src="images/default-cover.jpg" alt="Ảnh bìa truyện">
                </div>
              </div>
                
              <div class="story-info-form">
                <div class="form-section">
                  <h4>Tên truyện <span class="required">*</span></h4>
                  <input type="text" class="form-input" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                </div>
                
                <div class="form-row">
                  <div class="form-section">
                    <h4>Tác giả <span class="required">*</span></h4>
                    <input type="text" class="form-input" name="author_name" value="<?= htmlspecialchars($_POST['author_name'] ?? '') ?>" required>
                  </div>
                </div>
                  
                <div class="form-row">
                  <div class="form-section">
                    <h4>Tình trạng <span class="required">*</span></h4>
                    <select class="form-select" name="status" required>
                      <option value="ongoing" <?= ($_POST['status'] ?? '') === 'ongoing' ? 'selected' : '' ?>>Đang tiến hành</option>
                      <option value="completed" <?= ($_POST['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                      <option value="hiatus" <?= ($_POST['status'] ?? '') === 'hiatus' ? 'selected' : '' ?>>Tạm ngưng</option>
                    </select>
                  </div>                  
                
                  <div class="form-section">
                    <h4>Thể loại</h4>
                    <select class="form-select" name="genres[]" required>
                      <?php foreach ($genres as $genre): ?>
                        <option value="<?= $genre['genre_id'] ?>" <?= isset($_POST['genres']) && in_array($genre['genre_id'], $_POST['genres']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($genre['genre_name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>                  
                </div>

                <div class="form-section">
                  <h4>Mô tả</h4>
                  <textarea class="form-input" name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="form-section">
                  <h4>Ảnh bìa</h4>
                  <input type="file" id="cover-image-input" class="form-input" name="cover_image" accept="image/*">
                </div>

                <div class="form-actions">
                  <button type="submit" class="btn btn-cancel">Hủy bỏ</button>
                  <button type="submit" class="btn btn-save">Thêm truyện</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="plugins/jQuery/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>