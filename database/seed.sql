-- ========================================================
-- DATABASE SEED DATA: SECONDHAND MARKETPLACE
-- Dữ liệu mẫu ban đầu để kiểm thử giao diện & tính năng
-- ========================================================

SET NAMES utf8mb4;

-- 1. Nạp Danh Mục Sản Phẩm Mẫu
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`) VALUES
(1, 'Điện Thoại & Phụ Kiện', 'dien-thoai-phu-kien', 'bi bi-phone', 'Điện thoại cũ, ốp lưng, sạc cáp, tai nghe chính hãng'),
(2, 'Laptop & Thiết Bị Tin Học', 'laptop-may-tinh', 'bi bi-laptop', 'Laptop sinh viên, màn hình, bàn phím cơ, chuột gaming'),
(3, 'Đồ Gia Dụng Sinh Viên', 'do-gia-dung', 'bi bi-house-door', 'Nồi cơm điện, quạt mini, ấm siêu tốc, tủ vải, gương soi'),
(4, 'Sách Vở & Giáo Trình', 'sach-giao-trinh', 'bi bi-book', 'Giáo trình đại học, sách kinh tế, công nghệ thông tin, ngoại ngữ'),
(5, 'Thời Trang & Phụ Kiện', 'thoi-trang', 'bi bi-bag', 'Quần áo cũ thanh lý, balo, giày dép còn tốt')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- 2. Nạp Người Dùng Mẫu (Mật khẩu mặc định là: 123456)
-- Hash bcrypt tương ứng: $2y$10$4aXU/m4t5GqG0K7G/4hHqOMKj1U6u8D6KzP3e8J2H3K5g9W4X3y8K (hoặc kiểm tra bằng password_verify)
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `rating_avg`, `rating_count`) VALUES
(1, 'Admin Quản Trị', 'admin@marketplace.test', '$2y$10$eDkU8vG7UoQ5aP2gH1iRvuN2a2m1m6q1y8E3u7f8V5t4R3k2E1w2q', '0901234567', 'Hà Nội', 'admin', 5.0, 10),
(2, 'Nguyễn Văn Nam (Người Bán)', 'seller@marketplace.test', '$2y$10$eDkU8vG7UoQ5aP2gH1iRvuN2a2m1m6q1y8E3u7f8V5t4R3k2E1w2q', '0988776655', 'KTX Khu B ĐHQG, TP. Thủ Đức', 'seller', 4.8, 15),
(3, 'Trần Thị Mai (Người Mua)', 'buyer@marketplace.test', '$2y$10$eDkU8vG7UoQ5aP2gH1iRvuN2a2m1m6q1y8E3u7f8V5t4R3k2E1w2q', '0911223344', 'Cầu Giấy, Hà Nội', 'buyer', 5.0, 3)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- 3. Nạp Sản Phẩm Mẫu
INSERT INTO `products` (`id`, `user_id`, `category_id`, `title`, `description`, `price`, `original_price`, `condition`, `location`, `image`, `status`, `view_count`) VALUES
(1, 2, 2, 'Laptop Dell Latitude 7490 i5-8350U 8GB SSD 256GB', 'Máy sinh viên dùng học tập, văn phòng mượt mà. Pin còn dùng được khoảng 3 tiếng. Phụ kiện đi kèm có sạc zin và chuột không dây.', 4200000.00, 7500000.00, 'good', 'TP. Thủ Đức, TP.HCM', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=600', 'available', 120),
(2, 2, 1, 'iPhone 11 64GB Bản Quốc Tế Pin 85%', 'Ngoại hình 98%, camera nét căng, FaceID nhạy. Máy nguyên bản chưa từng qua sửa chữa, bán lại lên đời máy mới.', 5300000.00, 6500000.00, 'like_new', 'Bình Thạnh, TP.HCM', 'https://images.unsplash.com/photo-1574755393849-623942496936?w=600', 'available', 245),
(3, 2, 3, 'Nồi cơm điện mini Lock&Lock 0.8L', 'Nồi mua đầu năm học, nấu cơm nhanh dẻo cho 1-2 người ăn. Chuyển trọ dọn đồ nên nhượng lại giá rẻ.', 250000.00, 600000.00, 'good', 'KTX Khu A, TP. Thủ Đức', 'https://images.unsplash.com/photo-1544233726-9f1d2b27be8b?w=600', 'available', 45),
(4, 2, 4, 'Trọn bộ Giáo trình Kinh tế lượng + Xác suất thống kê', 'Sách còn mới tinh 99%, có ghi chú vài bài giảng quan trọng của thầy cô. Tặng kèm tập đề cương photo.', 80000.00, 180000.00, 'like_new', 'Cầu Giấy, Hà Nội', 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600', 'available', 60)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- 4. Nạp Đơn Hàng Mẫu
INSERT INTO `orders` (`id`, `order_code`, `buyer_id`, `seller_id`, `product_id`, `total_price`, `shipping_address`, `phone`, `notes`, `status`) VALUES
(1, 'ORD-20261110-001', 3, 2, 1, 4200000.00, 'Tòa nhà A1 KTX Bách Khoa, Hà Nội', '0911223344', 'Giao buổi chiều sau 17h giúp mình nhé', 'completed')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- 5. Nạp Đánh Giá Mẫu
INSERT INTO `reviews` (`id`, `order_id`, `reviewer_id`, `reviewee_id`, `rating`, `comment`) VALUES
(1, 1, 3, 2, 5, 'Người bán nhiệt tình, đóng gói kỹ, máy đúng như mô tả bài đăng. Cho shop 5 sao!')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- 6. Nạp Tin Nhắn Mẫu
INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `product_id`, `message`, `is_read`) VALUES
(1, 3, 2, 2, 'Chào bạn, chiếc iPhone 11 pin còn tốt không và có bớt chút tiền xăng xe không?', 1),
(2, 2, 3, 2, 'Chào bạn, pin 85% dùng cả ngày thoải mái nhé. Nếu bạn qua KTX lấy mình bớt 100k xăng xe nhé!', 1),
(3, 3, 2, 2, 'Dạ vâng, chiều mai tầm 5h mình ghé xem máy được không ạ?', 0)
ON DUPLICATE KEY UPDATE `id` = `id`;

