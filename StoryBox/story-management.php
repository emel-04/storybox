<?php
session_start();
require_once 'config/db_connection.php';

// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Khởi tạo biến
$is_logged_in = true;
$admin_info = [];
$stories = [];
$genres = [];
$error = '';
$success = '';
$search_query = '';
$filter_status = 'all';
$filter_genre = 'all';
$sort_order = 'newest';
$total_stories = 0;
$total_pages = 1;
$page = 1;

// Xử lý các thao tác (xóa, cập nhật)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $story_id = $_POST['story_id'] ?? null;
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'delete':
                // Xóa truyện
                $stmt = $pdo->prepare("DELETE FROM stories WHERE story_id = ?");
                $stmt->execute([$story_id]);
                
                // Ghi log hoạt động admin
                $activity_stmt = $pdo->prepare("INSERT INTO admin_activities 
                    (admin_id, activity_type, action, target_id, description, ip_address) 
                    VALUES (?, 'story_management', 'delete', ?, 'Xóa truyện ID: ?', ?)");
                $activity_stmt->execute([
                    $_SESSION['user_id'],
                    $story_id,
                    $story_id,
                    $_SERVER['REMOTE_ADDR']
                ]);
                
                $success = 'Đã xóa truyện thành công';
                break;
                
            case 'update_status':
                // Cập nhật trạng thái truyện
                $new_status = $_POST['status'] ?? 'ongoing';
                $stmt = $pdo->prepare("UPDATE stories SET status = ? WHERE story_id = ?");
                $stmt->execute([$new_status, $story_id]);
                
                // Ghi log hoạt động admin
                $activity_stmt = $pdo->prepare("INSERT INTO admin_activities 
                    (admin_id, activity_type, action, target_id, description, ip_address) 
                    VALUES (?, 'story_management', 'update', ?, 'Cập nhật trạng thái truyện ID: ? thành ' . $new_status, ?)");
                $activity_stmt->execute([
                    $_SESSION['user_id'],
                    $story_id,
                    $story_id,
                    $_SERVER['REMOTE_ADDR']
                ]);
                
                $success = 'Đã cập nhật trạng thái truyện thành công';
                break;
        }
    } catch (PDOException $e) {
        error_log("Lỗi khi xử lý thao tác truyện: " . $e->getMessage());
        $error = 'Đã xảy ra lỗi khi xử lý thao tác';
    }
}

// Lấy tham số tìm kiếm, lọc và phân trang
$search_query = $_GET['s'] ?? '';
$filter_status = $_GET['status'] ?? 'all';
$filter_genre = $_GET['genre'] ?? 'all';
$sort_order = $_GET['sort'] ?? 'newest';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Xây dựng câu truy vấn
try {
    // Lấy danh sách thể loại
    $genres = $pdo->query("SELECT * FROM genres ORDER BY genre_name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Câu truy vấn chính
    $query = "SELECT s.*, GROUP_CONCAT(g.genre_name SEPARATOR ', ') AS genres 
              FROM stories s
              LEFT JOIN story_genres sg ON s.story_id = sg.story_id
              LEFT JOIN genres g ON sg.genre_id = g.genre_id";
    
    $where = [];
    $params = [];
    $having = [];
    
    // Áp dụng bộ lọc trạng thái
    if ($filter_status !== 'all') {
        $where[] = "s.status = ?";
        $params[] = $filter_status;
    }
    
    // Áp dụng bộ lọc thể loại
    if ($filter_genre !== 'all') {
        $where[] = "sg.genre_id = ?";
        $params[] = $filter_genre;
    }
    
    // Áp dụng tìm kiếm
    if (!empty($search_query)) {
        $where[] = "(s.title LIKE ? OR s.author_name LIKE ?)";
        $search_param = "%$search_query%";
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    // Kết hợp điều kiện WHERE
    if (!empty($where)) {
        $query .= " WHERE " . implode(" AND ", $where);
    }
    
    // Group by
    $query .= " GROUP BY s.story_id";
    
    // Áp dụng sắp xếp
    if ($sort_order === 'newest') {
        $query .= " ORDER BY s.created_at DESC";
    } else {
        $query .= " ORDER BY s.created_at ASC";
    }
    
    // Thêm phân trang
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    // Thực hiện truy vấn
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Đếm tổng số truyện cho phân trang
    $count_query = "SELECT COUNT(DISTINCT s.story_id) as total 
                   FROM stories s
                   LEFT JOIN story_genres sg ON s.story_id = sg.story_id";
    
    if (!empty($where)) {
        $count_query .= " WHERE " . implode(" AND ", $where);
    }
    
    $count_stmt = $pdo->prepare($count_query);
    $count_params = array_slice($params, 0, count($params) - 2);
    $count_stmt->execute($count_params);
    $total_stories = $count_stmt->fetchColumn();
    $total_pages = ceil($total_stories / $limit);
    
} catch (PDOException $e) {
    error_log("Lỗi khi lấy danh sách truyện: " . $e->getMessage());
    $error = 'Đã xảy ra lỗi khi tải danh sách truyện';
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
  <title>StoryBox - Truyện</title>
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
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <!-- Tìm kiếm và bộ lọc -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Bộ lọc truyện</h3>
                    </div>
                    <div class="card-body">
                        <div id="filter-row">
                            <form method="GET" action="story-management.php">
                                <div id="search-container">
                                    <input type="search" name="s" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search_query) ?>">
                                </div>
                            </form>
                               
                            <form method="GET" action="story-management.php">
                                <div id="filter-options">
                                    <select class="filter-select" name="status" onchange="this.form.submit()">
                                        <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>Tất cả truyện</option>
                                        <option value="ongoing" <?= $filter_status === 'ongoing' ? 'selected' : '' ?>>Đang tiến hành</option>
                                        <option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="hiatus" <?= $filter_status === 'hiatus' ? 'selected' : '' ?>>Tạm ngưng</option>
                                    </select>
                                    
                                    <select class="filter-select" name="genre" onchange="this.form.submit()">
                                        <option value="all" <?= $filter_genre === 'all' ? 'selected' : '' ?>>Tất cả thể loại</option>
                                        <?php foreach ($genres as $genre): ?>
                                        <option value="<?= $genre['genre_id'] ?>" <?= $filter_genre == $genre['genre_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($genre['genre_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
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
            
                <!-- Danh sách truyện -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Danh sách truyện</h3>                    
                        <button onclick="window.location.href='add-story.php'" class="btn btn-update">Thêm truyện</button>
                        <div class="pagination-info">
                            Hiển thị <?= count($stories) ?> của <?= $total_stories ?> truyện
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="story-grid">
                            <?php foreach ($stories as $story): ?>
                            <div class="story-card">
                                <div class="story-cover">
                                    <img src="<?= htmlspecialchars($story['cover_image_url'] ?? 'images/story-default.jpg') ?>" alt="<?= htmlspecialchars($story['title']) ?>">
                                </div>
                                <div class="story-info">
                                    <h4 class="story-title"><?= htmlspecialchars($story['title']) ?></h4>
                                    <div class="story-meta">
                                        <p><strong>Tác giả:</strong> <?= htmlspecialchars($story['author_name']) ?></p>
                                        <p><strong>Thể loại:</strong> <?= htmlspecialchars($story['genres'] ?? 'Chưa phân loại') ?></p>
                                        <p><strong>Tình trạng:</strong> 
                                            <?php 
                                            $status_text = '';
                                            switch ($story['status']) {
                                                case 'ongoing': $status_text = 'Đang tiến hành'; break;
                                                case 'completed': $status_text = 'Hoàn thành'; break;
                                                case 'hiatus': $status_text = 'Tạm ngưng'; break;
                                                default: $status_text = $story['status'];
                                            }
                                            echo htmlspecialchars($status_text);
                                            ?>
                                        </p>
                                        <p><strong>Lượt xem:</strong> <?= number_format($story['total_views']) ?></p>
                                    </div>
                                </div>
                                <div class="story-actions">
                                    <button class="btn btn-update" onclick="showUpdateOptions(<?= $story['story_id'] ?>)">
                                        <i class="ti-reload"></i> Cập nhật
                                    </button>
                                    <form method="POST" action="stories.php" class="delete-form" onsubmit="return confirmDelete(event, <?= $story['story_id'] ?>)">
                                        <input type="hidden" name="story_id" value="<?= $story['story_id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-delete"><i class="ti-trash"></i> Xóa</button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($stories)): ?>
                                <div class="no-results">Không tìm thấy truyện nào phù hợp</div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Phân trang -->
                        <?php if (!empty($stories)): ?>
                            <?php include 'pagination.php'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal cập nhật -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tùy chọn cập nhật</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-options">
                <a href="#" class="modal-option" id="update-info-option">
                    <i class="ti-pencil-alt"></i> Cập nhật thông tin truyện
                </a>
                <a href="#" class="modal-option" id="update-chapter-option">
                    <i class="ti-list"></i> Quản lý chương truyện
                </a>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
    <div id="deleteModal" class="modal">
        <div class="modal-content confirmation-modal">
            <div class="modal-header">
                <h3>Xác nhận xóa</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <p>Bạn có chắc chắn muốn xóa truyện này? Hành động này không thể hoàn tác.</p>
            <form id="deleteForm" method="POST" action="stories.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="story_id" id="deleteStoryId" value="">
                <div class="confirmation-buttons">
                    <button type="button" class="btn btn-cancel" onclick="closeModal()">Hủy bỏ</button>
                    <button type="submit" class="btn btn-delete">Xóa</button>
                </div>
            </form>
        </div>
    </div>

  <script src="plugins/jQuery/jquery.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>