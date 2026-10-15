<?php
session_start();
require_once 'config/db_connection.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_info = [];
$reading_history = [];
$error = '';

try {
    // Lấy thông tin user
    $user_stmt = $pdo->prepare("SELECT username, avatar_url FROM users WHERE user_id = ?");
    $user_stmt->execute([$user_id]);
    $user_info = $user_stmt->fetch(PDO::FETCH_ASSOC);

    // Lấy lịch sử đọc
    $history_stmt = $pdo->prepare("SELECT h.*, s.title AS story_title, 
                                 c.chapter_number, c.title AS chapter_title,
                                 DATE_FORMAT(h.last_read_at, '%H:%i - %d/%m/%Y') AS formatted_time
                                 FROM reading_history h
                                 LEFT JOIN stories s ON h.story_id = s.story_id
                                 LEFT JOIN chapters c ON h.chapter_id = c.chapter_id
                                 WHERE h.user_id = ?
                                 ORDER BY h.last_read_at DESC
                                 LIMIT 50");
    $history_stmt->execute([$user_id]);
    $reading_history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error in history.php: " . $e->getMessage());
    $error = 'Đã xảy ra lỗi khi tải lịch sử đọc';
}

// Lấy thông tin user
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['username'] ?? 'User';
$user_avatar = $_SESSION['avatar'] ?? 'images/user.png';

$pdo = null;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>StoryBox - Lịch Sử Đọc</title>
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
        <h2 id="list-title">LỊCH SỬ ĐỌC</h2>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php elseif (empty($reading_history)): ?>
            <p>Bạn chưa có lịch sử đọc truyện nào</p>
        <?php else: ?>
            <div id="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Tên truyện</th>
                            <th>Chương</th>
                            <th>Đi đến</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reading_history as $history): ?>
                        <tr>
                            <td><?= htmlspecialchars($history['formatted_time']) ?></td>
                            <td><?= !empty($history['story_title']) ? htmlspecialchars($history['story_title']) : '-' ?></td>
                            <td>
                                <?php if (!empty($history['chapter_number'])): ?>
                                    Chương <?= $history['chapter_number'] ?>
                                    <?= !empty($history['chapter_title']) ? ': ' . htmlspecialchars($history['chapter_title']) : '' ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($history['chapter_id'])): ?>
                                    <a href="chapter.php?id=<?= $history['chapter_id'] ?>">Xem tiếp</a>
                                <?php elseif (!empty($history['story_id'])): ?>
                                    <a href="story-detail.php?id=<?= $history['story_id'] ?>">Xem tiếp</a>
                                <?php else: ?>
                                    <a href="story-list.php">Xem tiếp</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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