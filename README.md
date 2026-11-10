# Secondhand Marketplace (Chợ Đồ Cũ Sinh Viên / Người Dùng)

Dự án website mua bán, trao đổi đồ cũ trực tuyến xây dựng theo mô hình **MVC thuần (PHP + MySQL + PDO)**, không sử dụng framework nặng nề, phù hợp cho học tập và làm việc nhóm.

---

## 📁 Cấu trúc thư mục dự án

```text
market/
├── app/                            # Chứa toàn bộ logic nghiệp vụ (PHP)
│   ├── Config/                     # Cấu hình hệ thống
│   │   ├── database.php            # Kết nối PDO tới MySQL
│   │   └── constants.php           # Các hằng số (BASE_URL, đường dẫn upload...)
│   │
│   ├── Core/                       # Khung sườn MVC dùng chung (Do Team Lead dựng)
│   │   ├── Router.php              # Điều hướng URL
│   │   ├── Controller.php          # Base Controller (render view, trả json, redirect)
│   │   ├── Model.php               # Base Model (kết nối PDO, query CRUD cơ bản)
│   │   └── AuthMiddleware.php      # Kiểm tra đăng nhập & phân quyền
│   │
│   ├── Controllers/                # Xử lý request từ người dùng (Tách file theo từng TV)
│   │   ├── AuthController.php      # Đăng nhập, đăng ký, đăng xuất
│   │   ├── ProductController.php   # Đăng bán, sửa/xóa, chi tiết SP, tìm kiếm & lọc
│   │   ├── ChatController.php      # API gửi/nhận tin nhắn (AJAX/Long-polling)
│   │   ├── OrderController.php     # Tạo đơn, cập nhật trạng thái giao dịch
│   │   └── ReviewController.php    # Đánh giá người bán
│   │
│   ├── Models/                     # Tương tác trực tiếp với Database (SQL)
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Message.php
│   │   ├── Order.php
│   │   └── Review.php
│   │
│   └── Views/                      # Giao diện HTML nhúng PHP (Tách theo module)
│       ├── layouts/                # Khung giao diện dùng chung
│       │   ├── header.php
│       │   ├── navbar.php
│       │   └── footer.php
│       ├── auth/                   # login.php, register.php
│       ├── products/               # index.php (lọc/tìm), create.php, detail.php
│       ├── chat/                   # inbox.php (khung chat)
│       ├── orders/                 # buyer_orders.php, seller_orders.php, detail.php
│       └── profile/                # seller_profile.php, reviews.php
│
├── public/                         # Thư mục gốc mở ra ngoài trình duyệt (DocumentRoot)
│   ├── index.php                   # Điểm đầu vào duy nhất (Front Controller)
│   ├── .htaccess                   # Rewrite URL cho đẹp (ẩn đuôi .php)
│   ├── assets/
│   │   ├── css/                    # Chia file CSS theo từng module để tránh đụng độ
│   │   │   ├── global.css          # Biến màu sắc, reset CSS, layout chung
│   │   │   ├── products.css        # CSS riêng cho trang sản phẩm & tìm kiếm
│   │   │   ├── chat.css            # CSS riêng cho khung chat
│   │   │   └── orders.css          # CSS riêng cho quản lý đơn & đánh giá
│   │   ├── js/                     # Chia file JS xử lý sự kiện & Fetch/AJAX
│   │   │   ├── main.js             # Hàm tiện ích chung (format tiền tệ, toast thông báo)
│   │   │   ├── products.js         # Xử lý lọc sản phẩm AJAX, preview ảnh khi đăng bán
│   │   │   ├── chat.js             # Fetch API gửi/nhận tin nhắn real-time (AJAX polling)
│   │   └── └── orders.js           # Xử lý nút đổi trạng thái giao dịch, modal đánh giá
│   │   └── images/                 # Icon, logo mặc định của web
│   └── uploads/                    # Chứa ảnh người dùng tải lên (Thêm vào .gitignore)
│       ├── products/               # Ảnh đồ cũ đăng bán
│       └── avatars/                # Ảnh đại diện
│
├── database/                       # Quản lý phiên bản Database cho cả nhóm
│   ├── schema.sql                  # File tạo toàn bộ bảng (Users, Products, Orders...)
│   └── seed.sql                    # Dữ liệu mẫu để anh em test giao diện
│
├── .env.example                    # File mẫu cấu hình (DB_HOST, DB_NAME, DB_USER...)
├── .gitignore                      # Bỏ qua file .env, thư mục uploads/
└── README.md                       # Hướng dẫn cài đặt XAMPP/Laragon và quy tắc Git
```

---

## 🚀 Hướng dẫn cài đặt môi trường (Laragon / XAMPP)

### Cách 1: Sử dụng Laragon (Khuyên dùng)
1. Copy hoặc clone thư mục dự án vào thư mục `C:/laragon/www/market`.
2. Khởi động Laragon (bấm **Start All**).
3. Mở **HeidiSQL** hoặc **phpMyAdmin** trên Laragon:
   - Tạo mới database tên: `secondhand_marketplace` với collation `utf8mb4_unicode_ci`.
   - Chạy file `database/schema.sql` để tạo cấu trúc bảng.
   - Chạy file `database/seed.sql` để nạp dữ liệu mẫu ban đầu.
4. Tạo file cấu hình môi trường:
   - Copy file `.env.example` thành file `.env`.
   - Kiểm tra thông số kết nối Database (mặc định Laragon: user `root`, pass trống `""`).
5. Truy cập trình duyệt:
   - Đường dẫn: `http://localhost/market/public` (hoặc tên miền ảo `http://market.test`).

---

### Cách 2: Sử dụng XAMPP
1. Copy hoặc clone thư mục dự án vào thư mục `C:/xampp/htdocs/market`.
2. Bật Apache và MySQL trong **XAMPP Control Panel**.
3. Mở phpMyAdmin tại `http://localhost/phpmyadmin`:
   - Tạo database `secondhand_marketplace`.
   - Chọn database vừa tạo -> Chọn tab **Import** -> Import lần lượt 2 file:
     - `database/schema.sql`
     - `database/seed.sql`
4. Copy `.env.example` thành `.env` và kiểm tra lại `DB_USER` / `DB_PASS`.
5. Truy cập trình duyệt:
   - Đường dẫn: `http://localhost/market/public`

---

## 👥 Phân chia nhiệm vụ thành viên (Team Breakdown)

| Thành viên | Phụ trách chính | Files Controller & Model | Files View & Asset |
|---|---|---|---|
| **Team Lead** | Khung Core MVC, Database, Routing | `Core/*`, `Config/*`, `schema.sql` | `layouts/*`, `global.css`, `main.js` |
| **Thành viên 1** | Xác thực & Quản lý tài khoản | `AuthController.php`, `User.php` | `Views/auth/*`, `Views/profile/*` |
| **Thành viên 2** | Quản lý sản phẩm & Tìm kiếm/Lọc | `ProductController.php`, `Product.php` | `Views/products/*`, `products.css`, `products.js` |
| **Thành viên 3** | Nhắn tin & Trao đổi thời gian thực | `ChatController.php`, `Message.php` | `Views/chat/*`, `chat.css`, `chat.js` |
| **Thành viên 4** | Đặt hàng & Đánh giá người bán | `OrderController.php`, `Order.php`, `ReviewController.php`, `Review.php` | `Views/orders/*`, `orders.css`, `orders.js` |

---

## 🛡️ Quy tắc làm việc với Git

1. **Không commit đè trực tiếp lên nhánh `main`:**
   - Mỗi thành viên tạo nhánh riêng để phát triển:
     - `git checkout -b feature/auth` (Làm tính năng Auth)
     - `git checkout -b feature/product-crud` (Làm sản phẩm)
     - `git checkout -b feature/chat-ajax` (Làm nhắn tin)
     - `git checkout -b feature/order-review` (Làm đơn hàng & đánh giá)
2. **Quy tắc commit message:**
   - `feat: thêm chức năng lọc theo khoảng giá và danh mục`
   - `fix: sửa lỗi không nhận ảnh khi đăng bán sản phẩm`
   - `style: cập nhật giao diện khung chat`
3. **Trước khi tạo Pull Request:**
   - Kéo code mới nhất từ nhánh `main` về nhánh của mình: `git pull origin main`.
   - Test lại các chức năng đảm bảo không phát sinh xung đột (conflict).
   - **Tuyệt đối không đẩy file `.env` hoặc file ảnh cá nhân trong `uploads/` lên GitHub!**

