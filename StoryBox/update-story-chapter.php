<?php
session_start();
require_once 'config/db_connection.php';

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
    
    // Lấy danh sách chương truyện
    $chapters = $pdo->prepare("SELECT * FROM chapters WHERE story_id = ? ORDER BY chapter_number");
    $chapters->execute([$story_id]);
    
    // Xử lý xóa chương
    if (isset($_GET['delete'])) {
        $chapter_id = (int)$_GET['delete'];
        
        // Xóa chương
        $stmt = $pdo->prepare("DELETE FROM chapters WHERE chapter_id = ?");
        $stmt->execute([$chapter_id]);
        
        // Ghi log hoạt động
        $admin_id = $_SESSION['admin_id'];
        $stmt = $pdo->prepare("INSERT INTO admin_activities 
                             (admin_id, activity_type, action, target_id, description, ip_address) 
                             VALUES (?, 'chapter_management', 'delete', ?, 'Xóa chương truyện', ?)");
        $stmt->execute([$admin_id, $chapter_id, $_SERVER['REMOTE_ADDR']]);
        
        header("Location: manage-chapters.php?id=$story_id&success=1");
        exit;
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
  <title>StoryBox - Quản lý chương truyện</title>
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
                <div class="alert alert-error">Cập nhật thành công!</div>
                <?php endif; ?>        
                <?php if ($success): ?>
                <div class="alert alert-success">Cập nhật thất bại!</div>
                <?php endif; ?>
                                                
                <div class="content-card">
                    <div class="card-header">
                        <h3>Quản lý chương truyện</h3>
                    </div>
                    <!-- Thông tin: ảnh bìa, danh sách chương -->
                    <div class="card-body">                    
                        <h3 class="story-title" style="color: black; text-transform: uppercase;"><?= htmlspecialchars($story['title']) ?></h3>
                        <div class="story-form-container">
                            <div class="story-cover-form">
                            <div class="cover-preview">
                                <img src="<?= htmlspecialchars($story['cover_image_url'] ?? 'images/default-cover.jpg') ?>" alt="Ảnh bìa truyện">
                            </div>
                            </div>
                            <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">Chương</th>
                                    <th style="width: 40%;">Cập nhật</th>
                                    <th style="width: 20%;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chapters as $chapter): ?>
                                <tr>
                                    <td>
                                        <a href="edit-chapter.php?id=<?= $chapter['chapter_id'] ?>">
                                            Chương <?= $chapter['chapter_number'] ?> <?= htmlspecialchars($chapter['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($chapter['updated_at'])) ?></td>                    
                                    <td>
                                        <a href="manage-chapters.php?id=<?= $story_id ?>&delete=<?= $chapter['chapter_id'] ?>" 
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa chương này?')">
                                            <i class="ti-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            </table>
                        </div>                    
                        <a href="add-chapter.php?story_id=<?= $story_id ?>" class="btn btn-update">Thêm chương</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="plugins/jQuery/jquery.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>