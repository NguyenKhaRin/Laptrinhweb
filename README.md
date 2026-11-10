# Sàn Giao Dịch Đồ Cũ Trực Tuyến (Secondhand Marketplace)

> **Mô hình kiến trúc:** MVC thuần (PHP 8+ / PDO / MySQL / HTML5 / CSS3 / JavaScript Fetch API / Bootstrap 5)  
> **Mục tiêu:** Hệ thống trao đổi, thanh lý đồ cũ C2C sinh viên và người dùng, phân tách module độc lập cho nhóm 5 thành viên.

---

## 📁 Cấu trúc thư mục dự án

```text
market/
├── app/                            # Chứa toàn bộ logic nghiệp vụ (PHP MVC)
│   ├── Config/                     # Cấu hình kết nối & hằng số hệ thống
│   │   ├── database.php            # Kết nối PDO Singleton tới MySQL
│   │   └── constants.php           # Định nghĩa BASE_URL, ROOT_PATH, UPLOAD_PATH, hàm env()
│   │
│   ├── Core/                       # Khung sườn MVC dùng chung (Core Framework)
│   │   ├── Router.php              # Điều hướng URL, dynamic routing {id}, dispatch request
│   │   ├── Controller.php          # Base Controller (view, json, redirect, session user, flash)
│   │   ├── Model.php               # Base Model (PDO CRUD: all, find, where, create, update, delete)
│   │   ├── AuthMiddleware.php      # Middleware kiểm tra đăng nhập hệ thống
│   │   └── AdminMiddleware.php     # Middleware kiểm tra phân quyền Quản trị viên (role: admin)
│   │
│   ├── Controllers/                # Tầng điều khiển nhận & xử lý request
│   │   ├── AuthController.php      # [TV1] Đăng nhập, đăng ký, đăng xuất
│   │   ├── ProductController.php   # [TV2] Đăng bán, sửa/xóa, upload ảnh, chi tiết SP, tìm kiếm
│   │   ├── ChatController.php      # [TV3] API gửi/nhận tin nhắn real-time (AJAX polling)
│   │   ├── OrderController.php     # [TV1] Tạo đơn, xem đơn mua/bán, cập nhật trạng thái đơn
│   │   ├── ReviewController.php    # [TV2] Đánh giá uy tín & chấm điểm sao người bán
│   │   └── AdminController.php     # [TV1] Bảng điều khiển quản trị, quản lý user & duyệt bài
│   │
│   ├── Models/                     # Tương tác trực tiếp với cơ sở dữ liệu (SQL PDO)
│   │   ├── User.php                # [TV1] Quản lý tài khoản, tự động tính lại điểm sao uy tín
│   │   ├── Product.php             # [TV2] Lọc sản phẩm đa điều kiện, chi tiết kèm thông tin seller
│   │   ├── Message.php             # [TV3] Lưu trữ tin nhắn, lịch sử hội thoại, đánh dấu đã đọc
│   │   ├── Order.php               # [TV1] Đơn hàng người mua / người bán, đổi trạng thái
│   │   └── Review.php              # [TV2] Đánh giá đơn hàng và phản hồi người bán
│   │
│   └── Views/                      # Giao diện HTML nhúng PHP (Tách theo module độc lập)
│       ├── layouts/                # [TV4] Khung giao diện dùng chung
│       │   ├── header.php          # Head HTML, Bootstrap 5 CDN, Icons, Fonts
│       │   ├── navbar.php          # Thanh điều hướng theo role (Khách / User / Admin)
│       │   └── footer.php          # Chân trang & nạp scripts
│       ├── auth/                   # [TV1 & TV4] Giao diện xác thực
│       │   ├── login.php           # Form đăng nhập
│       │   └── register.php        # Form đăng ký thành viên
│       ├── products/               # [TV4] Giao diện bài đăng & tìm kiếm
│       │   ├── index.php           # Danh sách đồ cũ + Sidebar bộ lọc đa tiêu chí
│       │   ├── create.php          # Form đăng bán đồ cũ kèm preview ảnh tức thì
│       │   ├── edit.php            # Form cập nhật thông tin bài đăng
│       │   └── detail.php          # Chi tiết sản phẩm, modal đặt mua & liên hệ seller
│       ├── chat/                   # [TV5] Giao diện khung chat
│       │   └── inbox.php           # Khung chat 2 cột (danh sách hội thoại + khung chat real-time)
│       ├── orders/                 # [TV5] Giao diện quản lý giao dịch
│       │   ├── buyer_orders.php    # Đơn hàng tôi đã mua + modal đánh giá sao
│       │   ├── seller_orders.php   # Đơn bán hàng + nút cập nhật trạng thái nhanh
│       │   └── detail.php          # Chi tiết đơn, địa chỉ giao nhận, liên hệ đối phương
│       ├── profile/                # [TV5] Hồ sơ & Đánh giá
│       │   ├── seller_profile.php  # Trang cá nhân người bán & danh sách đồ đang rao
│       │   └── reviews.php         # Danh sách đánh giá chi tiết từ người mua
│       └── admin/                  # [TV5] Giao diện Quản trị viên
│           ├── dashboard.php       # Bảng điều khiển KPI (tổng user, bài đăng, doanh số)
│           ├── manage_users.php    # Quản lý tài khoản thành viên & phân quyền
│           ├── users.php           # Bảng danh sách người dùng
│           └── products.php        # Bảng duyệt & gỡ bài đăng vi phạm
│
├── public/                         # Thư mục gốc DocumentRoot mở ra ngoài web
│   ├── index.php                   # [TV1] Điểm đầu vào duy nhất (Front Controller + Autoload + Route)
│   ├── .htaccess                   # Rewrite URL ẩn đuôi .php chuẩn Apache
│   ├── assets/
│   │   ├── css/                    # Chia file CSS theo từng module để tránh xung đột
│   │   │   ├── global.css          # [TV4] Biến màu sắc, reset CSS, layout chung
│   │   │   ├── products.css        # [TV4] CSS riêng cho sản phẩm & bộ lọc
│   │   │   ├── chat.css            # [TV5] CSS riêng cho bong bóng chat
│   │   │   ├── orders.css          # [TV5] CSS riêng cho đơn hàng & đánh giá
│   │   │   └── admin.css           # [TV5] CSS riêng cho bảng điều khiển Admin
│   │   ├── js/                     # Chia file JS xử lý sự kiện & Fetch/AJAX
│   │   │   ├── main.js             # [TV4] Hàm tiện ích (format tiền tệ, auto alert dismiss)
│   │   │   ├── products.js         # [TV4] Preview ảnh upload, tự động submit filter
│   │   │   ├── chat.js             # [TV3] Fetch API polling tin nhắn real-time
│   │   │   ├── orders.js           # [TV5] Đổi trạng thái đơn hàng AJAX
│   │   │   └── admin.js            # [TV5] Thao tác bảng quản trị
│   │   └── images/                 # Logo, avatar mặc định (.gitkeep)
│   └── uploads/                    # Lưu trữ ảnh người dùng tải lên (Được Git ignore)
│       ├── products/               # Ảnh sản phẩm đồ cũ (.gitkeep)
│       └── avatars/                # Ảnh đại diện cá nhân (.gitkeep)
│
├── database/                       # Quản lý phiên bản Database cho cả nhóm
│   ├── schema.sql                  # [TV1] Tạo toàn bộ cấu trúc bảng (có role: admin, seller, buyer)
│   └── seed.sql                    # [TV1] Dữ liệu mẫu (tài khoản demo, đồ cũ mẫu, tin nhắn, đơn hàng)
│
├── .env.example                    # File mẫu cấu hình kết nối DB
├── .env                            # File cấu hình môi trường thực tế (bỏ qua trên Git)
├── .gitignore                      # Loại trừ file .env, ảnh upload và file tạm
└── README.md                       # Tài liệu hướng dẫn cài đặt & phân chia nhiệm vụ nhóm
```

---

## 1. 🛡️ HỆ THỐNG PHÂN QUYỀN NGƯỜI DÙNG (USER ROLES & PERMISSIONS)

Hệ thống sàn giao dịch đồ cũ C2C chia thành **3 vai trò chính** (lưu ở cột `role` trong bảng `users`):

### 👑 ROLE: ADMIN (Quản trị viên) — `role = 'admin'`
> **Mục tiêu:** Quản trị toàn bộ hệ thống, kiểm soát an toàn và tính minh bạch của sàn giao dịch.  
> **Quyền hạn & Luồng chức năng cốt lõi:**
> - **Quản lý người dùng:** Xem danh sách thành viên, cập nhật phân quyền (buyer/seller/admin), khóa tài khoản có hành vi gian lận.
> - **Duyệt & Quản lý bài đăng:** Gỡ bỏ các tin đăng thanh lý vi phạm chính sách (hàng cấm, hình ảnh phản cảm, thông tin rác).
> - **Trọng tài tranh chấp:** Xem thông tin giao dịch, can thiệp giải quyết khi người mua/người bán phát sinh khiếu nại.
> - **Thống kê tổng quan (KPI Dashboard):** Theo dõi số lượng bài đăng, số giao dịch hoàn tất, tổng giá trị trao đổi trên sàn.
> 
> **Thành viên phụ trách:** **TV1** (Backend Core & Admin Middleware) & **TV5** (Frontend Admin UI).

---

### 📦 ROLE: SELLER (Người bán) — `role = 'seller'` (hoặc `user`)
> **Mục tiêu:** Người dùng đăng tải đồ cũ cần thanh lý lên hệ thống và thương lượng bán cho khách.  
> **Quyền hạn & Luồng chức năng cốt lõi:**
> - **Đăng tin thanh lý:** Đăng bài kèm ảnh thực tế, mô tả chi tiết độ mới (% cũ: Mới 100%, Như mới 99%, Tốt 90%, Dùng ổn), giá thanh lý, địa chỉ xem hàng.
> - **Quản lý bài bán:** Chỉnh sửa giá, cập nhật trạng thái bài đăng (`available` -> `reserved` -> `sold` -> `hidden`), xóa bài đăng của chính mình.
> - **Thương lượng qua chat:** Tiếp nhận tin nhắn real-time từ người mua, trao đổi hẹn gặp xem đồ và chốt giá.
> - **Quản lý đơn bán:** Tiếp nhận yêu cầu đặt mua, cập nhật trạng thái: `Xác nhận đơn`, `Bắt đầu giao hàng / Hẹn gặp`, hoặc `Hủy đơn`.
> 
> **Thành viên phụ trách:** **TV2** (Backend Product/Catalog) & **TV4** (Frontend Catalog & Form Đăng Bán).

---

### 🛒 ROLE: BUYER (Người mua) — `role = 'buyer'` (hoặc `user`)
> **Mục tiêu:** Người tìm kiếm các món đồ cũ phù hợp nhu cầu, trao đổi và chốt giao dịch.  
> **Quyền hạn & Luồng chức năng cốt lõi:**
> - **Tìm kiếm & Lọc:** Tìm đồ cũ theo từ khóa, lọc theo danh mục, khoảng giá (min - max), tình trạng độ mới, vị trí địa lý.
> - **Trao đổi trực tiếp:** Nhắn tin real-time với người bán gắn liền với từng sản phẩm đồ cũ cụ thể.
> - **Đặt mua & Quản lý đơn:** Bấm đặt mua nhanh, điền địa chỉ & SĐT nhận hàng, theo dõi tiến độ đơn, bấm xác nhận hoàn tất giao dịch.
> - **Đánh giá & Phản hồi:** Chấm điểm sao (1 - 5 sao) và để lại bình luận nhận xét về uy tín người bán sau khi nhận hàng.
> 
> **Thành viên phụ trách:** **TV3** (Backend Chat Real-time), **TV4** (Frontend Search/Listing) & **TV5** (Frontend Chat & Order Flow).

---

## 2. 📊 BẢNG MA TRẬN PHÂN QUYỀN TRUY CẬP (ACCESS CONTROL LIST - ACL)

| Chức năng / Hành động | Khách vãng lai (Guest) | Người mua (Buyer) | Người bán (Seller) | Admin sàn |
|---|:---:|:---:|:---:|:---:|
| **Xem trang chủ, tìm kiếm & lọc đồ cũ** | ✅ Cho phép | ✅ Cho phép | ✅ Cho phép | ✅ Cho phép |
| **Xem chi tiết bài đăng & hồ sơ người bán** | ✅ Cho phép | ✅ Cho phép | ✅ Cho phép | ✅ Cho phép |
| **Đăng ký, Đăng nhập hệ thống** | ✅ Cho phép | ✅ Cho phép | ✅ Cho phép | ✅ Cho phép |
| **Đăng bài bán đồ cũ mới** | 🔒 Yêu cầu đăng nhập | 🔒 Cần mở chế độ bán | ✅ Cho phép | 🛡️ Cho phép kiểm duyệt |
| **Chỉnh sửa / Xóa bài đăng đồ cũ** | ❌ Không | ❌ Không | ✅ Chỉ bài của mình | 🛡️ Toàn quyền xóa bài vi phạm |
| **Chat trao đổi trực tiếp với người bán** | 🔒 Yêu cầu đăng nhập | ✅ Cho phép | ✅ Cho phép | 🛡️ Xem khi tranh chấp |
| **Tạo yêu cầu mua hàng / Đặt đơn** | 🔒 Yêu cầu đăng nhập | ✅ Cho phép | ❌ Không tự mua đồ mình | 🛡️ Quản lý / Trọng tài |
| **Cập nhật tiến độ đơn hàng (Giao/Hủy)** | ❌ Không | 📦 Xác nhận / Hủy đơn | 🚚 Xác nhận & Giao hàng | 🛡️ Can thiệp xử lý |
| **Đánh giá sao & Nhận xét uy tín** | ❌ Không | ⭐ Cho phép (sau khi mua) | ❌ Không tự đánh giá mình | 🛡️ Xóa đánh giá ảo |
| **Truy cập Bảng Quản trị Admin Dashboard** | ❌ Không | ❌ Bị chặn (403) | ❌ Bị chặn (403) | 🛡️ Toàn quyền quản trị |
| **Quản lý danh sách thành viên & Phân quyền** | ❌ Không | ❌ Không | ❌ Không | 🛡️ Toàn quyền |

---

## 3. 👥 PHÂN CHIA VAI TRÒ DỰ ÁN VÀ QUYỀN LÀM VIỆC (5 THÀNH VIÊN)

### 🥇 THÀNH VIÊN 1: Team Leader & Backend Core
* **Phụ trách:** Khung MVC Core, Cơ sở dữ liệu, Xác thực Auth, Phân quyền Role Middleware, Module Giao dịch & Quản trị Admin Backend.
* **Quyền hạn & Nghiệp vụ đảm nhận:**
  - Thiết kế cấu trúc thư mục, dựng `Router.php`, `Controller.php`, `Model.php`.
  - Xây dựng hệ thống phân quyền (`AuthMiddleware.php`, `AdminMiddleware.php`).
  - Viết logic Xác thực tài khoản (`AuthController.php`), Đơn hàng (`OrderController.php`) và Quản trị (`AdminController.php`).
  - Quản lý phiên bản Database (`schema.sql`, `seed.sql`).
* **Vùng file độc quyền (Ownership):**
  - `app/Config/*` (`database.php`, `constants.php`)
  - `app/Core/*` (`Router.php`, `Controller.php`, `Model.php`, `AuthMiddleware.php`, `AdminMiddleware.php`)
  - `app/Controllers/AuthController.php`, `OrderController.php`, `AdminController.php`
  - `app/Models/User.php`, `Order.php`
  - `database/schema.sql`, `database/seed.sql`
  - `public/index.php`, `public/.htaccess`, `.env.example`

---

### 🥈 THÀNH VIÊN 2: Backend Catalog & Reviews Specialist
* **Phụ trách:** Nghiệp vụ Người bán (Seller) đăng bán hàng & Nghiệp vụ Người mua (Buyer) tìm kiếm, đánh giá uy tín.
* **Quyền hạn & Nghiệp vụ đảm nhận:**
  - Viết logic xử lý sản phẩm: thêm mới, upload ảnh vào `public/uploads/products/`, chỉnh sửa, cập nhật trạng thái đã bán, xóa bài.
  - Viết logic lọc sản phẩm đa tiêu chí (keyword, category, khoảng giá, condition, sort).
  - Viết logic tạo đánh giá sao (1-5 sao) và nhận xét cho đơn hàng đã hoàn tất; tự động tính lại `rating_avg` cho người bán.
* **Vùng file độc quyền (Ownership):**
  - `app/Controllers/ProductController.php`
  - `app/Controllers/ReviewController.php`
  - `app/Models/Product.php`
  - `app/Models/Review.php`

---

### 🥉 THÀNH VIÊN 3: Real-time & API Engineer
* **Phụ trách:** Module Chat trao đổi & thương lượng giá trực tiếp giữa Buyer và Seller.
* **Quyền hạn & Nghiệp vụ đảm nhận:**
  - Xây dựng hạ tầng nhắn tin trực tiếp giữa Buyer và Seller theo thời gian thực (AJAX polling / Fetch API).
  - Thiết kế API lưu trữ tin nhắn theo ngữ cảnh sản phẩm (`product_id`), đánh dấu đã đọc (`is_read`), lấy danh sách cuộc trò chuyện gần nhất.
  - Viết logic client-side xử lý tự động cuộn khung chat, gửi tin không reload trang.
* **Vùng file độc quyền (Ownership):**
  - `app/Controllers/ChatController.php`
  - `app/Models/Message.php`
  - `public/assets/js/chat.js` (logic polling & Fetch API tin nhắn)

---

### 🎨 THÀNH VIÊN 4: Frontend Engineer 1 (Catalog & Listing UI)
* **Phụ trách:** Giao diện Khách vãng lai, Người mua tìm kiếm sản phẩm & Form đăng bán của Người bán.
* **Quyền hạn & Nghiệp vụ đảm nhận:**
  - Thiết kế Layout khung dùng chung: Header, Footer, Navbar responsive tự nhận diện trạng thái đăng nhập và role hiển thị menu phù hợp.
  - Xây dựng giao diện Trang chủ & Trang tìm kiếm sản phẩm kèm Sidebar bộ lọc trực quan.
  - Thiết kế Form đăng bán đồ cũ có xem trước ảnh (image preview) trước khi upload.
  - Xây dựng trang chi tiết sản phẩm và giao diện đăng nhập / đăng ký.
* **Vùng file độc quyền (Ownership):**
  - `app/Views/layouts/*` (`header.php`, `navbar.php`, `footer.php`)
  - `app/Views/products/*` (`index.php`, `create.php`, `edit.php`, `detail.php`)
  - `app/Views/auth/*` (`login.php`, `register.php`)
  - `public/assets/css/global.css`, `products.css`
  - `public/assets/js/main.js`, `products.js`

---

### 💻 THÀNH VIÊN 5: Frontend Engineer 2 (Chat, Order, Review & Admin UI)
* **Phụ trách:** Giao diện Khung chat, Quản lý đơn mua/bán của Buyer/Seller, Đánh giá & Bảng điều khiển Quản trị Admin.
* **Quyền hạn & Nghiệp vụ đảm nhận:**
  - Dựng giao diện màn hình Chat 2 cột giữa Buyer và Seller (bong bóng tin nhắn, danh sách hội thoại).
  - Thiết kế trang Quản lý đơn hàng tôi đã mua (`buyer_orders.php`) kèm modal gửi đánh giá sao.
  - Thiết kế trang Quản lý đơn bán (`seller_orders.php`) kèm các nút thao tác nhanh đổi trạng thái đơn.
  - Xây dựng trang chi tiết đơn hàng (`orders/detail.php`) và hồ sơ người bán (`profile/seller_profile.php`).
  - Dựng giao diện Bảng điều khiển Admin (`dashboard.php`, `manage_users.php`, `products.php`).
* **Vùng file độc quyền (Ownership):**
  - `app/Views/chat/*` (`inbox.php`)
  - `app/Views/orders/*` (`buyer_orders.php`, `seller_orders.php`, `detail.php`)
  - `app/Views/profile/*` (`seller_profile.php`, `reviews.php`)
  - `app/Views/admin/*` (`dashboard.php`, `manage_users.php`, `users.php`, `products.php`)
  - `public/assets/css/chat.css`, `orders.css`, `admin.css`
  - `public/assets/js/orders.js`, `admin.js`

---

## 🚀 Hướng dẫn cài đặt môi trường (Laragon / XAMPP)

### Cách 1: Sử dụng Laragon (Khuyên dùng)
1. Copy hoặc clone thư mục dự án vào thư mục `C:/laragon/www/market`.
2. Khởi động Laragon (bấm **Start All**).
3. Mở **HeidiSQL** hoặc **phpMyAdmin** trên Laragon:
   - Tạo mới database tên: `secondhand_marketplace` với bảng mã `utf8mb4_unicode_ci`.
   - Chạy file `database/schema.sql` để tạo toàn bộ cấu trúc bảng.
   - Chạy file `database/seed.sql` để nạp dữ liệu mẫu ban đầu.
4. Kiểm tra file cấu hình:
   - File `.env` đã được tạo sẵn từ `.env.example`.
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
4. Kiểm tra file `.env` (mặc định XAMPP: `DB_USER=root`, `DB_PASS=`).
5. Truy cập trình duyệt:
   - Đường dẫn: `http://localhost/market/public`

---

### 🔑 Tài khoản mẫu thử nghiệm (Mật khẩu mặc định: `123456`)

| Quyền hạn | Email đăng nhập | Mật khẩu | Chức năng kiểm thử chính |
|---|---|---|---|
| **Admin** | `admin@marketplace.test` | `123456` | Truy cập `/admin`, quản lý user, đổi phân quyền, gỡ bài vi phạm |
| **Seller** | `seller@marketplace.test` | `123456` | Đăng tin thanh lý đồ cũ, sửa bài, quản lý đơn hàng bán, chat |
| **Buyer** | `buyer@marketplace.test` | `123456` | Lọc đồ cũ, chat trả giá, đặt mua hàng, xác nhận nhận hàng, đánh giá sao |

---

## 🛡️ Quy tắc làm việc nhóm với Git

1. **Không commit trực tiếp lên nhánh `main`:**
   - Mỗi thành viên tạo và làm việc trên nhánh riêng theo tính năng:
     - TV1: `git checkout -b feature/auth-admin-core`
     - TV2: `git checkout -b feature/product-reviews`
     - TV3: `git checkout -b feature/realtime-chat`
     - TV4: `git checkout -b feature/catalog-listing-ui`
     - TV5: `git checkout -b feature/order-chat-admin-ui`
2. **Quy tắc commit message chuẩn Conventional Commits:**
   - `feat: thêm chức năng lọc theo khoảng giá và danh mục`
   - `fix: sửa lỗi không nhận ảnh khi đăng bán sản phẩm`
   - `style: cập nhật giao diện khung chat`
3. **Trước khi tạo Pull Request / Merge:**
   - Kéo code mới nhất từ nhánh `main` về nhánh của mình: `git pull origin main`.
   - Test lại các chức năng để đảm bảo không bị xung đột (conflict).
   - **Tuyệt đối không commit file `.env` hoặc file ảnh thực tế trong `uploads/` lên GitHub!**
