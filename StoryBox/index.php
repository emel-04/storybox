<?php
session_start();
require_once 'config/db_connection.php';

// Xử lý tìm kiếm nếu có
$search_results = [];
if (isset($_GET['s'])) {
    $search_query = trim($_GET['s']);
    
    try {
        $search_stmt = $pdo->prepare("SELECT story_id, title, author_name, cover_image_url 
                                     FROM stories 
                                     WHERE title LIKE :query OR author_name LIKE :query
                                     LIMIT 10");
        $search_param = "%$search_query%";
        $search_stmt->bindParam(':query', $search_param);
        $search_stmt->execute();
        $search_results = $search_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Search error: " . $e->getMessage());
    }
}

// Kiểm tra và khởi tạo biến
$is_logged_in = false;
$user_info = [];
$featured_stories = [];
$latest_stories = [];

try {
    // Lấy truyện nổi bật
    $featured_query = "SELECT story_id, title, author_name, cover_image_url, total_likes 
                      FROM stories 
                      WHERE status = 'ongoing' OR status = 'completed'
                      ORDER BY total_likes DESC 
                      LIMIT 7";
    $featured_result = $pdo->query($featured_query);
    
    if ($featured_result) {
        $featured_stories = $featured_result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy truyện mới cập nhật
    $latest_query = "SELECT s.story_id, s.title, s.author_name, s.cover_image_url, 
                    COALESCE(MAX(c.updated_at), s.updated_at) as last_update
                    FROM stories s
                    LEFT JOIN chapters c ON s.story_id = c.story_id
                    WHERE s.status = 'ongoing' OR s.status = 'completed'
                    GROUP BY s.story_id
                    ORDER BY last_update DESC
                    LIMIT 8";
    $latest_result = $pdo->query($latest_query);
    
    if ($latest_result) {
        $latest_stories = $latest_result->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Exception $e) {
    error_log("Error in index.php: " . $e->getMessage());
    // Không hiển thị lỗi ra người dùng để tránh lộ thông tin nhạy cảm
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
    <title>StoryBox - Trang Chủ</title>
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
    <!-- Truyện nổi bật -->
    <section id="featured-stories">
      <h2 id="featured-title">TRUYỆN NỔI BẬT</h2>
      <div id="carousel-container">
        <div id="story-carousel">
          <?php foreach ($featured_stories as $story): ?>
            <div class="carousel-item">
              <a href="story-detail.php?id=<?php echo $story['story_id']; ?>">
                <img src="<?php echo htmlspecialchars($story['cover_image_url']); ?>" 
                    alt="<?php echo htmlspecialchars($story['title']); ?>">
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>    
      <button class="carousel-btn prev-btn">❮</button>
      <button class="carousel-btn next-btn">❯</button>
      <div id="carousel-dots" class="dots-container"></div>
    </section>

    <!-- Mới cập nhật -->
    <section id="latest-updates">
      <h2 id="list-title">MỚI CẬP NHẬT</h2>
      <div id="story-grid">
        <?php foreach ($latest_stories as $story): ?>
          <div class="story-item">
            <a href="story-detail.php?id=<?php echo $story['story_id']; ?>">
              <img src="<?php echo htmlspecialchars($story['cover_image_url']); ?>" 
                    alt="<?php echo htmlspecialchars($story['title']); ?>">
              <div id="story-title"><?php echo htmlspecialchars($story['title']); ?></div>
            </a>
          </div>
        <?php endforeach; ?>
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