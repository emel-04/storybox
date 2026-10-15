<?php
session_start();
require_once 'config/db_connection.php';

// Khởi tạo biến
$chapter = [];
$story = [];
$comments = [];
$user_info = [];
$is_logged_in = false;
$error = '';

// Kiểm tra chapter_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$chapter_id = (int)$_GET['id'];

try {
    // Lấy thông tin chapter và story
    $stmt = $pdo->prepare("SELECT c.*, s.story_id, s.title AS story_title, s.author_name 
                          FROM chapters c
                          JOIN stories s ON c.story_id = s.story_id
                          WHERE c.chapter_id = ?");
    $stmt->execute([$chapter_id]);
    $chapter = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chapter) {
        $error = 'Chương truyện không tồn tại';
    } else {
        // Lấy danh sách chapter cùng story để dropdown
        $chapters_stmt = $pdo->prepare("SELECT chapter_id, chapter_number, title 
                                       FROM chapters 
                                       WHERE story_id = ? 
                                       ORDER BY chapter_number DESC");
        $chapters_stmt->execute([$chapter['story_id']]);
        $all_chapters = $chapters_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy chapter trước và sau
        $prev_stmt = $pdo->prepare("SELECT chapter_id FROM chapters 
                                   WHERE story_id = ? AND chapter_number < ? 
                                   ORDER BY chapter_number DESC LIMIT 1");
        $prev_stmt->execute([$chapter['story_id'], $chapter['chapter_number']]);
        $prev_chapter = $prev_stmt->fetch(PDO::FETCH_ASSOC);

        $next_stmt = $pdo->prepare("SELECT chapter_id FROM chapters 
                                   WHERE story_id = ? AND chapter_number > ? 
                                   ORDER BY chapter_number ASC LIMIT 1");
        $next_stmt->execute([$chapter['story_id'], $chapter['chapter_number']]);
        $next_chapter = $next_stmt->fetch(PDO::FETCH_ASSOC);

        // Lấy bình luận
        $comment_stmt = $pdo->prepare("SELECT c.*, u.username, u.avatar_url 
                                      FROM comments c
                                      JOIN users u ON c.user_id = u.user_id
                                      WHERE c.chapter_id = ? AND c.parent_comment_id IS NULL
                                      ORDER BY c.created_at DESC");
        $comment_stmt->execute([$chapter_id]);
        $comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy phản hồi cho từng bình luận
        foreach ($comments as &$comment) {
            $reply_stmt = $pdo->prepare("SELECT r.*, u.username, u.avatar_url 
                                       FROM comments r
                                       JOIN users u ON r.user_id = u.user_id
                                       WHERE r.parent_comment_id = ?
                                       ORDER BY r.created_at ASC");
            $reply_stmt->execute([$comment['comment_id']]);
            $comment['replies'] = $reply_stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Cập nhật view count nếu user đã đọc hơn 90% chapter
        if ($is_logged_in) {
            $history_stmt = $pdo->prepare("INSERT INTO reading_history 
                                         (user_id, story_id, chapter_id, last_read_at, progress) 
                                         VALUES (?, ?, ?, NOW(), 1)
                                         ON DUPLICATE KEY UPDATE 
                                         last_read_at = NOW(), progress = 1");
            $history_stmt->execute([$user_id, $chapter['story_id'], $chapter_id]);
        }
    }
} catch (PDOException $e) {
    error_log("Database error in chapter.php: " . $e->getMessage());
    $error = 'Đã xảy ra lỗi khi tải chương truyện';
}

// Xử lý thêm bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && $is_logged_in) {
    $comment_content = trim($_POST['comment']);
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    
    if (!empty($comment_content)) {
        try {
            $insert_stmt = $pdo->prepare("INSERT INTO comments 
                                         (user_id, story_id, chapter_id, parent_comment_id, content, created_at) 
                                         VALUES (?, ?, ?, ?, ?, NOW())");
            $insert_stmt->execute([
                $user_id,
                $chapter['story_id'],
                $chapter_id,
                $parent_id,
                $comment_content
            ]);
            
            // Cập nhật số comment trong story
            $update_stmt = $pdo->prepare("UPDATE stories SET total_comments = total_comments + 1 WHERE story_id = ?");
            $update_stmt->execute([$chapter['story_id']]);
            
            // Reload trang để hiển thị bình luận mới
            header("Location: chapter.php?id=" . $chapter_id);
            exit();
        } catch (PDOException $e) {
            error_log("Error adding comment: " . $e->getMessage());
            $error = 'Có lỗi khi thêm bình luận';
        }
    }
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
    <title>StoryBox - <?= htmlspecialchars($chapter['title'] ?? 'Đọc truyện') ?></title>
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
    <?php if ($error): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($chapter): ?>
      <a href="story-detail.php?id=<?= $chapter['story_id'] ?>" style="text-decoration: none;"><h1 id="story-title"><?= htmlspecialchars($chapter['story_title']) ?></h1></a>
      <div id="navigation">
        <div id="chapter-selector">
          <div id="list-dropdown">
              <select onchange="window.location.href='chapter.php?id='+this.value">
                  <?php foreach ($all_chapters as $chap): ?>
                      <option value="<?= $chap['chapter_id'] ?>" <?= ($chap['chapter_id'] == $chapter_id) ? 'selected' : '' ?>>
                          Chương <?= $chap['chapter_number'] ?> <?= htmlspecialchars($chap['title']) ?>
                      </option>
                  <?php endforeach; ?>
              </select>
              <i class="ti-angle-down"></i>
          </div>
        </div>
        <div class="nav-buttons">
          <?php if (!empty($prev_chapter)): ?>
            <a href="chapter.php?id=<?= $prev_chapter['chapter_id'] ?>"><button id="story-button"><i class="ti-arrow-left"></i></button></a>
          <?php endif; ?>
          <?php if (!empty($next_chapter)): ?>
            <a href="chapter.php?id=<?= $next_chapter['chapter_id'] ?>"><button id="story-button"><i class="ti-arrow-right"></i></button></a>
          <?php endif; ?>
        </div>
      </div>
      <div id="story-container">
        <div id="story-chapter">
          <?= nl2br(htmlspecialchars($chapter['content'])) ?>
        </div>
      </div>
    <?php endif; ?>
    <div id="comment-section">
      <h3>Bình luận</h3>      
      <!-- Form để viết bình luận mới -->
      <?php if ($is_logged_in): ?>
      <div id="comment-form">
        <div class="comment-avatar">
          <img src="<?= htmlspecialchars($user_avatar) ?>" alt="User Avatar">
        </div>
        <form method="POST" class="comment-input">
          <textarea name="comment" placeholder="Viết bình luận của bạn..." required></textarea>
          <button type="submit">Gửi</button>
        </form>
      </div>
      <?php else: ?>
      <div class="login-to-comment">
        <a href="login.php">Đăng nhập</a> để bình luận
      </div>
      <?php endif; ?>
      
      <!-- Danh sách bình luận -->
      <div id="comment-list">
        <?php foreach ($comments as $comment): ?>
        <!-- Bình luận chính -->
          <div class="comment-item">
            <div class="comment-avatar">
              <img src="<?= htmlspecialchars($comment['avatar_url'] ?? 'images/avatar.png') ?>" alt="<?= htmlspecialchars($comment['username']) ?>">
            </div>
            <div class="comment-content">
              <div class="comment-header">
                <span class="comment-author"><?= htmlspecialchars($comment['username']) ?></span>
                <span class="comment-time"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></span>
              </div>
              <div class="comment-text">
                <?= nl2br(htmlspecialchars($comment['content'])) ?>
              </div>
              <div class="comment-actions">
                <a href="#"><i class="ti-heart"></i> Thích</a>
                <a href="#" class="reply-btn" data-comment-id="<?= $comment['comment_id'] ?>"><i class="ti-comment"></i> Trả lời</a>
              </div>
              
              <!-- Form trả lời (ẩn) -->
              <div class="reply-form" id="reply-form-<?= $comment['comment_id'] ?>" style="display:none;">
                <div class="comment-avatar">
                  <img src="<?= $is_logged_in ? htmlspecialchars($user_info['avatar_url'] ?? 'images/avatar.png') : 'images/avatar.png' ?>" alt="User Avatar">
                </div>
                <form method="POST" class="comment-input">
                  <input type="hidden" name="parent_id" value="<?= $comment['comment_id'] ?>">
                  <textarea name="comment" placeholder="Viết phản hồi..." required></textarea>
                  <button type="submit">Gửi</button>
                  <button type="button" class="cancel-reply">Hủy</button>
                </form>
              </div>
              
              <!-- Phản hồi bình luận -->
              <?php foreach ($comment['replies'] as $reply): ?>
                <div class="comment-reply">
                  <div class="comment-item">
                    <div class="comment-avatar">
                      <img src="<?= htmlspecialchars($reply['avatar_url'] ?? 'images/avatar.png') ?>" alt="<?= htmlspecialchars($reply['username']) ?>">
                    </div>
                    <div class="comment-content">
                      <div class="comment-header">
                        <span class="comment-author"><?= htmlspecialchars($reply['username']) ?></span>
                        <span class="comment-time"><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?></span>
                      </div>
                      <div class="comment-text">
                        <?= nl2br(htmlspecialchars($reply['content'])) ?>
                      </div>
                      <div class="comment-actions">
                        <a href="#"><i class="ti-heart"></i> Thích</a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?> 
        
        <?php if (empty($comments)): ?>
        <p class="no-comments">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
        <?php endif; ?>
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