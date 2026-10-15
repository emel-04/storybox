<?php
session_start();
require_once 'config/db_connection.php';

// Kiểm tra trạng thái đăng nhập
$is_logged_in = false;
$user_info = [];

try {
    // Xử lý lựa chọn thể loại
    $genre_id = isset($_GET['genre']) ? (int)$_GET['genre'] : 0;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 12; // Số truyện hiển thị mỗi trang
    $offset = ($page - 1) * $limit;
    
    // Lấy danh sách thể loại cho dropdown
    $stmt_genres = $pdo->query("SELECT * FROM genres ORDER BY genre_name");
    $genres = $stmt_genres->fetchAll();
    $stmt_genres->closeCursor();
    
    // Lấy danh sách truyện
    if ($genre_id > 0) {
        // Lấy truyện theo thể loại
        $stmt = $pdo->prepare("CALL get_stories_by_genre(?, ?, ?)");
        $stmt->execute([$genre_id, $limit, $offset]);
        $stories = $stmt->fetchAll();
        $stmt->closeCursor();
        
        // Đếm tổng số truyện theo thể loại
        $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM story_genres WHERE genre_id = ?");
        $count_stmt->execute([$genre_id]);
        $total_stories = $count_stmt->fetchColumn();
        $count_stmt->closeCursor();
    } else {
        // Lấy tất cả truyện
        $stmt = $pdo->prepare("SELECT * FROM stories ORDER BY total_views DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        $stories = $stmt->fetchAll();
        $stmt->closeCursor();
        
        // Đếm tổng số truyện
        $count_stmt = $pdo->query("SELECT COUNT(*) FROM stories");
        $total_stories = $count_stmt->fetchColumn();
        $count_stmt->closeCursor();
    }
    
    // Tính tổng số trang cho phân trang
    $total_pages = ceil($total_stories / $limit);
    
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
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>StoryBox - Danh Sách Truyện</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
    <link href="css/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 30px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 7px;
            text-decoration: none;
            color: #333;
        }
        .pagination a.active {
            background-color:rgb(147, 207, 149);
            color: white;
            border: 1px solid rgb(147, 207, 149);
        }
        .no-stories {
            text-align: center;
            padding: 20px;
            grid-column: 1 / -1;
        }
    </style>
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
    <!-- Danh sách truyện -->
    <section id="story-list">
      <h2 id="list-title">DANH SÁCH TRUYỆN</h2>
      <div id="list-dropdown">
        <form method="get" action="story-list.php">
          <select name="genre" onchange="this.form.submit()">
              <option value="0">Tất cả</option>
              <?php foreach ($genres as $genre): ?>
                  <option value="<?= $genre['genre_id'] ?>" <?= ($genre_id == $genre['genre_id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($genre['genre_name']) ?>
                  </option>
              <?php endforeach; ?>
          </select>
          <i class="ti-angle-down"></i>
        </form>
      </div>
      <div id="story-grid">
        <?php if (count($stories) > 0): ?>
          <?php foreach ($stories as $story): ?>
            <div id="story-item">
                <a href="story-detail.php?id=<?= $story['story_id'] ?>">
                    <img src="<?= !empty($story['cover_image_url']) ? htmlspecialchars($story['cover_image_url']) : 'images/default-cover.jpg' ?>" alt="<?= htmlspecialchars($story['title']) ?>">
                    <div id="story-title"><?= htmlspecialchars($story['title']) ?></div>
                </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="no-stories">Không có truyện nào trong thể loại này.</p>
        <?php endif; ?>
      </div>
      
      <!-- Phân trang -->
      <?php if (!empty($stories)): ?>
          <?php include 'pagination.php'; ?>
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