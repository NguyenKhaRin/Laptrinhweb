<?php
/**
 * ========================================================
 * FRONT CONTROLLER - ĐIỂM ĐẦU VÀO DUY NHẤT CỦA HỆ THỐNG
 * ========================================================
 */

// Bắt đầu Session nếu chưa chạy
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Định nghĩa thư mục gốc
define('ROOT_DIR', dirname(__DIR__));

// Tự động nạp các class thuộc namespace App\
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = ROOT_DIR . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Nạp các hằng số cấu hình hệ thống
require_once ROOT_DIR . '/app/Config/constants.php';
require_once ROOT_DIR . '/app/Config/database.php';

use App\Core\Router;
use App\Core\AuthMiddleware;
use App\Core\AdminMiddleware;
use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\ChatController;
use App\Controllers\OrderController;
use App\Controllers\ReviewController;
use App\Controllers\AdminController;

// ========================================================
// ĐĂNG KÝ DANH SÁCH ROUTE (ĐIỀU HƯỚNG URL)
// ========================================================

// 1. Module Sản phẩm & Trang chủ
Router::get('/', [ProductController::class, 'index']);
Router::get('/products', [ProductController::class, 'index']);
Router::get('/products/detail/{id}', [ProductController::class, 'detail']);
Router::get('/products/create', [ProductController::class, 'create'], [AuthMiddleware::class]);
Router::post('/products/store', [ProductController::class, 'store'], [AuthMiddleware::class]);
Router::get('/products/edit/{id}', [ProductController::class, 'edit'], [AuthMiddleware::class]);
Router::post('/products/update/{id}', [ProductController::class, 'update'], [AuthMiddleware::class]);
Router::post('/products/delete/{id}', [ProductController::class, 'delete'], [AuthMiddleware::class]);
Router::get('/profile/seller/{id}', [ProductController::class, 'sellerProfile']);

// 2. Module Xác thực (Auth)
Router::get('/login', [AuthController::class, 'login']);
Router::post('/login', [AuthController::class, 'handleLogin']);
Router::get('/register', [AuthController::class, 'register']);
Router::post('/register', [AuthController::class, 'handleRegister']);
Router::get('/logout', [AuthController::class, 'logout']);

// 3. Module Nhắn tin trao đổi (Chat)
Router::get('/chat', [ChatController::class, 'inbox'], [AuthMiddleware::class]);
Router::get('/api/chat/messages', [ChatController::class, 'getMessages'], [AuthMiddleware::class]);
Router::post('/api/chat/send', [ChatController::class, 'sendMessage'], [AuthMiddleware::class]);

// 4. Module Đơn hàng & Giao dịch
Router::post('/orders/store', [OrderController::class, 'store'], [AuthMiddleware::class]);
Router::get('/orders/buyer', [OrderController::class, 'buyerOrders'], [AuthMiddleware::class]);
Router::get('/orders/seller', [OrderController::class, 'sellerOrders'], [AuthMiddleware::class]);
Router::get('/orders/detail/{id}', [OrderController::class, 'detail'], [AuthMiddleware::class]);
Router::post('/orders/update-status', [OrderController::class, 'updateStatus'], [AuthMiddleware::class]);

// 5. Module Đánh giá (Review)
Router::post('/reviews/store', [ReviewController::class, 'store'], [AuthMiddleware::class]);
Router::get('/profile/reviews/{seller_id}', [ReviewController::class, 'sellerReviews']);

// 6. Module Quản trị Admin (TV1 & TV5)
Router::get('/admin', [AdminController::class, 'dashboard'], [AdminMiddleware::class]);
Router::get('/admin/dashboard', [AdminController::class, 'dashboard'], [AdminMiddleware::class]);
Router::get('/admin/users', [AdminController::class, 'users'], [AdminMiddleware::class]);
Router::post('/admin/users/update-role', [AdminController::class, 'updateRole'], [AdminMiddleware::class]);
Router::get('/admin/products', [AdminController::class, 'products'], [AdminMiddleware::class]);
Router::post('/admin/products/delete/{id}', [AdminController::class, 'deleteProduct'], [AdminMiddleware::class]);

// ========================================================
// TÍNH TOÁN REQUEST URI VÀ DISPATCH ROUTER
// ========================================================

// Lấy URI hiện tại và chuẩn hóa
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Loại bỏ base folder path nếu chạy trong thư mục con (ví dụ /market/public)
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptName !== '/' && str_starts_with($requestUri, $scriptName)) {
    $requestUri = substr($requestUri, strlen($scriptName));
}

$requestUri = '/' . trim($requestUri, '/');
if (empty($requestUri) || $requestUri === '') {
    $requestUri = '/';
}

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Thực thi điều hướng
Router::dispatch($requestUri, $requestMethod);

