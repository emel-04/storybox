<?php
session_start();
// Kết nối database
require_once 'config/db_connection.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Khởi tạo biến thống kê
$stats = [
    'active_users' => 0,
    'total_likes' => 0,
    'total_comments' => 0,
    'recent_activities' => [],
    'admin_name' => $_SESSION['username'] ?? 'Admin',
    'admin_avt' => $_SESSION['avatar_url'] ?? 'Admin'
];

try {
    // Lấy số lượng người dùng hoạt động (đăng nhập trong 30 ngày gần nhất)
    $query = "SELECT COUNT(*) FROM users WHERE is_active = TRUE AND role = 'user'";
    $stmt = $pdo->query($query);
    $stats['active_users'] = $stmt->fetchColumn();

    // Lấy tổng số lượt thích
    $query = "SELECT SUM(total_likes) as total_likes FROM stories";
    $stmt = $pdo->query($query);
    $stats['total_likes'] = $stmt->fetchColumn();

    // Lấy tổng số bình luận
    $query = "SELECT SUM(total_comments) as total_comments FROM stories";
    $stmt = $pdo->query($query);
    $stats['total_comments'] = $stmt->fetchColumn();

    // Lấy hoạt động gần đây từ admin_activities
    $query = "SELECT 
                a.activity_id,
                u.username AS admin_name,
                a.activity_type,
                a.action,
                a.target_id,
                a.description,
                a.created_at
              FROM admin_activities a
              JOIN users u ON a.admin_id = u.user_id
              ORDER BY a.created_at DESC
              LIMIT 10";
    $stmt = $pdo->query($query);
    $stats['recent_activities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Xử lý lỗi nếu có
    die("Lỗi khi truy vấn dữ liệu: " . $e->getMessage());
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
  <title>StoryBox - Admin</title>
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
          <li id="nav-dashboard" class="active"><a href="index-admin.php"><i class="ti-dashboard"></i>Dashboard</a></li>
          <li id="nav-users"><a href="user-management.php"><i class="ti-user"></i> Quản lý User</a></li>
          <li id="nav-stories"><a href="story-management.php"><i class="ti-book"></i> Quản lý Truyện</a></li>
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
          <h1>Dashboard</h1>
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
        <!-- Thống kê chung -->
        <div id="stats-row"> 
          <div id="stat-active-users" class="stat-card">
            <div class="stat-icon bg-green">
              <i class="ti-user"></i>
            </div>
            <div class="stat-info">
              <h3><?php echo number_format($stats['active_users']); ?></h3>
              <p>Người dùng hoạt động</p>
            </div>
          </div>
          <div id="stat-likes" class="stat-card">
            <div class="stat-icon bg-pink">
              <i class="ti-heart"></i>
            </div>
            <div class="stat-info">
              <h3 id="total-likes-count"><?php echo number_format($stats['total_likes']); ?></h3>
              <p>Lượt lưu</p>
            </div>
          </div>          
          <div id="stat-comments" class="stat-card">
            <div class="stat-icon bg-orange">
              <i class="ti-comment"></i>
            </div>
            <div class="stat-info">
              <h3><?php echo number_format($stats['total_comments']); ?></h3>
              <p>Bình luận</p>
            </div>
          </div>
        </div>

        <!-- Hoạt động gần đây -->
        <div id="recent-activity-col">
          <div id="recent-activity-card" class="content-card">
            <div class="card-header">
              <h3>Hoạt động gần đây</h3>
            </div>
            <div class="card-body">
              <table id="activity-table" class="admin-table">
                <thead>
                  <tr>
                    <th>Thời gian</th>
                    <th>Loại hoạt động</th>
                    <th>Nội dung</th>
                    <th>Quản trị viên</th>
                    <th>Trạng thái</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($stats['recent_activities'] as $activity): ?>
                  <tr>
                    <td><?php echo date('H:i - d/m/Y', strtotime($activity['created_at'])); ?></td>
                    <td><?php echo $activity['action']; ?></td>                    
                    <td><?php echo htmlspecialchars($activity['description']); ?></td>
                    <td><?php echo htmlspecialchars($activity['admin_name']); ?></td>
                    <td><span class="status-badge paid">Hoàn thành</span></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="plugins/jQuery/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>