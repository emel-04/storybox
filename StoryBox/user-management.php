<?php
// user-management.php
session_start();
require_once 'config/db_connection.php'; // File cấu hình kết nối database

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Khởi tạo biến
$error = '';
$success = '';
$users = [];
$total_users = 0;
$total_pages = 1;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 9; // Số user mỗi trang

// Xử lý các thao tác xóa/khóa/mở khóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            // Xử lý xóa người dùng
            case 'delete':
                try {
                    $pdo->beginTransaction();
                    
                    // Xóa các bản ghi liên quan
                    $tables = ['reading_history', 'likes', 'comments'];
                    foreach ($tables as $table) {
                        $stmt = $pdo->prepare("DELETE FROM $table WHERE user_id = ?");
                        $stmt->execute([$user_id]);
                    }
                    
                    // Xóa user
                    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    $pdo->commit();
                    
                    // Ghi log
                    $log_stmt = $pdo->prepare("INSERT INTO admin_activities (admin_id, activity_type, action, target_id, description, ip_address)
                                              VALUES (?, 'user_management', 'delete', ?, ?, ?)");
                    $log_stmt->execute(
                        [$_SESSION['user_id'],
                        $user_id,
                        "Xóa người dùng ID: $user_id",
                        $_SERVER['REMOTE_ADDR']
                    ]);
                    
                    $success = 'Đã xóa người dùng và dữ liệu liên quan thành công';
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error = 'Lỗi khi xóa: ' . $e->getMessage();
                }
                break;
                
            case 'lock':
                $stmt = $pdo->prepare("UPDATE users SET is_active = FALSE WHERE user_id = ?");
                $stmt->execute([$user_id]);
                
                $log_stmt = $pdo->prepare("INSERT INTO admin_activities(admin_id, activity_type, action, target_id, description, ip_address) 
                                           VALUES (?, 'user_management', 'update', ?, ?, ?)");
                $log_stmt->execute([
                    $_SESSION['user_id'],
                    $user_id,
                    "Khóa người dùng ID: $user_id",
                    $_SERVER['REMOTE_ADDR']
                ]);
                
                $success = 'Đã khóa tài khoản thành công';
                break;
                
            case 'unlock':
                $stmt = $pdo->prepare("UPDATE users SET is_active = TRUE WHERE user_id = ?");
                $stmt->execute([$user_id]);
                
                $log_stmt = $pdo->prepare("INSERT INTO admin_activities(admin_id, activity_type, action, target_id, description, ip_address) 
                                           VALUES (?, 'user_management', 'update', ?, ?, ?)");
                $log_stmt->execute([
                    $_SESSION['user_id'],
                    $user_id,
                    "Mở khóa người dùng ID: $user_id",
                    $_SERVER['REMOTE_ADDR']
                ]);
                
                $success = 'Đã mở khóa tài khoản thành công';
                break;
        }
    } catch (PDOException $e) {
        $error = 'Lỗi khi xử lý thao tác: ' . $e->getMessage();
    }
}

// Xử lý bộ lọc và phân trang
$search_query = $_GET['s'] ?? '';
$filter_status = $_GET['status'] ?? 'all';
$sort_order = $_GET['sort'] ?? 'newest';

// Xây dựng câu truy vấn
$query = "SELECT user_id, username, email, avatar_url, is_active, 
          DATE_FORMAT(created_at, '%d/%m/%Y') as join_date 
          FROM users WHERE role = 'user'";
$count_query = "SELECT COUNT(*) FROM users WHERE role = 'user'";
$params = [];
$where = [];

// Áp dụng bộ lọc trạng thái
if ($filter_status === 'active') {
    $where[] = "is_active = TRUE";
} elseif ($filter_status === 'locked') {
    $where[] = "is_active = FALSE";
}

// Áp dụng tìm kiếm
if (!empty($search_query)) {
    $where[] = "(username LIKE ? OR email LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
}

// Kết hợp điều kiện WHERE
$query .= !empty($where) ? " AND " . implode(" AND ", $where) : "";
$count_query .= !empty($where) ? " AND " . implode(" AND ", $where) : "";

// Áp dụng sắp xếp
if ($sort_order === 'newest') {
    $query .= " ORDER BY created_at DESC";
} else {
    $query .= " ORDER BY created_at ASC";
}

// Thêm phân trang
$query .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = ($page - 1) * $limit;

// Thực hiện truy vấn
try {
    // Đếm tổng số user
    $stmt = $pdo->prepare($count_query);
    $stmt->execute(array_slice($params, 0, count($params) - 2));
    $total_users = $stmt->fetchColumn();
    $total_pages = ceil($total_users / $limit);

    // Lấy danh sách user
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Lỗi khi tải danh sách người dùng: ' . $e->getMessage();
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
  <title>StoryBox - Người Dùng</title>
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
          <li id="nav-users"  class="active"><a href="user-management.php"><i class="ti-user"></i> Quản lý User</a></li>
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
          <h1>Quản Lý Người Dùng</h1>
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
        <?php if ($error): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Tìm kiếm  và bộ lọc -->
        <div class="content-card">
          <div class="card-header">
            <h3>Bộ lọc người dùng</h3>
          </div>
          <div class="card-body">
            <div id="filter-row">
              <form method="GET" action="user-management.php">
                <div id="search-container">
                  <input type="search" name="s" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search_query) ?>">
                </div> 
              </form>
              
              <form method="GET" action="user-management.php">
                <div id="filter-options">
                  <select class="filter-select" name="status" onchange="this.form.submit()">
                    <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>Tất cả người dùng</option>
                    <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="locked" <?= $filter_status === 'locked' ? 'selected' : '' ?>>Bị khóa</option>
                  </select>
                  <select class="filter-select" name="sort" onchange="this.form.submit()">
                    <option value="newest" <?= $sort_order === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="oldest" <?= $sort_order === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                  </select>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Danh sách người dùng -->
        <div class="content-card">
          <div class="card-header">
            <h3>Danh sách người dùng</h3>
            <div class="pagination-info">
              Hiển thị <?= count($users) ?> của <?= $total_users ?> người dùng
            </div>
          </div>
          <div class="card-body">
            <div id="user-grid">
              <?php foreach ($users as $user): ?>
                <div class="user-card">
                  <div class="user-avatar">
                    <img src="<?= htmlspecialchars($user['avatar_url'] ?? 'images/avatar.png') ?>" alt="User Avatar">
                    <span class="user-status <?= $user['is_active'] ? 'active' : 'locked' ?>"></span>
                  </div>
                  <div class="user-info">
                    <h4><?= htmlspecialchars($user['username']) ?></h4>
                    <p class="user-email"><?= htmlspecialchars($user['email']) ?></p>
                    <div class="user-meta">
                      <span class="user-join">Tham gia: <?= htmlspecialchars($user['join_date']) ?></span>
                    </div>
                  </div>
                  <div class="user-actions">
                    <form method="POST" action="user-management.php">
                      <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                      <input type="hidden" name="action" value="delete">
                      <button type="submit" class="btn btn-delete"><i class="ti-trash"></i> Xóa</button>
                    </form>
                    
                    <?php if ($user['is_active']): ?>
                      <form method="POST" action="user-management.php">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                        <input type="hidden" name="action" value="lock">
                        <button type="submit" class="btn btn-lock"><i class="ti-lock"></i> Khóa</button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="user-management.php">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                        <input type="hidden" name="action" value="unlock">
                        <button type="submit" class="btn btn-unlock"><i class="ti-unlock"></i> Mở khóa</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
              
              <?php if (empty($users)): ?>
                <div class="no-results">Không tìm thấy người dùng nào phù hợp</div>
              <?php endif; ?>
            </div>

            <!-- Phân trang -->             
            <?php if (!empty($user)): ?>
                <?php include 'pagination.php'; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="plugins/jQuery/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>