<?php
// Kết nối cơ sở dữ liệu và kiểm tra quyền admin
session_start();
require_once 'config/db_connection.php'; // File cấu hình kết nối database

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: index.php');
  exit;
}

// Lấy ID truyện từ URL
$story_id = isset($_GET['story_id']) ? (int)$_GET['story_id'] : 0;

try {
    // Lấy thông tin truyện
    $stmt = $pdo->prepare("SELECT * FROM stories WHERE story_id = ?");
    $stmt->execute([$story_id]);
    $story = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$story) {
        die("Truyện không tồn tại");
    }
    
    // Lấy số chương cuối cùng
    $stmt = $pdo->prepare("SELECT MAX(chapter_number) FROM chapters WHERE story_id = ?");
    $stmt->execute([$story_id]);
    $last_chapter = $stmt->fetchColumn();
    $new_chapter_number = $last_chapter ? $last_chapter + 1 : 1;
    
    // Xử lý form khi submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $content = trim($_POST['content']);
        $chapter_number = (int)$_POST['chapter_number'];
    
        if (empty($content)) {
            $error = "Vui lòng nhập nội dung chương";
        } else {
            // Lưu chương
            $stmt = $pdo->prepare("INSERT INTO chapters (story_id, chapter_number, content) VALUES (?, ?, ?)");
            $stmt->execute([$story_id, $chapter_number, $content]);
    
            // Cập nhật thời gian
            $pdo->prepare("UPDATE stories SET updated_at = NOW() WHERE story_id = ?")->execute([$story_id]);
    
            // Log hoạt động
            $admin_id = $_SESSION['admin_id'];
            $chapter_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO admin_activities (admin_id, activity_type, action, target_id, description, ip_address)
                                   VALUES (?, 'chapter_management', 'create', ?, 'Thêm chương mới', ?)");
            $stmt->execute([$admin_id, $chapter_id, $_SERVER['REMOTE_ADDR']]);
    
            header("Location: manage-chapters.php?id=$story_id&success=1");
            exit;
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
  <title>StoryBox - Thêm chương truyện</title>
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
        <div class="content-card">
          <div class="card-header">
            <h3>Thêm chương truyện</h3>
          </div>
          <div class="card-body">
            
            <!-- Biểu mẫu thêm chương -->
            <form method="POST" class="story-form-container">
              <div class="story-cover-form">
                <div class="cover-preview">
                  <img src="<?= htmlspecialchars($story['cover_image_url'] ?? 'images/default-cover.jpg') ?>" alt="Ảnh bìa truyện">
                </div>
              </div>
              
              <div class="story-info-form">
                <div class="form-section">
                  <h3 style="text-transform: uppercase;"><?= htmlspecialchars($story['title']) ?></h3>
                </div>
                
                <div class="form-row">
                  <div class="form-section">
                    <h4>Chương <?= $new_chapter_number ?></h4>
                    <input type="hidden" name="chapter_number" value="<?= $new_chapter_number ?>">
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-section">
                    <h4>Nội dung</h4>
                    <textarea class="form-input" name="content" rows="8" 
                              placeholder="Nhập nội dung chương..." required></textarea>
                  </div>
                </div>
                 
                <div class="form-actions">
                  <a href="manage-chapters.php?id=<?= $story_id ?>" class="btn btn-cancel">Hủy bỏ</a>
                  <button type="submit" class="btn btn-save">Thêm chương</button>
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