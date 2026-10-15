# 📚 StoryBox - Nền Tảng Đọc Truyện Online

StoryBox là một nền tảng web đọc truyện trực tuyến, cho phép người dùng khám phá, đọc và quản lý các câu chuyện yêu thích. Hệ thống hỗ trợ cả người dùng và quản trị viên với đầy đủ tính năng quản lý nội dung.

## ✨ Tính Năng Chính

### Dành cho Người Dùng
- 🔍 **Tìm kiếm & Khám phá**: Tìm kiếm truyện theo tên, tác giả hoặc thể loại
- 📖 **Đọc truyện**: Giao diện đọc thoải mái với hỗ trợ nhiều chương
- ⭐ **Yêu thích**: Lưu truyện yêu thích để đọc sau
- 📜 **Lịch sử đọc**: Theo dõi tiến trình đọc của bạn
- 💬 **Bình luận**: Tương tác và chia sẻ cảm nhận về truyện
- 👍 **Thích**: Đánh dấu truyện yêu thích
- 🏆 **Bảng xếp hạng**: Xem truyện phổ biến nhất

### Dành cho Quản Trị Viên
- 📚 **Quản lý truyện**: Thêm, sửa, xóa truyện và chương
- 👥 **Quản lý người dùng**: Quản lý tài khoản và quyền truy cập
- 📊 **Thống kê**: Xem số liệu về lượt xem, lượt thích và hoạt động
- 🔧 **Quản lý thể loại**: Tổ chức truyện theo thể loại
- 📝 **Theo dõi hoạt động**: Ghi log các hoạt động quản trị

## 🛠️ Công Nghệ Sử Dụng

- **Backend**: PHP (Procedural)
- **Database**: MySQL với PDO
- **Frontend**: 
  - HTML5, CSS3
  - JavaScript (jQuery)
  - Themify Icons
- **Plugins**:
  - jQuery Match Height
  - Clipboard.js

## 📋 Yêu Cầu Hệ Thống

- PHP 7.4 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Apache/Nginx Web Server
- Bật extension PDO MySQL

## 🚀 Cài Đặt

### 1. Clone hoặc tải source code

```bash
git clone <repository-url>
cd Source
```

### 2. Thiết lập Database

```bash
# Import database schema
mysql -u root -p < Database/StoryBox-DB.sql

# Import dữ liệu mẫu (tùy chọn)
mysql -u root -p < Database/StoryBox-DB-InsertData.sql
```

### 3. Cấu hình kết nối Database

Mở file `StoryBox/config/db_connection.php` và cập nhật thông tin:

```php
$host = 'localhost';
$dbname = 'story_box';
$username = 'root';  // Thay đổi theo username của bạn
$password = '';      // Thay đổi theo password của bạn
```

### 4. Cấu hình Web Server

#### Với XAMPP:
1. Copy thư mục `StoryBox` vào `htdocs/`
2. Khởi động Apache và MySQL
3. Truy cập: `http://localhost/StoryBox/`

#### Với WAMP:
1. Copy thư mục `StoryBox` vào `www/`
2. Khởi động services
3. Truy cập: `http://localhost/StoryBox/`

### 5. Cấu hình quyền thư mục

Đảm bảo thư mục sau có quyền ghi:
- `StoryBox/images/user/` - Lưu avatar người dùng
- `StoryBox/images/story/` - Lưu ảnh bìa truyện

```bash
chmod 755 StoryBox/images/user/
chmod 755 StoryBox/images/story/
```

## 👤 Tài Khoản Mặc Định

Sau khi import dữ liệu mẫu, bạn có thể đăng nhập với:

**Quản trị viên:**
- Username: `admin` (hoặc xem trong file insert data)
- Password: Xem trong file `Database/StoryBox-DB-InsertData.sql`

**Người dùng thường:**
- Đăng ký tài khoản mới tại trang Register

## 📁 Cấu Trúc Thư Mục

```
Source/
├── Database/                    # SQL scripts
│   ├── StoryBox-DB.sql         # Database schema
│   └── StoryBox-DB-InsertData.sql  # Sample data
└── StoryBox/                    # Main application
    ├── config/                  # Configuration files
    │   └── db_connection.php   # Database connection
    ├── css/                     # Stylesheets
    │   ├── style.css           # Main styles
    │   ├── auth-style.css      # Authentication pages
    │   ├── story-style.css     # Story pages
    │   └── style-admin.css     # Admin panel
    ├── images/                  # Images & uploads
    │   ├── admin/              # Admin avatars
    │   ├── story/              # Story covers
    │   └── user/               # User avatars
    ├── js/                      # JavaScript files
    │   └── script.js           # Main scripts
    ├── plugins/                 # Third-party libraries
    │   ├── jQuery/
    │   ├── themify-icons/
    │   ├── match-height/
    │   └── clipboard/
    ├── index.php               # Homepage
    ├── login.php               # Login page
    ├── register.php            # Registration page
    ├── story-list.php          # Story listing
    ├── story-detail.php        # Story details
    ├── chapter.php             # Chapter reading
    ├── search.php              # Search page
    ├── ranking.php             # Rankings
    ├── favourite.php           # User favorites
    ├── history.php             # Reading history
    ├── index-admin.php         # Admin dashboard
    ├── story-management.php    # Story management
    ├── user-management.php     # User management
    ├── statistics.php          # Statistics
    ├── add-story.php           # Add new story
    ├── add-chapter.php         # Add new chapter
    ├── update-story-info.php   # Update story info
    └── update-story-chapter.php # Update chapter
```

## 🎯 Các Tính Năng Chi Tiết

### Database Schema

Database gồm các bảng chính:
- **users**: Thông tin người dùng
- **stories**: Thông tin truyện
- **chapters**: Các chương truyện
- **genres**: Thể loại truyện
- **story_genres**: Liên kết truyện và thể loại (many-to-many)
- **comments**: Bình luận
- **likes**: Lượt thích
- **reading_history**: Lịch sử đọc
- **admin_activities**: Log hoạt động admin
- **site_statistics**: Thống kê hệ thống

### Triggers & Stored Procedures

Database bao gồm:
- **Triggers**: Tự động cập nhật thống kê (likes, comments, views)
- **Stored Procedures**: 
  - Quản lý thể loại truyện
  - Tạo báo cáo thống kê
  - Lấy hoạt động admin gần đây

## 🔒 Bảo Mật

- Mật khẩu được hash bằng PHP password functions
- Sử dụng Prepared Statements để chống SQL Injection
- Session management cho authentication
- Input validation và sanitization
- CSRF protection (nên được implement thêm)

## 🐛 Troubleshooting

### Lỗi kết nối Database
```
Kết nối database thất bại: ...
```
**Giải pháp**: Kiểm tra lại thông tin trong `config/db_connection.php`

### Lỗi upload hình ảnh
**Giải pháp**: Kiểm tra quyền ghi của thư mục `images/`

### Session không hoạt động
**Giải pháp**: Đảm bảo `session_start()` được gọi đầu tiên và PHP session đã được cấu hình đúng

## 📈 Phát Triển Tiếp Theo

- [ ] Implement CSRF protection
- [ ] Thêm tính năng upload file an toàn hơn
- [ ] Responsive design cho mobile
- [ ] REST API
- [ ] Tích hợp email notifications
- [ ] Social login (Facebook, Google)
- [ ] Advanced search với filters
- [ ] Rating system (1-5 stars)
- [ ] Reading progress bar
- [ ] Night mode/Dark theme

## 👨‍💻 Tác Giả

**di:emel**

## 📄 License

Copyright © 2025 StoryBox. All rights reserved.
