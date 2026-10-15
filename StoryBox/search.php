<?php
session_start();
require_once 'config/db_connection.php';

// Kiểm tra tham số tìm kiếm
if (!isset($_GET['s'])) {
    header("Location: index.php");
    exit();
}

// Khởi tạo biến
$search_query = trim($_GET['s']);
$search_results = [];

// Chỉ thực hiện tìm kiếm nếu có từ khóa
if (!empty($search_query)) {
    try {
        $stmt = $pdo->prepare("SELECT story_id, title, author_name, cover_image_url 
                              FROM stories 
                              WHERE title LIKE ? OR author_name LIKE ?
                              ORDER BY total_views DESC");
        $search_param = "%$search_query%";
        $stmt->execute([$search_param, $search_param]);
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Lỗi truy vấn: " . $e->getMessage());
    }
}

// Lấy thông tin user
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['username'] ?? 'User';
$user_avatar = $_SESSION['avatar'] ?? 'images/user.png';
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <title>StoryBox - Tìm Kiếm: <?= htmlspecialchars($search_query) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
    <link href="css/style.css" rel="stylesheet">
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
            <li><a href="index.php" class="active">Trang Chủ</a></li>
            <li><a href="story-list.php">Danh Sách Truyện</a></li>
            <li><a href="ranking.php">Bảng Xếp Hạng</a></li>
        </ul>
        <form id="search-bar" action="search.php" method="get">
            <div id="search-container">
                <input type="search" name="s" placeholder="Tìm kiếm ..." required>
            </div>
        </form>
        
        <?php if ($is_logged_in): ?>
        <!-- Hiển thị khi đã đăng nhập -->
            <div id="user-info" class="dropdown">
                <div class="dropdown-toggle" id="userDropdown">
                <img id="user-avatar" src="<?= htmlspecialchars($user_avatar) ?>" alt="User Avatar">
                <span id="user-name"><?= htmlspecialchars($user_name) ?></span>
                <i class="ti-angle-down"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="favourite.php"><i class="ti-bookmark"></i> Yêu thích</a>      
                    <a href="history.php"><i class="ti-time"></i> Lịch sử đọc</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="text-danger"><i class="ti-power-off"></i> Đăng xuất</a>
                </div>
            </div>
            <?php else: ?>
            <!-- Hiển thị khi chưa đăng nhập -->
            <div id="auth-buttons">
                <a href="login.php" class="btn-auth">Đăng Nhập</a>
            </div>
        <?php endif; ?>
    </nav>

    <!-- Content -->
    <main id="main-content">
        <section id="search-results">
            <div class="search-header">
                <h2 id="list-title">KẾT QUẢ TÌM KIẾM: "<?= htmlspecialchars($search_query) ?>"</h2>
            </div>
            
            <?php if (!empty($search_results)): ?>
                <div id="story-grid">
                    <?php foreach ($search_results as $story): ?>
                        <div class="story-item">
                            <a href="story-detail.php?id=<?= $story['story_id'] ?>">
                                <img src="<?= !empty($story['cover_image_url']) ? htmlspecialchars($story['cover_image_url']) : 'images/default-cover.jpg' ?>" 
                                     alt="<?= htmlspecialchars($story['title']) ?>">
                                <div class="story-title"><?= htmlspecialchars($story['title']) ?></div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-results">Không tìm thấy truyện phù hợp với từ khóa "<?= htmlspecialchars($search_query) ?>"</p>
            <?php endif; ?>
        </section>
    </main>

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