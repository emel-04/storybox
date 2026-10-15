<?php
session_start();
require_once 'config/db_connection.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit();
}

try {    
    // Lấy các thống kê chính
    $stats = [
        'active_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = TRUE AND role = 'user'")->fetchColumn(),
        'banned_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = FALSE")->fetchColumn(),
        'total_likes' => $pdo->query("SELECT COUNT(*) FROM likes")->fetchColumn(),
        'total_comments' => $pdo->query("SELECT COUNT(*) FROM comments WHERE is_deleted = FALSE")->fetchColumn(),
        'total_views' => $pdo->query("SELECT SUM(total_views) FROM stories")->fetchColumn(),
        'total_stories' => $pdo->query("SELECT COUNT(*) FROM stories")->fetchColumn(),
    ];
    
    // Lấy thống kê 30 ngày gần nhất
    $weekly_stats = $pdo->query("
        SELECT 
            stat_date,
            daily_active_users,
            new_users,
            story_views,
            chapter_views,
            likes_count,
            comments_count
        FROM site_statistics
        ORDER BY stat_date DESC
        LIMIT 30
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
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
  <title>StoryBox - Thống Kê</title>
  <link rel="stylesheet" href="css/style-admin.css">
  <link rel="stylesheet" href="plugins/themify-icons/themify-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          <li id="nav-stories"><a href="story-management.php"><i class="ti-book"></i> Quản lý Truyện</a></li>
          <li id="nav-analytics" class="active"><a href="statistics.php"><i class="ti-stats-up"></i> Thống kê</a></li>
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
          <h1>Thống Kê</h1>
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
        <!-- Thống kê -->
        <div id="stats-row"> 
          <div id="stat-active-users" class="stat-card">
              <div class="stat-icon bg-green">
              <i class="ti-user"></i>
              </div>
              <div class="stat-info">
              <h3><?= number_format($stats['active_users']) ?></h3>
              <p>Người dùng hoạt động</p>
              </div>
          </div>
          
          <div id="stat-banned-users" class="stat-card">
              <div class="stat-icon bg-red">
              <i class="ti-na"></i>
              </div>
              <div class="stat-info">
              <h3><?= number_format($stats['banned_users']) ?></h3>
              <p>Người dùng bị khóa</p>
              </div>
          </div>
          
          <div id="stat-saves" class="stat-card">
              <div class="stat-icon bg-blue">
                  <i class="ti-book"></i>
              </div>
              <div class="stat-info">
                  <h3><?= number_format($stats['total_stories']) ?></h3>
                  <p>Truyện</p>
              </div>
          </div>

          <div id="stat-likes" class="stat-card">
              <div class="stat-icon bg-pink">
                  <i class="ti-heart"></i>
              </div>
              <div class="stat-info">
                  <h3><?= number_format($stats['total_likes']) ?></h3>
                  <p>Lượt lưu</p>
              </div>
          </div>          
                      
          <div id="stat-comments" class="stat-card">
              <div class="stat-icon bg-orange">
                  <i class="ti-comment"></i>
              </div>
              <div class="stat-info">
                  <h3><?= number_format($stats['total_comments']) ?></h3>
                  <p>Bình luận</p>
              </div>
          </div>
          
          <div id="stat-views" class="stat-card">
              <div class="stat-icon bg-purple">
              <i class="ti-eye"></i>
              </div>
              <div class="stat-info">
              <h3><?= number_format($stats['total_views']) ?></h3>
              <p>Lượt xem</p>
              </div>
          </div>
        </div>
        
        <!-- Biểu đồ thống kê -->
        <div class="chart-container">
          <h2>Thống kê 30 ngày gần nhất</h2>
          <canvas id="weeklyStatsChart"></canvas>
        </div>
      </div>
    </main>
  </div>

  <script src="plugins/jQuery/jquery.min.js"></script>
  <script src="js/script.js"></script>
  <script>
    // Vẽ biểu đồ thống kê
    document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById('weeklyStatsChart').getContext('2d');
      const weeklyStats = <?= json_encode(array_reverse($weekly_stats)) ?>;
      
      const labels = weeklyStats.map(stat => stat.stat_date);
      const data = {
        labels: labels,
        datasets: [
          {
            label: 'Người dùng hoạt động',
            data: weeklyStats.map(stat => stat.daily_active_users),
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            tension: 0.1
          },
          {
            label: 'Lượt xem truyện',
            data: weeklyStats.map(stat => stat.story_views),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
          },
          {
            label: 'Lượt thích',
            data: weeklyStats.map(stat => stat.likes_count),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
          }
        ]
      };
      
      new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: true,
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });
  </script>
</body>
</html>