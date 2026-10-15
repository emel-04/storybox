<?php
session_start();
require_once 'config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_info = [];
$favorite_stories = [];
$error = '';

try {
    $stmt = $pdo->prepare("SELECT s.story_id, s.title, s.cover_image_url, s.author_name, 
                          s.total_views, s.total_likes, s.status
                          FROM likes l
                          JOIN stories s ON l.story_id = s.story_id
                          WHERE l.user_id = ? AND l.is_saved = TRUE
                          ORDER BY l.created_at DESC");
    $stmt->execute([$user_id]);
    $favorite_stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error in favorite.php: " . $e->getMessage());
    $error = 'Đã xảy ra lỗi khi tải danh sách yêu thích';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    $story_id = $_POST['story_id'];
    
    try {
        $remove_stmt = $pdo->prepare("UPDATE likes SET is_saved = FALSE WHERE user_id = ? AND story_id = ?");
        $remove_stmt->execute([$user_id, $story_id]);
        header("Location: favorite.php");
        exit();
    } catch (PDOException $e) {
        error_log("Database error when removing favorite: " . $e->getMessage());
        $error = 'Đã xảy ra lỗi khi bỏ yêu thích';
    }
}

// Lấy thông tin user
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['username'] ?? 'User';
$user_avatar = $_SESSION['avatar'] ?? 'images/user.png';

$pdo = null;
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <title>StoryBox - Yêu Thích</title>
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
                <a href="index.html">
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
    </nav>

    <!-- Content -->
    <main id="main-content">
        <section id="story-list">
            <h2 id="list-title">TRUYỆN YÊU THÍCH CỦA BẠN</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (empty($favorite_stories)): ?>
                <p>Bạn chưa có truyện yêu thích nào</p>
            <?php else: ?>
                <div id="story-grid">
                    <?php foreach ($favorite_stories as $story): ?>
                        <div class="story-item" onclick="window.location.href='story-detail.php?id=<?= $story['story_id'] ?>'">
                            <form method="post" onclick="event.stopPropagation()">
                                <input type="hidden" name="story_id" value="<?php echo $story['story_id']; ?>">
                                <button type="submit" name="remove_favorite" class="remove-favorite" title="Bỏ yêu thích">X</button>
                            </form>
                            <img src="<?php echo htmlspecialchars($story['cover_image_url'] ?: 'images/default-cover.jpg'); ?>" 
                                alt="<?php echo htmlspecialchars($story['title']); ?>">
                            <div id="story-title"><?php echo htmlspecialchars($story['title']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
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