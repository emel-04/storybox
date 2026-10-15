<?php
// Kết nối cơ sở dữ liệu và kiểm tra quyền admin
session_start();
require_once 'config/db_connection.php'; // File cấu hình kết nối database

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Lấy ID truyện từ URL
$story_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Lấy thông tin truyện
    $stmt = $pdo->prepare("SELECT * FROM stories WHERE story_id = ?");
    $stmt->execute([$story_id]);
    $story = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$story) {
        die("Truyện không tồn tại");
    }
    
    // Lấy danh sách thể loại
    $genres = $pdo->query("SELECT * FROM genres ORDER BY genre_name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Lấy thể loại của truyện
    $story_genres = $pdo->prepare("SELECT g.genre_id FROM story_genres sg 
                                 JOIN genres g ON sg.genre_id = g.genre_id 
                                 WHERE sg.story_id = ?");
    $story_genres->execute([$story_id]);
    $selected_genres = $story_genres->fetchAll(PDO::FETCH_COLUMN);
    
    // Xử lý form khi submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title']);
        $author_name = trim($_POST['author_name']);
        $status = $_POST['status'];
        $selected_genres = isset($_POST['genres']) ? $_POST['genres'] : [];
        
        // Validate dữ liệu
        if (empty($title)) {
            $error = "Vui lòng nhập tên truyện";
        } else {
            // Cập nhật thông tin truyện
            $stmt = $pdo->prepare("UPDATE stories SET title = ?, author_name = ?, status = ?, updated_at = NOW() WHERE story_id = ?");
            $stmt->execute([$title, $author_name, $status, $story_id]);
            
            // Cập nhật thể loại
            // Xóa thể loại cũ
            $pdo->prepare("DELETE FROM story_genres WHERE story_id = ?")->execute([$story_id]);
            
            // Thêm thể loại mới
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
                                   VALUES (?, 'story_management', 'update', ?, 'Cập nhật thông tin truyện', ?)");
            $stmt->execute([$admin_id, $story_id, $_SERVER['REMOTE_ADDR']]);
            
            $success = "Cập nhật thông tin truyện thành công!";
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
  <title>StoryBox - Chỉnh sửa truyện</title>
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
        <?php if ($success): ?>
          <div class="alert alert-success">Cập nhật thành công!</div>
        <?php endif; ?>        
        <?php if ($error): ?>
          <div class="alert alert-error">Cập nhật thất bại!</div>
        <?php endif; ?>

        <!-- Cập nhật thông tin truyện -->
        <div class="content-card">
          <div class="card-header">
            <h3>Cập nhật thông tin truyện</h3>
          </div>
          <div class="card-body">            
            <form class="story-form-container" method="POST">
              <div class="story-cover-form">
                <div class="cover-preview">
                  <img src="<?= !empty($story['cover_image_url']) ? htmlspecialchars($story['cover_image_url']) : 'images/default-cover.jpg' ?>" 
                      alt="Ảnh bìa truyện">
                </div>
              </div>
            
              <div class="story-info-form">
                <div class="form-section">
                  <h4>Tên truyện</h4>
                  <input type="text" class="form-input" name="title" value="<?= htmlspecialchars($story['title']) ?>" required>
                </div>
                
                <div class="form-row">                  
                  <div class="form-section">
                    <h4>Tác giả</h4>
                    <input type="text" class="form-input" name="author_name" value="<?= htmlspecialchars($story['author_name']) ?>" required>
                  </div>
                </div>
                  
                <div class="form-row">
                  <div class="form-section">
                    <h4>Tình trạng</h4>
                    <select class="form-select" name="status" required>
                      <option value="ongoing" <?= $story['status'] === 'ongoing' ? 'selected' : '' ?>>Đang tiến hành</option>
                      <option value="completed" <?= $story['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                      <option value="hiatus" <?= $story['status'] === 'hiatus' ? 'selected' : '' ?>>Tạm ngưng</option>
                    </select>
                  </div>                  
                
                  <div class="form-section">
                    <h4>Thể loại</h4>
                    <select class="form-select" name="genres[]" required>
                      <?php foreach ($genres as $genre): ?>
                        <option value="<?= $genre['genre_id'] ?>" <?= in_array($genre['genre_id'], $selected_genres) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($genre['genre_name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>                  
                </div>

                <div class="form-section">
                  <h4>Mô tả</h4>
                  <textarea class="form-input" name="description"><?= htmlspecialchars($story['description']) ?></textarea>
                </div>

                <div class="form-actions">
                  <a href="manage-stories.php" class="btn btn-cancel">Hủy bỏ</a>
                  <button type="submit" class="btn btn-save">Lưu thay đổi</button>
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