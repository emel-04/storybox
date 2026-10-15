<?php
session_start();
require_once 'config/db_connection.php';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Khởi tạo biến
$story = [];
$chapters = [];
$genres = [];
$is_following = false;
$follow_count = 0;
$error = '';
$user_info = [];
$user_id = $_SESSION['user_id'] ?? null;
$is_logged_in = isset($_SESSION['user_id']);

// Kiểm tra ID truyện
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: story-list.php");
    exit();
}

$story_id = (int)$_GET['id'];

try {
    // Lấy thông tin truyện
    $stmt = $pdo->prepare("SELECT s.*, 
                          (SELECT COUNT(*) FROM likes WHERE story_id = s.story_id) AS follow_count
                          FROM stories s 
                          WHERE s.story_id = ?");
    $stmt->execute([$story_id]);
    $story = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$story) {
        $error = 'Truyện không tồn tại';
    } else {
        // Lấy danh sách chương
        $chapter_stmt = $pdo->prepare("SELECT chapter_id, chapter_number, title, 
                                      DATE_FORMAT(updated_at, '%d/%m/%Y') AS update_date,
                                      DATEDIFF(NOW(), updated_at) AS days_ago
                                      FROM chapters 
                                      WHERE story_id = ? 
                                      ORDER BY chapter_number DESC");
        $chapter_stmt->execute([$story_id]);
        $chapters = $chapter_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy thể loại
        $genre_stmt = $pdo->prepare("SELECT g.genre_id, g.genre_name 
                                    FROM story_genres sg
                                    JOIN genres g ON sg.genre_id = g.genre_id
                                    WHERE sg.story_id = ?");
        $genre_stmt->execute([$story_id]);
        $genres = $genre_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra user đã follow truyện này chưa
        if ($is_logged_in) {
            $follow_stmt = $pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND story_id = ?");
            $follow_stmt->execute([$user_id, $story_id]);
            $is_following = $follow_stmt->fetch() ? true : false;
            
            // Cập nhật lịch sử đọc nếu có chapter_id
            if (isset($_GET['chapter'])) {
                $chapter_id = (int)$_GET['chapter'];
                $history_stmt = $pdo->prepare("INSERT INTO reading_history 
                                             (user_id, story_id, chapter_id, last_read_at) 
                                             VALUES (?, ?, ?, NOW())
                                             ON DUPLICATE KEY UPDATE 
                                             last_read_at = NOW()");
                $history_stmt->execute([$user_id, $story_id, $chapter_id]);
            }
        }
        
        // Lấy số lượt follow
        $follow_count = $story['follow_count'];
        unset($story['follow_count']);
    }
} catch (PDOException $e) {
    error_log("Database error in story-detail.php: " . $e->getMessage());
    $error = 'Đã xảy ra lỗi khi tải thông tin truyện';
}

// Xử lý follow/unfollow
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $is_logged_in) {
  if (!$is_logged_in) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
  }

  // Kiểm tra CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      http_response_code(403);
      die(json_encode(['error' => 'Invalid CSRF token']));
  }

  // Lấy user_id từ session
  $user_id = $_SESSION['user_id'];
  
  try {
      $pdo->beginTransaction();
      
      if ($_POST['action'] === 'follow') {
          // Kiểm tra đã follow chưa để tránh duplicate
          $check_stmt = $pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND story_id = ?");
          $check_stmt->execute([$user_id, $story_id]);
          
          if (!$check_stmt->fetch()) {
              $stmt = $pdo->prepare("INSERT INTO likes (user_id, story_id) VALUES (?, ?)");
              $stmt->execute([$user_id, $story_id]);
              $follow_count++;
              $is_following = true;
          }
      } elseif ($_POST['action'] === 'unfollow') {
          $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND story_id = ?");
          $stmt->execute([$user_id, $story_id]);
          $follow_count--;
          $is_following = false;
      }
      
      // Cập nhật số lượt thích (sử dụng câu lệnh SQL tối ưu)
      $update_stmt = $pdo->prepare("UPDATE stories 
                                   SET total_likes = (SELECT COUNT(*) FROM likes WHERE story_id = ?) 
                                   WHERE story_id = ?");
      $update_stmt->execute([$story_id, $story_id]);
      
      $pdo->commit();
      
      // Trả về JSON response
      if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
          header('Content-Type: application/json');
          echo json_encode([
              'success' => true,
              'is_following' => $is_following,
              'follow_count' => $follow_count,
              'new_token' => generateCsrfToken() // Tạo token mới sau mỗi request
          ]);
          exit();
      }
  } catch (PDOException $e) {
      $pdo->rollBack();
      error_log("Database error in follow action: " . $e->getMessage());
      
      if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
          http_response_code(500);
          echo json_encode(['error' => 'Database error']);
          exit();
      }
  }
}

function generateCsrfToken() {
  if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

// Lấy thông tin user
$user_name = $_SESSION['username'] ?? 'User';
$user_avatar = $_SESSION['avatar'] ?? 'images/user.png';

$pdo = null;
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <title>StoryBox - <?= htmlspecialchars($story['title'] ?? 'Chi tiết truyện') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/story-style.css" rel="stylesheet">
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
      <div id="auth-buttons">
          <a href="login.php" class="btn-auth">Đăng Nhập</a>
      </div>
    <?php endif; ?>
  </nav>

  <!-- Content -->
  <main id="main-content">
    <!-- Thông tin truyện -->  
    <div id="story-header">
      <img src="<?= htmlspecialchars($story['cover_image_url'] ?? 'images/default-cover.jpg') ?>" alt="<?= htmlspecialchars($story['title']) ?>">
      <ul id="meta-list">          
        <h1 id="story-title"><?= htmlspecialchars($story['title']) ?></h1>
        <li>
          <i class="ti-user"> <strong>Tác giả:</strong></i>
          <span><?= htmlspecialchars($story['author_name']) ?></span>
        </li>
        <li>
          <i class="ti-receipt"> <strong>Tình trạng:</strong></i>
          <span><?= 
            $story['status'] === 'ongoing' ? 'Đang tiến hành' : 
            ($story['status'] === 'completed' ? 'Đã hoàn thành' : 'Tạm ngưng')
          ?></span>
        </li>
        <li>
          <i class="ti-tag"> <strong>Thể loại:</strong></i>
          <?php foreach ($genres as $genre): ?>
            <a href="story-list.php?genre=<?= $genre['genre_id'] ?>"><span class="genre-tag"><?= htmlspecialchars($genre['genre_name']) ?></span></a>
          <?php endforeach; ?>
        </li>
        <li>
          <i class="ti-eye"> <strong>Lượt xem:</strong></i>
          <span><?= number_format($story['total_views']) ?></span>
        </li>
        <li id="follow-container">
          <?php if ($is_logged_in): ?>
            <form id="follow-form" method="POST">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
              <input type="hidden" name="action" value="<?= $is_following ? 'unfollow' : 'follow' ?>">
              <button type="submit" id="story-button" style="background-color:rgb(226, 63, 63);" class="<?= $is_following ? 'followed' : 'not-followed' ?>">
                  <?= $is_following ? '❤︎ Đang theo dõi' : '+ Theo dõi' ?>
              </button>
            </form>
          <?php else: ?>
            <a href="login.php"><button id="story-button" style="background-color:rgb(226, 63, 63);">+ Theo dõi</button></a>
          <?php endif; ?>
          <div id="follow-count"><strong><?= number_format($follow_count) ?></strong>Người Đã Yêu Thích</div>
        </li>
        <li>
          <?php if (!empty($chapters)): ?>
            <a href="chapter.php?id=<?= $chapters[count($chapters)-1]['chapter_id'] ?>"><button id="story-button" style="margin-right: 20px;">Đọc chương đầu</button></a>
            <a href="chapter.php?id=<?= $chapters[0]['chapter_id'] ?>"><button id="story-button">Đọc chương cuối</button></a>
          <?php endif; ?>
        </li>
      </ul>
    </div>

    <!-- Tóm tắt và danh sách chương truyện --> 
    <div id="story-container"> 
      <div id="story-description">
          <h3 id="subject">TÓM TẮT NỘI DUNG TRUYỆN</h3>
          <p><?= nl2br(htmlspecialchars($story['description'])) ?></p>
      </div>

      <div id="chapter-list">
          <h3 id="subject">DANH SÁCH CHƯƠNG</h3>
          <table id="chapter-table">
              <thead>
                  <tr>
                      <th>Số chương</th>
                      <th>Cập nhật</th>
                  </tr>
              </thead>
              <tbody>
                <?php foreach ($chapters as $chapter): ?>
                    <tr>
                        <td><a href="chapter.php?id=<?= $chapter['chapter_id'] ?>" id="chapter-link">Chương <?= $chapter['chapter_number'] ?> <?= htmlspecialchars($chapter['title']) ?></a></td>
                        <td id="chapter-date">
                            <?= $chapter['days_ago'] == 0 ? 'Hôm nay' : 
                              ($chapter['days_ago'] == 1 ? 'Hôm qua' : 
                              $chapter['days_ago'] . ' ngày trước') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>  
              </tbody>
          </table>
      </div>
    </div>
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