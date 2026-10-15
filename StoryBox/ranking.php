<?php
session_start();
require_once 'config/db_connection.php';

// Xác định tham số sắp xếp (views hoặc saves)
$sort_by = isset($_GET['sort']) && in_array($_GET['sort'], ['views', 'saves']) ? $_GET['sort'] : 'views';

try {
    // Lấy top 10 truyện theo tiêu chí sắp xếp
    if ($sort_by === 'saves') {
        $stmt = $pdo->prepare("SELECT s.* FROM stories s
                              ORDER BY s.total_likes DESC
                              LIMIT 10");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM stories 
                              ORDER BY total_views DESC
                              LIMIT 10");
    }
    
    $stmt->execute();
    $stories = $stmt->fetchAll();
    $stmt->closeCursor();
    
} catch(PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
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
    <title>StoryBox - Bảng Xếp Hạng</title>
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
        <!-- Bảng xếp hạng -->
        <section id="story-list">
            <h2 id="list-title">BẢNG XẾP HẠNG</h2>
            
            <div id="list-dropdown">
                <form method="get" action="ranking.php">
                    <select name="sort" onchange="this.form.submit()">
                        <option value="views" <?php echo $sort_by === 'views' ? 'selected' : ''; ?>>Lượt xem</option>
                        <option value="saves" <?php echo $sort_by === 'saves' ? 'selected' : ''; ?>>Lượt lưu</option>
                    </select>
                    <button type="submit"><i class="ti-angle-down"></i></button>
                </form>
            </div>
            
            <div id="ranking-list">
                <!-- Top 3 -->
                <div class="top-three">
                    <?php if (isset($stories[1])): ?>
                    <div class="rank-card second">
                        <a href="story-detail.php?id=<?= $stories[1]['story_id'] ?>">
                            <div class="rank-image-wrapper">
                                <img src="<?php echo htmlspecialchars($stories[1]['cover_image_url'] ?: 'images/default.jpg'); ?>" alt="Top 2">
                                <span class="rank-number">2</span>
                            </div>
                            <h4><?php echo htmlspecialchars($stories[1]['title']); ?></h4>
                            <p><?php echo $sort_by === 'saves' ? number_format($stories[1]['total_likes']) . ' lượt lưu' : number_format($stories[1]['total_views']) . ' lượt xem'; ?></p>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($stories[0])): ?>
                    <div class="rank-card first">
                        <a href="story-detail.php?id=<?= $stories[0]['story_id'] ?>">
                            <div class="rank-image-wrapper">
                                <img src="<?php echo htmlspecialchars($stories[0]['cover_image_url'] ?: 'images/default.jpg'); ?>" alt="Top 1">
                                <span class="rank-number">1</span>
                            </div>
                            <h3><?php echo htmlspecialchars($stories[0]['title']); ?></h3>
                            <p><?php echo $sort_by === 'saves' ? number_format($stories[0]['total_likes']) . ' lượt lưu' : number_format($stories[0]['total_views']) . ' lượt xem'; ?></p>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($stories[2])): ?>
                    <div class="rank-card third">
                        <a href="story-detail.php?id=<?= $stories[2]['story_id'] ?>">
                            <div class="rank-image-wrapper">
                                <img src="<?php echo htmlspecialchars($stories[2]['cover_image_url'] ?: 'images/default.jpg'); ?>" alt="Top 3">
                                <span class="rank-number">3</span>
                            </div>
                            <h4><?php echo htmlspecialchars($stories[2]['title']); ?></h4>
                            <p><?php echo $sort_by === 'saves' ? number_format($stories[2]['total_likes']) . ' lượt lưu' : number_format($stories[2]['total_views']) . ' lượt xem'; ?></p>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>                  
                
                <!-- Từ top 4 đến top 10 -->
                <div class="ranking-list">
                    <?php for ($i = 3; $i < min(10, count($stories)); $i++): ?>
                    <div class="rank-item">
                        <a href="story-detail.php?id=<?= $stories[$i]['story_id'] ?>">
                            <span class="rank-number"><?php echo $i + 1; ?></span>
                            <img src="<?php echo htmlspecialchars($stories[$i]['cover_image_url'] ?: 'images/default.jpg'); ?>" alt="Top <?php echo $i + 1; ?>">
                            <div class="info">
                                <h5><?php echo htmlspecialchars($stories[$i]['title']); ?></h5>
                                <p><?php echo $sort_by === 'saves' ? number_format($stories[$i]['total_likes']) . ' lượt lưu' : number_format($stories[$i]['total_views']) . ' lượt xem'; ?></p>
                            </div>
                        </a>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
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