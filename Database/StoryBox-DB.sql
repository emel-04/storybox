-- Create database
CREATE DATABASE IF NOT EXISTS story_box;
USE story_box;

-- Table users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    avatar_url VARCHAR(255),
    bio TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_user_role (role),
    INDEX idx_user_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table genres
CREATE TABLE genres (
    genre_id INT AUTO_INCREMENT PRIMARY KEY,
    genre_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_genre_name (genre_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table stories (modified with author_name instead of author_id)
CREATE TABLE stories (
    story_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author_name VARCHAR(50) NOT NULL,
    cover_image_url VARCHAR(255),
    description TEXT,
    status ENUM('ongoing', 'completed', 'hiatus') DEFAULT 'ongoing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    total_views INT DEFAULT 0,
    total_likes INT DEFAULT 0,
    total_comments INT DEFAULT 0,
    FULLTEXT INDEX ft_story_title_desc (title, description),
    INDEX idx_story_author (author_name),
    INDEX idx_story_status (status),
    INDEX idx_story_popularity (total_views, total_likes, total_comments)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table story_genres (Many-to-many between stories and genres)
CREATE TABLE story_genres (
    story_genre_id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT NOT NULL,
    genre_id INT NOT NULL,
    FOREIGN KEY (story_id) REFERENCES stories(story_id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id) ON DELETE CASCADE,
    UNIQUE KEY uk_story_genre (story_id, genre_id),
    INDEX idx_genre_story (genre_id, story_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table chapters
CREATE TABLE chapters (
    chapter_id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT NOT NULL,
    chapter_number INT NOT NULL,
    title VARCHAR(255),
    content LONGTEXT NOT NULL,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (story_id) REFERENCES stories(story_id) ON DELETE CASCADE,
    UNIQUE KEY uk_story_chapter (story_id, chapter_number),
    INDEX idx_chapter_story (story_id),
    INDEX idx_chapter_views (view_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table comments
CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    story_id INT NOT NULL,
    chapter_id INT,
    parent_comment_id INT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (story_id) REFERENCES stories(story_id) ON DELETE CASCADE,
    FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id) ON DELETE CASCADE,
    FOREIGN KEY (parent_comment_id) REFERENCES comments(comment_id) ON DELETE SET NULL,
    INDEX idx_comment_story (story_id),
    INDEX idx_comment_chapter (chapter_id),
    INDEX idx_comment_user (user_id),
    INDEX idx_comment_parent (parent_comment_id),
    INDEX idx_comment_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table likes
CREATE TABLE likes (
    like_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    story_id INT NOT NULL,
    is_saved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (story_id) REFERENCES stories(story_id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_story_like (user_id, story_id),
    INDEX idx_like_story (story_id),
    INDEX idx_like_user (user_id),
    INDEX idx_like_saved (is_saved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table reading_history
CREATE TABLE reading_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    chapter_id INT NOT NULL,
    story_id INT NOT NULL,
    last_read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress FLOAT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id) ON DELETE CASCADE,
    FOREIGN KEY (story_id) REFERENCES stories(story_id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_chapter_history (user_id, chapter_id),
    INDEX idx_history_user (user_id),
    INDEX idx_history_story (story_id),
    INDEX idx_history_chapter (chapter_id),
    INDEX idx_history_last_read (last_read_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table admin_activities
CREATE TABLE admin_activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    activity_type ENUM('user_management', 'story_management', 'chapter_management', 'comment_management', 'system_management') NOT NULL,
    action ENUM('create', 'read', 'update', 'delete', 'other') NOT NULL,
    target_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_activity_admin (admin_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_activity_action (action),
    INDEX idx_activity_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table site_statistics
CREATE TABLE site_statistics (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    stat_date DATE NOT NULL UNIQUE,
    daily_active_users INT DEFAULT 0,
    new_users INT DEFAULT 0,
    story_views INT DEFAULT 0,
    chapter_views INT DEFAULT 0,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    INDEX idx_stat_date (stat_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========== TRIGGERS ==========
DELIMITER //

-- Trigger to update statistics when a new like is added
CREATE TRIGGER after_like_insert
AFTER INSERT ON likes
FOR EACH ROW
BEGIN
    UPDATE stories SET total_likes = total_likes + 1 WHERE story_id = NEW.story_id;
END //

-- Trigger to update statistics when a like is removed
CREATE TRIGGER after_like_delete
AFTER DELETE ON likes
FOR EACH ROW
BEGIN
    UPDATE stories SET total_likes = total_likes - 1 WHERE story_id = OLD.story_id;
END //

-- Trigger to update statistics when a new comment is added
CREATE TRIGGER after_comment_insert
AFTER INSERT ON comments
FOR EACH ROW
BEGIN
    UPDATE stories SET total_comments = total_comments + 1 WHERE story_id = NEW.story_id;
END //

-- Trigger to update chapter views
CREATE TRIGGER after_chapter_view_update
AFTER UPDATE ON reading_history
FOR EACH ROW
BEGIN
    IF NEW.progress > 0.9 AND OLD.progress <= 0.9 THEN
        UPDATE chapters SET view_count = view_count + 1 WHERE chapter_id = NEW.chapter_id;
        UPDATE stories SET total_views = total_views + 1 WHERE story_id = NEW.story_id;
    END IF;
END //

-- ========== STORED PROCEDURES ==========

-- Add genre to story
CREATE PROCEDURE add_genre_to_story(
    IN p_story_id INT,
    IN p_genre_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Check if story exists
    IF NOT EXISTS (SELECT 1 FROM stories WHERE story_id = p_story_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Story does not exist';
    END IF;
    
    -- Check if genre exists
    IF NOT EXISTS (SELECT 1 FROM genres WHERE genre_id = p_genre_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Genre does not exist';
    END IF;
    
    -- Add genre to story
    INSERT IGNORE INTO story_genres (story_id, genre_id)
    VALUES (p_story_id, p_genre_id);
    
    COMMIT;
END //

-- Remove genre from story
CREATE PROCEDURE remove_genre_from_story(
    IN p_story_id INT,
    IN p_genre_id INT
)
BEGIN
    DELETE FROM story_genres 
    WHERE story_id = p_story_id AND genre_id = p_genre_id;
END //

-- Get all genres for a story
CREATE PROCEDURE get_story_genres(
    IN p_story_id INT
)
BEGIN
    SELECT g.genre_id, g.genre_name, g.description
    FROM story_genres sg
    JOIN genres g ON sg.genre_id = g.genre_id
    WHERE sg.story_id = p_story_id
    ORDER BY g.genre_name;
END //

-- Get stories by genre (updated with author_name)
CREATE PROCEDURE get_stories_by_genre(
    IN p_genre_id INT,
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    SELECT s.story_id, s.title, s.author_name,
           s.cover_image_url, s.description, s.status,
           s.total_views, s.total_likes, s.total_comments
    FROM story_genres sg
    JOIN stories s ON sg.story_id = s.story_id
    WHERE sg.genre_id = p_genre_id
    ORDER BY s.total_views DESC
    LIMIT p_limit OFFSET p_offset;
END //

-- Generate daily statistics
CREATE PROCEDURE generate_daily_statistics()
BEGIN
    DECLARE stat_date DATE;
    SET stat_date = CURDATE();
    
    INSERT INTO site_statistics (
        stat_date,
        daily_active_users,
        new_users,
        story_views,
        chapter_views,
        likes_count,
        comments_count
    )
    SELECT
        stat_date,
        COUNT(DISTINCT rh.user_id) AS daily_active_users,
        SUM(CASE WHEN DATE(u.created_at) = stat_date THEN 1 ELSE 0 END) AS new_users,
        SUM(s.total_views) AS story_views,
        SUM(c.view_count) AS chapter_views,
        COUNT(DISTINCT l.like_id) AS likes_count,
        COUNT(DISTINCT cm.comment_id) AS comments_count
    FROM
        stories s
        LEFT JOIN users u ON 1=1
        LEFT JOIN chapters c ON 1=1
        LEFT JOIN likes l ON DATE(l.created_at) = stat_date
        LEFT JOIN comments cm ON DATE(cm.created_at) = stat_date
        LEFT JOIN reading_history rh ON DATE(rh.last_read_at) = stat_date
    ON DUPLICATE KEY UPDATE
        daily_active_users = VALUES(daily_active_users),
        new_users = VALUES(new_users),
        story_views = VALUES(story_views),
        chapter_views = VALUES(chapter_views),
        likes_count = VALUES(likes_count),
        comments_count = VALUES(comments_count);
END //

-- Get recent admin activities
CREATE PROCEDURE get_recent_admin_activities(IN days INT)
BEGIN
    SELECT 
        a.activity_id,
        u.username AS admin_name,
        a.activity_type,
        a.action,
        a.target_id,
        a.description,
        a.created_at
    FROM 
        admin_activities a
        JOIN users u ON a.admin_id = u.user_id
    WHERE 
        a.created_at >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL days DAY)
    ORDER BY 
        a.created_at DESC
    LIMIT 100;
END //

DELIMITER ;