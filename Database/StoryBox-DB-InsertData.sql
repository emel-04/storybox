use story_box;

-- Truyện
INSERT INTO stories (id, title, author_name, cover_image_url, description, status, created_at, total_views, total_likes, total_comments)
VALUES 
(1, 'Sự tích Trầu Cau', 'Truyện dân gian', 'images/story/sutichtraucau.jpg', 'Câu chuyện kể về tình cảm anh em giữa Tân và Lang. Sau hiểu lầm, Lang bỏ đi và hóa thành đá vôi; Tân đi tìm em, chết bên cạnh và hóa thành cây cau; người vợ cũng chết và hóa thành dây trầu. Từ đó, trầu cau trở thành biểu tượng của tình nghĩa vợ chồng trong văn hóa Việt.', 'ongoing', '2025-03-30 00:00:00', 4785, 2, 0),
(2, 'Cây tre trăm đốt', 'Truyện dân gian', 'images/story/caytretramdot.jpg', 'Chàng Khoai chăm chỉ làm thuê cho phú ông và được hứa gả con gái nếu tìm được cây tre trăm đốt. Với sự giúp đỡ của Bụt, anh đã thành công và buộc phú ông giữ lời hứa.', 'completed', '2025-03-24 00:00:00', 9615, 3, 0),
(3, 'Sự tích trái dưa hấu', 'Truyện dân gian', 'images/story/sutichtraiduahau.jpg', 'Mai An Tiêm bị vua đày ra đảo hoang. Tại đây, anh trồng được loại quả lạ (dưa hấu) và nhờ đó được vua tha tội, trở về và phổ biến giống cây này.', 'hiatus', '2025-04-03 00:00:00', 989, 2, 0),
(5, 'Sự tích chú Cuội cung trăng', 'Truyện dân gian', 'images/story/sutichchucuoicungtrang.jpg', 'Chú Cuội phát hiện cây thuốc quý có thể cứu người. Khi cây bay lên trời, Cuội bám vào và bị kéo lên cung trăng, trở thành hình ảnh quen thuộc trong văn hóa Việt.', 'ongoing', '2025-02-09 00:00:00', 2555, 4, 0),
(6, 'Làng', 'Kim Lân', 'images/story/Lang.jpg', 'Ông Hai, một người nông dân yêu làng, phải đối mặt với tin đồn làng mình theo giặc. Câu chuyện thể hiện tình yêu quê hương và lòng yêu nước sâu sắc.', 'hiatus', '2025-03-12 00:00:00', 2638, 2, 0),
(7, 'Chiếc thuyền ngoài xa', 'Nguyễn Minh Châu', 'images/story/chiecthuyenngoaixa.jpg', 'Nhiếp ảnh gia Phùng chụp được bức ảnh đẹp về chiếc thuyền nhưng phát hiện đằng sau là cảnh bạo lực gia đình. Truyện phản ánh sự phức tạp của cuộc sống và con người.', 'hiatus', '2025-02-05 00:00:00', 3498, 2, 0),
(8, 'Hai đứa trẻ', 'Thạch Lam', 'images/story/haiduatre.jpg', 'Chuyện kể về hai chị em Liên và An sống ở phố huyện nghèo, chờ đợi chuyến tàu đêm như một tia hy vọng. Truyện thể hiện sự tĩnh lặng và mong ước về một cuộc sống tốt đẹp hơn.', 'completed', '2025-02-23 00:00:00', 8459, 2, 0),
(9, 'Chiếc lược ngà', 'Nguyễn Quang Sáng', 'images/story/chieucluocnga.jpg', 'Ông Sáu làm chiếc lược ngà tặng con gái nhưng chưa kịp trao thì hy sinh. Truyện thể hiện tình cha con sâu nặng trong chiến tranh.', 'ongoing', '2025-03-26 00:00:00', 3902, 3, 0),
(12, 'Tắt đèn', 'Ngô Tất Tố', 'images/story/tatden.jpg', 'Chị Dậu phải bán con và bản thân để nộp sưu cho chồng. Tiểu thuyết phản ánh sự bất công và đau khổ của người nông dân dưới chế độ thực dân phong kiến.', 'hiatus', '2025-02-28 00:00:00', 9147, 3, 0),
(13, 'Phía sau nghi can X', 'Higashino Keigo', 'images/story/phiasaunghicanx.jpg', 'Giáo sư toán học Ishigami giúp người phụ nữ hàng xóm che giấu tội ác, dẫn đến cuộc đấu trí căng thẳng với thám tử Galileo.', 'ongoing', '2025-04-02 00:00:00', 7852, 2, 0),
(14, 'Tuổi thơ dữ dội', 'Phùng Quán', 'images/story/tuoithodudoi.jpg', 'Kể về những thiếu niên tham gia kháng chiến chống Pháp, thể hiện lòng yêu nước và sự hy sinh của thế hệ trẻ.', 'completed', '2025-02-07 00:00:00', 5872, 2, 0),
(15, 'Chí Phèo', 'Nam Cao', 'images/story/chipheo.jpg', 'Chí Phèo, từ người nông dân lương thiện trở thành kẻ lưu manh do xã hội đẩy đưa. Truyện phản ánh bi kịch của con người bị tha hóa.', 'ongoing', '2025-03-16 00:00:00', 1799, 2, 0),
(16, 'Giải Ngải Ký', 'Tống Ngọc', 'images/story/giaingaiky.jpg', 'Ba người bạn trẻ khám phá thế giới tâm linh với ma ngải, cầu cơ và tổ chức Âm dương sư, bắt đầu từ một vụ tai nạn kỳ lạ.', 'ongoing', '2025-03-04 00:00:00', 7786, 2, 0),
(17, 'Số đỏ', 'Vũ Trọng Phụng', 'images/story/sodo.jpg', 'Truyện châm biếm xã hội thượng lưu thành thị với nhân vật Xuân Tóc Đỏ, một kẻ vô học nhưng nhờ cơ hội trở thành người nổi tiếng.', 'completed', '2025-03-09 00:00:00', 232, 3, 0),
(19, 'Đất rừng phương nam', 'Đoàn Giỏi', 'images/story/datrungphuongnam.jpg', 'An—cậu bé mồ côi cha mẹ do chiến tranh, lưu lạc đến vùng rừng phương Nam. Tại đây, cậu hòa nhập với thiên nhiên và con người Nam Bộ, trải qua nhiều thử thách, học được tình yêu quê hương và lòng dũng cảm. Tác phẩm nổi bật với cảnh sắc thiên nhiên hùng vĩ và tinh thần đấu tranh kiên cường của người dân.', 'ongoing', '2025-03-21 00:00:00', 9564, 2, 0),
(20, 'Vợ nhặt', 'Kim Lân', 'images/story/yonhat.jpg', 'Trong nạn đói, Tràng ''nhặt'' được vợ. Câu chuyện phản ánh tình người và khát vọng sống trong hoàn cảnh khốn khó.', 'ongoing', '2025-02-24 00:00:00', 9504, 1, 0),
(22, 'Án mạng trên sông Nile', 'Agatha Christie', 'images/story/anmangtrensongnile.jpg', 'Thám tử Poirot điều tra vụ án mạng xảy ra trên du thuyền sông Nile, với nhiều nghi phạm và động cơ phức tạp.', 'completed', '2025-04-03 00:00:00', 1349, 2, 0),
(24, 'Sự im lặng của bầy cừu', 'Thomas Harris', 'images/story/suimlangcuabaycuu.jpg', 'Đặc vụ FBI Clarice Starling nhờ đến sự giúp đỡ của bác sĩ tâm thần ăn thịt người Hannibal Lecter để bắt tên sát nhân Buffalo Bill.', 'completed', '2025-04-14 00:00:00', 4251, 1, 0),
(25, 'Dế Mèn phiêu lưu ký', 'Tô Hoài', 'images/story/demenphieuluuky.jpg', 'Dế Mèn trải qua nhiều cuộc phiêu lưu, học hỏi và trưởng thành. Truyện mang tính giáo dục và giải trí cao.', 'ongoing', '2025-03-18 00:00:00', 7002, 2, 0),
(26, 'Trò chơi ở ngày tận thế', '첨G', 'images/story/trochoiongaytanthe.jpg', 'Giáo sư Park, một kẻ sống sót đơn độc ở Vùng Hoang Tàn suốt 7 năm. Nguồn cung cạn kiệt. Máy phát điện hỏng. Một băng đảng điên cuồng đang truy lùng anh. Trên bờ vực cái chết, anh đưa ra một quyết định tuyệt vọng! "Chết tiệt! Mày gọi đây là trò chơi hả?!" Đi vào trong một trò chơi!', 'ongoing', '2025-01-30 00:00:00', 9145, 1, 0),
(27, 'Ai đã đặt tên cho dòng sông?', 'Hoàng Phủ Ngọc Tường', 'images/story/aidadattendongsong.jpg', 'Tác phẩm miêu tả vẻ đẹp của sông Hương và thành phố Huế, thể hiện tình yêu quê hương sâu sắc.', 'completed', '2025-02-19 00:00:00', 8119, 2, 0),
(28, 'Hà Nội băm sáu phố phường', 'Thạch Lam', 'images/story/hanoibamsauphophuong.jpg', 'Ghi lại những nét đặc trưng của các phố phường Hà Nội, thể hiện tình yêu và sự gắn bó với thủ đô.', 'completed', '2025-03-27 00:00:00', 1029, 1, 0),
(29, 'Bí mật của người chồng', 'Liane Moriarty', 'images/story/bimatcanguoichong.jpg', 'Cecilia phát hiện bí mật động trời của chồng, đẩy gia đình vào khủng hoảng. Truyện khai thác tâm lý và những bí mật gia đình.', 'hiatus', '2025-01-23 00:00:00', 6447, 1, 0);

-- Thể loại
INSERT INTO genres (name, created_at) VALUES
('Truyện dân gian', '2025-01-11'),
('Truyện ngắn hiện đại', '2025-01-11'),
('Tiểu thuyết', '2025-01-11'),
('Tùy bút', '2025-01-11'),
('Trinh thám', '2025-01-11'),
('Kinh dị', '2025-01-11'),
('Giả tưởng', '2025-01-11');

-- Thể loại của truyện
INSERT INTO story_genres (story_id, genre_id) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 4),
(11, 6),
(12, 3),
(13, 5),
(14, 3),
(15, 3),
(16, 6),
(17, 3),
(18, 6),
(19, 4),
(20, 2),
(21, 4),
(22, 5),
(23, 5),
(24, 5),
(25, 3),
(26, 7),
(27, 4),
(28, 4),
(29, 5);

-- User
USE story_box;

-- Tạo 30 users
INSERT INTO users (username, email, password_hash, created_at, is_active) VALUES
('Minh', 'minh@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-01-15', true),
('Linh', 'linh@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-02-03', true),
('Hieu', 'hieu@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-02-18', true),
('Anh', 'anh@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-03-05', true),
('Tuan', 'tuan@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-03-22', false),
('Nga', 'nga@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-04-10', true),
('Khanh', 'khanh@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-04-25', true),
('Dung', 'dung@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-05-12', false),
('Hoa', 'hoa@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-05-30', true),
('Binh', 'binh@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-06-17', true),
('Quan', 'quan@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-07-04', false),
('Thao', 'thao@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-07-21', true),
('Phuc', 'phuc@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-08-08', true),
('My', 'my@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-08-25', false),
('Viet', 'viet@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-09-11', true),
('Lan', 'lan@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-09-28', true),
('Hung', 'hung@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-10-15', true),
('Nhi', 'nhi@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-11-02', true),
('Duc', 'duc@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-11-19', true),
('Giang', 'giang@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-12-06', true),
('Tam', 'tam@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2024-12-23', true),
('Son', 'son@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-01-09', true),
('Trang', 'trang@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-01-26', true),
('Long', 'long@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-02-12', true),
('Van', 'van@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-03-01', true),
('Khoa', 'khoa@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-03-18', true),
('Uyen', 'uyen@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-04-05', true),
('Phat', 'phat@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-04-22', true),
('Mai', 'mai@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-04-28', true),
('Bao', 'bao@gmail.com', '$2y$10$XkgBQf1TxMjQTcZgjA0z3eABIM.nakXVeQTwAIBNP2Y4iKs/0AN3a', '2025-04-29', true);

-- Like
INSERT INTO likes (user_id, story_id, created_at) VALUES
(6, 1, '2025-03-15 08:15:23'),
(6, 12, '2025-03-20 14:30:45'),
(6, 25, '2025-03-25 16:20:33'),
(7, 5, '2025-02-10 11:10:28'),
(8, 2, '2025-03-25 10:25:19'),
(8, 7, '2025-02-25 13:40:52'),
(8, 15, '2025-03-20 17:55:41'),
(10, 3, '2025-04-05 11:35:28'),
(10, 19, '2025-03-28 15:50:14'),
(11, 6, '2025-03-10 09:25:46'),
(11, 9, '2025-03-20 16:50:29'),
(11, 13, '2025-01-25 08:25:41'),
(11, 17, '2025-03-05 14:20:47'),
(11, 22, '2025-03-12 10:35:18'),
(12, 8, '2025-03-15 09:55:52'),
(13, 14, '2025-04-05 13:30:24'),
(13, 20, '2025-03-23 17:45:36'),
(13, 27, '2025-02-28 11:20:48'),
(14, 16, '2025-04-01 08:40:13'),
(14, 26, '2025-02-10 12:55:27'),
(16, 5, '2025-02-10 16:10:39'),
(16, 17, '2025-04-12 10:25:51'),
(16, 2, '2025-02-22 14:40:03'),
(16, 29, '2025-03-26 07:30:45'),
(17, 24, '2025-02-28 11:45:57'),
(18, 5, '2025-03-18 15:00:09'),
(18, 27, '2025-04-12 09:15:21'),
(18, 9, '2025-02-22 13:30:33'),
(20, 7, '2025-04-16 08:05:30'),
(20, 28, '2025-03-05 12:20:42'),
(21, 1, '2025-02-20 09:15:27'),
(21, 2, '2025-03-10 13:30:39'),
(21, 3, '2025-03-15 17:45:51'),
(21, 12, '2025-03-22 07:00:03'),
(21, 5, '2025-01-28 11:15:15'),
(22, 15, '2025-03-16 09:55:52'),
(23, 9, '2025-04-04 13:30:24'),
(23, 12, '2025-03-24 17:45:36'),
(25, 13, '2025-03-31 19:35:59'),
(25, 19, '2025-03-01 11:20:48'),
(26, 6, '2025-02-15 07:45:33'),
(26, 14, '2025-03-05 14:20:47'),
(26, 17, '2025-03-12 10:35:18'),
(26, 25, '2025-03-20 16:50:29'),
(27, 8, '2025-01-25 08:25:41'),
(28, 16, '2025-03-23 17:45:36'),
(28, 22, '2025-02-28 11:20:48');

-- Chapter
INSERT INTO chapters (story_id, chapter_number, content, view_count, created_at) VALUES
-- Sự tích Trầu Cau (story_id = 1, 3 chương)
(1, 1, 'Nội dung chương 1 của Sự tích Trầu Cau...', 120, '2025-03-30 10:00:00'),
(1, 2, 'Nội dung chương 2 của Sự tích Trầu Cau...', 100, '2025-03-31 10:00:00'),
(1, 3, 'Nội dung chương 3 của Sự tích Trầu Cau...', 80, '2025-04-01 10:00:00'),
-- Cây tre trăm đốt (story_id = 2, 4 chương)
(2, 1, 'Nội dung chương 1 của Cây tre trăm đốt...', 200, '2025-03-24 12:00:00'),
(2, 2, 'Nội dung chương 2 của Cây tre trăm đốt...', 180, '2025-03-25 12:00:00'),
(2, 3, 'Nội dung chương 3 của Cây tre trăm đốt...', 150, '2025-03-26 12:00:00'),
(2, 4, 'Nội dung chương 4 của Cây tre trăm đốt...', 130, '2025-03-27 12:00:00'),
-- Sự tích trái dưa hấu (story_id = 3, 2 chương)
(3, 1, 'Nội dung chương 1 của Sự tích trái dưa hấu...', 60, '2025-04-03 08:00:00'),
(3, 2, 'Nội dung chương 2 của Sự tích trái dưa hấu...', 50, '2025-04-04 08:00:00'),
-- Sự tích chú Cuội cung trăng (story_id = 5, 3 chương)
(5, 1, 'Nội dung chương 1 của Sự tích chú Cuội cung trăng...', 100, '2025-02-09 09:00:00'),
(5, 2, 'Nội dung chương 2 của Sự tích chú Cuội cung trăng...', 90, '2025-02-10 09:00:00'),
(5, 3, 'Nội dung chương 3 của Sự tích chú Cuội cung trăng...', 70, '2025-02-11 09:00:00'),
-- Làng (story_id = 6, 2 chương)
(6, 1, 'Nội dung chương 1 của Làng...', 80, '2025-03-12 11:00:00'),
(6, 2, 'Nội dung chương 2 của Làng...', 60, '2025-03-13 11:00:00'),
-- Chiếc thuyền ngoài xa (story_id = 7, 3 chương)
(7, 1, 'Nội dung chương 1 của Chiếc thuyền ngoài xa...', 110, '2025-02-05 10:00:00'),
(7, 2, 'Nội dung chương 2 của Chiếc thuyền ngoài xa...', 90, '2025-02-06 10:00:00'),
(7, 3, 'Nội dung chương 3 của Chiếc thuyền ngoài xa...', 70, '2025-02-07 10:00:00'),
-- Hai đứa trẻ (story_id = 8, 4 chương)
(8, 1, 'Nội dung chương 1 của Hai đứa trẻ...', 220, '2025-02-23 14:00:00'),
(8, 2, 'Nội dung chương 2 của Hai đứa trẻ...', 200, '2025-02-24 14:00:00'),
(8, 3, 'Nội dung chương 3 của Hai đứa trẻ...', 180, '2025-02-25 14:00:00'),
(8, 4, 'Nội dung chương 4 của Hai đứa trẻ...', 160, '2025-02-26 14:00:00'),
-- Chiếc lược ngà (story_id = 9, 3 chương)
(9, 1, 'Nội dung chương 1 của Chiếc lược ngà...', 130, '2025-03-26 15:00:00'),
(9, 2, 'Nội dung chương 2 của Chiếc lược ngà...', 110, '2025-03-27 15:00:00'),
(9, 3, 'Nội dung chương 3 của Chiếc lược ngà...', 90, '2025-03-28 15:00:00'),
-- Tắt đèn (story_id = 12, 5 chương)
(12, 1, 'Nội dung chương 1 của Tắt đèn...', 250, '2025-02-28 13:00:00'),
(12, 2, 'Nội dung chương 2 của Tắt đèn...', 230, '2025-03-01 13:00:00'),
(12, 3, 'Nội dung chương 3 của Tắt đèn...', 210, '2025-03-02 13:00:00'),
(12, 4, 'Nội dung chương 4 của Tắt đèn...', 190, '2025-03-03 13:00:00'),
(12, 5, 'Nội dung chương 5 của Tắt đèn...', 170, '2025-03-04 13:00:00'),
-- Phía sau nghi can X (story_id = 13, 4 chương)
(13, 1, 'Nội dung chương 1 của Phía sau nghi can X...', 240, '2025-04-02 16:00:00'),
(13, 2, 'Nội dung chương 2 của Phía sau nghi can X...', 220, '2025-04-03 16:00:00'),
(13, 3, 'Nội dung chương 3 của Phía sau nghi can X...', 200, '2025-04-04 16:00:00'),
(13, 4, 'Nội dung chương 4 của Phía sau nghi can X...', 180, '2025-04-05 16:00:00'),
-- Tuổi thơ dữ dội (story_id = 14, 3 chương)
(14, 1, 'Nội dung chương 1 của Tuổi thơ dữ dội...', 150, '2025-02-07 12:00:00'),
(14, 2, 'Nội dung chương 2 của Tuổi thơ dữ dội...', 130, '2025-02-08 12:00:00'),
(14, 3, 'Nội dung chương 3 của Tuổi thơ dữ dội...', 110, '2025-02-09 12:00:00'),
-- Chí Phèo (story_id = 15, 2 chương)
(15, 1, 'Nội dung chương 1 của Chí Phèo...', 70, '2025-03-16 09:00:00'),
(15, 2, 'Nội dung chương 2 của Chí Phèo...', 50, '2025-03-17 09:00:00'),
-- Giải Ngải Ký (story_id = 16, 3 chương)
(16, 1, 'Nội dung chương 1 của Giải Ngải Ký...', 190, '2025-03-04 14:00:00'),
(16, 2, 'Nội dung chương 2 của Giải Ngải Ký...', 170, '2025-03-05 14:00:00'),
(16, 3, 'Nội dung chương 3 của Giải Ngải Ký...', 150, '2025-03-06 14:00:00'),
-- Số đỏ (story_id = 17, 4 chương)
(17, 1, 'Nội dung chương 1 của Số đỏ...', 80, '2025-03-09 11:00:00'),
(17, 2, 'Nội dung chương 2 của Số đỏ...', 60, '2025-03-10 11:00:00'),
(17, 3, 'Nội dung chương 3 của Số đỏ...', 50, '2025-03-11 11:00:00'),
(17, 4, 'Nội dung chương 4 của Số đỏ...', 40, '2025-03-12 11:00:00'),
-- Đất rừng phương nam (story_id = 19, 5 chương)
(19, 1, 'Nội dung chương 1 của Đất rừng phương nam...', 280, '2025-03-21 10:00:00'),
(19, 2, 'Nội dung chương 2 của Đất rừng phương nam...', 260, '2025-03-22 10:00:00'),
(19, 3, 'Nội dung chương 3 của Đất rừng phương nam...', 240, '2025-03-23 10:00:00'),
(19, 4, 'Nội dung chương 4 của Đất rừng phương nam...', 220, '2025-03-24 10:00:00'),
(19, 5, 'Nội dung chương 5 của Đất rừng phương nam...', 200, '2025-03-25 10:00:00'),
-- Vợ nhặt (story_id = 20, 2 chương)
(20, 1, 'Nội dung chương 1 của Vợ nhặt...', 230, '2025-02-24 13:00:00'),
(20, 2, 'Nội dung chương 2 của Vợ nhặt...', 210, '2025-02-25 13:00:00'),
-- Án mạng trên sông Nile (story_id = 22, 3 chương)
(22, 1, 'Nội dung chương 1 của Án mạng trên sông Nile...', 90, '2025-04-03 15:00:00'),
(22, 2, 'Nội dung chương 2 của Án mạng trên sông Nile...', 70, '2025-04-04 15:00:00'),
(22, 3, 'Nội dung chương 3 của Án mạng trên sông Nile...', 50, '2025-04-05 15:00:00'),
-- Sự im lặng của bầy cừu (story_id = 24, 4 chương)
(24, 1, 'Nội dung chương 1 của Sự im lặng của bầy cừu...', 140, '2025-04-14 14:00:00'),
(24, 2, 'Nội dung chương 2 của Sự im lặng của bầy cừu...', 120, '2025-04-15 14:00:00'),
(24, 3, 'Nội dung chương 3 của Sự im lặng của bầy cừu...', 100, '2025-04-16 14:00:00'),
(24, 4, 'Nội dung chương 4 của Sự im lặng của bầy cừu...', 80, '2025-04-17 14:00:00'),
-- Dế Mèn phiêu lưu ký (story_id = 25, 3 chương)
(25, 1, 'Nội dung chương 1 của Dế Mèn phiêu lưu ký...', 210, '2025-03-18 12:00:00'),
(25, 2, 'Nội dung chương 2 của Dế Mèn phiêu lưu ký...', 190, '2025-03-19 12:00:00'),
(25, 3, 'Nội dung chương 3 của Dế Mèn phiêu lưu ký...', 170, '2025-03-20 12:00:00'),
-- Trò chơi ở ngày tận thế (story_id = 26, 2 chương)
(26, 1, 'Nội dung chương 1 của Trò chơi ở ngày tận thế...', 250, '2025-01-30 16:00:00'),
(26, 2, 'Nội dung chương 2 của Trò chơi ở ngày tận thế...', 230, '2025-01-31 16:00:00'),
-- Ai đã đặt tên cho dòng sông? (story_id = 27, 3 chương)
(27, 1, 'Nội dung chương 1 của Ai đã đặt tên cho dòng sông?...', 200, '2025-02-19 10:00:00'),
(27, 2, 'Nội dung chương 2 của Ai đã đặt tên cho dòng sông?...', 180, '2025-02-20 10:00:00'),
(27, 3, 'Nội dung chương 3 của Ai đã đặt tên cho dòng sông?...', 160, '2025-02-21 10:00:00'),
-- Hà Nội băm sáu phố phường (story_id = 28, 2 chương)
(28, 1, 'Nội dung chương 1 của Hà Nội băm sáu phố phường...', 60, '2025-03-27 09:00:00'),
(28, 2, 'Nội dung chương 2 của Hà Nội băm sáu phố phường...', 50, '2025-03-28 09:00:00'),
-- Bí mật của người chồng (story_id = 29, 4 chương)
(29, 1, 'Nội dung chương 1 của Bí mật của người chồng...', 170, '2025-01-23 11:00:00'),
(29, 2, 'Nội dung chương 2 của Bí mật của người chồng...', 150, '2025-01-24 11:00:00'),
(29, 3, 'Nội dung chương 3 của Bí mật của người chồng...', 130, '2025-01-25 11:00:00'),
(29, 4, 'Nội dung chương 4 của Bí mật của người chồng...', 110, '2025-01-26 11:00:00');

-- Reading history - user_id = 6
INSERT INTO reading_history (user_id, chapter_id, story_id, last_read_at, progress) VALUES
(6, 1, 1, '2025-04-01 08:30:00', 0.8), -- Chương 1 của Sự tích Trầu Cau
(6, 2, 1, '2025-04-02 09:15:00', 0.5), -- Chương 2 của Sự tích Trầu Cau
(6, 3, 1, '2025-04-03 10:00:00', 0.3), -- Chương 3 của Sự tích Trầu Cau
(6, 25, 12, '2025-03-01 13:30:00', 1.0), -- Chương 1 của Tắt đèn
(6, 26, 12, '2025-03-02 14:00:00', 0.7), -- Chương 2 của Tắt đèn
(6, 27, 12, '2025-03-03 15:20:00', 0.4), -- Chương 3 của Tắt đèn
(6, 60, 25, '2025-03-19 12:45:00', 0.9), -- Chương 1 của Dế Mèn phiêu lưu ký
(6, 61, 25, '2025-03-20 13:10:00', 0.6), -- Chương 2 của Dế Mèn phiêu lưu ký
(6, 10, 5, '2025-02-10 11:30:00', 0.8), -- Chương 1 của Sự tích chú Cuội
(6, 11, 5, '2025-02-11 12:15:00', 0.5), -- Chương 2 của Sự tích chú Cuội
(6, 18, 8, '2025-02-24 15:50:00', 1.0), -- Chương 1 của Hai đứa trẻ
(6, 19, 8, '2025-02-25 16:20:00', 0.7); -- Chương 2 của Hai đứa trẻ

